<?php

namespace App\Form;

use App\Entity\ApiManager;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ApiManagerForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('apiCreate', null, [
                'widget' => 'single_text',
            ])
            ->add('apiStatus')
            ->add('apiDescription', TextareaType::class, [
                'attr' => ['class' => 'tinymce'], // TinyMCE wird für dieses Feld aktiviert
            ])
            ->add('maintainer', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ApiManager::class,
        ]);
    }
}
