<?php

namespace App\Form;

use App\Entity\Message;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\UserGroups;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Chat' => 'chat',
                    'News' => 'news',
                    'System' => 'system',
                ],
                'placeholder' => 'Nachrichtentyp wählen',
            ])
            ->add('isUrgent', null, [
                'label' => 'Dringend?',
                'required' => false,
            ])
            // Empfänger als User-Auswahl (Multi-Select)
            ->add('recipients', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'multiple' => true,
                'required' => false,
                'label' => 'Empfänger (User)',
                'attr' => ['class' => 'select2'],
                'mapped' => false, // <<< HIER LIEGT DER TRICK!
            ])
            // Empfänger als Gruppen-Auswahl (Multi-Select)
            ->add('groups', EntityType::class, [
                'class' => UserGroups::class,
                'choice_label' => 'group_name',
                'multiple' => true,
                'required' => false,
                'label' => 'Empfänger (Gruppe)',
                'attr' => ['class' => 'select2'],
                'mapped' => false, // <<< EBENFALLS unmapped!
            ])
            // Attachments
            ->add('attachments', FileType::class, [
                'label' => 'Dateianhänge',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
            ]);
        // createdAt und sender weglassen (im Controller setzen!)
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
