<?php

namespace App\Form;

use App\Entity\UserRoles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class UserRolesType extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Höchste vorhandene Hierarchie holen
        $maxHierarchy = $this->entityManager->createQueryBuilder()
            ->select('MAX(r.hierarchy)')
            ->from(UserRoles::class, 'r')
            ->getQuery()
            ->getSingleScalarResult();

        $nextHierarchy = $maxHierarchy !== null ? ((int)$maxHierarchy + 1) : 1;

        $builder
            ->add('roleName')
            ->add('roleCreate', null, [
                'widget' => 'single_text',
            ])
            ->add('roleCreateBy')
            ->add('roleDescription', TextareaType::class, [
                'attr' => ['class' => 'tinymce'], // TinyMCE wird für dieses Feld aktiviert
            ])
            ->add('roleTag')
            ->add('hierarchy', IntegerType::class, [
                'data' => $nextHierarchy,
                'attr' => [
                    'readonly' => true,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserRoles::class,
        ]);
    }
}
