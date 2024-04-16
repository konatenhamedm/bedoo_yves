<?php

namespace App\Form;

use App\Entity\Locataire;
use App\Entity\Sitmatri;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocataireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('NPrenoms')


            ->add('DateNaiss', DateType::class,  [
                'attr' => ['class' => 'datepicker no-auto skip-init'], 'widget' => 'single_text', 'format' => 'dd/MM/yyyy',
                'label' => "Date de Naissance", 'empty_data' => date('d/m/Y'), 'required' => false, 'html5' => false
            ])
            ->add('numpiece', TextType::class, [
                'label' => 'Numéro pièce',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])
            ->add('LieuNaiss', TextType::class, [
                'label' => 'Lieu de Naissance',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])



            ->add(
                'InfoPiece',
                FichierType::class,
                [
                    'label' => 'Fichier',
                    'label' => 'Info pièce',
                    'doc_options' => $options['doc_options'],
                    'required' => $options['doc_required'] ?? true,
                    'validation_groups' => $options['validation_groups'],
                ]
            )

            ->add('Profession', TextType::class, [
                'label' => 'Profession',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])

            ->add('Ethnie', TextType::class, [
                'label' => 'Ethnie',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])


            ->add('NbEnfts', IntegerType::class, [
                'label' => 'Nbre enfants',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])


            ->add('NbPersChge', IntegerType::class, [
                'label' => 'Nbre Personne Charge',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])


            ->add('Pere', TextType::class, [
                'label' => 'Père',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])



            ->add('Mere', TextType::class, [
                'label' => 'Mère',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])

            ->add('Contacts', TextType::class, [
                'label' => 'Contact',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])


            ->add('Email', EmailType::class, [
                'label' => 'Email',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])


            ->add('NPConjointe', TextType::class, [
                'label' => 'NPConjointe',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])

            ->add('ProfConj', TextType::class, [
                'label' => 'Profession Conjointe',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])

            ->add('EthnieConj', TextType::class, [
                'label' => 'Ethnie Conjointe',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])


            ->add('ContactConj', TextType::class, [
                'label' => 'Contact Conjointe',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])

            ->add(
                'Genre',
                ChoiceType::class,
                [
                    'placeholder' => 'Choisir un genre',
                    'label' => 'Genre',
                    'required'     => false,
                    'expanded'     => false,
                    'attr' => ['class' => 'has-select2 form-select'],
                    'multiple' => false,
                    'choices'  => array_flip([
                        'G_MALE' => 'Masculin',
                        'G_FEMALE' => 'Feminin',
                    ]),
                ]
            )




            ->add(
                'VivezAvec',
                ChoiceType::class,
                array(
                    'choices' => array(
                        'Conjoint(e)' => 'conjoint',
                        'Colocataire' => 'colocataire',

                    ),
                    'choice_value' => null,
                    'multiple' => false,
                    'expanded' => true
                )
            )



            ->add('situationMatri', EntityType::class, [
                'placeholder' => '----',
                'class' => Sitmatri::class,
                'choice_label' => 'LibSituation',
                'label' => 'Situation matrimoniale',
                'attr' => ['class' => 'has-select2 form-select']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {



        $resolver->setDefaults([
            'data_class' => Locataire::class,
            'doc_required' => true,
            'doc_options' => [],
            'validation_groups' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
        $resolver->setRequired(['validation_groups']);
    }
}
