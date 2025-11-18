<?php

namespace App\Form;

use App\Entity\ModuleManager;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ModuleManagerForm extends AbstractType
{
    /**
     * Gibt ein Array aller Entitätsklassen im /Entity-Verzeichnis zurück.
     */
    private function getAllEntityClassNames(): array
    {
        $entityPath = __DIR__ . '/../Entity';
        $namespace = 'App\Entity';
        $entities = [];
        foreach (scandir($entityPath) as $file) {
            if ($file === '.' || $file === '..' || !str_ends_with($file, '.php')) continue;
            $basename = pathinfo($file, PATHINFO_FILENAME);
            $class = $namespace . '\\' . $basename;
            $entities[$basename] = $class;
        }
        return $entities;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $entityChoices = $this->getAllEntityClassNames();

        $builder
            ->add('name')
            ->add('create', null, [
                'label' => 'Created At',
                'attr' => [
                    'readonly' => $options['is_edit'], // Tipp: true bei Bearbeitung übergeben
                ],
            ])
            ->add('lastUpdate', null, [
                'label' => 'Updated At',
            ])
            ->add('logId', null, [
                'label' => 'Log ID',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Enter Log ID',
                ],
            ])
            ->add('correspondingFiles', null, [
                'mapped' => false, // NICHT autom. auf Entity mappen
            ])

            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'choices' => [
                    'Active' => 'active',
                    'Locked' => 'locked',
                    'Maintenance' => 'maintenance',
                ],
                'placeholder' => 'Status wählen',
                'expanded' => false,
                'multiple' => false,
                'required' => false,
            ])
            ->add('mappedEntitys', ChoiceType::class, [
                'label' => 'Mapped Entities',
                'choices' => $entityChoices,
                'multiple' => true,
                'expanded' => false,
                'required' => false,
            ])
            ->add('readme')
            ->add('description')
            ->add('maintainer', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'multiple' => true,
                'required' => false,
            ])
            ->add('moduleID', null, [
                'label' => 'Module ID',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Enter Module ID',
                ],
            ])
            ->add('dependencies', null, [
                'label' => 'Dependencies',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Enter dependencies',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ModuleManager::class,
            'is_edit' => false,
        ]);
    }
}
