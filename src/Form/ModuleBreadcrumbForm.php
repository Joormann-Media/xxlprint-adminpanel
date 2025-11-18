<?php

namespace App\Form;

use App\Entity\ModuleBreadcrumb;
use App\Entity\ModuleManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModuleBreadcrumbForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label')
            ->add('route')
            ->add('icon')
            ->add('sortOrder')
            ->add('routeParameters')
            ->add('module', EntityType::class, [
                'class' => ModuleManager::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ModuleBreadcrumb::class,
        ]);
    }
}
