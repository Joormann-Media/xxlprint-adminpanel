<?php

namespace App\Form;

use App\Entity\HelpEntry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class HelpEntryType extends AbstractType
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
            ->add('name')
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Aktiv' => 'active',
                    'Inaktiv' => 'inactive',
                ],
                'label' => 'Status des Popups',
                'attr' => ['class' => 'Custom-css-input']
            ])
            ->add('expiresAt', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
                'label' => 'Hilfe aktiv bis:',
                'data' => $currentTimestamp, // Set default value to current timestamp
            ])
            ->add('createdAt', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'Custom-css-input',

                ],
                'data' => $currentTimestamp,
            ])
            ->add('createdBy', TextType::class, [
                'attr' => [
                    'class' => 'Custom-css-input',
                    
                ],
                'data' => $user ? $user->getUsername() : '',
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'Custom-css-input'],
                'label' => 'PopUp Beschreibung:',
            ])
            ->add('content', TextareaType::class, [
                'attr' => ['class' => 'tinymce'], // TinyMCE wird für dieses Feld aktiviert
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HelpEntry::class,
        ]);
    }
}
