<?php

namespace App\Controller\Comptabilite;

use App\Entity\Factureloc;
use App\Form\FacturelocType;
use App\Repository\FacturelocRepository;
use App\Service\ActionRender;
use App\Service\FormError;
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
use App\Service\Breadcrumb;
use Doctrine\ORM\EntityManagerInterface;


#[Route('/ads/comptabilite/factureloc')]
class FacturelocController extends BaseController
{
    const INDEX_ROOT_NAME = 'app_comptabilite_factureloc_index';

    #[Route('/', name: 'app_comptabilite_factureloc_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('id', TextColumn::class, ['label' => 'Identifiant'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Factureloc::class,
            ])
            ->setName('dt_app_comptabilite_factureloc');
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
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Factureloc $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'edit' => [
                                    'url' => $this->generateUrl('app_comptabilite_factureloc_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_comptabilite_factureloc_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_comptabilite_factureloc_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'], 'render' => $renders['delete']
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


        return $this->render('comptabilite/factureloc/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission
        ]);
    }

    #[Route('/new', name: 'app_comptabilite_factureloc_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FacturelocRepository $facturelocRepository, FormError $formError): Response
    {
        $factureloc = new Factureloc();
        $form = $this->createForm(FacturelocType::class, $factureloc, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilite_factureloc_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_comptabilite_factureloc_index');


            if ($form->isValid()) {

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

        return $this->renderForm('comptabilite/factureloc/new.html.twig', [
            'factureloc' => $factureloc,
            'form' => $form,
        ]);
    }



    #[Route('/{id}/show', name: 'app_comptabilite_factureloc_show', methods: ['GET'])]
    public function show(Factureloc $factureloc): Response
    {
        return $this->render('comptabilite/factureloc/show.html.twig', [
            'factureloc' => $factureloc,
        ]);
    }


    #[Route('/{id}/frais', name: 'app_comptabilite_factureloc_frais', methods: ['GET'])]
    public function frais(Factureloc $factureloc, FacturelocRepository $facturelocRepository): Response
    {
        return $this->render('comptabilite/factureloc/frais.html.twig', [
            'factureloc' => $factureloc,
            'datas' => $facturelocRepository->findAllFactureLocataireImpayer($factureloc->getLocataire()->getId())
        ]);
    }

    #[Route('/{id}/details', name: 'app_comptabilite_factureloc_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Factureloc $factureloc, FacturelocRepository $facturelocRepository, FormError $formError, Breadcrumb $breadcrumb): Response
    {

        $modules = [
            [
                'label' => 'Frais de location',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_comptabilite_factureloc_frais', ['id' => $factureloc->getId()])
            ],
            [
                'label' => 'Versements',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_comptabilite_versmt_proprio_index', ['id' => $factureloc->getLocataire()->getId()])
            ],
            /*  [
                'label' => 'Gestion utilisateur',
                'icon' => 'bi bi-users',
                'href' => $this->generateUrl('app_config_location_ls', ['module' => 'utilisateur'])
            ],*/


        ];

        $breadcrumb->addItem([
            [
                'route' => 'app_default',
                'label' => 'Tableau de bord'
            ],
            [
                'label' => 'Paramètres'
            ]
        ]);

        $form = $this->createForm(FacturelocType::class, $factureloc, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilite_factureloc_edit', [
                'id' => $factureloc->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_comptabilite_factureloc_index');


            if ($form->isValid()) {

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

        return $this->renderForm('comptabilite/factureloc/edit.html.twig', [
            'factureloc' => $factureloc,
            'form' => $form,
            'modules' => $modules,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    #[Route('/{id}/versement', name: 'app_comptabilite_factureloc_versement', methods: ['GET', 'POST'])]
    public function Versement(Request $request, Factureloc $factureloc, FacturelocRepository $facturelocRepository, FormError $formError, Breadcrumb $breadcrumb): Response
    {


        $form = $this->createForm(FacturelocType::class, $factureloc, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilite_factureloc_versement', [
                'id' => $factureloc->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_comptabilite_factureloc_index');


            if ($form->isValid()) {

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

        return $this->renderForm('comptabilite/factureloc/edit.html.twig', [
            'factureloc' => $factureloc,
            'form' => $form,

        ]);
    }

    #[Route('/{id}/delete', name: 'app_comptabilite_factureloc_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Factureloc $factureloc, FacturelocRepository $facturelocRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_comptabilite_factureloc_delete',
                    [
                        'id' => $factureloc->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $facturelocRepository->remove($factureloc, true);

            $redirect = $this->generateUrl('app_comptabilite_factureloc_index');

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

        return $this->renderForm('comptabilite/factureloc/delete.html.twig', [
            'factureloc' => $factureloc,
            'form' => $form,
        ]);
    }
}
