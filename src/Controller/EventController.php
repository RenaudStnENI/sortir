<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\AnnuleType;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends Controller
{


    /**
     * @Route("/sortir/add", name="add")
     */
    public function add(EntityManagerInterface $em, Request $request)
    {

        $sortie = new Sortie();
        $sortie->setDateHeureDebut(new \DateTime());
        $sortie->setOrganisateur($this->getUser());

        $sortie->setSite($this->getUser()->getSite());
        $sortieForm = $this->createForm(SortieType::class,$sortie);
        $sortieForm->handleRequest($request);

        if($sortie->getDateLimiteInscription() < $sortie->getDateHeureDebut()) {
            if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
                if ($sortieForm->get('publier')->isclicked()) {
                    $etat = $this->getDoctrine()->getManager()->getReference(Etat::class, 3);
                    $sortie->setEtat($etat);
                    $sortie->addUser($this->getUser());
                    $em->persist($sortie);
                    $em->flush();
                    $this->addFlash('success', 'La sortie est bien publié !');
                    return $this->redirectToRoute("list");
                }elseif ($sortieForm->get('enregistrer')->isClicked()){
                    $etat = $this->getDoctrine()->getManager()->getReference(Etat::class, 1);
                    $sortie->setEtat($etat);
                    $em->persist($sortie);
                    $em->flush();
                    $this->addFlash('success', 'La sortie est bien enregistré !');
                    return $this->redirectToRoute("list");
                }
            }
        }else{
            $sortieForm->get('dateLimiteInscription')->addError(new FormError('La date de limite d\'inscription doit etre inferieur à la date de la sortie !'));
        }
        $title="add";
        return $this->render('event/add.html.twig', ["sortieForm"=>$sortieForm->createView(),"title"=>$title]);
    }

    /**
     * @Route("/sortir/modif/{id}", name="sortie_modif")
     */
    public function modifSortie(EntityManagerInterface $em, Request $request, $id)
    {

        $sortieRepo=$this->getDoctrine()->getRepository(sortie::class);
        $sortie = $sortieRepo->find($id);

        $sortieForm = $this->createForm(SortieType::class,$sortie);
        $sortieForm->handleRequest($request);

        $title="add";

        if($sortie->getDateLimiteInscription() < $sortie->getDateHeureDebut()) {
            if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
                if ($sortieForm->get('publier')->isclicked()) {
                    $etat = $this->getDoctrine()->getManager()->getReference(Etat::class, 3);
                    $sortie->setEtat($etat);
                    $em->persist($sortie);
                    $em->flush();
                    $this->addFlash('success', 'La sortie est bien publié !');
                    return $this->redirectToRoute("list");
                } elseif ($sortieForm->get('enregistrer')->isClicked() && $sortie->getEtat()->getId() <= 1) {
                    $etat = $this->getDoctrine()->getManager()->getReference(Etat::class, 1);
                    $sortie->setEtat($etat);
                    $em->persist($sortie);
                    $em->flush();
                    $this->addFlash('success', 'La sortie est bien enregistré !');
                } elseif ($sortieForm->get('enregistrer')->isClicked() && $sortie->getEtat()->getId() > 1){;
                    $sortieForm->addError(new FormError('Votre sortie est deja publier, vous ne pouvez pas l\'enregistrer !'));
                    return $this->render('event/modifSortie.html.twig', ["sortieForm"=>$sortieForm->createView(),"title"=>$title]);
                }
                    return $this->redirectToRoute("list");

            }
        }else{
            $sortieForm->get('dateLimiteInscription')->addError(new FormError('La date de limite d\'inscription doit etre inferieur à la date de la sortie !'));
        }

        return $this->render('event/modifSortie.html.twig', ["sortieForm"=>$sortieForm->createView(),"title"=>$title]);
    }

    /**
     * @Route("/sortir/add/requeteAjax", name="requeteAjax")
     */
    public function requeteAjax(Request $request, EntityManagerInterface $em){
        $select = $request->request->get('choix');
        $lieux = $em->getRepository(Lieu::class)->findBy(array('ville'=>$select));

        $lieuTab = [];
        foreach ($lieux as $lieu){
            $lieuTab[$lieu->getId()] = $lieu->getNom();
        }

        $response = new Response(json_encode($lieuTab));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/sortir/add/requeteLieu", name="requeteLieu")
     */
    public function requeteLieu(Request $request, EntityManagerInterface $em){
        $infoLieu = $request->request->get('detailLieu');
        $detail = $em->getRepository(Lieu::class)->find($infoLieu);

        $lieu = [
          'rue' => $detail->getRue(),
           'latitude' => $detail->getLatitude(),
            'longitude' => $detail->getLongitude(),
            'cp'=> $detail->getVille()->getCp()
        ];

        $response = new Response(json_encode($lieu));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


/**
     * @Route("/sortir/details/{id}",name="details",requirements={"id":"\d+"})
     */

    public function details(EntityManagerInterface $em, Request $request,Sortie $sortie)
    {
        $participants=$sortie->getUsers();

        $user_session = $this->getUser()->getId();

        $id_orga= $sortie->getOrganisateur();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $orga = $userRepo->find($id_orga);

        $title = "Détails";
        //$sortie=$ideaRepo->find($id);

        return $this->render("event/details.html.twig",
//            ["user_session"=>$user,"title" => $title, "sortie" => $sortie]);
            ["title" => $title, "sortie" => $sortie,"user_session"=>$user_session, 'participants'=>$participants, 'orga'=>$orga]);


    }



    /**
     * @Route("/sortir/annule/{id}",name="sortie_annule",requirements={"id":"\d+"})
     */
    public function annule(EntityManagerInterface $em, $id, Request $request, Sortie $sortieobj)
    {
        $user_session = $this->getUser()->getId();

        $title = "Annulée une sortie";

        $sortieRepo = $this->getDoctrine()->getRepository(sortie::class);
        $sortie = $sortieRepo->find($id);


        $annuleForm = $this->createForm(AnnuleType::class, $sortie);
        $annuleForm->handleRequest($request);

        if ($sortie->getEtat()->getLibelle()!='cloturee' or $sortie->getEtat()->getLibelle()!='ouverte') {
            if ($annuleForm->isSubmitted() && $annuleForm->isValid()) {
                if ($annuleForm->get('enregistrer')->isClicked()) {
                    $etat = $this->getDoctrine()->getManager()->getReference(Etat::class, 6);
                    $sortie->setEtat($etat);
                    $em->persist($sortie);
                    $em->flush();
                    $this->addFlash('success', 'La sortie est bien Annulée !');
                    return $this->redirectToRoute("list");
                }
            }
        } else {
            $annuleForm->get('dateLimiteInscription')->addError(new FormError('La date de limite d\'inscription doit etre inferieur à la date de la sortie !'));


        }
        return $this->render('event/annuleSortie.html.twig', ["annuleForm"=>$annuleForm->createView(),"title"=>$title, "sortie"=>$sortieobj,"user_session"=>$user_session]);

    }

    /**
     * @Route("/sortir/addParticipant{id}", name="addParticipant")
     */
    public function addUser(EntityManagerInterface $em, Sortie $sortie){

        $sortie->addUser($this->getUser());

        $em->flush();

        $this->addFlash('success', 'Vous etes inscrit à la sortie !');

        return $this->redirectToRoute('list');;
    }

    /**
     * @Route("/sortir/removeParticipant{id}", name="removeParticipant")
     */
    public function removeUser(EntityManagerInterface $em, Sortie $sortie){

        $sortie->removeUser($this->getUser());

        $em->flush();

        $this->addFlash('success', 'Vous n\'etes plus dans la sortie !');
        return $this->redirectToRoute('list');

    }

    /**
     * @Route("/sortir/publierSortie{id}", name="publier")
     */
    public function publier(EntityManagerInterface $em, Sortie $sortie){
        $etat = $this->getDoctrine()->getManager()->getReference(Etat::class, 3);
        $sortie->setEtat($etat);

        $em->flush();

        $this->addFlash('success', 'Vous avez bien publié la sortie!');
        return $this->redirectToRoute('list');

    }




}

