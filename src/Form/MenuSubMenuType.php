<?php

namespace App\Form;

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

class MenuSubMenuType extends AbstractType
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
        $lastMenuSubmenu = $this->entityManager->getRepository(MenuSubmenu::class)->findOneBy([], ['sortOrder' => 'DESC']);
        $nextSortOrder = $lastMenuSubmenu ? $lastMenuSubmenu->getSortOrder() + 1 : 1;
        $currentTimestamp = new \DateTime();
        $user = $this->security->getUser();
        $userroles = $this->entityManager->getRepository(UserRoles::class)->findAll();
        $menu = $this->entityManager->getRepository(MenuSubmenu::class)->findAll();

        $builder
            ->add('name')
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
                    //'style' => 'display: none;' // Feld wird ausgeblendet],
                ],
                'data' => $currentTimestamp,
            ])
            ->add('updateAt', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'Custom-css-input',
                    //'style' => 'display: none;' // Feld wird ausgeblendet],
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
            ->add('parentId', ChoiceType::class, [
                'choices' => $this->getMenuChoices(),
                'multiple' => false,
                'expanded' => false,
                'label' => 'Parent Menu:',
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
            'data_class' => MenuSubMenu::class,
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

    private function getMenuChoices(): array
    {
        $menus = $this->entityManager->getRepository(Menu::class)->findAll();
        $menuChoices = [];

        foreach ($menus as $menu) {
            $menuChoices[$menu->getName()] = $menu->getId();
        }

        return $menuChoices;
    }
}
