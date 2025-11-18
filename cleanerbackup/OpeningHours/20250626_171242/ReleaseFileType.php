<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;



class ReleaseFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'label' => 'Datei hochladen',
                'mapped' => false,
                'required' => true,
            ])
            ->add('platform', ChoiceType::class, [
                'choices' => [
                    'Windows' => 'win',
                    'Mac' => 'mac',
                    'Linux' => 'linux',
                    'Android' => 'android',
                    'iOS' => 'ios',
                    'Web' => 'web',
                ],
                'placeholder' => 'Plattform wählen',
            ])
            ->add('isPublic', CheckboxType::class, [
                'required' => false,
                'label' => 'Öffentlich zugänglich',
            ]);
    }
}
