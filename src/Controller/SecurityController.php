<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils,
                          Request $request): Response
    {
        $targetPath = $request->getSession()->get('_security.main.target_path');
        if (!preg_match("/\/image\/[0-9a-zA-Z]+/", $targetPath)) {
            return $this->redirectToRoute('app_main');
        }
         if ($this->getUser()) {
             return $this->redirectToRoute('app_main');
         }
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('security/login.html.twig', ['target_path' => $targetPath, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {

    }
}
