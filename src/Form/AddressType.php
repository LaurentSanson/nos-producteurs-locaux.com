<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Farm;
use PHP_CodeSniffer\Generators\Text;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("address", TextType::class, [
                "label" => "Adresse"
            ])
            ->add("restAddress", TextType::class, [
                "label" => "Complément d'adresse",
                "required" => false
            ])
            ->add("postCode", TextType::class, [
                "label" => "Code postal"
            ])
            ->add("city", TextType::class, [
                "label" => "Ville"
            ])
            ->add('position', PositionType::class, ["label" => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
