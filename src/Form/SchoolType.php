<?php

namespace App\Form;

use App\Entity\School;
use App\Entity\ContactPerson;
use App\Entity\OfficialAddress;
use App\Repository\OfficialAddressRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SchoolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, ['required' => false])
            ->add('street', null, ['required' => false])
            ->add('streetNo', null, ['required' => false])
            ->add('zip', null, ['required' => false])
            ->add('city', null, ['required' => false])
            ->add('phone', null, ['required' => false])
            ->add('email', null, ['required' => false])
            ->add('contactPerson', EntityType::class, [
                'class' => ContactPerson::class,
                'choice_label' => function ($person) {
                    if (method_exists($person, 'getFullName')) {
                        return $person->getFullName();
                    }
                    return trim(($person->getFirstName() ?? '') . ' ' . ($person->getLastName() ?? ''));
                },
                'placeholder' => '--- Select Contact ---',
                'required' => false,
            ])
            ->add('shorttag', null, ['required' => false])
            ->add('latitude', NumberType::class, [
                'required' => false,
                'scale' => 6,
                'html5' => true,
            ])
            ->add('longitude', NumberType::class, [
                'required' => false,
                'scale' => 6,
                'html5' => true,
            ])
            ->add('district', null, ['required' => false])
            ->add('additionalInfo', null, [
                'required' => false,
                'label' => 'Additional Info',
                'attr' => ['rows' => 4],
            ])
            // **Nur EIN Adressfeld – mit LIMIT!**
            ->add('address', EntityType::class, [
    'class' => OfficialAddress::class,
    'choice_label' => fn($a) => (string)$a,
    'required' => false,
    'query_builder' => function(OfficialAddressRepository $repo) use ($options) {
        $qb = $repo->createQueryBuilder('a')->orderBy('a.id', 'DESC')->setMaxResults(25);

        // Wenn eine aktuelle ID übergeben wurde und sie NICHT in den 25 ist, füge sie hinzu
        if (!empty($options['current_address_id'])) {
            $qb->orWhere('a.id = :currentId')->setParameter('currentId', $options['current_address_id']);
        }
        return $qb;
    },
    'placeholder' => 'Adresse suchen (Autocomplete empfohlen)',
])


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        'data_class' => School::class,
        'current_address_id' => null,
    ]);
}

}
