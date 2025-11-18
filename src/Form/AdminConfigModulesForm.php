<?php

namespace App\Form;

use App\Entity\AdminConfigModules;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminConfigModulesForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('moduleName')
            ->add('moduleDescription')
            ->add('minRole')
            ->add('moduleCreate', null, [
                'widget' => 'single_text',
            ])
            ->add('moduleBy')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdminConfigModules::class,
        ]);
    }
}
