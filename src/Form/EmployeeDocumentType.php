<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\EmployeeDocument;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class EmployeeDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('docType', ChoiceType::class, [
                'label' => 'Dokumenttyp',
                'choices' => [
                    'Führerschein' => 'Führerschein',
                    'Personenbeförderungsschein' => 'Personenbeförderungsschein',
                    'Personalausweis' => 'Personalausweis',
                    'Fahrerkarte' => 'Fahrerkarte',
                    'Fahrerqualifizierungsnachweis' => 'Fahrerqualifizierungsnachweis',
                    'Gesundheitszeugnis' => 'Gesundheitszeugnis',
                    'SchwerbehindertenAusweis' => 'SchwerbehindertenAusweis',
                    'Arbeitsvertrag' => 'Arbeitsvertrag',
                    'Notfallkontaktformular' => 'Notfallkontaktformular',
                    'Abmahnung' => 'Abmahnung',
                    'Zeugnis' => 'Zeugnis',
                    'Sonstiges' => 'Sonstiges',
                    'Urlaubsantrag' => 'Urlaubsantrag',
                    'Krankmeldung' => 'Krankmeldung',
                    'Lohnsteuerbescheinigung' => 'Lohnsteuerbescheinigung',
                    'Sozialversicherungsnachweis' => 'Sozialversicherungsnachweis',
                    'Steueridentifikationsnummer' => 'Steueridentifikationsnummer',

                ],
                'placeholder' => 'Bitte auswählen',
            ])
            ->add('docDate', DateType::class, [
                'label' => 'Ausstellungsdatum',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('docExpires', DateType::class, [
                'label' => 'Ablaufdatum',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('docValidated', DateType::class, [
                'label' => 'Prüfdatum',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('employee', EntityType::class, [
                'class' => Employee::class,
                'choice_label' => function (Employee $employee) {
                    // Zeige Vorname, Nachname und Personalnummer für bessere Übersicht
                    return $employee->getFirstName() . ' ' . $employee->getLastName() . ' (#' . $employee->getEmployeeNumber() . ')';
                },
                'label' => 'Mitarbeiter',
                'placeholder' => 'Mitarbeiter wählen',
            ])
            ->add('upload', FileType::class, [
                'label' => 'Dokument hochladen (PDF/Bild)',
                'required' => false,
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EmployeeDocument::class,
        ]);
    }
}
