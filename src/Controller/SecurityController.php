<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\ForgottenPasswordInput;
use App\Form\ForgottenPasswordType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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
     * @Route("/forgotten-password", name="security_forgotten_password")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param MailerInterface $mailer
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function forgottenPassword(
        Request $request,
        UserRepository $userRepository,
        MailerInterface $mailer
    ): Response {
        $forgottenPasswordInput = new ForgottenPasswordInput();
        $form = $this->createForm(ForgottenPasswordType::class, $forgottenPasswordInput)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneByEmail($forgottenPasswordInput->getEmail());
            $user->hasForgotHisPassword();
            $this->getDoctrine()->getManager()->flush();
            $email = (new TemplatedEmail())
                ->from('hello@nos-producteurs-locaux.com')
                ->to(new Address($user->getEmail(), $user->getFullName()))
                ->context(["forgottenPassword" => $user->getForgottenPassword()])
                ->htmlTemplate('emails/forgotten_password.html.twig');

            $mailer->send($email);
            $this->addFlash(
                "success",
                "Votre demande de rénitialisation de mot de passe a bien été prise en compte.
                Vous allez recevoir un email pour le réinitialiser"
            );

            return $this->redirectToRoute('security_login');
        }

        return $this->render('ui/security/forgotten_password.html.twig', [
            'forgottenPasswordForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reset-password/{token}", name="security_reset_password")
     * @param string $token
     * @param Request $request
     * @param UserRepository $userRepository
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @return Response
     * @throws NonUniqueResultException
     */
    public function resetPassword(
        string $token,
        Request $request,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $userPasswordEncoder
    ): Response {
        if (
            !Uuid::isValid($token)
            || null === ($user = $userRepository->getUserByForgottenPasswordToken(Uuid::fromString($token)))
        ) {
            $this->addFlash("danger", "Cette demande de réinitialisation de mot de passe n'existe pas");
            return $this->redirectToRoute("security_login");
        }

        $form = $this->createForm(ResetPasswordType::class, $user, [
        "validation_groups" => ["password"]
        ])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordEncoder->encodePassword($user, $user->getPlainPassword())
            );
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                "success",
                "Votre mot de passe a été réinitialisé avec succès"
            );

            return $this->redirectToRoute('security_login');
        }

        return $this->render('ui/security/reset_password.html.twig', [
            'resetPasswordForm' => $form->createView(),
        ]);
    }
}
