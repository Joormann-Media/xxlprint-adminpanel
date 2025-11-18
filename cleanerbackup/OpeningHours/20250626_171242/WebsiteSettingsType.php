<?php

namespace App\Form;

use App\Entity\WebsiteSettings;
use App\Entity\PopUpManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManagerInterface;

class WebsiteSettingsType extends AbstractType
{
    private $entityManager;

    // Hinzufügen des EntityManagers in den Konstruktor
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
{
    // Abrufen der Popups mit einer bestimmten Kategorie (filtere nach 'yourCategory')
    $popupChoices = [];
    $popups = $this->entityManager->getRepository(PopUpManager::class)
        ->createQueryBuilder('p')
        ->where('p.popupCategory = :category')
        ->setParameter('category', '1') // Ersetze 'yourCategory' mit der gewünschten Kategorie
        ->getQuery()
        ->getResult();

    // Popups in das choices Array einfügen, wobei der Name des PopUps als Schlüssel und das Objekt als Wert gespeichert wird
    foreach ($popups as $popup) {
        $popupChoices[$popup->getPopupName()] = $popup; // PopupName wird angezeigt, das PopUpManager-Objekt wird gespeichert
    }

    // Jetzt das Formular-Feld WebsiteMessageId mit den PopUpManager-Daten hinzufügen
    $builder
        ->add('websiteMode', ChoiceType::class, [
            'choices' => [
                'Aktiv' => 'active',
                'Wartung' => 'maintenance',
                'PopUp' => 'popup',
                'Urlaub' => 'vacation',
            ],
            'label' => 'WebSite - Status',
            'attr' => ['class' => 'Custom-css-input']
        ])
        ->add('lastUpdate', null, [
            'widget' => 'single_text',
            'label' => 'Letzte Aktualisierung:',
            'attr' => ['class' => 'Custom-css-input', 'readonly' => true],
        ])
        ->add('lastUpdateBy', null, [
            'label' => 'Aktualisiert von:',
            'attr' => ['class' => 'Custom-css-input', 'readonly' => true],
        ])
        ->add('WebsiteMessageId', EntityType::class, [
            'class' => PopUpManager::class,  // PopUpManager als Klasse verwenden
            'choices' => $popupChoices,      // Verwendet die oben gefüllte $popupChoices
            'choice_label' => 'popupName',   // Zeigt den PopUp-Namen im Dropdown an
            'label' => 'Website Message auswählen:',
            'attr' => ['class' => 'Custom-css-input'],
            'placeholder' => 'Wählen Sie ein PopUp',
            'empty_data' => null,            // Default-Wert null, wenn keine Auswahl getroffen wird
        ])
        ->add('activeUntil', DateTimeType::class, [
            'widget' => 'single_text',
            'label' => 'Aktiv bis:',
            'required' => false,
            'attr' => ['class' => 'form-control'],
        ]);
}


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WebsiteSettings::class,
        ]);
    }
}
