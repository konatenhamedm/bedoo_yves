<?php

namespace App\Controller\Comptabilite;

use App\Entity\VersmtProprio;
use App\Form\VersmtProprioType;
use App\Repository\VersmtProprioRepository;
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
use App\Service\Omines\Column\NumberFormatColumn;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Column\NumberColumn;

#[Route('/ads/comptabilite/versmt/proprio')]
class VersmtProprioController extends BaseController
{
    use FileTrait;
    const INDEX_ROOT_NAME = 'app_comptabilite_versmt_proprio_index';

    #[Route('/{id}', name: 'app_comptabilite_versmt_proprio_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory, $id): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('libelle', TextColumn::class, ['label' => 'Mois'])
            ->add('dateVersement', DateTimeColumn::class, ['label' => 'Date versement', 'format' => 'd-m-Y'])
            ->add('montant', NumberFormatColumn::class, ['label' => 'Montant payé'])
            ->add('type', TextColumn::class, ['label' => 'Type versement', 'field' => 't.LibType'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => VersmtProprio::class,
                'query' => function (QueryBuilder $qb) use ($id) {
                    $qb->select('f,loc')
                        ->from(VersmtProprio::class, 'f')
                        ->innerJoin('f.locataire', 'loc')
                        ->innerJoin('f.type_versement', 't')
                        ->andWhere('loc.id = :id')
                        ->setParameter('id', $id);
                }
            ])
            ->setName('dt_app_comptabilite_versmt_proprio' . $id);
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
                'print' => new ActionRender(function () use ($permission) {
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
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, VersmtProprio $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'edit' => [
                                    'url' => $this->generateUrl('app_comptabilite_versmt_proprio_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'print' => [
                                    'url' => $this->generateUrl('default_print_iframe', [
                                        'r' => 'app_achat_commande_print',
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


        return $this->render('comptabilite/versmt_proprio/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission,
            'locataire' => $id,

        ]);
    }



    #[Route('/{id}/print', name: 'app_achat_commande_print', methods: ['DELETE', 'GET'])]
    public function print(Request $request, VersmtProprioRepository $versmtProprioRepository, VersmtProprio $versmtProprio): Response
    {
        /* $lignes = $commande->getLigneCommandes();
        $service = $commande->getUtilisateur()->getService(); */
        $lettre = new \ChiffreEnLettre();

        return $this->renderPdf('comptabilite/versmt_proprio/imprime.html.twig', [
            'entreprise' => $this->entreprise,
            'versement' => $versmtProprio,
            'montant_lettre' => $lettre->Conversion($versmtProprio->getMontant())
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



    #[Route('/{id}/show', name: 'app_comptabilite_versmt_proprio_show', methods: ['GET'])]
    public function show(VersmtProprio $versmtProprio): Response
    {
        return $this->render('comptabilite/versmt_proprio/show.html.twig', [
            'versmt_proprio' => $versmtProprio,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comptabilite_versmt_proprio_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, VersmtProprio $versmtProprio, VersmtProprioRepository $versmtProprioRepository, FormError $formError): Response
    {

        $form = $this->createForm(VersmtProprioType::class, $versmtProprio, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilite_versmt_proprio_edit', [
                'id' => $versmtProprio->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_comptabilite_versmt_proprio_index');


            if ($form->isValid()) {

                $versmtProprioRepository->save($versmtProprio, true);
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

        return $this->renderForm('comptabilite/versmt_proprio/edit.html.twig', [
            'versmt_proprio' => $versmtProprio,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_comptabilite_versmt_proprio_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, VersmtProprio $versmtProprio, VersmtProprioRepository $versmtProprioRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_comptabilite_versmt_proprio_delete',
                    [
                        'id' => $versmtProprio->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $versmtProprioRepository->remove($versmtProprio, true);

            $redirect = $this->generateUrl('app_comptabilite_versmt_proprio_index');

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

        return $this->renderForm('comptabilite/versmt_proprio/delete.html.twig', [
            'versmt_proprio' => $versmtProprio,
            'form' => $form,
        ]);
    }
}
