<?php

namespace App\Form;

use App\Entity\DriverLicense;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DriverLicenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName')
            ->add('firstName')
            ->add('birthPlace')
            ->add('birthDate')
            ->add('issuedAt')
            ->add('expiresAt')
            ->add('authority')
            ->add('licenseNumber')
            ->add('signatureImage')
            ->add('address')
            ->add('classAM')
            ->add('classA1')
            ->add('classA2')
            ->add('classA')
            ->add('classB')
            ->add('classBE')
            ->add('classC1')
            ->add('classC1E')
            ->add('classC')
            ->add('classCE')
            ->add('classD1')
            ->add('classD1E')
            ->add('classD')
            ->add('classDE')
            ->add('classL')
            ->add('classM')
            ->add('classT')
            ->add('remarks')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DriverLicense::class,
        ]);
    }
}
