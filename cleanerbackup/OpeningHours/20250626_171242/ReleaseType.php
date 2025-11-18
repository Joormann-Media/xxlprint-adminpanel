<?php

namespace App\Form;

use App\Entity\Release;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Uid\Uuid;

class ReleaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('softwareId', TextType::class, [
                'attr' => ['readonly' => true],
            ])
            ->add('softwareName')
            ->add('version', TextType::class, [
                'empty_data' => '0.0.1',
                'data' => $options['data'] && $options['data']->getVersion() 
                    ? $options['data']->getVersion() 
                    : '0.0.1',
                
            ])
            ->add('releaseDate')
            ->add('downloadUrl', TextType::class, [
                'required' => false,
                'data' => $options['data'] && $options['data']->getDownloadUrl() 
                    ? $options['data']->getDownloadUrl() 
                    : null,
            ])
            ->add('releaseNotes')
            ->add('isPublic')
            ->add('platform', ChoiceType::class, [
                'choices' => [
                    'Windows (Desktop)' => 'windows',
                    'Windows (Server)' => 'windows-server',
                    'Linux (Desktop)' => 'linux',
                    'Linux (Server)' => 'linux-server',
                    'macOS' => 'macos',
                    'iOS' => 'ios',
                    'Android' => 'android',
                    'Web' => 'web',
                    'Desktop/Mobile' => 'desktop-mobile',
                    'Server (Other)' => 'server',
                    'ChromeOS' => 'chromeos',
                    'Raspberry Pi' => 'raspberry-pi',
                    'Other' => 'other',
                ],
                'group_by' => fn($val) => str_contains($val, 'server') ? 'Server' : 'Desktop/Mobile',
            ])
            
            ->add('releaseCreatedBy', TextType::class, [
                'attr' => ['readonly' => true],
            ])
            ->add('releaseCreatedAt')
            
            ->add('releaseDevStatus', ChoiceType::class, [
                'choices' => [
                    'Alpha' => 'alpha',
                    'Beta' => 'beta',
                    'Release Candidate' => 'rc',
                    'Stable' => 'stable',
                    'Deprecated' => 'deprecated',
                ],
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'tinymce'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Release::class,
        ]);
    }
}

