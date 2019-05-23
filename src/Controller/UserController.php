<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends Controller
{
    /**
     * @Route("/sortir/monProfil", name="monProfil")
     */
    public function monProfil (Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getUser();

        $userForm=$this->createForm(UserType::class,$user);
        $userForm->handleRequest($request);

        if($userForm->isSubmitted() && $userForm->isValid()){
            //hasher le mot de passe
            $hashed=$encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($hashed);
            //Enregistrer le user dans la BD
            $em->persist($user);
            $em->flush();
            $this->addFlash("success","Votre compte a été modifié");
            $this->redirectToRoute('monProfil');
        }

        return $this->render("user/monProfil.html.twig",
            ["userForm"=>$userForm->createView()]);

    }

    /**
     * security.yaml on a login_path: login
     * @Route("/", name="login")
     */
    public function login(AuthenticationUtils $authUtils) {

        $erreur = $authUtils->getLastAuthenticationError();

        if ($erreur!=null)
        {
            $this->addFlash("error","L'identifiant et/ou le mot de passe sont incorrects");
        }
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
