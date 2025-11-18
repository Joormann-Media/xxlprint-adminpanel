<?php

namespace App\Form;

use App\Entity\EMailSignature;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\SignatureGenerator; // ✔️ Richtiger Namespace

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EMailSignatureForm extends AbstractType
{
   private SignatureGenerator $signatureGenerator;
    private string $projectDir;

    // Autowiring der benötigten Services über den Konstruktor
    public function __construct(SignatureGenerator $signatureGenerator, ParameterBagInterface $params)
    {
        $this->signatureGenerator = $signatureGenerator;
        $this->projectDir = $params->get('kernel.project_dir');  // Get project directory
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $finder = new Finder();
        $finder->files()->in($this->projectDir.'/templates/signature')->name('*.html.twig');

        $templates = [];
        foreach ($finder as $file) {
            $name = $file->getBasename('.html.twig');
            $templates[strtoupper($name)] = $name;
        }

        $builder
            ->add('template', ChoiceType::class, [
                'choices' => $templates,
                'label' => 'Layout wählen',
                'attr' => ['class' => 'form-select sig-input']
            ])
            ->add('name')
            ->add('position')
            ->add('company')
            ->add('phone')
            ->add('mobile')
            ->add('email')
            ->add('website')
            ->add('address')
            ->add('linkedin')
            ->add('facebook')
            ->add('twitter')
            ->add('logoPath')
            ->add('bannerPath')
            ->add('disclaimer')
            ->add('htmlOutput')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EMailSignature::class,
        ]);
    }
}
