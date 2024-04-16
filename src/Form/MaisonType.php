<?php

namespace App\Form;

use App\Entity\Maison;
use App\Entity\Proprio;
use App\Entity\Quartier;
use App\Entity\Typemaison;
use App\Entity\Utilisateur;
use App\Entity\Ville;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class MaisonType extends AbstractType
{
    private $groupe;
    private $entreprise;
    public function __construct(Security $security)
    {
        $this->groupe = $security->getUser()->getGroupe()->getCode();
        $this->entreprise = $security->getUser()->getEmploye()->getEntreprise();
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        if ($this->groupe == 'SADM') {
            $builder
                ->add('IdAgent', EntityType::class, [
                    'class' => Utilisateur::class,
                    'choice_label' => 'NomComplet',
                    'label' => 'Agent de recrouvrement',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('e')
                            ->innerJoin('e.employe', 'em')
                            /*->andWhere('em.entreprise = :entreprise')
                            ->setParameter('user', $this->entreprise)*/;
                    },
                    'attr' => ['class' => 'has-select2 form-select']
                ])
                ->add('LibMaison')
                ->add('Localisation')
                ->add('Lot')
                ->add('Ilot')
                ->add('TFoncier')
                ->add('MntCom', TextType::class, [
                    'label' => false,
                    'attr' => ['class' => 'input-money']
                ])
                ->add('quartier', EntityType::class, [
                    'class' => Quartier::class,
                    'choice_label' => 'LibQuartier',
                    'label' => 'Quartier',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('e')
                            /*  ->innerJoin('e.employe', 'em')*/
                            /* ->innerJoin('em.entreprise', 'u')*/
                            /*  ->andWhere('e.entreprise = :entreprise') */
                            /*   ->setParameter('entreprise', $this->entreprise) */;
                    },
                    'attr' => ['class' => 'has-select2 form-select']
                ])
                ->add('proprio', EntityType::class, [
                    'class' => Proprio::class,
                    'choice_label' => 'nomPrenoms',
                    'label' => 'Choix du propriétaire ',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('e')
                            /*  ->innerJoin('e.employe', 'em')*/
                            /* ->innerJoin('em.entreprise', 'u')*/
                            ->andWhere('e.entreprise = :entreprise')
                            ->setParameter('entreprise', $this->entreprise);
                    },
                    'attr' => ['class' => 'has-select2 form-select']
                ])
                ->add('typemaison', EntityType::class, [
                    'placeholder' => '----',
                    'class' => Typemaison::class,
                    'choice_label' => 'LibType',
                    'label' => 'Type maison',
                    'attr' => ['class' => 'has-select2 form-select']
                ])
                ->add('appartements', CollectionType::class, [
                    'entry_type' => AppartementType::class,
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
        } else {
            $builder
                ->add('IdAgent', EntityType::class, [
                    'class' => Utilisateur::class,
                    'choice_label' => 'NomComplet',
                    'label' => 'Agent de recrouvrement',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('e')
                            ->innerJoin('e.employe', 'em')
                            ->innerJoin('em.fonction', 'f')
                            /* ->innerJoin('em.entreprise', 'u')*/
                            ->andWhere('em.entreprise = :entreprise')
                            ->andWhere('f.code = :code')
                            ->setParameter('code', 'AGENT')
                            ->setParameter('entreprise', $this->entreprise);
                    },
                    'attr' => ['class' => 'has-select2 form-select']
                ])
                ->add('LibMaison')
                ->add('Localisation')
                ->add('Lot')
                ->add('Ilot')
                ->add('TFoncier')
                ->add('MntCom', TextType::class, [
                    'label' => false,
                    'attr' => ['class' => 'input-money']
                ])
                ->add('quartier', EntityType::class, [
                    'placeholder' => '----',
                    'class' => Quartier::class,
                    'choice_label' => 'LibQuartier',
                    'label' => 'Quartier',
                    'attr' => ['class' => 'has-select2 form-select']
                ])
                ->add('proprio', EntityType::class, [
                    'placeholder' => '----',
                    'class' => Proprio::class,
                    'choice_label' => 'nomPrenoms',
                    'label' => 'Propriétaire',
                    'attr' => ['class' => 'has-select2 form-select']
                ])
                ->add('typemaison', EntityType::class, [
                    'placeholder' => '----',
                    'class' => Typemaison::class,
                    'choice_label' => 'LibType',
                    'label' => 'Type maison',
                    'attr' => ['class' => 'has-select2 form-select']
                ])
                ->add('appartements', CollectionType::class, [
                    'entry_type' => AppartementType::class,
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
        }
        $builder->get('MntCom')->addModelTransformer(new ThousandNumberTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Maison::class,
            /*'doc_required' => true,
            'doc_options' => [],*/

        ]);
        /*  $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');*/
    }
}
