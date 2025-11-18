<?php

namespace App\Form;

use App\Entity\CodingLanguage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CodingLanguageForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('logo')
            ->add('year')
            ->add('languageType')
            ->add('extension')
            ->add('developer')
            ->add('paradigm')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CodingLanguage::class,
        ]);
    }
}
