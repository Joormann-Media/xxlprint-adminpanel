<?php

namespace App\Form;

use App\Entity\DownloadLog;
use App\Entity\ReleaseFile;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DownloadLogForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('downloadedAt')
            ->add('ip')
            ->add('userAgent')
            ->add('token')
            ->add('releaseFile', EntityType::class, [
                'class' => ReleaseFile::class,
                'choice_label' => 'id',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DownloadLog::class,
        ]);
    }
}
