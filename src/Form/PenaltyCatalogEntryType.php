<?php

namespace App\Form;

use App\Entity\PenaltyCatalogEntry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PenaltyCatalogEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('offenseTitle')
            ->add('description')
            ->add('paragraph')
            ->add('category')
            ->add('vehicleTypes')
            ->add('penaltyMin')
            ->add('penaltyMax')
            ->add('points')
            ->add('drivingBanMonths')
            ->add('isProbezeitRelevant')
            ->add('severityLevel')
            ->add('active')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PenaltyCatalogEntry::class,
        ]);
    }
}
