<?php

namespace App\Form;

use App\Entity\GpsPosition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GpsPositionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('timestampUtc', null, [
                'widget' => 'single_text',
            ])
            ->add('latitude')
            ->add('longitude')
            ->add('speed')
            ->add('course')
            ->add('statusText')
            ->add('color')
            ->add('clientId')
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GpsPosition::class,
        ]);
    }
}
