<?php

namespace App\Form;

use App\Entity\ShortcodeButton;
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

class ShortcodeButtonType extends AbstractType
{
    private Security $security;
    private RouterInterface $router;

    public function __construct(Security $security, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->router = $router; // Assign the injected router
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUser = $this->security->getUser();
        $currentUsername = $currentUser ? $currentUser->getUsername() : null;
        $currentTimestamp = new \DateTime();

        $lastShortcode = $this->entityManager->getRepository(ShortCodeButton::class)->findOneBy([], ['sortOrder' => 'DESC']);
        $nextSortOrder = $lastShortcode ? $lastShortcode->getSortOrder() + 1 : 1;

        $builder
            ->add('tag')
            ->add('route', ChoiceType::class, [
                'choices' => $this->getRoutesAsChoices(), // Route-Auswahl für das Dropdown
                'multiple' => false, 
                'expanded' => false, 
                'label' => 'Route:',
                'attr' => ['class' => 'Custom-css-input'],
                'mapped' => true, 
                'required' => false,
            ])
            ->add('iconPath')
            ->add('label')
            ->add('style')
            ->add('paramList')
            ->add('isActive', ChoiceType::class, [
                'choices' => [
                    'Active' => true,
                    'Passive' => false,
                ],
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('sortOrder', HiddenType::class, [
                'data' => $nextSortOrder,
            ])
            ->add('createdBy', null, [
                'data' => $options['data']->getCreatedBy() ?? $currentUsername,
                'disabled' => true,
            ])
            ->add('createdAt', DateTimeType::class, [
                'data' => $options['data']->getCreatedAt() ?? $currentTimestamp,
                'widget' => 'single_text',
                'disabled' => true,
            ])
            ->add('updatedBy', null, [
                'data' => $currentUser ? $currentUser->getUsername() : null,
                'disabled' => true,
            ])
            ->add('updatedAt', DateTimeType::class, [
                'data' => $currentTimestamp,
                'widget' => 'single_text',
                'disabled' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShortcodeButton::class,
        ]);
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
