<?php

namespace App\Form;

use App\Entity\VrrBusstop;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{IntegerType, TextType, NumberType, DateType, CheckboxType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VrrBusstopType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('VERSION', IntegerType::class, ['required' => true, 'attr' => ['class' => 'form-control']])
            ->add('STOP_NR', IntegerType::class, ['required' => true, 'attr' => ['class' => 'form-control']])
            ->add('STOP_TYPE', IntegerType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
            ->add('STOP_NAME', TextType::class, ['required' => true, 'attr' => ['class' => 'form-control']])
            ->add('STOP_NAME_WO_LOCALITY', TextType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
            ->add('STOP_SHORT_NAME', TextType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
            ->add('STOP_POS_X', NumberType::class, ['required' => false, 'scale' => 6, 'attr' => ['class' => 'form-control']])
            ->add('STOP_POS_Y', NumberType::class, ['required' => false, 'scale' => 6, 'attr' => ['class' => 'form-control']])
            ->add('PLACE', TextType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
            ->add('OCC', IntegerType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
            ->add('FARE_ZONE_1_NR', IntegerType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
            ->add('FARE_ZONE_2_NR', IntegerType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
            ->add('FARE_ZONE_3_NR', IntegerType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
            ->add('FARE_ZONE_4_NR', IntegerType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
            ->add('FARE_ZONE_5_NR', IntegerType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
            ->add('FARE_ZONE_6_NR', IntegerType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
            ->add('GLOBAL_ID', TextType::class, ['required' => true, 'attr' => ['class' => 'form-control']])
            ->add('VALID_FROM', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('VALID_TO', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('PLACE_ID', TextType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
            ->add('GIS_MOT_FLAG', IntegerType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
            ->add('IS_CENTRAL_STOP', CheckboxType::class, ['required' => false, 'attr' => ['class' => 'form-check-input']])
            ->add('IS_RESPONSIBLE_STOP', CheckboxType::class, ['required' => false, 'attr' => ['class' => 'form-check-input']])
            ->add('INTERCHANGE_TYPE', IntegerType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
            ->add('INTERCHANGE_QUALITY', IntegerType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VrrBusstop::class,
        ]);
    }
}
