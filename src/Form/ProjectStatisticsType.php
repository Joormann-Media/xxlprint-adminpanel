<?php

namespace App\Form;

use App\Entity\ProjectStatistics;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectStatisticsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('createdAt')
            ->add('entitiesCount')
            ->add('routesCount')
            ->add('formsCount')
            ->add('controllersCount')
            ->add('filesCount')
            ->add('directoriesCount')
            ->add('servicesCount')
            ->add('commandsCount')
            ->add('shellScriptsCount')
            ->add('pythonScriptsCount')
            ->add('phpLinesCount')
            ->add('pythonLinesCount')
            ->add('shellLinesCount')
            ->add('totalLinesCount')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProjectStatistics::class,
        ]);
    }
}
