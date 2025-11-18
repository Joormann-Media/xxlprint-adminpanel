<?php

namespace App\Form;

use App\Entity\Menu;
use App\Entity\UserRoles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MenuType extends AbstractType
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
        $currentTimestamp = new \DateTime();
        $user = $this->security->getUser();
        $userroles = $this->entityManager->getRepository(UserRoles::class)->findAll();
        
        $lastMenu = $this->entityManager->getRepository(Menu::class)->findOneBy([], ['sortOrder' => 'DESC']);
        $nextSortOrder = $lastMenu ? $lastMenu->getSortOrder() + 1 : 1;

        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'Custom-css-input',
                    
                ],
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Aktiv' => 'active',
                    'Inaktiv' => 'inactive',
                ],
                'label' => 'Set Status',
                'attr' => ['class' => 'Custom-css-input']
            ])
            ->add('createAt', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'Custom-css-input',
                ],
                'data' => $currentTimestamp,
                
            ])
            ->add('updateAt', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'Custom-css-input',
                ],
                'data' => $currentTimestamp,
                
            ])
            ->add('updateBy', TextType::class, [
                'attr' => [
                    'class' => 'Custom-css-input',
                    
                ],
                'data' => $user ? $user->getUsername() : '',
                
            ])
            ->add('minRole', ChoiceType::class, [
                'choices' => $this->getRolesAsChoices(),
                'multiple' => false,
                'expanded' => false,
                'label' => 'Roles:',
                'attr' => ['class' => 'Custom-css-input'],
                'mapped' => true,
                'required' => false,
            ])
            ->add('sortOrder', HiddenType::class, [
                'data' => $nextSortOrder,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
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
}
