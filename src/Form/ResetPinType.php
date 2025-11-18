<?php
// src/Form/ResetPinType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class ResetPinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pin', PasswordType::class, [
                'label' => 'Neuer PIN',
                'constraints' => [
                    new NotBlank(['message' => 'Bitte gib einen PIN ein.']),
                    new Length([
                        'min' => 4,
                        'max' => 6,
                        'minMessage' => 'Der PIN muss mindestens {{ limit }} Ziffern lang sein.',
                        'maxMessage' => 'Der PIN darf höchstens {{ limit }} Ziffern lang sein.',
                    ]),
                ],
                'attr' => ['autocomplete' => 'new-password'],
            ]);
    }
}
