<?php

namespace App\Form;

use App\Entity\QrCodeEntry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QrCodeEntryForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content')
            ->add('type')
            ->add('colorDark')
            ->add('colorLight')
            ->add('createdAt')
            ->add('filePath')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QrCodeEntry::class,
        ]);
    }
}
