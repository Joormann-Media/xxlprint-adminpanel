<?php

namespace App\Form;

use App\Entity\Release;
use App\Entity\ReleaseFile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReleaseFileForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('originalFilename')
            ->add('storedFilename')
            ->add('platform')
            ->add('filesize')
            ->add('sha256')
            ->add('uploadedAt')
            ->add('isPublic')
            ->add('downloadUrl')
            ->add('release', EntityType::class, [
                'class' => Release::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReleaseFile::class,
        ]);
    }
}
