<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\UserProfile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('birthdate', null, [
                'widget' => 'single_text',
            ])
            ->add('phonePrivate')
            ->add('phoneMobile')
            ->add('street')
            ->add('postalCode')
            ->add('city')
            ->add('country')
            ->add('language', ChoiceType::class, [
                'label' => 'Sprachen',
                'choices' => [
                    'Deutsch' => 'de',
                    'Englisch' => 'en',
                    'Französisch' => 'fr',
                    'Griechisch' => 'gr',
                    'Niederländisch' => 'nl',
                    'Türkisch' => 'tr',
                ],
                'multiple' => true,
                'expanded' => true, // Checkboxen
            ])
            ->add('profileVisibility', ChoiceType::class, [
                'label' => 'Profil-Sichtbarkeit',
                'choices' => [
                    '👁️ Profil sichtbar' => 1,
                    '🚫 Profil unsichtbar' => 0,
                ],
                'expanded' => false, // Dropdown
                'multiple' => false,
            ])
            ->add('linkedin')
            ->add('twitter')
            ->add('facebook')
            ->add('instagram')
            ->add('xing')
            ->add('website')
            ->add('motto')
            ->add('tiktok')


            ->add('removeAvatar', CheckboxType::class, [
                'label' => 'Aktuelles Avatar löschen',
                'mapped' => false,
                'required' => false,
            ])
            ->add('avatar', FileType::class, [
                'label' => 'Avatar (Bilddatei)',
                'required' => false,
                'mapped' => false, // This field is not mapped to the entity
            ])
            ->add('removeAvatar', CheckboxType::class, [
                'label' => 'Avatar löschen',
                'required' => false,
                'mapped' => false, // This field is not mapped to the entity
            ])
            
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserProfile::class,
        ]);
    }
}
