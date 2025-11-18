<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Benutzername',
            ])
            ->add('prename', TextType::class, [
                'label' => 'Vorname',
            ])
            ->add('name', TextType::class, [
                'label' => 'Nachname',
            ])
            ->add('userpin', PasswordType::class, [
                'label' => 'Benutzer-PIN',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Bitte gib eine PIN ein']),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Die PIN muss mindestens {{ limit }} Zeichen lang sein',
                        // max offengelassen – kann also beliebig lang sein
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Passwort',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Bitte gib ein Passwort ein']),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Das Passwort muss mindestens {{ limit }} Zeichen lang sein',
                        // max offengelassen – kann also beliebig lang sein
                    ]),
                ],
            ]);
            // Email-Feld NICHT mehr ins Formular – kommt jetzt aus dem Controller
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
