<?php

namespace App\Form;

use App\Entity\Language;
use App\Entity\LanguageFile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class LanguageFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('language', EntityType::class, [
                'class' => Language::class,
                'choice_label' => 'name',
                'label' => 'Sprache',
                'required' => true,
            ])
            ->add('filename')
            ->add('content', TextareaType::class, [
                'label' => 'File-Inhalt (z.B. YAML/JSON)',
                'required' => false,
            ])
            ->add('updatedAt', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Letzte Änderung',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LanguageFile::class,
        ]);
    }
}
