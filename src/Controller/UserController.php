<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends Controller
{
    /**
     * @Route("/sortir/monProfil", name="monProfil")
     */
    public function monProfil(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();

        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            //Enregistrer le user dans la BD
            $em->persist($user);
            $em->flush();
            $this->addFlash("success", "Votre compte a été modifié");
            $this->redirectToRoute('monProfil');
        }

        return $this->render("user/monProfil.html.twig",
            ["userForm" => $userForm->createView()]);

    }

    // à ajouter dan le .twig href="{{ path('afficherProfil',{'id':user.id}) }}"

    /**
     * @Route("/sortir/profil/{id}",name="profil",requirements={"id"="\d+"})
     * @Template()
     */
    public function Profil($id)
    {
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $userDetail = $userRepo->find($id);

        return ['userDetail' => $userDetail];
    }


    /**
     * security.yaml on a login_path: login
     * @Route("/", name="login")
     */
    public function login(AuthenticationUtils $authUtils)
    {

        $erreur = $authUtils->getLastAuthenticationError();

        if ($erreur != null) {
            $this->addFlash("error", "L'identifiant et/ou le mot de passe sont incorrects");
        }
        return $this->render("user/login.html.twig",
            []);
    }


    public function logout()
    {
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


    /**
     * @Route("/sortir/changerMotDePasse", name="changerMotDePasse")
     * @Template()
     */
    public function changePassword(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)

    {

        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {


            $oldPasswordSaisi = $form->get("oldPassword")->getData();
            $nouveauPassword = $form->get("password")->getData();

            if ($encoder->isPasswordValid($user,$oldPasswordSaisi)) {


                $hashed = $encoder->encodePassword($user, $nouveauPassword);
                $user->setPassword($hashed);

                $em->persist($user);
                $em->flush();
                $this->addFlash("success", "Votre mot de passe a été modifié.");
                $this->redirectToRoute('monProfil');
            }

            else {

                $this->addFlash("error", "l'ancien mot de passe n'est pas correct. ");
                $this->redirectToRoute('changerMotDePasse');
            }

        }

        return $this->render("user/changerMotDePasse.html.twig",
            ["form" => $form->createView()]);

    }

}


