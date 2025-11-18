<?php

namespace App\Form;

use App\Entity\TaxiCalculator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class TaxiCalculatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('baseFeeDay')
            ->add('baseFeeNight')
            ->add('pricePerKmDay')
            ->add('pricePerKmNight')
            ->add('sectionMetersDay')
            ->add('sectionMetersNight')
            ->add('waitPriceFirst5MinHour')
            ->add('waitSectionSecondsFirst5Min')
            ->add('waitPriceFrom6MinHour')
            ->add('waitSectionSecondsFrom6Min')
            ->add('largeCabSurcharge')
            ->add('withdrawalFee')
            ->add('validFrom')
            ->add('notes')
            ->add('regulationPdfFile', VichFileType::class, [
                'required' => false,
                'label' => 'Verordnung (PDF)',
                'allow_delete' => true,
                'download_uri' => true,
            ])
            ->add('updatedAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TaxiCalculator::class,
        ]);
    }
}
