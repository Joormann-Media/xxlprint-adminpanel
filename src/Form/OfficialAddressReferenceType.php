<?php

// src/Form/OfficialAddressReferenceType.php

namespace App\Form;

use App\Entity\OfficialAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class OfficialAddressReferenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('officialAddress', EntityType::class, [
            'class' => OfficialAddress::class,
            'choice_label' => function($address) {
                return (string)$address;
            },
            'placeholder' => 'Adresse suchen ...',
            'required' => true,
            'attr' => [
                'class' => 'address-autocomplete', // Für JS
            ],
            // 'query_builder' => ... wenn du die Vorauswahl begrenzen willst
        ]);
    }
}

