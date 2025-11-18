<?php

namespace App\Form;

use App\Entity\LicenceClass;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class LicenceClassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('shortName')
            ->add('description')
            ->add('infoBox')
            ->add('includes', EntityType::class, [
        'class' => LicenceClass::class,
        'multiple' => true,
        'expanded' => false,
        'required' => false,
        'choice_label' => function ($lc) {
            return $lc->getShortName() . ' - ' . $lc->getDescription();
        },
        'label' => 'Schließt folgende Klassen mit ein',
    ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LicenceClass::class,
        ]);
    }
}
