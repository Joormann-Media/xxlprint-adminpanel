<?php

namespace App\Form;

use App\Entity\MediaItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class MediaItemForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('type')
            ->add('sourceSystem')
            ->add('localPath')
            ->add('coverUrl')
            ->add('description')
            ->add('externalId')
            ->add('isFavorite')
            ->add('isVisible')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MediaItem::class,
        ]);
    }
}
