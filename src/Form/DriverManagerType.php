<?php

namespace App\Form;

use App\Entity\DriverManager;
use App\Entity\LicenceClass;
use App\Entity\User;
use App\Entity\Vehicle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DriverManagerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('licenceExpires')
            ->add('specialDriverInfo')
->add('user', EntityType::class, [
    'class' => User::class,
    'choice_label' => 'fullName', // Das ruft automatisch getFullName() auf jedem User-Objekt auf!
    'placeholder' => 'Bitte auswählen',
])

            ->add('licenceClasses', EntityType::class, [
                'class' => LicenceClass::class,
                'choice_label' => 'shortName',
                'multiple' => true,
            ])
            ->add('knownVehicle', EntityType::class, [
                'class' => Vehicle::class,
                'choice_label' => 'licensePlate',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DriverManager::class,
        ]);
    }
}
