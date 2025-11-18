<?php

namespace App\Form;

use App\Entity\ShellCommands;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShellCommandsForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('commandShort')
            ->add('commandFull')
            ->add('commandDescription')
            ->add('commandCategory')
            ->add('commandCreateDate', null, [
                'widget' => 'single_text',
            ])
            ->add('commandUser', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username', // Display the username instead of the ID
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShellCommands::class,
        ]);
    }
}
