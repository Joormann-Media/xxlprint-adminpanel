<?php

namespace App\Form;

use App\Entity\Message;
use App\Entity\MessageRecipient;
use App\Entity\User;
use App\Entity\UserGroups;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageRecipientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isAll')
            ->add('someIntegerField')
            ->add('message', EntityType::class, [
                'class' => Message::class,
                'choice_label' => 'id',
            ])
            ->add('recipientUser', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('recipientGroup', EntityType::class, [
                'class' => UserGroups::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MessageRecipient::class,
        ]);
    }
}
