<?php

namespace App\Form;

use App\Entity\AiManager;
use App\Entity\User;
use App\Entity\UserRoles;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AiManagerForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('aiName')
            ->add('aiModel')
            ->add('aiSetup')
            ->add('aiPath')
            ->add('aiHost')
            ->add('aiDescription')
            ->add('aiCreated', null, [
                'widget' => 'single_text',
            ])
                ->add('aiAvatar', TextType::class, [
        'label' => 'Avatar-Dateiname',
        'attr' => [
            'readonly' => true,
            'placeholder' => 'Avatar auswählen oder hochladen',
            'class' => 'form-control avatar-filename-field'
        ]
    ])
            ->add('aiStatus', ChoiceType::class, [
                'choices' => [
                    'Aktiv' => 'active',
                    'Wartung' => 'maintenance',
                    'Offline' => 'offline',
                    'Fehler' => 'error',
                ],
                'label' => 'Status',
                'placeholder' => 'Bitte wählen',
            ])
            ->add('aiCategory', ChoiceType::class, [
    'choices' => [
        'Text-KI (LLM)' => 'llm',
        'Text-zu-Sprache (TTS)' => 'tts',
        'Sprache-zu-Text (STT)' => 'stt',
        'KI-Trainer' => 'trainer',
        'Bild-KI' => 'image',
        'Audio-KI' => 'audio',
        'Computer Vision' => 'vision',
        'Agent/Workflow' => 'agent',
        'Tools/Hilfsdienste' => 'tools',
        'Sonstige' => 'other',
    ],
    'label' => 'Kategorie',
    'placeholder' => 'Bitte wählen',
])

            ->add('aiHealthUrl')
            ->add('aiLastCheckedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('aiApiToken')
            ->add('aiLastResponseMs')
            ->add('aiLastError')
            ->add('aiMaintainer', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
            ])
            ->add('aiMinrole', EntityType::class, [
                'class' => UserRoles::class,
                'choice_label' => 'role_name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AiManager::class,
        ]);
    }
}
