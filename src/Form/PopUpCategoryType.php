<?php

namespace App\Form;

use App\Entity\PopUpCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PopUpCategoryType extends AbstractType
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
            ->add('categoryName')
            ->add('erstelltAm', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
                'label' => 'PopUp aktiv bis:',
                'data' => $currentTimestamp, // Set default value to current timestamp
            ])
            ->add('erstelltVon')
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'tinymce'], // TinyMCE wird für dieses Feld aktiviert
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PopUpCategory::class,
        ]);
    }
}
