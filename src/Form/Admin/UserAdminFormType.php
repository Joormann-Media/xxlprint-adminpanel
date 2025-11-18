<?php
namespace App\Form\Admin;

use App\Entity\Permission;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Entity\UserRole;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;

class UserAdminFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Hole das UserRoleRepository direkt über den EntityManager
        $rolesRepository = $options['roles_repository']; // Updated variable name
        $roles = $rolesRepository->findAll(); // Alle Rollen aus der UserRole-Entität

        // Die Rollen als Key-Value-Paar speichern: Anzeigen wird der role_name, gespeichert wird der role_tag
        $roleChoices = [];
        foreach ($roles as $role) {
            $roleChoices[$role->getRoleName()] = $role->getRoleTag(); // role_name als Label, role_tag als Wert
        }

        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'choices' => $roleChoices,  // Alle Rollen aus der UserRole-Entität
                'multiple' => true, // Mehrfachauswahl ermöglichen
                'expanded' => true, // Checkboxen für Mehrfachauswahl
                'data' => $options['data']->getRoles(), // Aktuelle Rollen des Users vorauswählen
            ])
            ->add('password')
            ->add('isVerified', ChoiceType::class, [
                'choices' => [
                    'Ja' => true,
                    'Nein' => false,
                ],
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('username')
            ->add('regDate', null, [
                'widget' => 'single_text',
            ])
            ->add('lastlogindate', null, [
                'widget' => 'single_text',
            ])
            ->add('userpin')
            ->add('prename')
            ->add('name')
            ->add('usergroups')
            ->add('passwordChangedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('failedAttempts')
            ->add('isLocked', ChoiceType::class, [
                'choices' => [
                    'Ja' => true,
                    'Nein' => false,
                ],
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('isActive', ChoiceType::class, [
                'choices' => [
                    'Ja' => true,
                    'Nein' => false,
                ],
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('twoFactorSecret')
            ->add('isTwoFactorEnabled', ChoiceType::class, [
                'choices' => [
                    'Ja' => true,
                    'Nein' => false,
                ],
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('avatar')
            ->add('adminOverride', ChoiceType::class, [
                'choices' => [
                    'Ja' => true,
                    'Nein' => false,
                ],
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('adminOverrideId')
            ->add('userDir')
            ->add('permissions', EntityType::class, [
                'class' => Permission::class,
                'choice_label' => 'name', // Display the name of the permission
                'choice_value' => 'id',  // Store the ID of the permission
                'multiple' => true,
                'expanded' => true, // Use checkboxes for multiple selection
            ])
            ->add('profile', EntityType::class, [
                'class' => UserProfile::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'roles_repository' => null, // Updated variable name
        ]);
    }
}
