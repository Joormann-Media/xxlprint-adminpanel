<?php

namespace App\Form;

use App\Entity\Permission;
use App\Entity\User;
use App\Entity\UserRoles;
use App\Entity\UserGroups;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface; // Add this line
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{
    private $security;
    private EntityManagerInterface $entityManager;
    

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();
        $userrole = $this->security->getUser()->getRoles();
        
        
        $userroles = $this->entityManager->getRepository(UserRoles::class)->findAll();
        $usergroups = $this->entityManager->getRepository(UserGroups::class)->findAll();
        
        
        $roleChoices = [];
        foreach ($userroles as $userRole) {
        $roleChoices[$userRole->getRoleName()] = $userRole->getRoleTag();

        $usergroupChoices = [];
        foreach ($usergroups as $usergroup) {
            $usergroupChoices[$usergroup->getGroupName()] = $usergroup->getId(); // Name als Label, ID als Wert
        }
        }
        $user = $options['data'] ?? null; // Statt $this->security->getUser()
        
    

        $builder
->add('mobileVerified', ChoiceType::class, [
            'choices' => [
                'Yes' => true,
                'No' => false,
            ],
            'expanded' => false, // Change to dropdown
            'label' => 'Mobile Verified:',
             'disabled' => !$this->security->isGranted('ROLE_USERADMIN'),
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
        ])

        ->add('customerId', TextType::class, [
            'required' => false,
            'label' => 'Customer ID:',
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
            'disabled' => !$this->security->isGranted('ROLE_USERADMIN'),
            
        ])


        ->add('email', EmailType::class, [
            'required' => false,
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
            'label' => 'E-Mail Adresse:',
        ])
        ->add('username', TextType::class, [
            'required' => false,
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
            'label' => 'Benutzername:',
        ])
        ->add('prename', TextType::class, [
            'required' => false,
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
            'label' => 'Vorname:',
        ])
        ->add('name', TextType::class, [
            'required' => false,
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
            'label' => 'Nachname:',
        ])
        ->add('mobile', TextType::class, [
            'required' => false,
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
            'label' => 'Mobilnummer:',
        ])


        
        
        ->add('twoFactorSecret', TextType::class, [
            'required' => false,
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
            'label' => '2FA Secret:',
        ])
        ->add('regDate', DateTimeType::class, [
            'widget' => 'single_text',
            'required' => false,
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
            'label' => 'Registrierungsdatum:',
            'empty_data' => (new \DateTime())->format('Y-m-d\TH:i'), // Default to current date/time
        ])
        ->add('isTwoFactorEnabled', ChoiceType::class, [
            'choices' => [
                'Ja' => true,
                'Nein' => false,
                
            ],
            'expanded' => false, // Change to dropdown
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
            'label' => '2FA aktiv:',
        ])
        ->add('avatar', TextType::class, [
            'required' => false,
            'label' => 'Avatar / Bild URL',
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
            'label' => 'Avatar:',
        ])

        ->add('roles', ChoiceType::class, [
    'label' => 'Rollen',
    'choices' => $roleChoices,
    'multiple' => true,
    'expanded' => true,
    'required' => false,
    'disabled' => !$this->security->isGranted('ROLE_USERADMIN'),
    'data' => $options['data'] instanceof User ? $options['data']->getRoles() : [],
        ])
        ->add('usergroups', ChoiceType::class, [
            'label' => 'User Groups:',
            'choices' => $this->getUserGroupsAsChoices(), // Use the method to get choices
            'required' => false,
            'multiple' => true,
            'disabled' => !$this->security->isGranted('ROLE_USERADMIN'),
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
        ])
        
        ->add('password', HiddenType::class, [
            'required' => false,
            'attr' => [
                'value' => $user ? $user->getPassword() : '', // Set current user's password as value
            ],
        ])
        ->add('isVerified', ChoiceType::class, [
            'choices' => [
                'Yes' => true,
                'No' => false,
            ],
            'expanded' => false, // Change to dropdown
            'label' => 'Is Verified:',
            'disabled' => !$this->security->isGranted('ROLE_USERADMIN'),
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
            'data' => $options['data'] instanceof User ? $options['data']->isVerified() : false, // Default value
        ])
        ->add('lastlogindate', DateTimeType::class, [
            'widget' => 'single_text',
            'required' => false,
            'label' => 'Last Login Date:',
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
        ])
        ->add('userpin', HiddenType::class, [
            'required' => false,
            'attr' => [
                'value' => $user ? $user->getUserPin() : '', // Set current user's PIN as value
            ],
        ])

        ->add('passwordChangedAt', DateTimeType::class, [
            'widget' => 'single_text',
            'required' => false,
            'label' => 'Password Changed At:',
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
        ])
        ->add('failedAttempts', TextType::class, [
            'required' => false,
            'label' => 'Failed Attempts:',
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
        ])
        ->add('isLocked', ChoiceType::class, [
            'choices' => [
                'Yes' => true,
                'No' => false,
            ],
            'expanded' => false, // Change to dropdown
            'label' => 'Is Locked:',
            'disabled' => !$this->security->isGranted('ROLE_USERADMIN'),
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
        ])
        ->add('isActive', ChoiceType::class, [
            'choices' => [
                'Yes' => true,
                'No' => false,
            ],
            'expanded' => false, // Change to dropdown
            'label' => 'Is Active:',
            'disabled' => !$this->security->isGranted('ROLE_USERADMIN'),
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
        ])
        ->add('adminOverride', ChoiceType::class, [
            'choices' => [
                'Yes' => true,
                'No' => false,
            ],
            'expanded' => false, // Change to dropdown
            'label' => 'Admin Override:',
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
        ])
        ->add('adminOverrideId', TextType::class, [
            'required' => false,
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
            'label' => 'Admin Override ID:',
        ])
        ->add('userDir', TextType::class, [
            'required' => false,
            'label' => 'User Directory:',
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
        ])
        ->add('maxDevice', TextType::class, [
            'required' => false,
            'label' => 'Max Devices:',
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
            'disabled' => !$this->security->isGranted('ROLE_USERADMIN'),
        ])
        ->add('permissions', EntityType::class, [
            'class' => Permission::class,
            'choice_label' => 'name', // 'id' kann bleiben, falls du das willst
            'multiple' => true,
            'expanded' => true, // Checkboxen statt Multi-Select
            'required' => false,
            'attr' => ['class' => 'Custom-css-input'], // Beispiel für Styling
            'label' => 'Permissions:',]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
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
}
