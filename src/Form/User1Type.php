<?php

namespace App\Form;

use App\Entity\Permission;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use App\Entity\UserRoles;
use App\Entity\UserGroups;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class User1Type extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct( EntityManagerInterface $entityManager)
    {
            $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $routes = $options['routes'] ?? []; // Erhalte die übergebenen Routen
        $usergroups = $this->entityManager->getRepository(UserGroups::class)->findAll();
        $userroles = $this->entityManager->getRepository(UserRoles::class)->findAll();

        $builder
            ->add('email', null, [
                'attr' => ['class' => 'Custom-css-input'],
                'required' => false,
            ])

            ->add('password', HiddenType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('isVerified', null, [
                'attr' => ['class' => 'Custom-css-input'],
                'required' => false,
            ])
            ->add('username', null, [
                'attr' => ['class' => 'Custom-css-input'],
                'required' => false,
            ])
            ->add('regDate', null, [
                'attr' => ['class' => 'Custom-css-input'],
                'required' => false,
            ])
            ->add('lastlogindate', null, [
                'attr' => ['class' => 'Custom-css-input'],
                'required' => false,
            ])
            ->add('userpin', HiddenType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('prename', null, [
                'attr' => ['class' => 'Custom-css-input'],
                'required' => false,
            ])
            ->add('name', null, [
                'attr' => ['class' => 'Custom-css-input'],
                'required' => false,
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => $this->getRolesAsChoices(), // Alle möglichen Rollen abrufen
                'multiple' => true, // Mehrfachauswahl aktivieren
                'expanded' => false, // Checkboxen statt Dropdown
                'label' => 'Roles:',
                'data' => $options['existing_roles'] ?? [], // Hier werden die gespeicherten Rollen vorausgewählt
                'attr' => ['class' => 'Custom-css-input'],
                'mapped' => true, // Damit die Rollen mit dem User-Objekt verknüpft sind
                'required' => false,
            ])
            
            ->add('usergroups', ChoiceType::class, [
                'choices' => $this->getUserGroupsAsChoices(),
                'multiple' => true,
                'expanded' => false,
                'label' => 'User Groups:',
                'data' => $options['existing_usergroups'] ?? [], // Hier werden die gespeicherten Usergroups vorausgewählt
                'attr' => ['class' => 'Custom-css-input'],
                'mapped' => false,
                'required' => false,
            ])
            
            
            ->add('permissions', EntityType::class, [
                'class' => Permission::class,
                'choice_label' => 'name', // 'id' kann bleiben, falls du das willst
                'multiple' => true,
                'expanded' => true, // Checkboxen statt Multi-Select
                'required' => false,
            ])
            
            // ➜ NEUE FELDER HINZUGEFÜGT
            ->add('passwordChangedAt', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'Custom-css-input'],
                'label' => 'Passwort geändert am:',
                'required' => false,
            ])
            ->add('failedAttempts', IntegerType::class, [
                'attr' => ['class' => 'Custom-css-input'],
                'label' => 'Fehlgeschlagene Versuche:',
                'required' => false,
            ])
            ->add('isLocked', CheckboxType::class, [
                'required' => false,
                'attr' => ['class' => 'Custom-css-input'],
                'label' => 'Gesperrt:',
            ])
            ->add('isActive', CheckboxType::class, [
                'required' => false,
                'attr' => ['class' => 'Custom-css-input'],
                'label' => 'Aktiv:',
            ])
            ->add('twoFactorSecret', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'Custom-css-input'],
                'label' => '2FA Geheimnis:',
            ])
            ->add('isTwoFactorEnabled', CheckboxType::class, [
                'required' => false,
                'attr' => ['class' => 'Custom-css-input'],
                'label' => '2FA aktiviert:',
            ])

            ->add('avatar', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'Custom-css-input',
                    'placeholder' => 'NO Avatar ;-(' // Zeigt "NO Avatar ;-(" als Platzhalter an
                ],
                'data' => $options['existing_avatar'] ?? 'https://admin.joormann-media.de/gfx/systemgfx/profilpic_placeholder_ger.jpeg',
                'label' => 'Avatar:',
            ]);
    }
    private function getRolesAsChoices(): array
    {
        $roles = $this->entityManager->getRepository(UserRoles::class)->findBy([], ['hierarchy' => 'ASC']);
        $roleChoices = [];
    
        foreach ($roles as $role) {
            $roleChoices[$role->getRoleName()] = $role->getRoleTag(); 
            // 🔹 Anzeige: `role_name` ("System-Administrator")
            // 🔹 Gespeicherter Wert: `role_tag` ("ROLE_ADMIN")
        }
    
        // dump($roleChoices); // Debugging, falls nötig
        return $roleChoices;
    }

    private function getRoutesAsChoices(array $routes): array
    {
        $routeChoices = [];
        foreach ($routes as $mainRoute => $routeData) {
            $routeChoices[$routeData['path']] = $routeData['path']; // Add main route
            foreach ($routeData['subroutes'] as $subroute) {
                $routeChoices[$subroute['path']] = $subroute['path']; // Add subroute
            }
        }
        return $routeChoices;
    }

    private function getUserGroupsAsChoices(): array
{
    $usergroups = $this->entityManager->getRepository(UserGroups::class)->findAll();
    $usergroupChoices = [];

    foreach ($usergroups as $usergroup) {
        $usergroupChoices[$usergroup->getGroupName()] = $usergroup->getId(); // Name als Label, ID als Wert
    }

    return $usergroupChoices;
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'existing_roles' => [], // Standardwert als leeres Array
            'existing_usergroups' => [], // Standardwert als leeres Array
        
        ]);
    }
}
