<?php

namespace App\Form;

use App\Entity\Vehicle;
use App\Entity\VehicleInspectionInterval;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehicleInspectionIntervalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('inspectionType', TextType::class, [
                'label' => 'Prüf-/Inspektionstyp',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('intervalMonths', NumberType::class, [
                'label' => 'Intervall (Monate)',
                'attr' => ['class' => 'form-control', 'min' => 1],
            ])
            ->add('legalBasis', TextType::class, [
                'label' => 'Rechtsgrundlage',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('mandatory', ChoiceType::class, [
                'label' => 'Pflicht',
                'choices' => [
                    'Ja' => 'ja',
                    'Nein' => 'nein',
                    'Optional' => 'optional',
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('dateLast', DateType::class, [
                'label' => 'Letzte Prüfung',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('dateNext', DateType::class, [
                'label' => 'Nächste Prüfung',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            // Das Fahrzeugfeld kannst du im Subformular meist ausblenden,
            // weil es vom übergeordneten Vehicle gesetzt wird.
            ->add('vehicle', EntityType::class, [
                'class' => Vehicle::class,
                'choice_label' => 'licensePlate',
                'label' => 'Fahrzeug',
                'attr' => ['class' => 'form-select'],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VehicleInspectionInterval::class,
        ]);
    }
}
