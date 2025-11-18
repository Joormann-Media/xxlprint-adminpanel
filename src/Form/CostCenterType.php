<?php

namespace App\Form;

use App\Entity\CostCenter;
use App\Entity\Employee;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Company;


class CostCenterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('name')
            ->add('description')
            ->add('active')
            ->add('employees', EntityType::class, [
                'class' => Employee::class,
                'choice_label' => 'fullName', // oder wie dein Feld heißt
                'multiple' => true,
            ])
            // CostCenterType.php
            ->add('company', EntityType::class, [
                'class' => Company::class,
                'choice_label' => 'companyname', // oder wie dein Feld halt heißt
                'label' => 'Firma',
                'required' => true,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CostCenter::class,
        ]);
    }
}
