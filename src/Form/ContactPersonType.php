<?php

namespace App\Form;

use App\Entity\Auftraggeber;
use App\Entity\ContactPerson;
use App\Entity\School;
use App\Entity\Schoolkids;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ContactPersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('salutation')
            ->add('firstName')
            ->add('lastName')
            ->add('role')
            ->add('street')
            ->add('streetNumber')
            ->add('zip')
            ->add('city')
            ->add('phone')
            ->add('fax')
            ->add('email')
            ->add('web')
            ->add('user', EntityType::class, [
    'class' => User::class,
    'choice_label' => function(User $user) {
        return $user->getUsername() . ' (' . $user->getEmail() . ')';
    },
    'placeholder' => '--- Benutzer auswählen ---',
    'required' => false,
])
->add('school', EntityType::class, [
    'class' => School::class,
    'choice_label' => 'name',
    'required' => false,
    'placeholder' => 'Keine Schule zugeordnet',
])
->add('auftraggeber', EntityType::class, [
    'class' => Auftraggeber::class,
    'choice_label' => 'name',
    'required' => false,
    'placeholder' => 'Kein Auftraggeber zugeordnet',
])
->add('schoolkid', EntityType::class, [
    'class' => Schoolkids::class,
    'choice_label' => fn($schoolkid) => $schoolkid->getFirstName().' '.$schoolkid->getLastName(),
    'required' => false,
    'placeholder' => 'Kein Schulkind zugeordnet',
])
->add('contactType', ChoiceType::class, [
    'choices' => [
        'Privat' => 'private',
        'Geschäftlich' => 'business',
        'Notfallkontakt' => 'emergency',
        'Fahrer' => 'driver',
        'Erziehungsberechtigte*r' => 'guardian',
        'Schule' => 'school',
        'Lieferant' => 'vendor',
    ],
    'required' => false,
    'placeholder' => 'Typ auswählen...',
])



        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactPerson::class,
        ]);
    }
}
