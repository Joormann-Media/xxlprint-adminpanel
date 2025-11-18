<?php

namespace App\Form;

use App\Entity\CityPostalcode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CityPostalcodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('postcode')
            ->add('city')
            ->add('state')
            ->add('lat')
            ->add('lon')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CityPostalcode::class,
        ]);
    }
}
