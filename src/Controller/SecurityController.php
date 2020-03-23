<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\User;
use App\Form\Type\RegistrationType;
use App\Services\SecurityServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController {
    private $securityServices;

    /**
     * SecurityController constructor.
     */
    public function __construct(SecurityServices $securityServices) {
        $this->securityServices = $securityServices;
    }

    /**
     * @Route("/signup", name="app_signup")
     */
    public function signup(Request $request) {
        $errors = null;
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $errors = $this->securityServices->getErrors($form);

            if ($form->isValid()) {
                $this->securityServices->createUser($user);
                $this->securityServices->createFirstAccount($user);

                $this->addFlash('success', 'Votre compte a bien été créé.');
            }
        }

        return $this->render('default/signup.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('home_manager');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('default/index.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout() {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
