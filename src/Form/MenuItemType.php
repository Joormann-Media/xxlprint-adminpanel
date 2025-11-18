<?php

namespace App\Form;

use App\Entity\MenuItem;
use App\Entity\MenuSubMenu;
use App\Entity\Menu;
use App\Entity\UserRoles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Doctrine\ORM\EntityManagerInterface; // Add this line
use Symfony\Component\Routing\RouterInterface;

class MenuItemType extends AbstractType
{
    private $security;
    private EntityManagerInterface $entityManager;
    private RouterInterface $router;

    public function __construct(Security $security, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $lastMenuSubmenu = $this->entityManager->getRepository(Menuitem::class)->findOneBy([], ['sortOrder' => 'DESC']);
        $nextSortOrder = $lastMenuSubmenu ? $lastMenuSubmenu->getSortOrder() + 1 : 1;

        $currentTimestamp = new \DateTime();
        $user = $this->security->getUser();
        $userroles = $this->entityManager->getRepository(UserRoles::class)->findAll();
        $menu = $this->entityManager->getRepository(Menu::class)->findAll();
        $subMenu = $this->entityManager->getRepository(MenuSubMenu::class)->findAll();
        $menu = $this->entityManager->getRepository(MenuItem::class)->findAll();


        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'Custom-css-input',
                    
                ],
            
            ])

            ->add('route', ChoiceType::class, [
                'choices' => $this->getRoutesAsChoices(), // Route-Auswahl für das Dropdown
                'multiple' => false, 
                'expanded' => false, 
                'label' => 'Route:',
                'attr' => ['class' => 'Custom-css-input'],
                'mapped' => true, 
                'required' => false,
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
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Aktiv' => 'active',
                    'Inaktiv' => 'inactive',
                ],
                'label' => 'Set Status',
                'attr' => ['class' => 'Custom-css-input']
            ])
            ->add('lastUpdate', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'Custom-css-input',
                ],
                'data' => new \DateTime(), // Dynamically set the current timestamp
                'empty_data' => (new \DateTime())->format('Y-m-d H:i:s'), // Ensure a default value
                
            ])
            ->add('lastUpdateBy', TextType::class, [
                'attr' => [
                    'class' => 'Custom-css-input',
                    
                ],
                'data' => $user ? $user->getUsername() : '',
                
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
            ->add('menuId', ChoiceType::class, [
                'choices' => $this->getMenuChoices(),
                'multiple' => false,
                'expanded' => false,
                'label' => 'Menu:',
                'attr' => ['class' => 'Custom-css-input'],
                'mapped' => true,
                'required' => false,
            ])
            ->add('subMenuId', ChoiceType::class, [
                'choices' => $this->getSubMenuChoices(),
                'multiple' => false,
                'expanded' => false,
                'label' => 'Sub Menu:',
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
            'data_class' => MenuItem::class,
        ]);
    }
    private function getRolesAsChoices(): array
    {
        $roles = $this->entityManager->getRepository(UserRoles::class)->findBy([], ['hierarchy' => 'ASC']); // Sort by hierarchy
        $roleChoices = [];
    
        foreach ($roles as $role) {
            $roleChoices[$role->getRoleName()] = $role->getRoleTag(); 
            // 🔹 Anzeige: `role_name` ("System-Administrator")
            // 🔹 Gespeicherter Wert: `role_tag` ("ROLE_ADMIN")
        }
    
        return $roleChoices;
    }
    private function getMenuChoices(): array
    {
        $menus = $this->entityManager->getRepository(Menu::class)->findAll();
        $menuChoices = [];

        foreach ($menus as $menu) {
            $menuChoices[$menu->getName()] = $menu->getId();
        }

        return $menuChoices;
    }
    private function getSubMenuChoices(): array
    {
        $subMenus = $this->entityManager->getRepository(MenuSubMenu::class)->findAll();
        $subMenuChoices = [];

        foreach ($subMenus as $subMenu) {
            $subMenuChoices[$subMenu->getName()] = $subMenu->getId();
        }

        return $subMenuChoices;
    }
    private function getRoutesAsChoices(): array
    {
        $routes = $this->router->getRouteCollection();
        $routeChoices = [];

        foreach ($routes as $name => $route) {
            $path = $route->getPath();
            $routeChoices[$path] = $name; // Anzeige-Name = Pfad, gespeicherter Wert = Routenname
        }

        return $routeChoices;
    }
}
