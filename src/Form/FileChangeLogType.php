<?php

namespace App\Form;

use App\Entity\FileChangeLog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileChangeLogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filePath')
            ->add('eventType')
            ->add('eventTime')
            ->add('oldFilePath')
            ->add('fileSize')
            ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FileChangeLog::class,
        ]);
    }
}
