<?php

namespace App\Form;

use App\Entity\School;
use App\Entity\SchoolDeparture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolDepartureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('weekday')
            ->add('departureTime')
            ->add('readyTime')
            ->add('busLine')
            ->add('notes')
            ->add('specialDeparture', null, [
                'label' => 'Sonderfahrt',
                'required' => false,
            ])
            ->add('school', EntityType::class, [
                'class' => School::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SchoolDeparture::class,
        ]);
    }
}
