<?php

namespace App\Form;

use App\Entity\Appartement;
use App\Entity\Locataire;
use App\Entity\Contratloc;
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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Security;

class ContratlocType extends AbstractType
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
        $builder

            /*  ->add('DateDebut', DateType::class,  [
                'attr' => ['class' => 'datepicker no-auto skip-init'], 'widget' => 'single_text', 'format' => 'dd/MM/yyyy',
                'label' => "Date debu", 'empty_data' => date('d/m/Y'), 'required' => false, 'html5' => false
            ])
            ->add('DateDebut', DateType::class,  [
                'attr' => ['class' => 'datepicker no-auto skip-init'], 'widget' => 'single_text', 'format' => 'dd/MM/yyyy',
                'label' => "Date fin", 'empty_data' => date('d/m/Y'), 'required' => false, 'html5' => false
            ])*/
            ->add('NbMoisCaution', TextType::class, [
                'empty_data' => '0',
                'attr' => ['class' => ' nb_mois_caution input-money'],

            ])

            ->add('MntCaution', TextType::class, [

                'attr' => ['class' => 'input-money input-mnt_caution input-mnt'],

            ])
            ->add('NbMoisAvance', TextType::class, [
                'attr' => ['class' => 'nbre_avance input-money input-mnt'],
                'empty_data' => '0',
            ])
            ->add('MntAvance', TextType::class, [
                'empty_data' => '0',
                'attr' => ['class' => 'input-money input-mnt_avance'],
            ])
            //->add('MntLoyer')
            ->add('AutreInfos')
            ->add(
                'ScanContrat',
                FichierType::class,
                [
                    /*  'label' => 'Fichier',*/
                    'label' => 'Lien social ou famillial',
                    'doc_options' => $options['doc_options'],
                    'required' => $options['doc_required'] ?? true,
                    'validation_groups' => $options['validation_groups'],
                ]
            )

            ->add('DateEntree', DateType::class,  [
                'attr' => [
                    'class' => 'datepicker no-auto skip-init'
                ],
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy',
                'label' => "Date d'entree",
                'empty_data' => date('d/m/Y'),
                'required' => false,
                'html5' => false
            ])
            ->add('DateProchVers', DateType::class,  [
                'attr' => [
                    'class' => 'datepicker no-auto skip-init'
                ],
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy',
                'label' => "Date du prochain versement",
                'empty_data' => date('d/m/Y'),
                'required' => false,
                'html5' => false
            ])



            /* ->add('dateremise', DateType::class, [
                "label" => "Date de remise",
                "required" => true,
                "widget" => 'single_text',
                "input_format" => 'Y-m-d',
                "by_reference" => true,
                "empty_data" => '',
                'attr' => ['class' => 'date']
            ])*/
            ->add('MntLoyerPrec', TextType::class, ['attr' => ['class' => 'input-money input-mnt']])
            ->add('MntLoyerIni', TextType::class, ['attr' => ['class' => 'input-money input-mnt']])
            ->add('MntLoyerActu', TextType::class, ['attr' => ['class' => 'input-money input-mnt']])
            ->add('MntArriere', TextType::class, ['attr' => ['class' => 'input-money input-mnt']])

            ->add(
                'DejaLocataire',
                ChoiceType::class,
                array(
                    'choices' => array(
                        'Oui' => 'oui',
                        'Non' => 'non',

                    ),
                    'choice_value' => null,
                    'multiple' => false,
                    'expanded' => true
                )
            )

            ->add(
                'StatutLoc',
                ChoiceType::class,

                array(
                    'label' => false,
                    'choices' => array(
                        'Nouveau (elle)' => 'nouveau',
                        'Ancien(ne)' => 'ancien',

                    ),

                    'choice_value' => null,
                    'multiple' => false,
                    'expanded' => true,

                ),


            )
            ->add('Fraisanex', TextType::class, ['empty_data' => '0', 'attr' => ['class' => 'input-money']])
            //  ->add('Etat')
            //->add('TotVerse')

            ->add('locataire', EntityType::class, [
                'placeholder' => '----',
                'class' => Locataire::class,
                'choice_label' => 'NPrenoms',
                'label' => 'Locataire',
                'attr' => ['class' => 'has-select2 form-select']
            ])


            ->add(
                'Regime',
                ChoiceType::class,
                [
                    'choices' => [
                        'Payé-Consommé' => 'Paye_Consomme',
                        'Consommé-Payé' => 'Consomme_Paye',

                    ],
                    // 'choice_value' => null,
                    'multiple' => false,
                    'expanded' => true,
                    'required' => true,
                    'data' => 'Paye_Consomme',
                ]
            )
            ->add(
                'Nature',
                ChoiceType::class,
                [
                    'choices'  => [
                        '....' => null,
                        'Habitation' => 'habitation',
                        'Magasin' => 'Magasin',
                    ],

                    'mapped' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'label'        => false,
                    "required" => true,
                    'attr' => ['class' => 'has-select2 changer'],
                ]
            );

        if ($options['type'] == "edit") {
            $builder->add('appart', EntityType::class, [
                'class' => Appartement::class,
                'choice_label' => 'getNomComplet',
                'placeholder' => '-----',
                'label' => 'Choix de appartement ',
                'choice_attr' => function (Appartement $appartement) {
                    return ['data-value' => $appartement->getLoyer()];
                },
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('e')
                        ->innerJoin('e.maisson', 'm')
                        ->innerJoin('m.proprio', 'p')
                        ->leftJoin('e.appartContratlocs', 'temp', 'WITH')
                        ->orWhere('temp.id = :id')
                        /* ->leftJoin('e.contratlocs', 'c') */
                        ->orWhere('e.Oqp = :etat')
                        ->andWhere('p.entreprise = :entreprise')
                        ->orWhere('e.id = :id')
                        ->setParameter('etat', 0)
                        ->setParameter('id', $options['contratId'])
                        ->setParameter('entreprise', $this->entreprise);
                },
                'attr' => ['class' => 'has-select2 form-select appart']
            ]);
        } else {
            $builder->add('appart', EntityType::class, [
                'class' => Appartement::class,
                'choice_label' => 'getNomComplet',
                'placeholder' => '-----',
                'label' => 'Choix de appartement ',
                'choice_attr' => function (Appartement $appartement) {
                    return ['data-value' => $appartement->getLoyer()];
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->innerJoin('e.maisson', 'm')
                        ->innerJoin('m.proprio', 'p')
                        ->andWhere('e.Oqp = :etat')
                        ->andWhere('p.entreprise = :entreprise')
                        ->setParameter('etat', 0)
                        ->setParameter('entreprise', $this->entreprise);
                },
                'attr' => ['class' => 'has-select2 form-select appart']
            ]);
        }

        $builder->get('MntLoyerPrec')->addModelTransformer(new ThousandNumberTransformer());

        /*   if ($builder->get('MntLoyerIni')->getData()) */
        $builder->get('MntLoyerIni')->addModelTransformer(new ThousandNumberTransformer());
        /* if ($builder->get('MntLoyerActu')->getData()) */
        $builder->get('MntLoyerActu')->addModelTransformer(new ThousandNumberTransformer());
        /* if ($builder->get('MntArriere')->getData()) */
        $builder->get('MntArriere')->addModelTransformer(new ThousandNumberTransformer());
        /* if ($builder->get('NbMoisCaution')->getData()) */
        $builder->get('NbMoisCaution')->addModelTransformer(new ThousandNumberTransformer());
        /* if ($builder->get('Fraisanex')->getData()) */
        $builder->get('Fraisanex')->addModelTransformer(new ThousandNumberTransformer());
        /* if ($builder->get('NbMoisAvance')->getData()) */
        $builder->get('NbMoisAvance')->addModelTransformer(new ThousandNumberTransformer());
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
        $resolver->setRequired('type');
        $resolver->setRequired('contratId');
    }
}
