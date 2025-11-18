<?php

namespace App\Form;

use App\Entity\VacationManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class VacationManagerType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();
        $currentTimestamp = new \DateTime();

        $builder
        ->add('vacationStatus', ChoiceType::class, [
            'choices' => [
                'Aktiv' => 'active',
                'Inaktiv' => 'inactive',
            ],
            'label' => 'Status des Urlaubs',
            'attr' => ['class' => 'Custom-css-input']
        ])
        ->add('vacationExpires', DateTimeType::class, [
            'widget' => 'single_text',
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
            'label' => 'Urlaub aktiv bis:',
        ])
        ->add('vacationCreate', DateTimeType::class, [
            'widget' => 'single_text',
            'attr' => [
                'class' => 'Custom-css-input',
                'style' => 'display: none;' // Feld wird ausgeblendet],
            ],
            'data' => $currentTimestamp,
        ])
        ->add('vacationStart', DateTimeType::class, [
            'widget' => 'single_text',
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
            'label' => 'Urlaub beginnt am:',
        ])
        ->add('vacationUser', TextType::class, [
            'attr' => [
                'class' => 'Custom-css-input',
                
            ],
            'data' => $user ? $user->getUsername() : '',
        ])
        ->add('vacationDescription', TextAreaType::class, [
            'attr' => ['class' => 'Custom-css-input'],
            'label' => 'Urlaub Kurztext:',
        ])
            ->add('vacationContent', TextareaType::class, [
                'attr' => ['class' => 'tinymce'], // TinyMCE wird für dieses Feld aktiviert
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VacationManager::class,
        ]);
    }
}
