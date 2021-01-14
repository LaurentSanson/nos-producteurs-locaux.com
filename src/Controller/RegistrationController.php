<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Producer;
use App\Handler\RegistrationHandler;
use App\Security\Guard\EmailVerifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register/{role}", name="app_register")
     * @param string $role
     * @param Request $request
     * @param RegistrationHandler $handler
     * @return Response
     */
    public function register(string $role, Request $request, RegistrationHandler $handler): Response
    {
        $user = Producer::ROLE === $role ? new Producer() : new Customer();

        if ($handler->handle($request, $user)) {
            return $this->redirectToRoute("index");
        }

        return $this->render("ui/security/register.html.twig", [
            "form" => $handler->createView()
        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     * @param Request $request
     * @return Response
     */
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Votre email est maintenant confirmé');

        return $this->redirectToRoute('index');
    }
}
