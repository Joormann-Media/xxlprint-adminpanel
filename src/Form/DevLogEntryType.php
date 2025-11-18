<?php

namespace App\Form;

use App\Entity\DevLogEntry;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType; // <--- Import ergänzt


class DevLogEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content', TextareaType::class, [
                'attr' => ['class' => 'tinymce'], // TinyMCE wird für dieses Feld aktiviert
            ])
            ->add('createdAt', DateTimeType::class, [
                'widget' => 'single_text',
                'disabled' => true,
                'required' => false,
                'mapped' => false, // Achtung: dann ist's wirklich nur Anzeige!
            ])
            ->add('updatedAt', DateTimeType::class, [
                'widget' => 'single_text',
                'disabled' => true,
                'required' => false,
                'mapped' => false, // Achtung: dann ist's wirklich nur Anzeige!
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullname',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DevLogEntry::class,
        ]);
    }
}
