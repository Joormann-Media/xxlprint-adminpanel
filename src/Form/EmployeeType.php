<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\LicenceClass;
use App\Entity\User;
use App\Entity\ContactPerson;
use App\Entity\CostCenter;
use App\Entity\Company;
use App\Entity\OfficialAddress;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use App\Form\DataTransformer\OfficialAddressToIdTransformer;
use Doctrine\ORM\EntityManagerInterface;


class EmployeeType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ====== Standardfelder ======
            ->add('firstName', TextType::class, [
                'label' => 'Vorname'
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nachname'
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'label' => 'E-Mail'
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'label' => 'Telefon'
            ])
            ->add('employeeNumber', TextType::class, [
                'required' => false,
                'label' => 'Personalnummer'
            ])
            ->add('isDriver', CheckboxType::class, [
                'label' => 'Fahrer?',
                'required' => false
            ])
            ->add('birthDate', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Geburtsdatum'
            ])
            ->add('shortCode', TextType::class, [
                'required' => false,
                'label' => 'Kürzel'
            ])
            ->add('hiredAt', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Eingestellt am'
            ])
            ->add('agreedHoursDaily', NumberType::class, [
                'required' => false,
                'label' => 'Tägliche Sollstunden'
            ])
            ->add('agreedHoursWeekly', NumberType::class, [
                'required' => false,
                'label' => 'Wöchentliche Sollstunden'
            ])
            ->add('agreedHoursMonthly', NumberType::class, [
                'required' => false,
                'label' => 'Monatliche Sollstunden'
            ])

            // ====== Relationen ======
            ->add('licenceClasses', EntityType::class, [
                'class' => LicenceClass::class,
                'multiple' => true,
                'expanded' => false,
                'label' => 'Führerscheinklassen',
                'required' => false,
                'choice_label' => 'shortName' // anpassen falls anders!
            ])
            ->add('vehicles', EntityType::class, [
                'class' => \App\Entity\Vehicle::class,
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'label' => 'Fahrzeuge',
                'choice_label' => 'licensePlate' // anpassen falls anders!
            ])
            // Mentor/Patin raus! Kein ->add('employee', ...)
            ->add('user', EntityType::class, [
                'class' => User::class,
                'required' => false,
                'label' => 'Systembenutzer'
            ])
            ->add('emergencyContact', EntityType::class, [
                'class' => ContactPerson::class,
                'required' => false,
                'label' => 'Notfallkontakt'
            ])
           ->add('company', EntityType::class, [
    'class' => Company::class,
    'required' => false,
    'label' => 'Firma',
    'choice_label' => 'companyname' // <--- KORREKT!
])

            ->add('costCenters', EntityType::class, [
                'class' => CostCenter::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'label' => 'Kostenstellen',
                'by_reference' => false,
            ])
            
->add('address', HiddenType::class, [
            'required' => false,
            'attr' => [
                'class' => 'js-address-autocomplete',
                'data-url' => '/official-address/api/address/suggest'
            ]
        ])

            ;
            $builder->get('address')
        ->addModelTransformer(new OfficialAddressToIdTransformer($this->em));
        // vacations/documents als eigene Embedded-Forms (z. B. in Tabs/Card im Template)
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
        ]);
    }

}
