<?php

namespace App\Handler;

use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CreateProductHandler
 * @package App\Handler
 */
class CreateProductHandler extends AbstractHandler
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var FlashBagInterface
     */
    private FlashBagInterface $flashBag;

    /**
     * CreateProductHandler constructor.
     * @param EntityManagerInterface $entityManager
     * @param FlashBagInterface $flashBag
     */
    public function __construct(EntityManagerInterface $entityManager, FlashBagInterface $flashBag)
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
    }

    /**
     * @inheritDoc
     */
    protected function process($data, array $options): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
        $this->flashBag->add(
            "success",
            "Votre produit a été créé avec succès."
        );
    }

    /**
     * @inheritDoc
     */
    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault("form_type", ProductType::class);
    }
}
