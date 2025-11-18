<?php

namespace App\Form;

use App\Entity\Scripting;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScriptingForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('scriptname')
            ->add('scriptDevstart')
            ->add('scriptUpdate')
            ->add('scriptReadme')
            ->add('language')
            ->add('scriptowner', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('scriptmaintainer', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Scripting::class,
        ]);
    }
}
