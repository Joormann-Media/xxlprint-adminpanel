<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\UserHistory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserHistoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('timestamp', null, [
                'widget' => 'single_text',
            ])
            ->add('action')
            ->add('ipAddress')
            ->add('device')
            ->add('browserFingerprint')
            ->add('metaData')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserHistory::class,
        ]);
    }
}
