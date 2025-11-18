<?php

namespace App\Form;

use App\Entity\KidsBlacklist;
use App\Entity\Schoolkids;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KidsBlacklistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reason')
            ->add('createdAt')
            ->add('kid', EntityType::class, [
                'class' => Schoolkids::class,
                'choice_label' => 'id',
            ])
            ->add('enemy', EntityType::class, [
                'class' => Schoolkids::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => KidsBlacklist::class,
        ]);
    }
}
