<?php

namespace App\Form;

use App\Entity\License;
use App\Entity\LicenseActivation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LicenseActivationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('hardwareKey')
            ->add('hostname')
            ->add('activatedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('license', EntityType::class, [
                'class' => License::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LicenseActivation::class,
        ]);
    }
}
