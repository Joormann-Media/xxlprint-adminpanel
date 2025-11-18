<?php

namespace App\Form;

use App\Entity\School;
use App\Entity\SchoolTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolTimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('weekday')
            ->add('schoolStart')
            ->add('arrivalTime')
            ->add('schoolEnd')
            ->add('breaks')
            ->add('school', EntityType::class, [
                'class' => School::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SchoolTime::class,
        ]);
    }
}
