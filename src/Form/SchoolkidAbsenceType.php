<?php

namespace App\Form;

use App\Entity\SchoolkidAbsence;
use App\Entity\Schoolkids;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class SchoolkidAbsenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startAway')
            ->add('endAway')
            ->add('reasonAway')
            ->add('reportedBy')
                ->add('reportMethod', ChoiceType::class, [
        'choices'  => [
            'Telefon'      => 'Phone',
            'E-Mail'       => 'Email',
            'WhatsApp'     => 'WhatsApp',
            'Messenger'    => 'Messenger',
            'Sonstiges'    => 'Other',
        ],
        'placeholder' => 'Bitte wählen',
    ])
            ->add('receivedBy')
            ->add('reportedAt')
            ->add('createdAt')
                ->add('schoolkid', EntityType::class, [
        'class' => Schoolkids::class,
        'choice_label' => function (Schoolkids $k) {
            return $k->getLastName() . ' ' . $k->getFirstName();
        },
        'placeholder' => 'Schüler auswählen',
        'attr' => ['class' => 'select2'], // <-- Wichtig für Select2!
        'required' => true,
    ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SchoolkidAbsence::class,
        ]);
    }
}
