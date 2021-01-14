<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\ForgottenPasswordInput;
use App\Handler\ForgottenPasswordHandler;
use App\Handler\ResetPasswordHandler;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Uid\Uuid;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('ui/security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @codeCoverageIgnore
     * @Route("/logout", name="security_logout")
     */
    public function logout(): void
    {
    }

    /**
     * @param Request $request
     * @param ForgottenPasswordHandler $handler
     * @return Response
     * @Route("/forgotten-password", name="security_forgotten_password")
     */
    public function forgottenPassword(Request $request, ForgottenPasswordHandler $handler): Response
    {
        $forgottenPasswordInput = new ForgottenPasswordInput();

        if ($handler->handle($request, $forgottenPasswordInput)) {
            return $this->redirectToRoute("security_login");
        }

        return $this->render("ui/security/forgotten_password.html.twig", [
            "form" => $handler->createView()
        ]);
    }

    /**
     * @Route("/reset-password/{token}", name="security_reset_password")
     * @param string $token
     * @param Request $request
     * @param UserRepository $userRepository
     * @param ResetPasswordHandler $handler
     * @return Response
     * @throws NonUniqueResultException
     */
    public function resetPassword(
        string $token,
        Request $request,
        UserRepository $userRepository,
        ResetPasswordHandler $handler
    ): Response {
        if (
            !Uuid::isValid($token)
            || null === ($user = $userRepository->getUserByForgottenPasswordToken(Uuid::fromString($token)))
        ) {
            $this->addFlash("danger", "Cette demande d'oubli de mot de passe n'existe pas.");
            return $this->redirectToRoute("security_login");
        }

        if ($handler->handle($request, $user)) {
            return $this->redirectToRoute("security_login");
        }

        return $this->render("ui/security/reset_password.html.twig", [
            "form" => $handler->createView()
        ]);
    }
}
