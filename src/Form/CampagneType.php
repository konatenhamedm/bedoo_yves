<?php

namespace App\Form;

use App\Entity\Annee;
use App\Entity\Campagne;
use App\Entity\Tabmois;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampagneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('LibCampagne', TextType::class, [
                'attr' => ['class' => 'libelleCampagne']
            ])
            ->add('MntTotal')
            ->add('annee', EntityType::class, [
                'class' => Annee::class,
                'choice_label' => 'libelle',
                'label' => 'Année en cours ',
                'choice_attr' => function (Annee $annee) {
                    return ['data-value' => $annee->getLibelle()];
                },
                'query_builder' => function (EntityRepository $em) {
                    return $em->createQueryBuilder('e')
                        ->andWhere('e.libelle = :annee')
                        ->setParameter('annee', date_format(new \DateTime(), "Y"));
                },
                'attr' => ['class' => 'has-select2 form-select annee']
            ])
            /*   ->add('employe', EntityType::class,
            [
                'class' => Employe::class,
                'choice_label' => 'nomComplet',
                'attr' => ['class' => 'has-select2 form-select'],
                'query_builder' => function (EntityRepository $er) {
                    return $er->withoutAccount();
                }
            ]
        ) */


            ->add('mois', EntityType::class, [
                'class' => Tabmois::class,
                'choice_label' => 'libMois',
                'placeholder' => '-----',
                'label' => 'Choix du mois ',
                'choice_attr' => function (Tabmois $tabmois) {
                    return ['data-value' => $tabmois->getLibMois()];
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e');
                    /* ->leftJoin('e.mois', 'ASC')
                        ->andWhere('e.libelle = :annee')
                        ->setParameter('annee', date_format(new \DateTime(), "Y")); */
                },
                'attr' => ['class' => 'has-select2 form-select mois']
            ])
            ->add('campagneContrats', CollectionType::class, [
                'entry_type' => CampagneContratType::class,
                'entry_options' => [
                    'label' => false,
                    /*'doc_options' => $options['doc_options'],
                    'doc_required' => $options['doc_required'],
                    'validation_groups' => $options['validation_groups'],*/
                ],
                'allow_add' => true,
                'label' => false,
                'by_reference' => false,
                'allow_delete' => true,
                'prototype' => true,
            ]);
            /*->add('entreprise')*/;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Campagne::class,
        ]);
    }
}
