<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\User;
use App\Entity\Ville;
use App\Form\AddUserType;
use App\Form\ImportcsvType;
use App\Form\SiteType;
use App\Form\UserType;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends Controller
{


    /**
     * @Route("/sortir/admin/addUtilisateur", name="addUtilisateur")
     */
    public function addUser(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        $user = new User();
        $userForm = $this->createForm(AddUserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {

            // Enregistrer fichier photo

            $username = $userForm->get('username')->getData();
            $fichier = $userForm->get('fichier')->getNormData();

            if ($fichier->isValid()) {
                $fichier->move($this->getParameter('photo_directory'), $username);
            }


            //hasher le mot de passe
            $hashed = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hashed);
            //Enregistrer le user dans la BD
            $em->persist($user);
            $em->flush();
            $this->addFlash("success", "L'utilisateur a été ajouté");
            $this->redirectToRoute("list");
        }

        return $this->render("admin/addUtilisateur.html.twig",
            ["userForm" => $userForm->createView()]);

    }

    /**
     * @Route("/sortir/admin/ville", name="ville")
     * @Template()
     */
    public function ville(Request $request, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        $villeRepo = $this->getDoctrine()->getRepository(Ville::class);
        $villes = $villeRepo->findBy([], ['nom' => 'ASC']);

        $newVille = new Ville();
        $villeForm = $this->createForm(VilleType::class, $newVille);
        $villeForm->handleRequest($request);

        if ($villeForm->isSubmitted() && $villeForm->isValid()) {

            $em->persist($newVille);
            $em->flush();
            $this->addFlash("success", "La ville a été ajouté");
            $this->redirectToRoute("list");
        }

        return $this->render('admin/ville.html.twig',
            ['villes' => $villes, "villeForm" => $villeForm->createView()]);
    }


    /**
     * @Route("/sortir/admin/site", name="site")
     * * @Template()
     */
    public function site(Request $request, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        $siteRepo = $this->getDoctrine()->getRepository(Site::class);
        $sites = $siteRepo->findBy([], ['nom' => 'ASC']);

        $newSite = new Site();
        $siteForm = $this->createForm(SiteType::class, $newSite);
        $siteForm->handleRequest($request);

        if ($siteForm->isSubmitted() && $siteForm->isValid()) {

            $em->persist($newSite);
            $em->flush();
            $this->addFlash("success", "Le site a été ajouté");
            $this->redirectToRoute("list");
        }

        return $this->render('admin/site.html.twig',
            ['sites' => $sites, "siteForm" => $siteForm->createView()]);

    }

    /**
     * @Route("/sortir/admin/site/{id}",name="deleteSite")
     */
    public function deleteSite(Request $request, EntityManagerInterface $em, $id)

    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $siteRepo = $this->getDoctrine()->getRepository(Site::class);
        $siteDelete = $siteRepo->find($id);


        if (($siteDelete->getUsers() > 1) or ($siteDelete->getSorties() > 1)) {

            dump($siteDelete->getUsers());
            dump($siteDelete->getSorties());
            $this->addFlash("success", "Des utilisateurs ou des sorties sont attaché(e)s à ce site, imposible de supprimer.");
            $this->redirectToRoute("site");

        } elseif (($siteDelete->getUsers() == null) or ($siteDelete->getSorties() == null)) {


            dump($siteDelete->getUsers());
            dump($siteDelete->getSorties());
            $em = $this->getDoctrine()->getManager();
            $em->remove($siteDelete);
            $em->flush();
            $this->addFlash("success", "Le site a été supprimé");
            $this->redirectToRoute("site");

        } else {

            dump($siteDelete->getUsers());
            dump($siteDelete->getSorties());
            $em = $this->getDoctrine()->getManager();
            $em->remove($siteDelete);
            $em->flush();
            $this->addFlash("success", "Le site a été supprimé");
            $this->redirectToRoute("site");

        }


        $siteRepo = $this->getDoctrine()->getRepository(Site::class);
        $sites = $siteRepo->findBy([], ['nom' => 'ASC']);

        $newSite = new Site();
        $siteForm = $this->createForm(SiteType::class, $newSite);
        $siteForm->handleRequest($request);


        return $this->render('admin/site.html.twig', ['sites' => $sites, "siteForm" => $siteForm->createView()]);

    }


    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    /**
     * @Route("/sortir/admin/importCSV",name="importCSV")
     */
    public function addCSV(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        //on récupère le fichier
        $importcsvForm = $this->createForm(ImportcsvType::class);
        $importcsvForm->handleRequest($request);

        if ($importcsvForm->isSubmitted() && $importcsvForm->isValid()) {

            if ($importcsvForm['fichier']->getData() != null) {
                dump($importcsvForm['fichier']);
                $file = $importcsvForm['fichier']->getData();
                $fileName = 'participants.csv';

                //on stocke le fichier pour ensuite le traiter
                $file->move($this->getParameter('photo_directory'), $fileName);

                //on traite le fichier
                $utilisateurs = array(); // $utilisateurs va contenir les users extraits du fichier csv
                $ligne = 0;

                // Import du fichier CSV
                //$handle = fichier à manipuler
                //$data = ligne du fichier à traiter
                if (($handle = fopen("./photoUser/" . $fileName, "r")) !== FALSE) { // Lecture du fichier, à adapter
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) { // Eléments séparés par un point-virgule, à modifier si necessaire
                        $num = count($data); // Nombre d'éléments sur la ligne traitée
                        $ligne++;
                        for ($c = 0; $c < $num; $c++) {
                            $utilisateurs[$ligne] = array(
                                "nom" => $data[0],
                                "prenom" => $data[1],
                                "telephone" => $data[2],
                                "email" => $data[3],
                                "idSite" => $data[4],
                                "username"=>$data[5]
                            );
                        }
                    }
                    fclose($handle);
                }

                $siteRepo = $em->getRepository(Site::class);

                //on ajoute en bdd les utilisateurs du tableau $utilisateurs
                foreach ($utilisateurs as $utilisateur) {
                    $user = new User();

                    $hashed = $encoder->encodePassword($user, "123");
                    $user->setPassword($hashed);
                    $user->setActif(true);
                    $user->setAdmin(false);

                    $user->setNom($utilisateur['nom']);
                    $user->setPrenom($utilisateur['prenom']);
                    $user->setMail($utilisateur['email']);
                    $user->setTelephone($utilisateur['telephone']);
                    $user->setSite($siteRepo->find($utilisateur['idSite']));
                    $user->setUsername($utilisateur['username']);


                    $em->persist($user);
                }
                $em->flush();
                $this->addFlash("success", "Utilisateurs créés avec succès !");
                $this->redirectToRoute('list');

            } else {
                $this->addFlash("danger", "Aucun fichier soumis");
            }
        }
        return $this->render('admin/importcsv.html.twig',['importcsvForm' => $importcsvForm->createView()]);
    }
}
