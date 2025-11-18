<?php

namespace App\Form;

use App\Entity\GraphicsReference;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GraphicsReferenceForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roomName')
            ->add('roomDate')
            ->add('originalGraphicsPath')
            ->add('originalGraphicsMeta')
            ->add('reworkGraphicsPatch')
            ->add('reworkGraphicsMeta')
            ->add('reworkArtis')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GraphicsReference::class,
        ]);
    }
}
