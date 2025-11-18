<?php

namespace App\Form;

use App\Entity\ApiClient;
use App\Entity\PartnerCompany;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApiClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('passkey')
            ->add('ipWhitelist')
            ->add('authKey')
            ->add('isValid')
            ->add('expires')
            ->add('registerToken')
            ->add('createdAt')
            ->add('partnerCompany', EntityType::class, [
                'class' => PartnerCompany::class,
                'choice_label' => 'name', // oder was immer du anzeigen willst
                'placeholder' => '— Bitte wählen —',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ApiClient::class,
        ]);
    }
}
