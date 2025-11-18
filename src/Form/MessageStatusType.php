<?php

namespace App\Form;

use App\Entity\MessageRecipient;
use App\Entity\MessageStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageStatusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('deliveredAt')
            ->add('displayedAt')
            ->add('confirmedAt')
            ->add('deviceInfo')
            ->add('ip')
            ->add('recipient', EntityType::class, [
                'class' => MessageRecipient::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MessageStatus::class,
        ]);
    }
}
