<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\UserToken;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserTokenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('token')
            ->add('type')
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('expiresAt', null, [
                'widget' => 'single_text',
            ])
            ->add('used')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserToken::class,
        ]);
    }
}
