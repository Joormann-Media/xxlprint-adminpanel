<?php

namespace App\Form;

use App\Entity\IconIndex;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IconIndexForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('iconPath')
            ->add('iconName')
            ->add('iconCategory')
            ->add('iconTags')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => IconIndex::class,
        ]);
    }
}
