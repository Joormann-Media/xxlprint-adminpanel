<?php

namespace App\Form;

use App\Entity\UserGroups;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class UserGroupsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('GroupName', null, [
                'label' => 'Gruppe Name:',
            ])
            ->add('GroupDescription', TextareaType::class, [
                'attr' => ['class' => 'tinymce'], // TinyMCE wird für dieses Feld aktiviert
                'label' => 'Gruppen Beschreibung:',
            ])
            ->add('groupLogo', FileType::class, [
                'label' => 'Gruppen Logo:',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Bitte lade ein gültiges Bild hoch.',
                    ])
                ],
            ])
            ->add('groupMembers', null, [
                'label' => 'Gruppen Mitglieder:',
                'attr' => [
                    'class' => 'select2',
                    'multiple' => true,
                    'data-placeholder' => 'Wählen Sie Mitglieder aus',
                ],
            ])
            ->add('groupCreate', null, [
                'widget' => 'single_text',
                'label' => 'Erstellungs-Datum:',
            ])
            ->add('groupCReateBy', null, [
                'label' => 'Gruppe erstellt von:',
            ])
            ->add('baseDir', TextType::class, [
                'label' => 'Verzeichnisname:',
                'required' => false,
                'attr' => [
                    'readonly' => true,
                    
                ],
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserGroups::class,
        ]);
    }
}
