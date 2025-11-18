<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\MileageLog;
use App\Entity\Vehicle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MileageLogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date')
            ->add('startMile')
            ->add('endMile')
            ->add('purpose')
            ->add('route')
            ->add('startLocation')
            ->add('endLocation')
            ->add('passengers')
            ->add('gasDate')
            ->add('gasQuantity')
            ->add('refuelReceipt')
            ->add('adBlueDate')
            ->add('adBlueQuantity')
            ->add('oilDate')
            ->add('oilQuantity')
            ->add('washerFluidDate')
            ->add('washerFluidQuantity')
            ->add('notes')
            ->add('signature')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('vehicle', EntityType::class, [
                'class' => Vehicle::class,
                'choice_label' => 'id',
            ])
            ->add('driver', EntityType::class, [
                'class' => Employee::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MileageLog::class,
        ]);
    }
}
