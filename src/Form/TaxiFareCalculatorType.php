<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TaxiFareCalculatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('km', NumberType::class, [
                'label' => 'Kilometer',
                'scale' => 2,
                'attr' => ['step' => '0.01', 'min' => '0']
            ])
            ->add('standzeitMin', NumberType::class, [
                'label' => 'Standzeit (Minuten)',
                'scale' => 2,
                'required' => false,
                'attr' => ['step' => '0.01', 'min' => '0']
            ])
            ->add('isNight', CheckboxType::class, [
                'label' => 'Nachttarif/Sonn-/Feiertag',
                'required' => false,
            ])
            ->add('isLargeCab', CheckboxType::class, [
                'label' => 'Großraumtaxi (mehr als 4 Personen)',
                'required' => false,
            ])
            ->add('calculate', SubmitType::class, [
                'label' => 'Preis berechnen'
            ])
        ;
    }
}
