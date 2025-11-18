<?php

namespace App\Form;

use App\Entity\ApiToken;
use App\Entity\PartnerCompany;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ApiTokenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('token', null, [
    'attr' => ['readonly' => true],
])

                    ->add('type', ChoiceType::class, [
            'choices' => [
                '🔑 Registrierung' => 'register',
                '✉️ Einladung'     => 'invitation',
                '🛠 API-Zugriff'    => 'api',
                '🔐 Externer Zugriff' => 'access',
                '🪄 Admin-Magic'    => 'admin_magic',
            ],
            'placeholder' => 'Typ auswählen...',
            'attr' => ['class' => 'form-select'],
        ])
            ->add('createdAt')
            ->add('expiresAt')
            ->add('used')
            ->add('partnerCompany', EntityType::class, [
                'class' => PartnerCompany::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ApiToken::class,
        ]);
    }
}
