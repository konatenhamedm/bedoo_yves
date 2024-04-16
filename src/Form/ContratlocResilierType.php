<?php

namespace App\Form;

use App\Entity\Appartement;
use App\Entity\Locataire;
use App\Entity\Contratloc;
use App\Entity\Motif;
use App\Entity\Tabmois;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Security;

class ContratlocResilierType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('DateFin', DateType::class,  [
                'attr' => [
                    'class' => 'datepicker no-auto skip-init'
                ],
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy',
                'label' => "Date de fin",
                'empty_data' => date('d/m/Y'),
                'required' => false,
                'html5' => false
            ])
            ->add('motif', EntityType::class, [
                'class' => Motif::class,
                'choice_label' => 'LibMotif',
                'placeholder' => '-----',
                'label' => 'Choix du motif',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e');
                },
                'attr' => ['class' => 'has-select2 form-select']
            ])
            ->add('CautionRemise', TextType::class, [
                'empty_data' => '0',
                'attr' => ['class' => 'input-money'],

            ])
            ->add('details', TextareaType::class, [

                'attr' => ['class' => ' '],

            ])




            ->add(
                'FichierResiliation',
                FichierType::class,
                [
                    /*  'label' => 'Fichier',*/
                    'label' => 'Ajouter un fichier',
                    'doc_options' => $options['doc_options'],
                    'required' => $options['doc_required'] ?? true,
                    'validation_groups' => $options['validation_groups'],
                ]
            );
        $builder->get('CautionRemise')->addModelTransformer(new ThousandNumberTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {

        $resolver->setDefaults([
            'data_class' => Contratloc::class,
            'doc_required' => true,
            'doc_options' => [],
            'validation_groups' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
        $resolver->setRequired(['validation_groups']);
    }
}
