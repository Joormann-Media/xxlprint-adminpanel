<?php

namespace App\Form;

use App\Entity\Schoolkids;
use App\Entity\SchoolkidTrip;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolkidTripType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('weekday')
            ->add('tripType')
            ->add('pickupTime')
            ->add('weekPattern')
            ->add('pickupAddress')
            ->add('destination')
            ->add('comment')
            ->add('specialTrip')
            ->add('validFrom')
            ->add('validTo')
            ->add('canceled')
            ->add('canceledAt')
            ->add('cancellationType')
            ->add('cancellationReason')
            ->add('canceledBy')
            ->add('schoolkid', EntityType::class, [
                'class' => Schoolkids::class,
                'choice_label' => 'id',
            ])
            ->add('canceledReceivedBy', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SchoolkidTrip::class,
        ]);
    }
}
