<?php

namespace App\Form;

use App\Entity\Slot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SlotType
 * @package App\Form
 */
class SlotType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("startedAt", DateTimeType::class, [
            "widget" => "single_text",
            "input" => "datetime_immutable",
            "label" => false
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault("data_class", Slot::class);
    }
}
