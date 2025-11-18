<?php

namespace App\Form;

use App\Entity\ConcessionManager;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConcessionManagerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('concessionNumber')
            ->add('type')
            ->add('company')
            ->add('validFrom')
            ->add('validUntil')
            ->add('authority')
            ->add('issueDate')
            ->add('status')
            ->add('documentFile')
            ->add('vehicleLimit')
            ->add('notes')
            ->add('description')
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('updatedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('createdBy', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ConcessionManager::class,
        ]);
    }
}
