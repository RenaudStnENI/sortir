<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
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
        $sortie->setOrganisateur($this->getUser()->getId());
        $sortie->setSite($this->getUser()->getSite());
        $sortieForm = $this->createForm(SortieType::class,$sortie);
        $sortieForm->handleRequest($request);

        if($sortie->getDateLimiteInscription() < $sortie->getDateHeureDebut()) {
            if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
                if ($sortieForm->get('publier')->isclicked()) {
                    $etat = $this->getDoctrine()->getManager()->getReference(Etat::class, 2);
                    $sortie->setEtat($etat);
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

        if($sortie->getDateLimiteInscription() < $sortie->getDateHeureDebut()) {
            if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
                if ($sortieForm->get('publier')->isclicked()) {
                    $etat = $this->getDoctrine()->getManager()->getReference(Etat::class, 2);
                    $sortie->setEtat($etat);
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
            'longitude' => $detail->getLongitude()
        ];

        $response = new Response(json_encode($lieu));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


/**
     * @Route("/sortir/details/{id}",name="details",requirements={"id":"\d+"})
     */

    public function details(Sortie $sortie)
    {

        $user_session = $this->getUser()->getId();
        $title = "Détails";
        //$sortie=$ideaRepo->find($id);

        return $this->render("event/details.html.twig",
//            ["user_session"=>$user,"title" => $title, "sortie" => $sortie]);
            ["title" => $title, "sortie" => $sortie,"user_session"=>$user_session]);

    }

    /**
     * @Route("/sortir/detail/inscription/{id}", name="inscription_sortie")
     */
    public function inscription_sortie($id,EntityManagerInterface $em)
    {

        $sortie = $em->getRepository(Sortie::class)->find($id);



        //TODO !!!!


//        return $this->redirectToRoute("details",
//            ['sortie'=>$sortie]);


    }

    /**
     * @Route("/sortir/detail/desistement/{id}", name="desistement_sortie")
     */
    public function desistement_sortie($id,EntityManagerInterface $em)
    {

        $sortie = $em->getRepository(Sortie::class)->find($id);



        //TODO !!!!


//        return $this->redirectToRoute("details",
//            ['sortie'=>$sortie]);


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

        if ($sortie->getDateLimiteInscription() < $sortie->getDateHeureDebut()) {
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
}

