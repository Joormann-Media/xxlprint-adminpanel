<?php

namespace App\Form;

use App\Entity\EmployeeVacation;
use App\Entity\VacationBooking;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VacationBookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateTaken')
            ->add('days')
            ->add('reason')
            ->add('comment')
            ->add('employeeVacation', EntityType::class, [
                'class' => EmployeeVacation::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VacationBooking::class,
        ]);
    }
}
