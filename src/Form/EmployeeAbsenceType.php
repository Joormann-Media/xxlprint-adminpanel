<?php

// src/Form/EmployeeAbsenceType.php
namespace App\Form;

use App\Entity\EmployeeAbsence;
use App\Entity\Employee;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class EmployeeAbsenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startAway', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Beginn Abwesenheit',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('endAway', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Ende Abwesenheit',
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('reasonAway', null, [
                'label' => 'Grund der Abmeldung',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('reportedBy', null, [
                'label' => 'Gemeldet von',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('reportMethod', ChoiceType::class, [
                'choices'  => [
                    'Telefon'      => 'Phone',
                    'E-Mail'       => 'Email',
                    'WhatsApp'     => 'WhatsApp',
                    'Messenger'    => 'Messenger',
                    'Sonstiges'    => 'Other',
                ],
                'placeholder' => 'Bitte wählen',
                'label' => 'Meldeweg',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('receivedBy', null, [
                'label' => 'Entgegengenommen von',
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('reportedAt', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Gemeldet am',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('createdAt', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Erstellt am',
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('employee', EntityType::class, [
                'class' => Employee::class,
                'choice_label' => function (Employee $e) {
                    return $e->getLastName() . ' ' . $e->getFirstName() . ' (' . $e->getEmployeeNumber() . ')';
                },
                'placeholder' => 'Mitarbeiter auswählen',
                'attr' => ['class' => 'select2 form-select'],
                'required' => true,
                'label' => 'Mitarbeiter',
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EmployeeAbsence::class,
        ]);
    }
}
