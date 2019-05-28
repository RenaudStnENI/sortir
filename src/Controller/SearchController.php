<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\User;
use App\Form\SearchSortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends Controller
{

    /**
     * @Route("/sortir", name="list")
     */
    public function list(EntityManagerInterface $em, Request $request, SortieRepository $sortieRepo)
    {
        $title = "Accueil";


        $searchSortieForm = $this->createForm(SearchSortieType::class);
        $searchSortieForm->handleRequest($request);

        if ($searchSortieForm->isSubmitted() && $searchSortieForm->isValid()) {
            $criteres = $searchSortieForm->getData();
            $events = $sortieRepo->searchSorties($criteres);


        } else {
            $sortieRepo = $em->getRepository(Sortie::class);
            $events = $sortieRepo->findBy([], ["dateLimiteInscription" => "DESC"], 30);


        }


        return $this->render('search/sortie.html.twig', ["events" => $events, "title" => $title, 'search_form' => $searchSortieForm->createView()]);
    }


}
