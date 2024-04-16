<?php

namespace App\Form;

use App\Entity\Entreprise;
use App\Entity\Quartier;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class QuartierType extends AbstractType
{

    private $groupe;
    private $entreprise;
    public function __construct(Security $security){
        $this->groupe = $security->getUser()->getGroupe()->getCode();
        $this->entreprise = $security->getUser()->getEmploye()->getEntreprise();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if($this->groupe == "SADM") {
            $builder
                ->add('LibQuartier')
                ->add('ville',EntityType::class, [
                    'placeholder' => '----',
                    'class' => Ville::class,
                    'choice_label' => 'lib_ville',
                    'label' => 'Ville',
                    'attr' => ['class' => 'has-select2 form-select']
                ]) ->add('entreprise',EntityType::class, [
                    'placeholder' => '----',
                    'class' => Entreprise::class,
                    'choice_label' => 'denomination',
                    'label' => 'Entreprise',
                    'attr' => ['class' => 'has-select2 form-select']
                ])

            ;
        }else{
            $builder
                ->add('LibQuartier')
                ->add('ville',EntityType::class, [
                    'placeholder' => '----',
                    'class' => Ville::class,
                    'choice_label' => 'lib_ville',
                    'label' => 'Ville',
                    'attr' => ['class' => 'has-select2 form-select']
                ])

            ;
        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quartier::class,
        ]);
    }
}
