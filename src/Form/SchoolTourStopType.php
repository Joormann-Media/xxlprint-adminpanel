<?php

namespace App\Form;

use App\Entity\SchoolTourStop;
use App\Entity\Schoolkids;
use App\Entity\School;
use App\Entity\StopPoint;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolTourStopType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('stopPoint', EntityType::class, [
                'class' => StopPoint::class,
                'choice_label' => 'name',
                'placeholder' => '— StopPoint auswählen —',
                'required' => false,
            ])
            ->add('address', TextType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Freie Adresse'],
            ])
            ->add('latitude', TextType::class, ['required' => false])
            ->add('longitude', TextType::class, ['required' => false])
            ->add('plannedTime', TimeType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('sortOrder', IntegerType::class)
            ->add('notes', TextareaType::class, ['required' => false])
            ->add('kids', EntityType::class, [
                'class' => Schoolkids::class,
                'choice_label' => fn(Schoolkids $kid) => $kid->getFirstName().' '.$kid->getLastName(),
                'multiple' => true,
                'expanded' => false, // Select-Box
                'required' => false,
                'attr' => ['class' => 'form-select'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SchoolTourStop::class,
        ]);
    }
}
