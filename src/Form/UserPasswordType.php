<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

/**
 * Class UserPasswordType
 * @package App\Form
 */
class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("currentPassword", PasswordType::class, [
                "mapped" => false,
                "constraints" => [
                    new UserPassword(["groups" => "password"])
                ]
            ])
            ->add("plainPassword", RepeatedType::class, [
                "type" => PasswordType::class,
                "invalid_message" => "Le mot de passe et sa confirmation ne sont pas identiques"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => "password"
        ]);
    }
}
