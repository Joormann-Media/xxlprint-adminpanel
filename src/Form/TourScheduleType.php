<?php

namespace App\Form;

use App\Entity\TourSchedule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class TourScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('year', IntegerType::class)
            ->add('weekdays', ChoiceType::class, [
                'choices' => [
                    'Montag' => 1,
                    'Dienstag' => 2,
                    'Mittwoch' => 3,
                    'Donnerstag' => 4,
                    'Freitag' => 5,
                    'Samstag' => 6,
                    'Sonntag' => 7,
                ],
                'expanded' => true, // Checkboxen
                'multiple' => true,
                'label' => 'Betriebstage'
            ])
            ->add('onlyWeekdays')
            ->add('onlyWeekend')
            ->add('onlyDuringHolidays')
            ->add('notDuringHolidays')
            ->add('startTime')
            ->add('endTime')
            ->add('dateFrom')
            ->add('dateTo')
            // Optional: Special Dates kannst du als CollectionType machen, ist aber meist ein späterer Schritt.
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TourSchedule::class,
        ]);
    }
}
