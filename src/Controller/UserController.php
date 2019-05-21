<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @Route("/sortir/monProfil", name="monProfil")
     */
    public function monProfil (Request $request, EntityManagerInterface $em)
    {
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepo->find(1);

        $userForm=$this->createForm(UserType::class,$user);
        $userForm->handleRequest($request);

        return $this->render("user/monProfil.html.twig",
            ["userForm"=>$userForm->createView()]);

    }

    /**
     * security.yaml on a login_path: login
     * @Route("/login", name="login")
     */
    public function login() {
        return $this->render("user/login.html.twig",
            []);
    }


    public function logout(){}






















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
