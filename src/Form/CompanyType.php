<?php

namespace App\Form;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('companyname', TextType::class, [
                
                'label' => 'Firmenname:',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('companyLogo', TextType::class, [
                'mapped' => true,
                'required' => false,
                'attr' => [
                    'readonly' => true,
                    'class' => 'Custom-css-input invisible-content',
                ],
                'label' => 'Firmenlogo (Pfad/URL):',
                'empty_data' => '',
            ])
            ->add('ceoprename', TextType::class, [
                
                'label' => 'Vorname Geschäftsführer*in:',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('ceoname', TextType::class, [
                
                'label' => 'Nachname Geschäftsführer*in:',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('street', TextType::class, [
                
                'label' => 'Straße:',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('streetno', TextType::class, [
                
                'label' => 'Hausnummer:',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('zipcode', TextType::class, [
                
                'label' => 'Postleitzahl:',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('city', TextType::class, [
                
                'label' => 'Stadt:',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('location', TextType::class, [
                
                'label' => 'Land/Bundesland:',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('phone', TextType::class, [
                
                'label' => 'Telefon:',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('fax', TextType::class, [
                
                'label' => 'Fax:',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('email', TextType::class, [
                
                'label' => 'E-Mail:',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('web', TextType::class, [
                
                'label' => 'Webseite:',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('taxno', TextType::class, [
                
                'label' => 'Steuernummer:',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('taxid', TextType::class, [
                
                'label' => 'USt-IdNr.:',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('personalNrMin', IntegerType::class, [
                
                'label' => 'Personalnummer (von):',
                'required' => false,
                'empty_data' => null,
            ])
            ->add('personalNrMax', IntegerType::class, [
                
                'label' => 'Personalnummer (bis):',
                'required' => false,
                'empty_data' => null,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
