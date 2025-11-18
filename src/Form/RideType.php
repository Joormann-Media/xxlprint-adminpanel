<?php

namespace App\Form;

use App\Entity\Auftraggeber;
use App\Entity\Ride;
use App\Entity\RideVariation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rideId')
            ->add('startName')
            ->add('startStreet')
            ->add('startStreetNo')
            ->add('startZip')
            ->add('startCity')
            ->add('startCountry')
            ->add('destStreet')
            ->add('destStreetNo')
            ->add('destZip')
            ->add('destCity')
            ->add('destCountry')
            ->add('rideDescription')
            ->add('rideDateStart')
            ->add('rideDateEnd')
            ->add('rideTime')
            ->add('rideLength')
            ->add('client', EntityType::class, [
                'class' => Auftraggeber::class,
                'choice_label' => 'name',
            ])
            ->add('rideVariation', EntityType::class, [
                'class' => RideVariation::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ride::class,
        ]);
    }
}
