<?php

namespace App\Form;

use App\Entity\ShortcodeImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ShortcodeImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tag') // Renamed from 'key' to 'tag'
            ->add('filename')
            ->add('path', null, [
                'data' => 'gfx/shortcode/',
            ])
            ->add('title')
            ->add('description')
            ->add('isActive')
            ->add('sortOrder')
            ->add('createdBy')
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('updatedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('preview', HiddenType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'data-shortcode-preview' => true
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShortcodeImage::class,
        ]);
    }
}
