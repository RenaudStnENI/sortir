<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
                    $this->addFlash('success', 'La sortie est bien ajoutée !');
                    return $this->redirectToRoute("list");
                }elseif ($sortieForm->get('enregistrer')->isClicked()){
                    $etat = $this->getDoctrine()->getManager()->getReference(Etat::class, 1);
                    $sortie->setEtat($etat);
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
        return $this->render('event/add.html.twig', ["sortieForm"=>$sortieForm->createView(),"title"=>$title, "lieu"=>$lieu, "ville"=>$ville]);
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
     * @Route("/sortir/add/requeteCp", name="requeteCp")
     */
    public function requeteCp(Request $request, EntityManagerInterface $em){
        $selectCp = $request->request->get('cp');
        $codePostal = $em->getRepository(Ville::class)->findBy(array('ville'=>$selectCp));

        $cpTab = [];
        foreach ($codePostal as $liste){
            $cpTab[$liste->getNom()] = $liste->getCp();
        }
        $response = new Response(json_encode($cpTab));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


/**
     * @Route("/sortir/details/{id}",name="details",requirements={"id":"\d+"})
     */

    public function details(Sortie $sortie)
    {

//        $user = $this->getUser()->getUsername();
        $title = "Détails";
        //$sortie=$ideaRepo->find($id);

        return $this->render("event/details.html.twig",
//            ["user_session"=>$user,"title" => $title, "sortie" => $sortie]);
            ["title" => $title, "sortie" => $sortie]);

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







}

