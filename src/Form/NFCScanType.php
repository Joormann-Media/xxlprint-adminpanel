<?php

namespace App\Form;

use App\Entity\NFCScan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NFCScanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('uid')
            ->add('atr')
            ->add('chipType')
            ->add('memory')
            ->add('rawInfo')
            ->add('manufacturer')
            ->add('mediumType')         // NEU
            ->add('mediumDescription')  // NEU
            ->add('features')
            ->add('protocols')
            ->add('scannedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('lockStatus')
            ->add('isWritable')
            ->add('writeEndurance')
            ->add('writeCounter')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NFCScan::class,
        ]);
    }
}
