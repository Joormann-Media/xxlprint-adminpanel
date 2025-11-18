<?php

namespace App\Form;

use App\Entity\School;
use App\Entity\Schoolkids;
use App\Entity\SchoolTour;
use App\Entity\User;
use App\Enum\SchoolTourStatus;
use App\Enum\CompanionRequirement;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolTourType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var SchoolTour $tour */
        $tour = $options['data'] ?? null;

        $builder
            ->add('name', null, [
                'label' => 'Tourname',
                'attr' => ['class' => 'form-control', 'placeholder' => 'z. B. Schulbus Linie 1'],
            ])
            ->add('description', null, [
                'label' => 'Beschreibung',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Tourtyp',
                'choices' => [
                    'Hinfahrt' => 'outbound',
                    'Rückfahrt' => 'return',
                ],
                'placeholder' => 'Bitte auswählen',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('operatingDays', ChoiceType::class, [
                'label' => 'Betriebstage',
                'choices' => [
                    'Montag'     => 'monday',
                    'Dienstag'   => 'tuesday',
                    'Mittwoch'   => 'wednesday',
                    'Donnerstag' => 'thursday',
                    'Freitag'    => 'friday',
                    'Samstag'    => 'saturday',
                    'Sonntag'    => 'sunday',
                ],
                'multiple' => true,
                'expanded' => true, // Checkboxen
            ])
            ->add('schoolYear', null, [
                'label' => 'Schuljahr',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'z. B. 2025/2026'],
            ])
            ->add('startAddress', null, [
                'label' => 'Startadresse',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('startTime', DateTimeType::class, [
                'label' => 'Startzeit',
                'widget' => 'single_text',
                'html5' => true,
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('endAddress', null, [
                'label' => 'Zieladresse',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('endTime', DateTimeType::class, [
                'label' => 'Zielzeit',
                'widget' => 'single_text',
                'html5' => true,
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('route', null, [
                'label' => 'Route (GeoJSON)',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 4],
            ])
            ->add('distance', null, [
                'label' => 'Distanz (km)',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'z. B. 12.5'],
            ])
            ->add('duration', null, [
                'label' => 'Dauer (Sekunden)',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'z. B. 1800'],
            ])
            ->add('school', EntityType::class, [
                'class' => School::class,
                'choice_label' => 'name',
                'placeholder' => 'Bitte Schule auswählen',
                'label' => 'Schule',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('kids', EntityType::class, [
                'class' => Schoolkids::class,
                'choice_label' => fn(Schoolkids $kid) => $kid->getLastName() . ', ' . $kid->getFirstName(),
                'multiple' => true,
                'expanded' => true,
                'label' => 'Kinder',
                'attr' => ['class' => 'form-check'],
                'query_builder' => function (EntityRepository $er) use ($tour) {
                    $qb = $er->createQueryBuilder('k');
                    if ($tour && $tour->getSchool()) {
                        $qb->andWhere('k.school = :school')
                           ->setParameter('school', $tour->getSchool());
                    } else {
                        $qb->andWhere('1=0');
                    }
                    return $qb->orderBy('k.lastName', 'ASC');
                },
            ])
            ->add('stops', CollectionType::class, [
                'entry_type' => SchoolTourStopType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'label' => false,
            ])
            ->add('maintainer', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'placeholder' => 'Bitte auswählen',
                'label' => 'Verantwortlich',
                'required' => false,
                'attr' => ['class' => 'form-select'],
            ])
            ->add('approvedBy', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'placeholder' => 'Bitte auswählen',
                'label' => 'Genehmigt von',
                'required' => false,
                'attr' => ['class' => 'form-select'],
            ])
            ->add('approvedDate', DateTimeType::class, [
                'label' => 'Genehmigungsdatum',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('status', ChoiceType::class, [
    'label' => 'Status',
    'choices' => [
        '✅ Aktiv'              => SchoolTourStatus::ACTIVE,
        '❌ Inaktiv'            => SchoolTourStatus::INACTIVE,
        '📝 In Bearbeitung'     => SchoolTourStatus::PENDING_WIP,
        '⏳ Warten auf Freigabe'=> SchoolTourStatus::PENDING_APPROVAL,
    ],
    'choice_value' => fn (?SchoolTourStatus $enum) => $enum?->value,
    'choice_label' => fn (SchoolTourStatus $enum) => $enum->label(),
    'placeholder'  => false,
    'required'     => true,
    'empty_data'   => SchoolTourStatus::PENDING_WIP,  // Enum, nicht String
    'data'         => $tour?->getStatus() ?? SchoolTourStatus::PENDING_WIP,
    'attr'         => ['class' => 'form-select'],
])

->add('companionRequirement', ChoiceType::class, [
    'label' => 'Begleitung',
    'choices' => [
        'Nicht erforderlich' => CompanionRequirement::NOT_REQUIRED,
        'Optional'           => CompanionRequirement::OPTIONAL,
        'Erforderlich'       => CompanionRequirement::REQUIRED,
    ],
    'choice_value' => fn (?CompanionRequirement $enum) => $enum?->value,
    'choice_label' => fn (CompanionRequirement $enum) => $enum->label(),
    'placeholder'  => false,
    'required'     => true,
    'empty_data'   => CompanionRequirement::NOT_REQUIRED,  // Enum, nicht String
    'data'         => $tour?->getCompanionRequirement() ?? CompanionRequirement::NOT_REQUIRED,
    'attr'         => ['class' => 'form-select'],
])




        ;

        // Dynamische Anpassung der Kids bei Änderung der Schule
        $builder->get('school')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $school = $event->getForm()->getData();
            $form = $event->getForm()->getParent();

            $form->add('kids', EntityType::class, [
                'class' => Schoolkids::class,
                'choice_label' => fn(Schoolkids $kid) => $kid->getLastName() . ', ' . $kid->getFirstName(),
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (EntityRepository $er) use ($school) {
                    $qb = $er->createQueryBuilder('k');
                    if ($school) {
                        $qb->andWhere('k.school = :school')
                           ->setParameter('school', $school);
                    } else {
                        $qb->andWhere('1=0');
                    }
                    return $qb->orderBy('k.lastName', 'ASC');
                },
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SchoolTour::class,
        ]);
    }
}
