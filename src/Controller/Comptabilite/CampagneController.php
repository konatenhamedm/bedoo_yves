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
use App\Entity\TypeVersements;
use App\Entity\VersmtProprio;
use App\Repository\ContratlocRepository;
use App\Repository\LocataireRepository;
use App\Repository\MaisonRepository;
use App\Repository\TabmoisRepository;
use App\Repository\TypeVersementsRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\VersmtProprioRepository;
use App\Service\Utils;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Length;

#[Route('/ads/comptabilite/campagne')]
class CampagneController extends BaseController
{
    use FileTrait;

    const INDEX_ROOT_NAME = 'app_comptabilite_campagne_index';

    #[Route('/print', name: 'app_fiche_etat_campagne_all', methods: ['DELETE', 'GET'])]
    public function printAll(Request $request, CampagneRepository  $campagneRepository, ContratlocRepository $contratlocRepository): Response
    {


        return $this->renderPdf('comptabilite/campagne/imprime_recouvrement_several_campagne.html.twig', [
            'entreprise' => $this->entreprise,
            'campagnes' => $campagneRepository->findAll(),
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

    #[Route('/{id}/print', name: 'app_fiche_etat_campagne', methods: ['DELETE', 'GET'])]
    public function print(Request $request, Campagne $campagne, FacturelocRepository $facturelocRepository, MaisonRepository $maisonRepository, UtilisateurRepository $utilisateurRepository): Response
    {

        $sommeCommission = 0;

        $factures = $facturelocRepository->findBy(array('compagne' => $campagne));
        $facturesSommeCommission = $facturelocRepository->findBy(array('compagne' => $campagne));
        //dd($facturesSommeCommission);
        $allMaisons = [];
        $allMaisonsSommeCommission = [];
        $allAgents = [];
        $i = 0;
        $j = 0;
        foreach ($factures as $key => $value) {


            $allMaisons[$i]['id'] = $value->getAppartement()->getMaisson()->getId();

            array_push($allAgents, $value->getAppartement()->getMaisson()->getIdAgent()->getId());
            $i++;
        }

        foreach ($facturesSommeCommission as $key => $value) {

            /*   if ($value->getStatut() == 'payer') { */
            $allMaisonsSommeCommission[] = $value->getAppartement()->getMaisson()->getId();

            array_push($allAgents, $value->getAppartement()->getMaisson()->getIdAgent()->getId());
            /* } */

            //$allMaisons[$i]['maison'] = $value->getAppartement()->getMaisson()->getLibMaison();
            //$sommeCommission += $value->getAppartement()->getMaisson()->getMntCom();

        }



        //dd(array_unique($allMaisons, SORT_REGULAR));
        for ($i = 0; $i < count(array_unique($allMaisonsSommeCommission, SORT_REGULAR)); $i++) {
            $sommeCommission += $maisonRepository->find($allMaisonsSommeCommission[$i])->getMntCom();
        }
        //  dd($sommeCommission);

        // dd($facturelocRepository->findAllFactureCampagne($campagne->getId())[0]['somme']);
        $somme =  $campagne->getMntTotal() - $facturelocRepository->findAllFactureCampagne($campagne->getId());
        return $this->renderPdf('comptabilite/campagne/imprime_recouvrement_campagne.html.twig', [
            'entreprise' => $this->entreprise,
            'campagne' => $campagne,
            'montant_encaisse' => $somme,
            'reste_encaisse' => $facturelocRepository->findAllFactureCampagne($campagne->getId()),
            'commission' =>  $sommeCommission,
            'agents' =>  $utilisateurRepository->getAllAgents(array_unique($allAgents, SORT_REGULAR)),
            /* 'versement' => $versmtProprio,
            'montant_lettre' => $lettre->Conversion($versmtProprio->getMontant()) */
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
    #[Route('/', name: 'app_comptabilite_campagne_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('idf', TextColumn::class, ['className' => 'w-1px', 'field' => 'l.id', 'label' => '', 'render' => function ($value, Campagne $context) {
                return sprintf('<input type="checkbox" name="selectAll" id="selectAll" value="%s">', $value);
            }])
            ->add('LibCampagne', TextColumn::class, ['label' => 'Campagne'])
            ->add('NbreProprio', NumberColumn::class, ['label' => 'Nbre Proprio'])
            ->add('NbreLocataire', NumberColumn::class, ['label' => 'Nbre locataire'])
            ->add('MntTotal', NumberColumn::class, ['label' => 'Total'])
            ->add('MontantRestant', TextColumn::class, ['label' => 'Reste à recouvrer ', 'className' => 'text-end w-50px', 'render' => function ($value, $context) {
                return Utils::formatNumber($context->getMontantRestant());
            }])
            //->add('getMontantRestant', NumberColumn::class, ['label' => 'Reste à recouvrer'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Campagne::class,
                'query' => function (QueryBuilder $qb) {
                    $qb->select('m,e')
                        ->from(Campagne::class, 'm')
                        ->join('m.entreprise', 'e');


                    if ($this->groupe != "SADM") {
                        $qb->andWhere('e = :entreprise')
                            ->setParameter('entreprise', $this->entreprise);
                    }
                }
            ])
            ->setName('dt_app_comptabilite_campagne');
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
                'delete' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return true;
                    } elseif ($permission == 'RU') {
                        return false;
                    } elseif ($permission == 'CRUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return false;
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
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Campagne $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'edit' => [
                                    'target' => '#exampleModalSizeSm2',
                                    'url' => $this->generateUrl('app_comptabilite_campagne_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'print' => [
                                    'url' => $this->generateUrl('default_print_iframe', [
                                        'r' => 'app_fiche_etat_campagne',
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
                                'show' => [
                                    'url' => $this->generateUrl('app_comptabilite_campagne_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                /* 'delete' => [
                                    'target' => '#exampleModalSizeSm2',
                                    'url' => $this->generateUrl('app_comptabilite_campagne_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'], 'render' => $renders['delete']
                                ]*/
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


        return $this->render('comptabilite/campagne/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission
        ]);
    }


    #[Route('/impayer', name: 'app_gestion_loyer_impayer_index', methods: ['GET', 'POST'])]
    public function indeximpayer(Request $request, DataTableFactory $dataTableFactory): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            // ->add('id', TextColumn::class, ['label' => 'Identifiant'])
            ->add('LibFacture', TextColumn::class, ['label' => 'Loyer'])
            ->add('locataire', TextColumn::class, ['field' => 'loc.NPrenoms', 'label' => 'Locataire'])
            ->add('appartement', TextColumn::class, ['field' => 'a.LibAppart', 'label' => 'Appartement',])
            ->add('MntFact', TextColumn::class, ['label' => 'Montant'])
            ->add('SoldeFactLoc', TextColumn::class, ['label' => 'Reste à payer'])
            ->add('DateLimite', DateTimeColumn::class, ['label' => 'Date limite', 'format' => 'd/m/Y'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Factureloc::class,
                'query' => function (QueryBuilder $qb) {
                    $qb->select('en,c,f,a,loc')
                        ->from(Factureloc::class, 'f')
                        ->innerJoin('f.compagne', 'c')
                        ->innerJoin('c.entreprise', 'en')
                        ->join('f.appartement', 'a')
                        ->join('f.locataire', 'loc')
                        ->andWhere('f.statut = :statut')
                        ->setParameter('statut', 'impayer');

                    if ($this->groupe != "SADM") {
                        $qb->andWhere('en = :entreprise')
                            ->setParameter('entreprise', $this->entreprise);
                    }
                }

            ])
            ->setName('dt_app_comptabilite_loyer_impayer');
        if ($permission != null) {

            $renders = [


                'payer' => new ActionRender(function () use ($permission) {
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

                                'payer' => [
                                    'url' => $this->generateUrl('app_comptabilite_factureloc_edit', ['id' => $value]), 'ajax' => false, 'icon' => '%icon% bi bi-cash', 'attrs' => ['class' => 'btn-warning'], 'render' => $renders['payer']
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


        return $this->render('comptabilite/campagne/impayer.html.twig', [
            'datatable' => $table,
            'permition' => $permission,

        ]);
    }

    /**
     * Fonction pour afficher la grip des payer
     *
     * @param  $request
     * @param DataTableFactory $dataTableFactory
     * @return Response
     */
    #[Route('/payer', name: 'app_gestion_loyer_payer_index', methods: ['GET', 'POST'])]
    public function indexpayer(Request $request, DataTableFactory $dataTableFactory): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            // ->add('id', TextColumn::class, ['label' => 'Identifiant'])
            ->add('LibFacture', TextColumn::class, ['label' => 'Loyer'])
            ->add('locataire', TextColumn::class, ['field' => 'loc.NPrenoms', 'label' => 'Locataire'])
            ->add('appartement', TextColumn::class, ['field' => 'a.LibAppart', 'label' => 'Appartement',])
            ->add('MntFact', TextColumn::class, ['label' => 'Montant'])
            ->add('DateLimite', DateTimeColumn::class, ['label' => 'Date limite', 'format' => 'd/m/Y'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Factureloc::class,
                'query' => function (QueryBuilder $qb) {
                    $qb->select('en,c,f,a,loc')
                        ->from(Factureloc::class, 'f')
                        ->innerJoin('f.compagne', 'c')
                        ->innerJoin('c.entreprise', 'en')
                        ->join('f.appartement', 'a')
                        ->join('f.locataire', 'loc')
                        ->andWhere('f.statut = :statut')
                        ->setParameter('statut', 'payer');

                    if ($this->groupe != "SADM") {
                        $qb->andWhere('en = :entreprise')
                            ->setParameter('entreprise', $this->entreprise);
                    }
                }

            ])
            ->setName('dt_app_comptabilite_loyer_payer');
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


        return $this->render('comptabilite/campagne/payer.html.twig', [
            'datatable' => $table,
            'permition' => $permission,

        ]);
    }


    #[Route(path: '/getdateLimite/{mois}/{annee}', name: 'date_limite', methods: ['GET'])]
    public function getdateLimite(JoursMoisEntrepriseRepository $joursMoisEntrepriseRepository, $mois, $annee): Response
    {

        $dateActuelle = new \DateTime(date(DATE_ATOM, mktime(0, 0, 0,  intval($mois), 1, intval($annee))));

        $dateMoisSuivant = $dateActuelle->add(new \DateInterval('P1M'));

        $dateMoisSuivant->setDate($dateMoisSuivant->format('Y'), $dateMoisSuivant->format('m'), $joursMoisEntrepriseRepository->getJour($this->entreprise) ? intval($joursMoisEntrepriseRepository->getJour($this->entreprise)['libelle']) : 5);
        //dd($dateActuelle);
        return $this->json(
            date_format($dateActuelle, "d/m/Y")
        );
    }

    private function numeroVersement()
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
        return (date("y") . '-' . 'ESP' . '-' . date("m", strtotime("now")) . '-' . str_pad($nb, 3, '0', STR_PAD_LEFT));
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

    public function logique($facture, $data, $contrat, $form, $appart, $isTrue = false)
    {

        $facture->setStatut(Factureloc::ETATS_STATUT['payer']);
        $facture->setEncaisse(Factureloc::ETATS['oui']);
        $facture->setSoldeFactLoc(0);

        if ($isTrue) {
            $solde = $contrat->getMntAvance() - $data->getLoyer();
            $contrat->setMntAvance($solde);
        }


        $versement = new VersmtProprio();
        $versement->setProprio($appart->getMaisson()->getProprio());
        $versement->setMaison($appart->getMaisson());
        $versement->setTypeVersement($this->typeVersementsRepository->findOneBy(array('CodTyp' => 'ESP')));
        $versement->setDateVersement(new \DateTime());
        $versement->setLibelle($form->get('LibCampagne')->getData());
        $versement->setMontant($data->getLoyer());
        $versement->setNumero($this->numeroVersement());
        $versement->setLocataire($contrat->getLocataire());
        $versement->setNumeroRecu($this->numeroVersements());
        $this->versementRepo->save($versement, true);
        $facture->setVersement($versement);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/new', name: 'app_comptabilite_campagne_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        CampagneRepository $campagneRepository,
        JoursMoisEntrepriseRepository $joursMoisEntrepriseRepository,
        FormError $formError,
        ContratlocRepository $contratlocRepository,
        AppartementRepository $appartementRepository,
        FacturelocRepository $facturelocRepository,
        TypeVersementsRepository $typeVersementsRepository,
        VersmtProprioRepository $versmtProprioRepository,
        LocataireRepository $locataireRepository,
        TabmoisRepository $tabmoisRepository
    ): Response {
        $campagne = new Campagne();
        // $campagne->setMois($tabmoisRepository->find(1));
        //dd();
        $somme = 0;
        $dateActuelle = new \DateTime();
        $dateMoisSuivant = $dateActuelle->add(new \DateInterval('P1M'));

        $dateMoisSuivant->setDate($dateMoisSuivant->format('Y'), $dateMoisSuivant->format('m'), $joursMoisEntrepriseRepository->getJour($this->entreprise) ? intval($joursMoisEntrepriseRepository->getJour($this->entreprise)['libelle']) : 5);

        $tableau_locataire = [];
        $tableau_proprio = [];

        if ($contratlocRepository->getContratLocActif($this->entreprise)) {
            foreach ($contratlocRepository->getContratLocActif($this->entreprise) as $contratloc) {
                $campagneContrat = new CampagneContrat();
                $campagneContrat->setLoyer($contratloc->getAppart()->getLoyer());
                $campagneContrat->setProprietaire($contratloc->getAppart()->getMaisson()->getProprio());
                $campagneContrat->setMaison($contratloc->getAppart()->getMaisson());
                $campagneContrat->setNumAppartement($contratloc->getAppart());
                $campagneContrat->setLocataire($contratloc->getLocataire());
                $campagneContrat->setDateLimite($dateMoisSuivant);
                $campagne->AddCampagneContrat($campagneContrat);

                $somme += $contratloc->getAppart()->getLoyer();

                array_push($tableau_locataire, $contratloc->getLocataire()->getId());
                array_push($tableau_proprio, $contratloc->getAppart()->getMaisson()->getProprio()->getId());
            }
        }
        $campagne->setMntTotal($somme);
        $campagne->setEntreprise($this->entreprise);
        // dd(array_unique($tableau_locataire));
        $form = $this->createForm(CampagneType::class, $campagne, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilite_campagne_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_comptabilite_campagne_index');

            if ($form->isValid()) {

                $campagne->setNbreProprio(count(array_unique($tableau_proprio)));

                $campagne->setNbreLocataire(count(array_unique($tableau_locataire)));
                $campagneRepository->save($campagne, true);

                if ($form->get('campagneContrats')->getData()) {
                    $solde = 0;
                    $soldeRestant = 0;
                    foreach ($form->get('campagneContrats')->getData() as $data) {
                        // dd($data->getNumAppartement());
                        $facture = new Factureloc();

                        $contrat = $contratlocRepository->findOneBy(array('appart' => $data->getNumAppartement()));
                        $locataire = $locataireRepository->find($contrat->getLocataire()->getId());

                        if ($contrat->getMntAvance() > 0) {

                            if ($contrat->getMntAvance() >= $data->getLoyer()) {
                                $this->logique($facture, $data, $contrat, $form, $data->getNumAppartement(), true);
                            } else {

                                if ($locataire->getSoldeRestant() >= $data->getLoyer()) {

                                    $this->logique($facture, $data, $contrat, $form, $data->getNumAppartement(), false);
                                    $locataire->setSoldeRestant($locataire->getSoldeRestant() - $data->getLoyer());
                                    $locataireRepository->save($locataire, true);
                                } else {
                                    //$solde = $data->getLoyer() - $contrat->getMntAvance();
                                    $facture->setStatut('impayer');
                                    $facture->setEncaisse(Factureloc::ETATS['non']);
                                    $facture->setSoldeFactLoc($data->getLoyer());
                                    $contrat->setMntAvance(0);
                                }
                            }

                            $contratlocRepository->save($contrat, true);
                        } elseif ($contrat->getMntAvance() == 0) {
                            if ($locataire->getSoldeRestant() >= $data->getLoyer()) {

                                $this->logique($facture, $data, $contrat, $form, $data->getNumAppartement(), false);
                                $locataire->setSoldeRestant($locataire->getSoldeRestant() - $data->getLoyer());
                                $locataireRepository->save($locataire, true);
                                $contratlocRepository->save($contrat, true);
                            } else {
                                $facture->setSoldeFactLoc($data->getLoyer());
                                $facture->setStatut(Factureloc::ETATS_STATUT['impayer']);
                                $facture->setEncaisse(Factureloc::ETATS['non']);
                            }
                        }

                        // $form->getData()->getMntAvance()

                        $facture->setLocataire($contrat->getLocataire());
                        $facture->setAppartement($data->getNumAppartement());
                        $facture->setLibFacture($form->get('LibCampagne')->getData());
                        $facture->setCompagne($campagne);
                        $facture->setMntFact($data->getLoyer());
                        $facture->setContrat($contrat);
                        $facture->setDateLimite($data->getDateLimite());
                        $facture->setDateEmission(new \DateTime());
                        $facture->setMois($form->get('mois')->getData());


                        $facturelocRepository->save($facture, true);
                    }
                }

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

        return $this->renderForm('comptabilite/campagne/new.html.twig', [
            'campagne' => $campagne,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_comptabilite_campagne_show', methods: ['GET'])]
    public function show(Campagne $campagne): Response
    {
        return $this->render('comptabilite/campagne/show.html.twig', [
            'campagne' => $campagne,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comptabilite_campagne_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Campagne $campagne, CampagneRepository $campagneRepository, FormError $formError): Response
    {

        $form = $this->createForm(CampagneType::class, $campagne, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilite_campagne_edit', [
                'id' => $campagne->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_comptabilite_campagne_index');


            if ($form->isValid()) {

                $campagneRepository->save($campagne, true);
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

        return $this->renderForm('comptabilite/campagne/edit.html.twig', [
            'campagne' => $campagne,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_comptabilite_campagne_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Campagne $campagne, CampagneRepository $campagneRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_comptabilite_campagne_delete',
                    [
                        'id' => $campagne->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $campagneRepository->remove($campagne, true);

            $redirect = $this->generateUrl('app_comptabilite_campagne_index');

            $message = 'Opération effectuée avec succès';

            $response = [
                'statut' => 1,
                'message' => $message,
                'redirect' => $redirect,
                'data' => $data
            ];

            $this->addFlash('success', $message);

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($redirect);
            } else {
                return $this->json($response);
            }
        }

        return $this->renderForm('comptabilite/campagne/delete.html.twig', [
            'campagne' => $campagne,
            'form' => $form,
        ]);
    }
}
