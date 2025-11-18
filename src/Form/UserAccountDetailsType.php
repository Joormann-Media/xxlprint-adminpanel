<?php

namespace App\Form;

use App\Entity\Permission;
use App\Entity\User;
use App\Entity\UserProfile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAccountDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles')
            ->add('password')
            ->add('isVerified')
            ->add('username')
            ->add('regDate', null, [
                'widget' => 'single_text',
            ])
            ->add('lastlogindate', null, [
                'widget' => 'single_text',
            ])
            ->add('userpin')
            ->add('prename')
            ->add('name')
            ->add('usergroups')
            ->add('passwordChangedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('failedAttempts')
            ->add('isLocked')
            ->add('isActive')
            ->add('twoFactorSecret')
            ->add('isTwoFactorEnabled')
            ->add('avatar')
            ->add('adminOverride')
            ->add('adminOverrideId')
            ->add('userDir')
            ->add('maxDevice')
            ->add('permissions', EntityType::class, [
                'class' => Permission::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('profile', EntityType::class, [
                'class' => UserProfile::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
