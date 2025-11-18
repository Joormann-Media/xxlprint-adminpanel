<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\WorkLog;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkLogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('clockInAt')
            ->add('location')
            ->add('method')
            ->add('deviceUid')
            ->add('source')
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('employee', EntityType::class, [
                'class' => Employee::class,
                'choice_label' => 'id',
            ])
            ->add('clockIn', null, [
                'label' => 'Clock In',
                'choices' => [
                    'Nein' => 0,
                    'Ja' => 1,
                ],
                'expanded' => true,
                'multiple' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkLog::class,
        ]);
    }
}
