<?php

namespace App\Form;

use App\Entity\SymlinkCreator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SymlinkCreatorForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sourcePath')
            ->add('sourceDestination')
            ->add('symlinkCreated', null, [
                'widget' => 'single_text',
            ])
            ->add('symlinkCreatedBy')
            ->add('symlinkStatus')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SymlinkCreator::class,
        ]);
    }
}
