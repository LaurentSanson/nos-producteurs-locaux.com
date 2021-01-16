<?php

namespace App\Handler;

use App\Form\RegistrationFormType;
use App\Security\Guard\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

/**
 * Class RegistrationHandler
 * @package App\Handler
 */
class RegistrationHandler extends AbstractHandler
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var EmailVerifier
     */
    private EmailVerifier $emailVerifier;

    /**
     * @var FlashBagInterface
     */
    private FlashBagInterface $flashBag;

    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $userPasswordEncoder;

    /**
     * RegistrationHandler constructor.
     * @param EntityManagerInterface $entityManager
     * @param EmailVerifier $emailVerifier
     * @param FlashBagInterface $flashBag
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EmailVerifier $emailVerifier,
        FlashBagInterface $flashBag,
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        $this->entityManager = $entityManager;
        $this->emailVerifier = $emailVerifier;
        $this->flashBag = $flashBag;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @inheritDoc
     */
    protected function process($data, array $options): void
    {
        $data->setPassword(
            $this->userPasswordEncoder->encodePassword($data, $data->getPlainPassword())
        );
        $this->entityManager->persist($data);
        $this->entityManager->flush();
        $this->flashBag->add("success", "Votre inscription a été effectuée avec succès.");

        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $data,
            (new TemplatedEmail())
                ->from(new Address('confirmation@nos-producteurs-locaux.com', 'Nos Producteurs Locaux'))
                ->to($data->getEmail())
                ->subject('Veuillez confirmer votre email')
                ->htmlTemplate('emails/confirmation_email.html.twig')
        );
    }

    /**
     * @inheritDoc
     */
    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault("form_type", RegistrationFormType::class);
        $resolver->setDefault("form_options", [
            "validation_groups" => ["Default", "password"]
        ]);
    }
}
