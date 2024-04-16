<?php

namespace App\Form;

use App\Entity\DemandeInscription;
use App\Entity\Pays;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DemandeInscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email', 'attr' => ['placeholder' => '']

            ])
            ->add('denomination')
            // ->add('statut')
            ->add('contact')
            ->add('adresse')
            ->add('ville')
            ->add('siteWeb')
            ->add('pays', EntityType::class, [
                'class' => Pays::class,
                'mapped' => true,
                'required' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => 'libelle',
                'label' => 'Pays',
                'attr' => ['class' => 'has-select2']
            ])
            ->add('valider', SubmitType::class, ['label' => 'valider', 'attr' => ['class' => 'btn btn-warning btn-ajax btn-sm']])
            ->add('rejeter', SubmitType::class, ['label' => 'Rejeter', 'attr' => ['class' => 'btn btn-danger btn-ajax btn-sm']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // 'data_class' => DemandeInscription::class,
        ]);
    }
}
