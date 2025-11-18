<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\District;
use App\Entity\GeoCoordinate;
use App\Entity\HouseNumber;
use App\Entity\OfficialAddresses;
use App\Entity\PostalCode;
use App\Entity\Street;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfficialAddressesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isValid')
            ->add('confidenceScore')
            ->add('source')
            ->add('correctedAt')
            ->add('notes')
            ->add('street', EntityType::class, [
                'class' => Street::class,
                'choice_label' => 'id',
            ])
            ->add('houseNumber', EntityType::class, [
                'class' => HouseNumber::class,
                'choice_label' => 'id',
            ])
            ->add('postalCode', EntityType::class, [
                'class' => PostalCode::class,
                'choice_label' => 'id',
            ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'id',
            ])
            ->add('district', EntityType::class, [
                'class' => District::class,
                'choice_label' => 'id',
            ])
            ->add('coordinates', EntityType::class, [
                'class' => GeoCoordinate::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OfficialAddresses::class,
        ]);
    }
}
