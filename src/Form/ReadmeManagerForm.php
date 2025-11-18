<?php

namespace App\Form;

use App\Entity\ReadmeManager;
use App\Entity\User;
use App\Entity\UserRoles;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReadmeManagerForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('target')
            ->add('targetId')
            ->add('title')
            ->add('content')
            ->add('status')
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('author', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
            ])
            ->add('minRole', EntityType::class, [
                'class' => UserRoles::class,
                'choice_label' => 'role_name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReadmeManager::class,
        ]);
    }
}
