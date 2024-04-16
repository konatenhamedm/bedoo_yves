<?php

namespace App\Controller\Parametre;

use App\Entity\JoursMoisEntreprise;
use App\Form\JoursMoisEntrepriseType;
use App\Repository\JoursMoisEntrepriseRepository;
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
use Doctrine\ORM\QueryBuilder;
use App\Controller\BaseController;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;

#[Route('/ads/parametre/jours/mois/entreprise')]
class JoursMoisEntrepriseController extends BaseController
{
    const INDEX_ROOT_NAME = 'app_parametre_jours_mois_entreprise_index';

    #[Route('/', name: 'app_parametre_jours_mois_entreprise_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('dateDebut', DateTimeColumn::class, ['label' => 'Date debut', "format" => 'd-m-Y'])
            //->add('dateFin', DateTimeColumn::class, ['label' => 'Date fin'])
            ->add('joursMois', TextColumn::class, ['field' => 'j.libelle', 'label' => 'Jour campagne'])
            ->add('entreprise', TextColumn::class, ['field' => 'en.denomination', 'label' => 'Entreprise'])
            ->add('remise', TextColumn::class, ['className' => 'w-1px', 'field' => 'l.id', 'label' => 'Etat', 'render' => function ($value, JoursMoisEntreprise $context) {

                if ($context->getDateFin() == null) {
                    $label = 'En cours';
                    $color = 'success';
                } else {
                    $label = 'Pas actif';
                    $color = 'danger';
                }


                return sprintf('<span class="badge badge-%s">%s</span>', $color, $label);
            }])
            ->createAdapter(ORMAdapter::class, [
                'entity' => JoursMoisEntreprise::class,
                'query' => function (QueryBuilder $qb) {
                    $qb->select('l')
                        ->from(JoursMoisEntreprise::class, 'l')
                        ->join('l.joursMois', 'j')
                        ->leftJoin('l.entreprise', 'en');

                    if ($this->groupe != "SADM") {
                        $qb->andWhere('en = :entreprise')
                            ->setParameter('entreprise', $this->entreprise);
                    }
                }
            ])

            ->setName('dt_app_parametre_jours_mois_entreprise');
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
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, JoursMoisEntreprise $context) use ($renders) {
                        $options = [

                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeNormal',

                            'actions' => [
                                'edit' => [
                                    'url' => $this->generateUrl('app_parametre_jours_mois_entreprise_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_parametre_jours_mois_entreprise_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_parametre_jours_mois_entreprise_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'], 'render' => $renders['delete']
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


        return $this->render('parametre/jours_mois_entreprise/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission
        ]);
    }

    #[Route('/new', name: 'app_parametre_jours_mois_entreprise_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError, JoursMoisEntrepriseRepository $jourRepo): Response
    {
        $joursMoisEntreprise = new JoursMoisEntreprise();

        $form = $this->createForm(JoursMoisEntrepriseType::class, $joursMoisEntreprise, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_jours_mois_entreprise_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_jours_mois_entreprise_index');
            $jour = $jourRepo->findOneBy(array('entreprise' => $this->entreprise, 'active' => 1));
            if ($form->isValid()) {

                if ($jour) {
                    # code...
                    $jour->setActive(0);
                    $jour->setDateFin(new \DateTime());
                    $jourRepo->save($jour, true);
                }
                //$joursMoisEntreprise->setTotVerse($somme);
                $joursMoisEntreprise->setActive(1);
                $joursMoisEntreprise->setDateDebut(new \DateTime());
                $joursMoisEntreprise->setEntreprise($this->entreprise);
                $entityManager->persist($joursMoisEntreprise);

                $entityManager->flush();

                $data = true;
                $message = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
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

        return $this->renderForm('parametre/jours_mois_entreprise/new.html.twig', [
            'jours_mois_entreprise' => $joursMoisEntreprise,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_parametre_jours_mois_entreprise_show', methods: ['GET'])]
    public function show(JoursMoisEntreprise $joursMoisEntreprise): Response
    {
        return $this->render('parametre/jours_mois_entreprise/show.html.twig', [
            'jours_mois_entreprise' => $joursMoisEntreprise,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_parametre_jours_mois_entreprise_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, JoursMoisEntreprise $joursMoisEntreprise, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(JoursMoisEntrepriseType::class, $joursMoisEntreprise, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_jours_mois_entreprise_edit', [
                'id' => $joursMoisEntreprise->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_jours_mois_entreprise_index');


            if ($form->isValid()) {

                $entityManager->persist($joursMoisEntreprise);
                $entityManager->flush();

                $data = true;
                $message = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
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

        return $this->renderForm('parametre/jours_mois_entreprise/edit.html.twig', [
            'jours_mois_entreprise' => $joursMoisEntreprise,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_parametre_jours_mois_entreprise_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, JoursMoisEntreprise $joursMoisEntreprise, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_parametre_jours_mois_entreprise_delete',
                    [
                        'id' => $joursMoisEntreprise->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($joursMoisEntreprise);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_parametre_jours_mois_entreprise_index');

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

        return $this->renderForm('parametre/jours_mois_entreprise/delete.html.twig', [
            'jours_mois_entreprise' => $joursMoisEntreprise,
            'form' => $form,
        ]);
    }
}
