<?php

namespace App\Form;

use App\Entity\Farm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FarmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Nom de votre exploitation",
                'empty_data' => ""
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $form = $event->getForm();
                /** @var Farm $farm */
                $farm = $event->getData();

                if ($farm->getId() !== null) {
                    $form
                        ->add("image", ImageType::class, [
                            "label" => false
                        ])
                        ->add("address", AddressType::class, [
                            "label" => false
                        ])
                        ->add("description", TextareaType::class, [
                            "label" => "Présentation de votre exploitation"
                        ]);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Farm::class,
        ]);
    }
}
