<?php

namespace App\Form;

use App\Entity\Factureloc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FacturelocType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /* ->add('LibFacture')
            ->add('MntFact')
            ->add('SoldeFactLoc')
            ->add('DateEmission')
            ->add('DateLimite')
            ->add('statut')
            ->add('compagne')
            ->add('mois')
            ->add('contrat')
            ->add('locataire')
            ->add('appartement') */;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Factureloc::class,
        ]);
    }
}
