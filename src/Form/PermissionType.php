<?php

namespace App\Form;

use App\Entity\Permission;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Entity\UserRoles;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface;

class PermissionType extends AbstractType
{
    private Security $security;
    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $countryChoices = array_flip(Countries::getNames());
        $user = $this->security->getUser();
        $routes = $options['routes'] ?? []; // Erhalte die übergebenen Routen
        
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'Custom-css-input'],
                'label' => 'Permission Name:',
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'tinymce'], // TinyMCE wird für dieses Feld aktiviert
                'label' => 'Permission Description:',
            ])
            ->add('createdate', DateTimeType::class, [
                'widget' => 'single_text',
                'mapped' => false, // 💡 Verhindert, dass das Feld ins Entity-Objekt geschrieben wird
                'disabled' => true, // Nutzer kann es nicht ändern
                'data' => new \DateTime(), // Standardwert: Aktuelles Datum
                'attr' => ['class' => 'Custom-css-input'],
                'label' => 'Created At:',   
            ])
            ->add('createBy', TextType::class, [
                'mapped' => false, // Prevents the field from being written to the entity object
                'disabled' => true, // User cannot change it
                'data' => $user ? $user->getUsername() : '', // Ensure the value is null-safe
                'attr' => ['class' => 'Custom-css-input'],
                'label' => 'Created By:',
            ])
            ->add('permissionRoute', ChoiceType::class, [
                'choices' => $this->getRoutesAsChoices($routes), // Include both main routes and subroutes
                'expanded' => false,
                'multiple' => false,
                'label' => 'Permission Route:',
                'attr' => ['class' => 'Custom-css-input'],
            ])
            ->add('isActive', ChoiceType::class, [
                'attr' => ['class' => 'Custom-css-input'],
                'label' => 'Permission Active:',
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
            ])
            ->add('onMobileIOS', HiddenType::class)
            ->add('onMobileAndroid', HiddenType::class)
            ->add('onOtherMobile', HiddenType::class)
            ->add('onChromeOS', HiddenType::class)
            ->add('onWindows', HiddenType::class)
            ->add('onLinux', HiddenType::class)
            ->add('onMacOS', HiddenType::class)
            ->add('allowedCountries', ChoiceType::class, [
                'attr' => ['class' => 'Custom-css-input'],
                'choices' => $countryChoices,
                'multiple' => true,
                'expanded' => false, // Dropdown
                'label' => 'Allowed Countries',
                'required' => false,
            ])
            ->add('blockedCountries', ChoiceType::class, [
                'attr' => ['class' => 'Custom-css-input'],
                'choices' => $countryChoices,
                'multiple' => true,
                'expanded' => false, // Dropdown
                'label' => 'Blocked Countries:',
                'required' => false,
            ])
            ->add('minRole', ChoiceType::class, [
                'choices' => $this->getRolesAsChoices(),
                'expanded' => false, // Standard-Dropdown statt Radiobuttons
                'multiple' => false, // Nur eine Rolle auswählbar
                'label' => 'Minimale Rolle:',
                'attr' => ['class' => 'Custom-css-input'],
            ])
            ->add('pinRequired', ChoiceType::class, [
                'attr' => ['class' => 'Custom-css-input'],
                'label' => 'Pin Required:',
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
            ]);
    }

    private function getRolesAsChoices(): array
    {
        $roles = $this->entityManager->getRepository(UserRoles::class)->findAll();
        $roleChoices = [];
        foreach ($roles as $role) {
            $roleChoices[$role->getRoleName()] = $role->getRoleName(); // Speichert den Namen als Key & Value
        }
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Permission::class,
            'routes' => [], // Standardwert für das Routen-Array
        ]);
    }
    
}
