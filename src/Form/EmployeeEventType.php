<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\EmployeeEvent;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type')
            ->add('title')
            ->add('startDate')
            ->add('endDate')
            ->add('status')
            ->add('requestedAt')
            ->add('decisionAt')
            ->add('comment')
            ->add('isFullDay')
            ->add('employee', EntityType::class, [
                'class' => Employee::class,
                'choice_label' => 'username',
            ])
            ->add('decidedBy', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EmployeeEvent::class,
        ]);
    }
}
