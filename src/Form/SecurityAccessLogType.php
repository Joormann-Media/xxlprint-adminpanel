<?php

namespace App\Form;

use App\Entity\SecurityAccessLog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SecurityAccessLogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('timestamp')
            ->add('user')
            ->add('sourceIp')
            ->add('sessionType')
            ->add('status')
            ->add('details')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SecurityAccessLog::class,
        ]);
    }
}
