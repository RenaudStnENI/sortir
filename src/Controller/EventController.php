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
}

