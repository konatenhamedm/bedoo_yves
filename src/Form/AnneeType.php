<?php

namespace App\Form;

use App\Entity\Annee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnneeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            ->add('date_debut' ,DateType::class,  [
        'attr' => ['class' => 'datepicker no-auto skip-init']
        , 'widget' => 'single_text'
        , 'format' => 'dd/MM/yyyy',
        'label'=>'Date debut'
        , 'empty_data' => date('d/m/Y')
        , 'required' => false
        , 'html5' => false
    ])
            ->add('date_fin', DateType::class,  [
                'attr' => ['class' => 'datepicker no-auto skip-init']
                , 'widget' => 'single_text'
                , 'format' => 'dd/MM/yyyy',
                'label'=>"Date fin"
                , 'empty_data' => date('d/m/Y')
                , 'required' => false
                , 'html5' => false
            ])
            /*->add('autre_info')
            ->add('etat')*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annee::class,
        ]);
    }
}
