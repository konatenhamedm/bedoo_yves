<?php

namespace App\Controller\Location;

use App\Entity\Employe;
use App\Entity\Locataire;
use App\Form\LocataireType;
use App\Repository\LocataireRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\BaseController;
use App\Controller\FileTrait;
use App\Entity\Contratloc;
use Omines\DataTablesBundle\Adapter\ArrayAdapter;
use Omines\DataTablesBundle\Column\MapColumn;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\Column\TwigStringColumn;
use Omines\DataTablesBundle\Exporter\DataTableExporterEvents;
use Omines\DataTablesBundle\Exporter\Event\DataTableExporterResponseEvent;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

#[Route('/ads/location/locataire')]
class LocataireController extends BaseController
{
    use FileTrait;
    const INDEX_ROOT_NAME = 'app_location_locataire_index';

    #[Route('/print', name: 'app_fiche_etat_locataire_all', methods: ['DELETE', 'GET'])]
    public function print(Request $request, LocataireRepository $locataireRepository): Response
    {




        return $this->renderPdf('location/locataire/imprimme_all.html.twig', [
            'entreprise' => $this->entreprise,
            'locataires' => $locataireRepository->findAll(),
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

    #[Route('/', name: 'app_location_locataire_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);
        if ($this->groupe == "SADM") {
            $table = $dataTableFactory->create()
                //->add('id', TextColumn::class, ['label' => 'Identifiant'])

                // ->add('InfoPiece', TextColumn::class, ['label' => 'Nationalité'])

                ->add('entreprise', TextColumn::class, ['field' => 'en.denomination', 'label' => 'Entreprise'])
                ->add('NPrenoms', TextColumn::class, ['label' => 'Nom et Prénom(s)'])
                ->add('Profession', TextColumn::class, ['label' => 'Profession'])
                ->add('Contacts', TextColumn::class, ['label' => 'Contact'])
                ->add('Email', TextColumn::class, ['label' => 'Email'])
                ->add('InfoPiece', TextColumn::class, ['label' => 'Num pièce'])
                ->createAdapter(ORMAdapter::class, [
                    'entity' => Locataire::class,
                    'query' => function (QueryBuilder $qb) {
                        $qb->select('en, l')
                            ->from(Locataire::class, 'l')
                            ->join('l.entreprise', 'en');
                        /* 
                    if ($this->groupe != "SADM") {
                        $qb->andWhere('en = :entreprise')
                            ->setParameter('entreprise', $this->entreprise);
                    } */
                    }
                ])
                ->setName('dt_app_location_locataire');
        } else {
            $table = $dataTableFactory->create()
                //->add('id', TextColumn::class, ['label' => 'Identifiant'])

                // ->add('InfoPiece', TextColumn::class, ['label' => 'Nationalité'])

                ///->add('entreprise', TextColumn::class, ['field' => 'en.denomination', 'label' => 'Entreprise'])


                ->add('idf', TextColumn::class, ['className' => 'w-1px', 'field' => 'l.id', 'label' => '', 'render' => function ($value, Locataire $context) {
                    return sprintf('<input type="checkbox" name="selectAll" id="selectAll" value="%s">', $value);
                }])

                ->add('NPrenoms', TextColumn::class, ['label' => 'Nom et Prénom(s)'])
                ->add('Profession', TextColumn::class, ['label' => 'Profession'])
                ->add('Contacts', TextColumn::class, ['label' => 'Contact'])
                ->add('Email', TextColumn::class, ['label' => 'Email'])
                ->add('InfoPiece', TextColumn::class, ['label' => 'Num pièce'])
                ->addEventListener(DataTableExporterEvents::PRE_RESPONSE, function (DataTableExporterResponseEvent $e) {
                    $response = $e->getResponse();
                    $response->deleteFileAfterSend(true);
                    $ext = $response->getFile()->getExtension();
                    $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'custom_filename.' . $ext);
                })
                ->createAdapter(ORMAdapter::class, [
                    'entity' => Locataire::class,
                    'query' => function (QueryBuilder $qb) {
                        $qb->select('en, l')
                            ->from(Locataire::class, 'l')
                            ->join('l.entreprise', 'en')
                            ->andWhere('en = :entreprise')
                            ->setParameter('entreprise', $this->entreprise);;
                    }
                ])
                ->setName('dt_app_location_locataire');
        }

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
                'imprime' => new ActionRender(function () use ($permission) {
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
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Locataire $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'edit' => [
                                    'target' => '#exampleModalSizeSm2',
                                    'url' => $this->generateUrl('app_location_locataire_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_location_locataire_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],

                                'imprime' => [
                                    'url' => $this->generateUrl('fichier_index_autre', ['id' => $value]), 'ajax' => false, 'icon' => '%icon% fa fa-download', 'attrs' => ['class' => 'btn-success', 'target' => '_blank'], 'render' => $renders['imprime']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_location_locataire_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'], 'render' => $renders['delete']
                                ]
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


        return $this->render('location/locataire/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission
        ]);
    }

    #[Route('/new', name: 'app_location_locataire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, LocataireRepository $locataireRepository, FormError $formError): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'oui'];
        $locataire = new Locataire();
        $form = $this->createForm(LocataireType::class, $locataire, [
            'method' => 'POST',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_location_locataire_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_location_locataire_index');


            if ($form->isValid()) {

                if ($this->groupe != "SADM") {
                    $locataire->setEntreprise($this->entreprise);
                }

                $locataireRepository->save($locataire, true);
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

        return $this->renderForm('location/locataire/new.html.twig', [
            'locataire' => $locataire,
            'form' => $form,
            'user_groupe' => $this->groupe
        ]);
    }

    #[Route('/{id}/show', name: 'app_location_locataire_show', methods: ['GET'])]
    public function show(Locataire $locataire): Response
    {
        return $this->render('location/locataire/show.html.twig', [
            'locataire' => $locataire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_location_locataire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Locataire $locataire, LocataireRepository $locataireRepository, FormError $formError): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'autre'];
        $form = $this->createForm(LocataireType::class, $locataire, [
            'method' => 'POST',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_location_locataire_edit', [
                'id' => $locataire->getId()
            ])
        ]);
        //  dd($locataire);
        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_location_locataire_index');


            if ($form->isValid()) {

                $locataireRepository->save($locataire, true);
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

        return $this->renderForm('location/locataire/edit.html.twig', [
            'locataire' => $locataire,
            'form' => $form,
            'use_groupe' => $this->groupe
        ]);
    }

    #[Route('/{id}/delete', name: 'app_location_locataire_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Locataire $locataire, LocataireRepository $locataireRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_location_locataire_delete',
                    [
                        'id' => $locataire->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $locataireRepository->remove($locataire, true);

            $redirect = $this->generateUrl('app_location_locataire_index');

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

        return $this->renderForm('location/locataire/delete.html.twig', [
            'locataire' => $locataire,
            'form' => $form,
        ]);
    }
}
