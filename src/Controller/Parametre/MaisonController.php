<?php

namespace App\Controller\Parametre;

use App\Entity\Maison;
use App\Form\MaisonType;
use App\Repository\MaisonRepository;
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
use App\Controller\FileTrait;
use Doctrine\ORM\QueryBuilder;

#[Route('/ads/parametre/maison')]
class MaisonController extends BaseController
{
    use FileTrait;
    const INDEX_ROOT_NAME = 'app_parametre_maison_index';


    #[Route('/print', name: 'app_fiche_etat_maison_all', methods: ['DELETE', 'GET'])]
    public function print(Request $request, MaisonRepository $maisonRepository): Response
    {




        return $this->renderPdf('parametre/maison/imprimme_all.html.twig', [
            'entreprise' => $this->entreprise,
            'maisons' => $maisonRepository->findAll(),
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

    #[Route('/', name: 'app_parametre_maison_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);


        $table = $dataTableFactory->create()
            ->add('Proprio', TextColumn::class, ['label' => 'Proprietaire', 'field' => 'p.nomPrenoms'])
            ->add('quartier', TextColumn::class, ['label' => 'Quartier', 'field' => 'q.LibQuartier'])
            ->add('contacts', TextColumn::class, ['label' => 'Contacts'])
            ->add('LibMaison', TextColumn::class, ['label' => 'Maison'])
            ->add('agent', TextColumn::class, ['label' => 'Agent de recouvrement', 'field' => 'e.NomComplet'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Maison::class,
                'query' => function (QueryBuilder $qb) {
                    $qb->select('m,p,q')
                        ->from(Maison::class, 'm')
                        ->join('m.proprio', 'p')
                        ->leftJoin('m.quartier', 'q')
                        ->join('m.IdAgent', 'a')
                        ->join('a.employe', 'e');

                    if ($this->groupe != "SADM") {
                        $qb->andWhere('p.entreprise = :entreprise')
                            ->setParameter('entreprise', $this->entreprise);
                    }
                }



            ])
            ->setName('dt_app_parametre_maison');
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
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Maison $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeSm2',

                            'actions' => [
                                'edit' => [
                                    'url' => $this->generateUrl('app_parametre_maison_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_parametre_maison_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_parametre_maison_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'], 'render' => $renders['delete']
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


        return $this->render('parametre/maison/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission
        ]);
    }

    #[Route('/new', name: 'app_parametre_maison_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MaisonRepository $maisonRepository, FormError $formError): Response
    {
        $maison = new Maison();
        $form = $this->createForm(MaisonType::class, $maison, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_maison_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_maison_index');


            if ($form->isValid()) {

                $maisonRepository->save($maison, true);
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

        return $this->renderForm('parametre/maison/new.html.twig', [
            'maison' => $maison,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_parametre_maison_show', methods: ['GET'])]
    public function show(Maison $maison): Response
    {
        return $this->render('parametre/maison/show.html.twig', [
            'maison' => $maison,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_parametre_maison_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Maison $maison, MaisonRepository $maisonRepository, FormError $formError): Response
    {

        $form = $this->createForm(MaisonType::class, $maison, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_maison_edit', [
                'id' => $maison->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_maison_index');


            if ($form->isValid()) {

                $maisonRepository->save($maison, true);
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

        return $this->renderForm('parametre/maison/edit.html.twig', [
            'maison' => $maison,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_parametre_maison_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Maison $maison, MaisonRepository $maisonRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_parametre_maison_delete',
                    [
                        'id' => $maison->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $maisonRepository->remove($maison, true);

            $redirect = $this->generateUrl('app_parametre_maison_index');

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

        return $this->renderForm('parametre/maison/delete.html.twig', [
            'maison' => $maison,
            'form' => $form,
        ]);
    }
}
