<?php

// src/Form/ResetPasswordPinType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordPinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Neues Passwort',
                'mapped' => false,
                'required' => true,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(['message' => 'Bitte gib ein Passwort ein']),
                    new Length(['min' => 4, 'minMessage' => 'Mindestens 4 Zeichen']),
                ],
            ])
            ->add('userpin', PasswordType::class, [
                'label' => 'Neue PIN',
                'mapped' => false,
                'required' => true,
                'attr' => ['autocomplete' => 'new-pin'],
                'constraints' => [
                    new NotBlank(['message' => 'Bitte gib eine PIN ein']),
                    new Length(['min' => 4, 'minMessage' => 'Mindestens 4 Zeichen']),
                ],
            ]);
    }
}

