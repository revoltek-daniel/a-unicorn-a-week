<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 *
 * @package DanielBundle\Controller
 */
class SecurityController extends AbstractController
{
    /**
     * @param AuthenticationUtils $authUtils
     */
    #[Route('/login', name: 'security_login')]
    public function loginAction(AuthenticationUtils $authUtils): Response
    {
         // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render(
            'security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error'         => $error,
            ]
        );
    }

    #[Route('/logout', name: 'security_logout')]
    public function logoutAction()
    {
    }
}
