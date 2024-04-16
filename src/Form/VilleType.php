<?php

namespace App\Form;

use App\Entity\Icon;
use App\Entity\Pays;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lib_ville')
            ->add('pays', EntityType::class, [
                'class' => Pays::class,
                'choice_label' => 'libelle',
                'label' => 'Pays',
                'attr' => ['class' => 'has-select2 form-select']
            ])
            /*->add('abrege_ville')*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ville::class,
        ]);
    }
}
