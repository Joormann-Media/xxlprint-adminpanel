<?php

namespace App\Form;

use App\Entity\Menu;
use App\Entity\UserMenuConfig;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserMenuConfigForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sortOrder')
            ->add('menuPosition')
            ->add('menuId', EntityType::class, [
                'class' => Menu::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserMenuConfig::class,
        ]);
    }
}
