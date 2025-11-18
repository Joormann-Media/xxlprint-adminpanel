<?php

namespace App\Form;

use App\Entity\DialogueReference;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DialogueReferenceForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roomId')
            ->add('dialogId')
            ->add('dialogText')
            ->add('dialogLang')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DialogueReference::class,
        ]);
    }
}
