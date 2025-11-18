<?php

namespace App\Form;

use App\Entity\BlogCategory;
use App\Entity\DevelopersBlog;
use App\Entity\Project;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DevelopersBlogForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('slug')
            ->add('content')
            ->add('excerpt')
            ->add('featuredImageUrl')
            ->add('status')
            ->add('readingTime')
            ->add('publishedAt')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('tags')
            ->add('blogPostId')
            ->add('commentsAllowed')
            ->add('commentsVisibility')
            ->add('author', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('projekt', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'id',
            ])
            ->add('blockedUsers', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('category', EntityType::class, [
                'class' => BlogCategory::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DevelopersBlog::class,
        ]);
    }
}
