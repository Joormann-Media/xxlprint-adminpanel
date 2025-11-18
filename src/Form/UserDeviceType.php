<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\UserDevice;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserDeviceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('deviceName')
            ->add('deviceFingerprint')
            ->add('ipAddress')
            ->add('userAgent')
            ->add('registeredAt', null, [
                'widget' => 'single_text',
            ])
            ->add('isTrusted')
            ->add('isActive')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'disabled' => true, // Prevent user from modifying this field
                'attr' => ['class' => 'd-none'], // Hide the field in the form
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserDevice::class,
        ]);
    }
}
