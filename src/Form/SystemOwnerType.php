<?php

namespace App\Form;

use App\Entity\OfficialAddress;
use App\Entity\SystemOwner;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use App\Form\DataTransformer\OfficialAddressToIdTransformer;

class SystemOwnerType extends AbstractType
{
    private OfficialAddressToIdTransformer $addressTransformer;
    public function __construct(OfficialAddressToIdTransformer $addressTransformer)
    {
        $this->addressTransformer = $addressTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname')
            ->add('lastname')
            ->add('email')
            ->add('phone')
            ->add('address', HiddenType::class, [
                            'required' => true,
                            'attr' => [
                                'class' => 'js-address-autocomplete',
                                'data-autocomplete-url' => '/official-address/api/address/suggest',
                            ],
                        ])
                    ;
        $builder->get('address')->addModelTransformer($this->addressTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SystemOwner::class,
            
        ]);
    }
}
