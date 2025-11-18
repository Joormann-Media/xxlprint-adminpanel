<?php

namespace App\Form;

use App\Entity\NfcStickerCard;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class NfcStickerCardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('uid', null, [
                'label' => 'UID',
            ])
            ->add('type', ChoiceType::class, [
                'choices'  => [
                    'Sticker' => 'sticker',
                    'Karte' => 'karte',
                    'Sonstiges' => 'sonstiges',
                ],
                'label' => 'Typ',
            ])
            ->add('label', null, [
                'label' => 'Label',
                'required' => false,
            ])
            ->add('purpose', null, [
                'label' => 'Zweck',
                'required' => false,
            ])
            ->add('target', null, [
                'label' => 'Ziel (Fahrzeug, Objekt, etc.)',
                'required' => false,
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'Aktiv?',
                'required' => false,
            ])
            ->add('issuedAt', DateTimeType::class, [
                'label' => 'Ausgegeben am',
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('lastScanAt', DateTimeType::class, [
                'label' => 'Letzter Scan',
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('extra', TextareaType::class, [
                'label' => 'Extra (JSON, optional)',
                'required' => false,
                'attr' => [
                    'rows' => 6,
                    'placeholder' => '{"beispiel": "Daten"}'
                ],
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Kommentar',
                'required' => false,
                'attr' => ['rows' => 3],
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (?User $user) {
                    // Zeige Username, sonst E-Mail, sonst ID
                    if (!$user) return '';
                    return $user->getUsername() ?: $user->getEmail() ?: $user->getId();
                },
                'placeholder' => '— keinem User zugeordnet —',
                'required' => false,
                'label' => 'Benutzer (optional)',
            ]);
            // createdAt und updatedAt: Meist automatisch, selten im Formular gebraucht!
            // ->add('createdAt')
            // ->add('updatedAt')
        ;

        // Extra-JSON-Transform (array <-> JSON string)
        $builder->get('extra')
            ->addModelTransformer(new CallbackTransformer(
                // DB -> Form (array to JSON string)
                function ($value) {
                    return $value ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '';
                },
                // Form -> DB (JSON string to array)
                function ($value) {
                    if (empty($value)) return null;
                    $decoded = json_decode($value, true);
                    return is_array($decoded) ? $decoded : null;
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NfcStickerCard::class,
        ]);
    }
}
