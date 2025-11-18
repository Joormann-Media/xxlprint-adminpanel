<?php

namespace App\Form;

use App\Entity\SystemConfig;
use App\Entity\SystemOwner;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SystemConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('timezone')
            ->add('locale')
            ->add('dateFormat')
            ->add('currency')
            ->add('systemName')
            ->add('systemLogoUrl')
            ->add('enable2FA')
            ->add('sessionTimeout')
            ->add('supportEmail')
            ->add('maintenanceMode')
            ->add('maintenanceMessage')
            ->add('systemOwner', EntityType::class, [
                'class' => SystemOwner::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SystemConfig::class,
        ]);
    }
}
