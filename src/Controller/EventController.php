<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends Controller
{
    /**
     * @Route("/", name="list")
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
     * @Route("/add", name="add")
     */
    public function add(EntityManagerInterface $em, Request $request)
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class,$sortie);
        $sortieForm->handleRequest($request);
        if($sortieForm->isSubmitted() && $sortieForm->isValid()){
            $em->persist();
            $em->flush();
            $this->addFlash('success','La sortie est bien ajoutée !');
            return $this->redirectToRoute("list");
        }
        $title="add";
        return $this->render('event/add.html.twig', ["sortieForm"=>$sortieForm->createView(),"title"=>$title]);
    }
}
