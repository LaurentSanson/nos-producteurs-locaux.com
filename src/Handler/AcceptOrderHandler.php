<?php

namespace App\Handler;

use App\Entity\Customer;
use App\Form\AcceptOrderType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Class AcceptOrderHandler
 * @package App\Handler
 */
class AcceptOrderHandler extends AbstractHandler
{
    /**
     * @var FlashBagInterface
     */
    private FlashBagInterface $flashBag;

    /**
     * @var WorkflowInterface
     */
    private WorkflowInterface $orderStateMachine;

    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    /**
     * AcceptOrderHandler constructor.
     * @param FlashBagInterface $flashBag
     * @param WorkflowInterface $orderStateMachine
     * @param MailerInterface $mailer
     */
    public function __construct(
        FlashBagInterface $flashBag,
        WorkflowInterface $orderStateMachine,
        MailerInterface $mailer
    ) {
        $this->flashBag = $flashBag;
        $this->orderStateMachine = $orderStateMachine;
        $this->mailer = $mailer;
    }

    /**
     * @inheritDoc
     */
    protected function process($data, array $options): void
    {
        /** @var Customer $customer */
        $customer = $data->getCustomer();
        $this->orderStateMachine->apply($data, 'accept');
        $email = (new TemplatedEmail())
            ->to(new Address($customer->getEmail(), $customer->getFullName()))
            ->from("hello@nos-producteur-locaux.com")
            ->subject("NPL : Commande acceptée")
            ->context(["order" => $data, "customer" => $customer])
            ->htmlTemplate('emails/choose_your_slot.html.twig');
        $this->mailer->send($email);
        $this->flashBag->add('success', "La commande a été acceptée avec succès.");
    }

    /**
     * @inheritDoc
     */
    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault("form_type", AcceptOrderType::class);
    }
}
