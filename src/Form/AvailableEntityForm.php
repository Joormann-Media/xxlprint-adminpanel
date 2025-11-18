<?php

namespace App\Form;

use App\Entity\AvailableEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvailableEntityForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('displayName')
            ->add('className')
            ->add('tag')
            ->add('description')
            ->add('active')
            ->add('icon')
            ->add('sortOrder')
            ->add('dependencies')
            ->add('extraMeta')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AvailableEntity::class,
        ]);
    }
}
