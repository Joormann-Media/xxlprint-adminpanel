<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFullFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue(['message' => 'You should agree to our terms.']),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a password']),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('username')
            ->add('prename')
            ->add('name')
            ->add('userpin')
            ->add('regDate', DateTimeType::class)
            ->add('lastlogindate', DateTimeType::class, ['required' => false])
            ->add('usergroups', TextType::class, ['required' => false])
            ->add('isActive', CheckboxType::class, ['required' => false])
            ->add('avatar')
            ->add('passwordChangedAt', DateTimeType::class, ['required' => false])
            ->add('failedAttempts', IntegerType::class)
            ->add('isLocked', CheckboxType::class)
            ->add('twoFactorSecret', TextType::class, ['required' => false])
            ->add('isTwoFactorEnabled', CheckboxType::class)
            ->add('createdAt', DateTimeType::class, ['required' => false])
            ->add('createdBy', TextType::class, ['required' => false])
            ->add('updatedAt', DateTimeType::class, ['required' => false])
            ->add('updatedBy', TextType::class, ['required' => false])
            ->add('ipAddress', TextType::class, ['required' => false])
            ->add('browserFingerprint', TextType::class, ['required' => false])
            ->add('deviceInfo', TextType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
