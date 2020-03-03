<?php

namespace App\Controller;

use App\Form\LoginFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="show_login")
     */
    public function loginAction(AuthenticationUtils $authenticationUtils)
    {
        $form = $this->createForm(LoginFormType::class);
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/showLogin.html.twig', [
            'loginForm' => $form->createView(),
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }
    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(AuthenticationUtils $authenticationUtils)
    {
        throw new \Exception('Will be intercepted before getting here');
    }
}
