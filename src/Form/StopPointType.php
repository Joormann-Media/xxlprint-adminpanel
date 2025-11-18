<?php

namespace App\Form;

use App\Entity\StopPoint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class StopPointType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name/Description'
            ])
            ->add('street', TextType::class, [
                'label' => 'Street',
                'required' => false,
            ])
            ->add('streetNumber', TextType::class, [
                'label' => 'Street Number',
                'required' => false,
            ])
            ->add('crossingStreet', TextType::class, [
                'label' => 'Crossing Street',
                'required' => false,
            ])
            ->add('zip', TextType::class, [
                'label' => 'ZIP',
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'label' => 'City',
                'required' => false,
            ])
            ->add('latitude', null, [
                'label' => 'Latitude',
                'required' => false,
            ])
            ->add('longitude', null, [
                'label' => 'Longitude',
                'required' => false,
            ])
            ->add('type', TextType::class, [
                'label' => 'Type (address, crossing, bus_stop, bus_parking)',
                'required' => false,
            ])
            ->add('maxPersons', null, [
                'label' => 'Max. Persons',
                'required' => false,
            ])
            ->add('notes', null, [
                'label' => 'Notes',
                'required' => false,
            ])
            ->add('stopPointIcon', TextType::class, [
                'label' => 'Icon (URL, Emoji, SVG...)',
                'required' => false,
            ])
            ->add('mapViewport', TextType::class, [
    'label' => false,
    'required' => false,
    'attr' => ['hidden' => true, 'id' => 'mapViewport']
])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StopPoint::class,
        ]);
    }
}
