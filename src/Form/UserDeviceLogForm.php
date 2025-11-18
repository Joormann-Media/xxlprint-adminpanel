<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\UserDevice;
use App\Entity\UserDeviceLog;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserDeviceLogForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('actionType')
            ->add('ipAddress')
            ->add('timestamp')
            ->add('result')
            ->add('note')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('device', EntityType::class, [
                'class' => UserDevice::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserDeviceLog::class,
        ]);
    }
}
