<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('email', EmailType::class, [
                'required' => false,
                'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
                'label' => 'E-Mail Adresse:',
            ])
            ->add('username', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
                'label' => 'Benutzername:',
            ])
            ->add('prename', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
                'label' => 'Vorname:',
            ])
            ->add('name', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
                'label' => 'Nachname:',
            ])
            ->add('twoFactorSecret', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
                'label' => '2FA Secret:',
            ])
            ->add('regDate', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
                'label' => 'Registrierungsdatum:',
                'empty_data' => (new \DateTime())->format('Y-m-d\TH:i'), // Default to current date/time
            ])
            ->add('isTwoFactorEnabled', ChoiceType::class, [
                'choices' => [
                    'Ja' => true,
                    'Nein' => false,
                ],
                'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
                'label' => '2FA aktiv:',
            ])
            ->add('avatar', TextType::class, [
                'required' => false,
                'label' => 'Avatar / Bild URL',
                'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
                'label' => 'Avatar:',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
