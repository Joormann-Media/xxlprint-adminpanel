<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Vehicle;
use App\Entity\VehicleTracking;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehicleTrackingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('timestamp')
            ->add('latitude')
            ->add('longitude')
            ->add('speed')
            ->add('course')
            ->add('street')
            ->add('postalcode')
            ->add('city')
            ->add('display')
            ->add('vehicle', EntityType::class, [
                'class' => Vehicle::class,
                'choice_label' => 'id',
            ])
            ->add('driver', EntityType::class, [
                'class' => Employee::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VehicleTracking::class,
        ]);
    }
}
