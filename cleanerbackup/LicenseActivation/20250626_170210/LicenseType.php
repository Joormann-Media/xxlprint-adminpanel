<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\License;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LicenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('productName')
            ->add('licenseKey')
            ->add('version')
            ->add('validUntil', null, [
                'widget' => 'single_text',
            ])
            ->add('maxActivations')
            ->add('status')
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => License::class,
        ]);
    }
}
