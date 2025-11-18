<?php

namespace App\Form;

use App\Entity\School;
use App\Entity\Schoolkids;
use App\Entity\ContactPerson;
use App\Entity\OfficialAddress;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class SchoolkidsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Stammdaten
            ->add('lastName', TextType::class, [
                'label' => 'Last Name'
            ])
            ->add('firstName', TextType::class, [
                'label' => 'First Name'
            ])
            ->add('dateOfBirth', DateType::class, [
                'label' => 'Date of Birth',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('street', TextType::class, [
                'label' => 'Street'
            ])
            ->add('streetNumber', TextType::class, [
                'label' => 'Street Number'
            ])
            ->add('zip', TextType::class, [
                'label' => 'ZIP'
            ])
            ->add('city', TextType::class, [
                'label' => 'City'
            ])
            ->add('district', TextType::class, [
                'label' => 'District',
                'required' => false
            ])

            // Kontakt (freie Felder)
            ->add('contactPersonName', TextType::class, [
                'label' => 'Contact Person (Textfeld)',
                'required' => false
            ])
            ->add('contactPersonPhone', TextType::class, [
                'label' => 'Contact Phone',
                'required' => false
            ])
            ->add('kidPhone', TextType::class, [
                'label' => 'Kid Phone',
                'required' => false
            ])
            ->add('contactEmail', TextType::class, [
                'label' => 'Contact Email',
                'required' => false
            ])

            // Kontaktperson als Relation
            ->add('contactPerson', EntityType::class, [
                'class' => ContactPerson::class,
                'choice_label' => fn (ContactPerson $cp) => $cp->getFullName(),
                'label' => 'Zugewiesene Kontaktperson',
                'placeholder' => '– bitte wählen –',
                'required' => false,
            ])

            // Mehrere Kontaktpersonen als Collection (optional, wenn du es brauchst)
            ->add('contactPersons', CollectionType::class, [
                'entry_type' => ContactPersonType::class, // Muss vorhanden sein!
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Weitere Kontaktpersonen',
                'required' => false,
                'prototype' => true,
            ])

            // Hilfsmittel & Begleiter
            ->add('needsAid', CheckboxType::class, [
                'label' => 'Needs Aid (e.g. wheelchair, walker)',
                'required' => false
            ])
            ->add('aidType', TextType::class, [
                'label' => 'Aid Type',
                'required' => false
            ])
            ->add('hasCompanion', CheckboxType::class, [
                'label' => 'Has Companion',
                'required' => false
            ])
            ->add('companionName', TextType::class, [
                'label' => 'Companion Name',
                'required' => false
            ])
            ->add('requiredSeats', IntegerType::class, [
                'label' => 'Required Seats',
                'required' => false,
                'attr' => ['min' => 1]
            ])

            // Geo
            ->add('latitude', NumberType::class, [
                'label' => 'Latitude',
                'required' => false
            ])
            ->add('longitude', NumberType::class, [
                'label' => 'Longitude',
                'required' => false
            ])

            // Adresse aus dem Adressverzeichnis (OfficialAddress)
 ->add('address', HiddenType::class, [
    'required' => false,
    'attr' => [
        'class' => 'select2-address',
        'data-placeholder' => 'Adresse suchen...',
        'data-initial-text' => $options['address_text'] ?? '',
    ],
])



            // Sonstiges
            ->add('specialInfos', TextareaType::class, [
                'label' => 'Special Infos',
                'required' => false
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'Active',
                'required' => false
            ])

            // Schule
            ->add('school', EntityType::class, [
                'class' => School::class,
                'choice_label' => 'name',
                'label' => 'School',
                'required' => true,
            ])
        ;
    }

public function configureOptions(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        'data_class' => Schoolkids::class,
        'address_text' => '', // <- WICHTIG! Default setzen
    ]);
}

}
