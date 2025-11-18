<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\LicenceClass;
use App\Entity\Vehicle;
use App\Entity\ConcessionManager;
use App\Form\VehicleDocumentType;
use App\Form\VehicleInspectionIntervalType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehicleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('bezeichnung', null, [
                'label' => 'Bezeichnung',
                'required' => false,
                'attr' => [
                    'placeholder' => 'z.B. Mein Bus, Taxi 1, ...',
                ],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Fahrzeugtyp',
                'choices' => [
                    'Reisebus Standard'       => 'Reisebus Standard',
                    'Reisebus Doppeldecker'   => 'Reisebus Doppeldecker',
                    'Linienbus Standard'      => 'Linienbus Standard',
                    'Linienbus Gelenk'        => 'Linienbus Gelenk',
                    'Kleinbus (16er)'         => 'Kleinbus (16er)',
                    'Kleinbus (8er)'          => 'Kleinbus (8er)',
                    'Kleinbus (8er) Rollstuhl'=> 'Kleinbus (8er) Rollstuhl',
                    'Großraum-Taxi'           => 'Großraum-Taxi',
                    'Standard-Taxi'           => 'Standard-Taxi',
                    'Standard-Taxi Limousine' => 'Standard-Taxi Limousine',
                    'Standard-Taxi Kombi'     => 'Standard-Taxi Kombi',
                    'Rollstuhl-Lift-Taxi'     => 'Rollstuhl-Lift-Taxi',
                    'Rollstuhl-Taxi'          => 'Rollstuhl-Taxi',
                    'Transporter'             => 'Transporter',
                ],
                'placeholder' => 'Bitte wählen',
                'required' => true,
            ])
            ->add('licensePlate', null, [
                'label' => 'Kennzeichen',
            ])
            ->add('radiotransceiver', null, [
                'label' => 'Funkgerät (z.B. CB-Radio) vorhanden?',
                'required' => false,
            ])
            ->add('vehicleNumber', null, [
                'label' => 'Interne Fahrzeugnummer',
            ])
            ->add('seatCount', null, [
                'label' => 'Anzahl Sitzplätze',
            ])
            ->add('wheelchair', null, [
                'label' => 'Rollstuhlplatz vorhanden?',
                'required' => false,
            ])
            ->add('axleCount', NumberType::class, [
                'label' => 'Anzahl Achsen',
                'attr' => ['min' => 1],
                'required' => true,
            ])
            ->add('axleLoad', NumberType::class, [
                'label' => 'Max. Achslast (kg)',
                'required' => false,
                'scale' => 2,
            ])
            ->add('width', NumberType::class, [
                'label' => 'Breite (m)',
                'required' => false,
                'scale' => 2,
            ])
            ->add('height', NumberType::class, [
                'label' => 'Höhe (m)',
                'required' => false,
                'scale' => 2,
            ])
            ->add('length', NumberType::class, [
                'label' => 'Länge (m)',
                'required' => false,
                'scale' => 2,
            ])
            ->add('emptyWeight', NumberType::class, [
                'label' => 'Leergewicht (kg)',
                'required' => false,
                'scale' => 2,
            ])
            ->add('maxWeight', NumberType::class, [
                'label' => 'zul. Gesamtgewicht (kg)',
                'required' => false,
                'scale' => 2,
            ])
            ->add('maxLoad', NumberType::class, [
                'label' => 'Nutzlast (kg)',
                'required' => false,
                'scale' => 2,
            ])
            ->add('driver', EntityType::class, [
                'class' => Employee::class,
                'choice_label' => 'id', // Besser: name oder anpassen auf 'fullName'
                'label' => 'Zugeordneter Fahrer',
                'required' => false,
                'placeholder' => 'Kein Fahrer zugeordnet',
            ])
            ->add('minLicenceClass', EntityType::class, [
                'class' => LicenceClass::class,
                'choice_label' => 'shortName',
                'label' => 'Minimale Führerscheinklasse',
            ])
            // QR-Code ist hier als FileType, muss im Controller extra behandelt werden!
            ->add('vehicleQr', FileType::class, [
                'label' => 'QR-Code (Bild hochladen)',
                'required' => false,
                'mapped' => false, // Datei-Upload wird separat gehandhabt
                'help' => 'Lade hier einen QR-Code für das Fahrzeug hoch.',
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'choices' => [
                    'Aktiv' => 'active',
                    'Werkstatt' => 'werkstatt',
                    'Stillgelegt' => 'stillgelegt',
                    'Reserviert' => 'reserviert',
                    'Ausgemustert' => 'ausgemustert',
                ],
                'required' => true,
                'placeholder' => 'Bitte wählen',
            ])
            ->add('vehicleInfos', TextareaType::class, [
                'label' => 'Fahrerinfos / Hinweise',
                'required' => false,
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'z.B. Besonderheiten, Abfahrtsort, Ausstattung, Hinweise für den Fahrer, ...',
                ],
            ])
            ->add('vin', null, [
                'label' => 'Fahrgestellnummer (VIN)',
                'required' => false,
            ])
            ->add('buildYear', NumberType::class, [
                'label' => 'Baujahr',
                'required' => false,
                'attr' => ['min' => 1900, 'max' => date('Y')],
            ])
            ->add('firstRegister', DateType::class, [
                'label' => 'Erstzulassung',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('currentRegisterDate', DateType::class, [
                'label' => 'Aktuelle Zulassung',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('registrationStatus', ChoiceType::class, [
                'label' => 'Zulassungsstatus',
                'choices' => [
                    'Zugelassen' => 'zugelassen',
                    'Abgemeldet' => 'abgemeldet',
                    'Stillgelegt' => 'stillgelegt',
                    'Exportiert' => 'exportiert',
                    'Außer Betrieb' => 'ausser_betrieb',
                ],
                'required' => true,
                'placeholder' => 'Bitte wählen',
            ])
            ->add('toiletStatus', ChoiceType::class, [
                'label' => 'Toilettenstatus',
                'required' => false,
                'placeholder' => 'Bitte wählen',
                'choices' => [
                    'Vorhanden – OK'        => 'ok',
                    'Vorhanden – Defekt'    => 'defect',
                    'Nicht vorhanden'       => 'not_available',
                ],
            ])
            ->add('speed100', ChoiceType::class, [
                'label' => 'Zulassung 100 km/h',
                'required' => false,
                'placeholder' => 'Bitte wählen',
                'choices' => [
                    'Ja (zulässig)' => 'yes',
                    'Nein (Siehe Info)' => 'no',
                    'Nicht nötig' => 'not_needed',
                ],
            ])
            ->add('ahk', null, [
                'label' => 'Anhängerkupplung vorhanden?',
                'required' => false,
            ])
            ->add('trailerLoad', NumberType::class, [
                'label' => 'Max. Anhängelast (kg)',
                'required' => false,
                'scale' => 2,
            ])
            ->add('wheelchairRamp', null, [
                'label' => 'Rollstuhlrampe vorhanden?',
                'required' => false,
            ])
            ->add('lifter', null, [
                'label' => 'Lifter vorhanden?',
                'required' => false,
            ])
            ->add('concession', EntityType::class, [
                'class' => ConcessionManager::class,
                'choice_label' => 'concessionNumber',
                'label' => 'Konzession',
                'required' => false,
                'placeholder' => 'Keine Konzession zugeordnet',
            ])
            // --- NEU: Dokumente als CollectionType ---
            ->add('vehicleDocuments', CollectionType::class, [
                'entry_type' => VehicleDocumentType::class,
                'entry_options' => ['label' => false],
                'label' => 'Fahrzeugdokumente',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
                'prototype' => true,
            ])
            // --- NEU: Inspektionsintervalle als CollectionType ---
            ->add('vehicleInspectionIntervals', CollectionType::class, [
                'entry_type' => VehicleInspectionIntervalType::class,
                'entry_options' => ['label' => false],
                'label' => 'Inspektions-/Wartungsintervalle',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
                'prototype' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicle::class,
        ]);
    }
}
