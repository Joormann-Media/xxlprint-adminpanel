<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Vehicle;
use App\Entity\VehicleDocument;
use App\Entity\DoctypeManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class VehicleDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('vehicleDoctype', EntityType::class, [
                'class' => DoctypeManager::class,
                'choice_label' => 'doctypeName',
                'label' => 'Dokumenttyp',
                'placeholder' => 'Bitte wählen ...',
                'attr' => ['class' => 'form-select'], // Bootstrap-Styling
            ])
            ->add('vehicle', EntityType::class, [
                'class' => Vehicle::class,
                'choice_label' => 'licensePlate',
                'label' => 'Fahrzeug',
                'attr' => ['class' => 'form-select'], // Bootstrap-Styling
            ])
            ->add('vehicleDocuser', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'label' => 'Hochgeladen von',
                'attr' => ['class' => 'form-select'], // Bootstrap-Styling
            ])
            ->add('vehicleDocimage', FileType::class, [
                'label' => 'Dokument/Bild (PDF, Bild, ...)',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new File([
                        'maxSize' => '8M',
                        'mimeTypes' => [
                            'application/pdf',
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Bitte ein PDF, JPG, PNG oder GIF hochladen!',
                    ])
                ],
            ])
            ->add('vehicleDocadd', null, [
                'label' => 'Hochlade-Datum',
                'disabled' => true,
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => true,
                    'style' => 'background:#f6f6fa;'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VehicleDocument::class,
        ]);
    }
}
