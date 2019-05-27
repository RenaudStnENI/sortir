<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\User;
use App\Entity\Ville;
use App\Form\AddUserType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends Controller
{


    /**
     * @Route("/sortir/admin/addUtilisateur", name="addUtilisateur")
     */
    public function addUser (Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        $user = new User();
        $userForm=$this->createForm(AddUserType::class,$user);
        $userForm->handleRequest($request);

        if($userForm->isSubmitted() && $userForm->isValid()){

            // Enregistrer fichier photo

            $username = $userForm->get('username')->getData();
            $fichier = $userForm->get('fichier')->getNormData();

            if ($fichier->isValid())
            {
                $fichier->move($this->getParameter('photo_directory'),$username);
            }


            //hasher le mot de passe
            $hashed=$encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($hashed);
            //Enregistrer le user dans la BD
            $em->persist($user);
            $em->flush();
            $this->addFlash("success","L'utilisateur a été ajouté");
            $this->redirectToRoute("list");
        }

        return $this->render("admin/addUtilisateur.html.twig",
            ["userForm"=>$userForm->createView()]);

    }

    /**
     * @Route("/sortir/admin/ville", name="ville")
     * @Template()
     */
    public  function ville()
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        $villeRepo=$this->getDoctrine()->getRepository(Ville::class);
        $villes=$villeRepo->findBy([],['nom'=> 'ASC']);

        return $this->render('admin/ville.html.twig',
            ['villes'=>$villes]);
    }



    /**
     * @Route("/sortir/admin/site", name="site")
     * * @Template()
     */
    public function site()
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        $siteRepo=$this->getDoctrine()->getRepository(Site::class);
        $sites=$siteRepo->findBy([],['nom'=> 'ASC']);

        return $this->render('admin/site.html.twig',
            ['sites'=>$sites]);

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
}
