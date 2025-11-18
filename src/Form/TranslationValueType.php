<?php

namespace App\Form;

use App\Entity\Language;
use App\Entity\TranslationKey;
use App\Entity\TranslationValue;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class TranslationValueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value')
            ->add('translationKey', EntityType::class, [
                'class' => TranslationKey::class,
                'choice_label' => 'id',
            ])
            ->add('language', EntityType::class, [
                'class' => Language::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TranslationValue::class,
        ]);
    }
}
