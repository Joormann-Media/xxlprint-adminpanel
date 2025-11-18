<?php

namespace App\Form;

use App\Entity\Tour;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TourType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('schoolkids', EntityType::class, [
    'class' => \App\Entity\Schoolkids::class,
    'choice_label' => fn($kid) => $kid->getLastName() . ', ' . $kid->getFirstName(),
    'multiple' => true,
    'expanded' => true,
    'label' => 'Schulkinder',
    'required' => false,
    'query_builder' => function(\App\Repository\SchoolkidsRepository $repo) use ($builder) {
        $school = $builder->getData()->getSchool();
        if (!$school) {
            // Noch keine Schule gewählt → keine Kinder anzeigen!
            return $repo->createQueryBuilder('k')->where('1=0');
        }
        return $repo->createQueryBuilder('k')
            ->where('k.school = :school')
            ->setParameter('school', $school);
    },
])

->add('school', EntityType::class, [
    'class' => \App\Entity\School::class,
    'choice_label' => 'name', // oder wie das Feld bei dir heißt
    'placeholder' => '--- Schule wählen ---',
    'required' => true,
    'label' => 'Schule',
])


            ->add('stopPoints', CollectionType::class, [
                'entry_type' => StopPointType::class, // Das musst du bauen!
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Haltestellen',
                'required' => false,
            ])
            ->add('schedules', CollectionType::class, [
                'entry_type' => TourScheduleType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Betriebsregel(n)'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tour::class,
        ]);
    }
}
