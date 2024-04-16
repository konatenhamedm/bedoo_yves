<?php

namespace App\Controller\Comptabilite;

use App\Entity\Campagne;
use App\Entity\Factureloc;
use App\Entity\Maison;
use App\Form\CampagneType;
use App\Repository\AppartementRepository;
use App\Repository\CampagneRepository;
use App\Repository\FacturelocRepository;
use App\Repository\JoursMoisEntrepriseRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\BaseController;
use App\Controller\FileTrait;
use App\Entity\CampagneContrat;
use App\Entity\VersmtProprio;
use App\Form\VersmtProprioRemiseType;
use App\Repository\CampagneContratRepository;
use App\Repository\ContratlocRepository;
use App\Repository\LocataireRepository;
use App\Repository\TabmoisRepository;
use App\Repository\VersmtProprioRepository;
use App\Service\Omines\Column\NumberFormatColumn;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Func;

#[Route('/ads/comptabilite/point')]
class PointpaiementController extends BaseController
{
    use FileTrait;
    const INDEX_ROOT_NAME_LOCATAIRE = 'app_point_locataire_index';
    const INDEX_ROOT_NAME_PROPRIETAIRE = 'app_point_proprietaire_index';






    #[Route('/locataire', name: 'app_point_locataire_index', methods: ['GET', 'POST'])]
    public function indexLocataire(Request $request, DataTableFactory $dataTableFactory): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME_LOCATAIRE);

        $table = $dataTableFactory->create()
            // ->add('id', TextColumn::class, ['label' => 'Identifiant'])
            ->add('locataire', TextColumn::class, ['field' => 'loc.NPrenoms', 'label' => 'Locataire'])
            ->add('maisson', TextColumn::class, ['field' => 'mai.LibMaison', 'label' => 'Maison',])
            ->add('mois', TextColumn::class, ['label' => 'Mois'])
            ->add('MntFact', TextColumn::class, ['field' => 'en.denomination', 'label' => 'Loyer'])
            ->add('SoldeFactLoc', TextColumn::class, ['label' => 'Montant'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Factureloc::class,
                'query' => function (QueryBuilder $qb) {
                    $qb->select('en,c,f,a,loc')
                        ->from(Factureloc::class, 'f')
                        ->innerJoin('f.compagne', 'c')
                        ->innerJoin('c.entreprise', 'en')
                        ->join('f.appartement', 'a')
                        ->join('a.maisson', 'mai')
                        ->join('f.locataire', 'loc');
                    //->andWhere('f.statut = :statut')
                    //->setParameter('statut', 'impayer')

                    if ($this->groupe != "SADM") {
                        $qb->andWhere('en = :entreprise')
                            ->setParameter('entreprise', $this->entreprise);
                    }
                }

            ])
            ->setName('dt_app_comptabilite_point_locataire');
        if ($permission != null) {

            $renders = [


                'show' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return true;
                    } elseif ($permission == 'RD') {
                        return true;
                    } elseif ($permission == 'RU') {
                        return true;
                    } elseif ($permission == 'CRUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return true;
                    }
                    return true;
                }),

            ];


            $hasActions = false;

            foreach ($renders as $_ => $cb) {
                if ($cb->execute()) {
                    $hasActions = true;
                    break;
                }
            }

            if ($hasActions) {
                $table->add('id', TextColumn::class, [
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Factureloc $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [

                                'show' => [
                                    'url' => $this->generateUrl('app_location_contratloc_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                            ]

                        ];
                        return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                    }
                ]);
            }
        }

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('comptabilite/point/locataire.html.twig', [
            'datatable' => $table,
            'permition' => $permission,

        ]);
    }

    #[Route('/{id}/print', name: 'app_fiche_etat_by_proprietaire', methods: ['DELETE', 'GET'])]
    public function print(Request $request, Factureloc $factureloc, CampagneRepository  $campagneRepository, ContratlocRepository $contratlocRepository, CampagneContratRepository $campagneContratRepository, FacturelocRepository $facturelocRepository): Response
    {
        $dataMaisons = $campagneContratRepository->getAllHomeProprio($factureloc->getCompagne()->getId(), $factureloc->getAppartement()->getMaisson()->getProprio()->getId());
        $somme = 0;

        foreach ($dataMaisons as $key => $value) {

            // dd($value->getMntCom());
            $somme += (int)$value['MntCom'];
        }

        return $this->renderPdf('comptabilite/point/imprime_point_proprietaire.html.twig', [
            'entreprise' => $this->entreprise,
            'maisons' => $dataMaisons,
            'commission' => $somme,
            'reste' => $facturelocRepository->TotalPoprioCampagne($factureloc->getAppartement()->getMaisson()->getProprio(), $factureloc->getCompagne())[0]['total'] - $somme,
            'data' => $factureloc,
            'locataires' => $facturelocRepository->getAllLocataireByProprioCampagne($factureloc->getAppartement()->getMaisson()->getProprio(), $factureloc->getCompagne()),
            'total' => $facturelocRepository->TotalPoprioCampagne($factureloc->getAppartement()->getMaisson()->getProprio(), $factureloc->getCompagne())[0]
            /* 'montant_lettre' => $lettre->Conversion($versmtProprio->getMontant()) */
            /* 'service' => $service */
        ], [
            'orientation' => 'P',
            'protected' => true,
            'showWaterkText' => true,
            'fontDir' => [
                $this->getParameter('font_dir') . '/arial',
                $this->getParameter('font_dir') . '/trebuchet',
            ],
            'watermarkImg' => "",
            'entreprise' =>  $this->entreprise->getDenomination()
        ], true);
    }

    /**
     * Fonction pour afficher la grip des payer
     *
     * @param  $request
     * @param DataTableFactory $dataTableFactory
     * @return Response
     */
    #[Route('/proprietaire', name: 'app_point_proprietaire_index', methods: ['GET', 'POST'])]
    public function indexProprietaire(Request $request, DataTableFactory $dataTableFactory, FacturelocRepository $facturelocRepository): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME_PROPRIETAIRE);

        $table = $dataTableFactory->create()
            // ->add('id', TextColumn::class, ['label' => 'Identifiant'])



            ->add('nomPrenoms', TextColumn::class, ['field' => 'pro.nomPrenoms', 'label' => 'Proprietaire'])
            ->add('maisson', TextColumn::class, ['field' => 'mai.LibMaison', 'label' => 'Maison',])
            ->add('Mois', TextColumn::class, ['label' => 'Mois', 'field' => 'c.LibCampagne'])
            ->add('MntFact', NumberFormatColumn::class, [
                'field' => 'en.denomination', 'label' => 'Total à encaisser',
                'render' => function ($value, Factureloc $context) {
                    return $this->factureRepo->getMontantDuByProprioCampagneMaison($context->getCompagne()->getId(), $context->getAppartement()->getMaisson()->getId())[0]['total'];
                }
            ])
            ->add('Encaisse', NumberFormatColumn::class, [
                'field' => 'en.denomination', 'label' => 'Total recouvré',
                'render' => function ($value, Factureloc $context) {
                    return $this->factureRepo->getMontantDuByProprioCampagneMaison($context->getCompagne()->getId(), $context->getAppartement()->getMaisson()->getId())[0]['encaisse'];
                }
            ])
            ->add('reste', NumberFormatColumn::class, [
                'field' => 'en.denomination', 'label' => 'Reste',
                'render' => function ($value, Factureloc $context) {
                    return $this->factureRepo->getMontantDuByProprioCampagneMaison($context->getCompagne()->getId(), $context->getAppartement()->getMaisson()->getId())[0]['reste'];
                }
            ])
            ->add('commission', NumberFormatColumn::class, [
                'field' => 'en.denomination', 'label' => 'Commission',
                'render' => function ($value, Factureloc $context) {
                    return $context->getAppartement()->getMaisson()->getMntCom();
                }
            ])
            ->add('remise', TextColumn::class, ['className' => 'w-1px', 'field' => 'l.id', 'label' => 'Remis', 'render' => function ($value, Factureloc $context) {

                if ($context->getRemiseProprio() == null) {
                    $label = 'Pas rémis';
                    $color = 'danger';
                } else {
                    $label = 'Rémis';
                    $color = 'success';
                }


                return sprintf('<span class="badge badge-%s">%s</span>', $color, $label);
            }])

            ->createAdapter(ORMAdapter::class, [
                'entity' => Factureloc::class,
                'query' => function (QueryBuilder $qb) {
                    $qb->select('en,mai,c,f,a,pro')
                        ->from(Factureloc::class, 'f')
                        ->innerJoin('f.compagne', 'c')
                        ->innerJoin('c.entreprise', 'en')
                        ->join('f.appartement', 'a')
                        ->join('a.maisson', 'mai')
                        ->join('mai.proprio', 'pro');
                    /* ->groupBy('pro.nomPrenoms')
                        ->addGroupBy('c.LibCampagne'); */
                    //->andWhere('f.statut = :statut')
                    //->setParameter('statut', 'impayer')

                    if ($this->groupe != "SADM") {
                        $qb->andWhere('en = :entreprise')
                            ->setParameter('entreprise', $this->entreprise);
                    }
                }

            ])
            ->setName('dt_app_comptabilite_point_proprietaire');
        if ($permission != null) {

            $renders = [

                'edit' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return false;
                    } elseif ($permission == 'RU') {
                        return true;
                    } elseif ($permission == 'CRUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return false;
                    }
                }),
                'edit_desactiver' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return false;
                    } elseif ($permission == 'RU') {
                        return true;
                    } elseif ($permission == 'CRUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return false;
                    }
                }),
                'show' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return true;
                    } elseif ($permission == 'RD') {
                        return true;
                    } elseif ($permission == 'RU') {
                        return true;
                    } elseif ($permission == 'CRUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return true;
                    }
                    return true;
                }),

            ];


            $hasActions = false;

            foreach ($renders as $_ => $cb) {
                if ($cb->execute()) {
                    $hasActions = true;
                    break;
                }
            }

            if ($hasActions) {
                $table->add('id', TextColumn::class, [
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Factureloc $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'edit' => [
                                    'url' => $this->generateUrl('app_achat_remise_argent_proprio', ['id' => $value, 'montant' => $this->factureRepo->getMontantDuByProprioCampagneMaison($context->getCompagne()->getId(), $context->getAppartement()->getMaisson()->getId())[0]['total'] - $context->getAppartement()->getMaisson()->getMntCom()]), 'ajax' => true,
                                    'target' => '#exampleModalSizeLg2', 'icon' => '%icon% bi bi-cash', 'attrs' => ['class' => 'btn-default'],  'render' => new ActionRender(fn () => $context->getRemiseProprio() == null)
                                ],
                                'print' => [
                                    'url' => $this->generateUrl('default_print_iframe', [
                                        'r' => 'app_fiche_etat_by_proprietaire',
                                        'params' => [
                                            'id' => $value,
                                        ]
                                    ]),
                                    'ajax' => true,
                                    'target' =>  '#exampleModalSizeSm2',
                                    'icon' => '%icon% bi bi-printer',
                                    'attrs' => ['class' => 'btn-main btn-stack']
                                    //, 'render' => new ActionRender(fn() => $source || $etat != 'cree')
                                ],
                                'edit_desactiver' => [
                                    'url' => $this->generateUrl('app_achat_remise_argent_proprio_voir', ['id' => $context->getVersement() ? $context->getVersement()->getId() : $value]), 'ajax' => true,
                                    'target' => '#exampleModalSizeLg2', 'icon' => '%icon% bi bi-cash', 'attrs' => ['class' => 'btn-danger', 'disabled' => 'disabled'],  'render' => new ActionRender(fn () => $context->getRemiseProprio() != null)
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_comptabilite_factureloc_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                            ]

                        ];
                        return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                    }
                ]);
            }
        }

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('comptabilite/point/proprietaire.html.twig', [
            'datatable' => $table,
            'permition' => $permission,

        ]);
    }

    private function numeroVersements()
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(VersmtProprio::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0) {
            $nb = 1;
        } else {
            $nb = $nb + 1;
        }
        return ('AGI' . '-' . str_pad($nb, 3, '0', STR_PAD_LEFT));
        // return (date("y") . '-' . 'ESP' . '-' . date("m", strtotime("now")) . '-' . str_pad($nb, 3, '0', STR_PAD_LEFT));
    }


    #[Route('/{id}new', name: 'app_achat_remise_argent_proprio_new',  methods: ['GET', 'POST'])]
    #[Route('/{id}/{montant}/new', name: 'app_achat_remise_argent_proprio', methods: ['GET', 'POST'])]
    public function new(Request $request, VersmtProprioRepository $versmtProprioRepository, $montant, FormError $formError, ?int $id, Factureloc $factureloc, FacturelocRepository $facturelocRepository, TabmoisRepository $tabmoisRepository, LocataireRepository $locataireRepository, ContratlocRepository $contratlocRepository): Response
    {


        $validationGroups = ['Default', 'FileRequired', 'non'];
        $versmtProprio = new VersmtProprio();
        $versmtProprio->setMontant($montant);
        $form = $this->createForm(VersmtProprioRemiseType::class, $versmtProprio, [
            'method' => 'POST',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_achat_remise_argent_proprio', ['id' => $id, 'montant' => $montant]),
        ]);
        $form->handleRequest($request);

        // $locataire = $locataireRepository->find($id);
        $factures = $facturelocRepository->findAllFactureLocataire($id);

        // $proprioId = $proprio;


        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $date = $form->get('dateVersement')->getData();

            $montant = (int) $form->get('montant')->getData();

            //dd($tabmoisRepository->findOneBy(array('NumMois' => (int)$date->format('m'))));


            $response = [];

            $redirect = $this->generateUrl('app_config_point_index');



            if ($form->isValid()) {

                $versmtProprio->setLibelle($factureloc->getLibFacture());
                $versmtProprio->setProprio($factureloc->getAppartement()->getMaisson()->getProprio());
                $versmtProprio->setMaison($factureloc->getAppartement()->getMaisson());
                //$versmtProprio->setLocataire($locataireRepository->find($id));
                $versmtProprio->setNumeroRecu($this->numeroVersements());
                $versmtProprioRepository->save($versmtProprio, true);
                $factureloc->setVersement($versmtProprio);
                $factureloc->setRemiseProprio(Factureloc::ETATS_STATUT_REMISE['remis']);
                $facturelocRepository->save($factureloc, true);
                $data = true;
                $message = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->renderForm('comptabilite/versmt_proprio/new_remise.html.twig', [
            'versmt_proprio' => $versmtProprio,
            'form' => $form,
            // 'id' => $id
        ]);
    }

    #[Route('/{id}/new', name: 'app_achat_remise_argent_proprio_voir', methods: ['GET', 'POST'])]
    public function voir(Request $request, VersmtProprio $versmtProprio): Response
    {


        $validationGroups = ['Default', 'FileRequired', 'non'];


        $form = $this->createForm(VersmtProprioRemiseType::class, $versmtProprio, [
            'method' => 'POST',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_achat_remise_argent_proprio_voir', ['id' => $versmtProprio->getId()]),
        ]);
        $form->handleRequest($request);



        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $date = $form->get('dateVersement')->getData();

            $montant = (int) $form->get('montant')->getData();

            //dd($tabmoisRepository->findOneBy(array('NumMois' => (int)$date->format('m'))));


            $response = [];

            $redirect = $this->generateUrl('app_config_point_index');



            if ($form->isValid()) {


                $data = true;
                $message = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = "deddd";
                $statut = 0;
                $statutCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->renderForm('comptabilite/versmt_proprio/new_remise_edit.html.twig', [
            'versmt_proprio' => $versmtProprio,
            'form' => $form,
            // 'id' => $id
        ]);
    }
}
