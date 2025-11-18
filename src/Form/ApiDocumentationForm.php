<?php

namespace App\Form;

use App\Entity\ApiDocumentation;
use App\Entity\ApiManager;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ApiDocumentationForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('documentation_short')
            ->add('documentation')
            ->add('createAt', null, [
                'widget' => 'single_text',
            ])
            ->add('lastUpdate', null, [
                'widget' => 'single_text',
            ])
            ->add('apiManager', EntityType::class, [
                'class' => ApiManager::class,
                'choice_label' => 'name',
            ])
            ->add('createBy', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ApiDocumentation::class,
        ]);
    }
}
