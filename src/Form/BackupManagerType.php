<?php

namespace App\Form;

use App\Entity\BackupManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BackupManagerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('createdAt')
            ->add('type')
            ->add('pathSql')
            ->add('pathProject')
            ->add('gitRemoteStatus')
            ->add('gitStatusTimestamp')
            ->add('gitStatusMessage')
            ->add('notes')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BackupManager::class,
        ]);
    }
}
