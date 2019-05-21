<?php

namespace App\Controller;

use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @Route("/monProfil", name="monProfil")
     */
    public function monProfil (Request $request, EntityManagerInterface $em)
    {
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepo->find(1);

        $registerForm=$this->createForm(RegisterType::class,$user);
        $registerForm->handleRequest($request);





    }




















    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
