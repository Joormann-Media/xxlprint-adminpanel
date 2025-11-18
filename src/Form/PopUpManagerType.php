<?php

namespace App\Form;

use App\Entity\PopUpManager;
use App\Repository\PopUpCategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PopUpManagerType extends AbstractType
{
    private $security;
    private $popUpCategoryRepository;

    public function __construct(Security $security, PopUpCategoryRepository $popUpCategoryRepository)
    {
        $this->security = $security;
        $this->popUpCategoryRepository = $popUpCategoryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();
        $currentTimestamp = new \DateTime();
        
        // Hole alle PopUp-Kategorien aus der Datenbank
        $categories = $this->popUpCategoryRepository->findAll();

        // Konvertiere die Kategorien in ein Array für das Dropdown
        $categoryChoices = [];
        foreach ($categories as $category) {
            $categoryChoices[$category->getCategoryName()] = $category->getId(); // Zeigt den Namen an, speichert die ID
        }

        $builder
            ->add('popupName', null, [
                'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
                'label' => 'Name des Popups',
            ])
            ->add('popupStatus', ChoiceType::class, [
                'choices' => [
                    'Aktiv' => 'active',
                    'Inaktiv' => 'inactive',
                ],
                'label' => 'Status des Popups',
                'attr' => ['class' => 'Custom-css-input']
            ])
            ->add('popupExpires', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
                'label' => 'PopUp aktiv bis:',
                'data' => $currentTimestamp, // Set default value to current timestamp
            ])
            ->add('popupCreate', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'Custom-css-input',
                    'style' => 'display: none;' // Feld wird ausgeblendet
                ],
                'data' => $currentTimestamp,
            ])
            ->add('popupUser', TextType::class, [
                'attr' => [
                    'class' => 'Custom-css-input',
                ],
                'data' => $user ? $user->getUsername() : '',
            ])
            ->add('popupDescription', TextareaType::class, [
                'attr' => ['class' => 'Custom-css-input'],
                'label' => 'PopUp Beschreibung:',
            ])
            ->add('popupContent', TextareaType::class, [
                'attr' => ['class' => 'tinymce'], // TinyMCE wird für dieses Feld aktiviert
            ])
            ->add('popupActiveFrom', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'Custom-css-input'],
                'label' => 'PopUp aktiv ab:',
            ])
            ->add('popupCategory', ChoiceType::class, [
                'choices' => $categoryChoices, // Dropdown mit Kategorien
                'label' => 'Kategorie des Popups:',
                'attr' => ['class' => 'Custom-css-input'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PopUpManager::class,
        ]);
    }
}
