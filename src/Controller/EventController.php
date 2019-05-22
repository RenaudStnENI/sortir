<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends Controller
{
    /**
     * @Route("/sortir", name="list")
     */
    public function list(EntityManagerInterface $em)
    {
        $title="Accueil";

        $sortiesRepo = $em->getRepository(Sortie::class);
        //$choses=$ideaRepo->findAll();
        $events = $sortiesRepo->findBy([], ["dateLimiteInscription" => "DESC"], 30);
        return $this->render('event/list.html.twig', ["events"=>$events,"title"=>$title]);
    }

    /**
     * @Route("/sortir/add", name="add")
     */
    public function add(EntityManagerInterface $em, Request $request)
    {
        $sortie = new Sortie();
        $sortie->setDateHeureDebut(new \DateTime());
        $etat = $this->getDoctrine()->getManager()->getReference(Etat::class, 1);
        $sortie->setEtat($etat);
        $sortieForm = $this->createForm(SortieType::class,$sortie);
        $sortieForm->handleRequest($request);

        if($sortie->getDateLimiteInscription() < $sortie->getDateHeureDebut()) {
            if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
                $em->persist($sortie);
                $em->flush();
                $this->addFlash('success', 'La sortie est bien ajoutée !');
                return $this->redirectToRoute("list");
            }
        }else{
            $sortieForm->get('dateLimiteInscription')->addError(new FormError('La date de limite d\'inscription doit etre inferieur à la date de la sortie !'));
        }
        $title="add";
        return $this->render('event/add.html.twig', ["sortieForm"=>$sortieForm->createView(),"title"=>$title]);
    }
}
