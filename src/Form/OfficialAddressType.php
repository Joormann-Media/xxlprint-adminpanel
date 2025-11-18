<?php

namespace App\Form;

use App\Entity\OfficialAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfficialAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('postcode')
            ->add('city')
            ->add('district')
            ->add('street')
            ->add('houseNumber')
            ->add('houseNumberRange')
            ->add('lat')
            ->add('lon')
            ->add('country')
            ->add('source')
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OfficialAddress::class,
        ]);
    }
}
