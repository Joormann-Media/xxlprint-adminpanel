<?php

namespace App\Form;

use App\Entity\DashboardModules;
use App\Entity\User;
use App\Entity\UserDashboardConfig;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserDashboardConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $highestSortOrder = $options['highest_sort_order'] ?? 0;
        $highestPosition = $options['highest_position'] ?? 0;

        $builder
            ->add('sortOrder', null, [
                'data' => $highestSortOrder + 1, // Increment highest sortOrder by 1
            ])
            ->add('isVisible')
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('updatedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('position', null, [
                'data' => $highestPosition + 1, // Increment highest position by 1
            ])
            //->add('settings')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('module', EntityType::class, [
                'class' => DashboardModules::class,
                'choice_label' => 'name', // Display the name of the module
                'choices' => $options['available_modules'], // Use the available_modules option
            ])
        ;
    }
    

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserDashboardConfig::class,
            'available_modules' => [], // Define the available_modules option with a default value
            'highest_sort_order' => 0, // Default highest sortOrder
            'highest_position' => 0, // Default highest position
        ]);
    }
}
