<?php

namespace App\Form;

use App\Entity\Documentations;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class DocumentationsForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('docuName')
            ->add('docuShortdescr')
            ->add('docuDescription', TextareaType::class, [
                'attr' => ['class' => 'tinymce'], // TinyMCE wird für dieses Feld aktiviert
            ])
            ->add('docuCreate', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('docuUpdate', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('docuVersion')
            ->add('docuMaintainer', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullname',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Documentations::class,
        ]);
    }
}
