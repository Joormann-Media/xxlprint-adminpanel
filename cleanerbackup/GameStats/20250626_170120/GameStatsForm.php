<?php

namespace App\Form;

use App\Entity\GameStats;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameStatsForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('statTimestamp')
            ->add('projectName')
            ->add('folder')
            ->add('files')
            ->add('images')
            ->add('archives')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GameStats::class,
        ]);
    }
}
