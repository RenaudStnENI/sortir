<?php

namespace App\Controller;

use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
}
