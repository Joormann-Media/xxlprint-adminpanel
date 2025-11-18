<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\CustomerUser;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CustomerUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('password')
            ->add('roles', ChoiceType::class, [
                'label' => 'Rolle',
                'choices' => [
                    'Benutzer' => 'ROLE_USER',
                    'Kunde' => 'ROLE_CUSTOMER',
                    'Admin' => 'ROLE_ADMIN',
                ],
                'expanded' => false,  // Dropdown (true = Radiobuttons/Checkboxen)
                'multiple' => true,   // Rollen sind ein Array!
                'required' => true,
            ])
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomerUser::class,
        ]);
    }
}
