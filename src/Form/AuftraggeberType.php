<?php

namespace App\Form;

use App\Entity\Auftraggeber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuftraggeberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('ansprechpartner')
            ->add('strasse')
            ->add('strasseNr')
            ->add('plz')
            ->add('stadt')
            ->add('telefon')
            ->add('email')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Auftraggeber::class,
        ]);
    }
}
