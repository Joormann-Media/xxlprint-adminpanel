<?php

namespace App\Form;

use App\Entity\SshApiKeys;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SshApiKeysForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sshapikey')
            ->add('sshapikeyDescription')
            ->add('sshapikeyExpiration')
            ->add('sshapikeyCreate')
            ->add('sshapikeyUpdate')
            ->add('sshapikeyValid')
            ->add('sshapikeyOwner', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SshApiKeys::class,
        ]);
    }
}
