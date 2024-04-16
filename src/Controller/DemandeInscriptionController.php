<?php

namespace App\Controller;

use App\Entity\DemandeInscription;
use App\Entity\Employe;
use App\Entity\Entreprise;
use App\Form\DemandeInscriptionType;
use App\Repository\DemandeInscriptionRepository;
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
use App\DTO\DemandeDTO;
use App\Entity\Utilisateur;
use App\Form\DemandeInscriptionRejeterType;
use App\Repository\FonctionRepository;
use App\Repository\GroupeRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/ads/demande/inscription')]
class DemandeInscriptionController extends BaseController
{


    const INDEX_ROOT_NAME = 'app_demande_inscription_index';

    #[Route('/{etat}', name: 'app_demande_inscription_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory, $etat): Response
    {

        if ($etat == "cree") {
            $titre = 'demandes en attente de traitement';
        } elseif ($etat == 'valide') {
            $titre = 'demandes  validées';
        } else {
            $titre = 'demandes réjetées';
        }


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('email', TextColumn::class, ['label' => 'Email'])
            ->add('denomination', TextColumn::class, ['label' => 'Denomination'])
            ->add('pays', TextColumn::class, ['field' => 'p.libelle', 'label' => 'Pays'])
            ->add('ville', TextColumn::class, ['label' => 'Ville'])
            ->add('contact', TextColumn::class, ['label' => 'Contact'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => DemandeInscription::class,
                'query' => function (QueryBuilder $qb) use ($etat) {
                    $qb->select('d, p')
                        ->from(DemandeInscription::class, 'd')
                        ->andWhere('d.statut = :etat')
                        ->setParameter('etat', $etat)
                        ->join('d.pays', 'p');
                }
            ])
            ->setName('dt_app_demande_inscription' . $etat);
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
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, DemandeInscription $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'edit' => [
                                    'url' => $this->generateUrl('app_demande_inscription_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_demande_inscription_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_demande_inscription_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'], 'render' => $renders['delete']
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


        return $this->render('demande_inscription/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission,
            'etat' => $etat,
            'titre' => $titre,
        ]);
    }

    #[Route('/demande/inscription', name: 'app_demande_inscription_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError, DemandeInscriptionRepository $demandeInscriptionRepository): Response
    {
        $demandeDTO = new DemandeDTO();
        $form = $this->createForm(DemandeInscriptionType::class, $demandeDTO, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_demande_inscription_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_demande_inscription_new');
            $fullRedirect = false;

            if ($form->isValid()) {

                if ($demandeInscriptionRepository->findOneBy(array('email' => $demandeDTO->getEmail(), 'statut' => 'cree')) || $demandeInscriptionRepository->findOneBy(array('email' => $demandeDTO->getEmail(), 'statut' => 'valide'))) {
                    $data = true;
                    $message = '';
                    $this->addFlash('danger', 'Une demande a été deja faite avec cet email');
                } else {
                    $demandeInscription = new DemandeInscription();

                    $demandeInscription->setEmail($demandeDTO->getEmail());
                    $demandeInscription->setDenomination($demandeDTO->getDenomination());
                    $demandeInscription->setContact($demandeDTO->getContact());
                    $demandeInscription->setAdresse($demandeDTO->getAdresse());
                    $demandeInscription->setSiteWeb($demandeDTO->getSiteWeb());
                    $demandeInscription->setPays($demandeDTO->getPays());
                    $demandeInscription->setVille($demandeDTO->getVille());
                    $demandeInscription->setStatut(DemandeInscription::ETATS['cree']);

                    $entityManager->persist($demandeInscription);
                    $entityManager->flush();

                    $data = true;
                    $message = 'Opération effectuée avec succès';
                    $this->addFlash('success', 'Votre demande a été crée avec succès ,elle est en attente de traitement. Nous vous notifierons par email');
                }


                $statut = 1;
                // $this->addFlash('success', $message);
                $fullRedirect = true;
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            if ($isAjax) {
                return $this->json(compact('statut',  'redirect', 'data', 'fullRedirect'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->renderForm('security/register.html.twig', [
            'demande_inscription' => $demandeDTO,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_demande_inscription_show', methods: ['GET'])]
    public function show(DemandeInscription $demandeInscription): Response
    {
        return $this->render('demande_inscription/show.html.twig', [
            'demande_inscription' => $demandeInscription,
        ]);
    }

    private function numero()
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(Entreprise::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0) {
            $nb = 1;
        } else {
            $nb = $nb + 1;
        }
        return (date("y") . 'ENT' . date("m", strtotime("now")) . str_pad($nb, 3, '0', STR_PAD_LEFT));
    }

    #[Route('/{id}/edit', name: 'app_demande_inscription_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        DemandeInscription $demandeInscription,
        EntityManagerInterface $entityManager,
        FormError $formError,
        FonctionRepository $fonctionRepository,
        GroupeRepository $groupeRepository,
        UserPasswordHasherInterface $userPasswordHasher,
        SendMailService $sendMailService
    ): Response {

        $form = $this->createForm(DemandeInscriptionType::class, $demandeInscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_demande_inscription_edit', [
                'id' => $demandeInscription->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_demandes_index');
            $workflow = $this->workflow->get($demandeInscription, 'demande');

            if ($form->isValid()) {

                if ($form->getClickedButton()->getName() === 'valider') {

                    $entreprise = new Entreprise();
                    $entreprise->setCode($this->numero());
                    $entreprise->setDenomination($form->get('denomination')->getData());
                    $entreprise->setContacts($form->get('contact')->getData());
                    $entreprise->setAdresse($form->get('adresse')->getData());
                    $entreprise->setSiteWeb($form->get('siteWeb')->getData());
                    $entreprise->setEmail($form->get('email')->getData());
                    $entreprise->setVille($form->get('ville')->getData());
                    $entreprise->setPays($form->get('pays')->getData());

                    $entityManager->persist($entreprise);
                    $entityManager->flush();


                    $employe = new Employe();
                    $employe->setNom($form->get('denomination')->getData());
                    $employe->setPrenom($form->get('denomination')->getData());
                    $employe->setContact($form->get('contact')->getData());
                    $employe->setAdresseMail($form->get('email')->getData());
                    $employe->setEntreprise($entreprise);

                    $entityManager->persist($employe);
                    $entityManager->flush();

                    $utilisateur = new Utilisateur();

                    $utilisateur->setGroupe($groupeRepository->findOneBy(array('code' => 'Administrateur')));
                    $utilisateur->setEmploye($employe);

                    $username = 'Administrateur' . $this->numero();
                    $utilisateur->setUsername($username);
                    $utilisateur->setPassword($userPasswordHasher->hashPassword($utilisateur, $form->get('denomination')->getData() . '-' . $this->numero()));

                    $entityManager->persist($utilisateur);
                    $entityManager->flush();

                    $workflow->apply($demandeInscription, 'validation');
                    $entityManager->persist($demandeInscription);
                    $entityManager->flush();


                    $info_user = [
                        'login' => $form->get('email')->getData(),
                        'password' => $form->get('denomination')->getData() . '-' . $this->numero()
                    ];

                    $context = compact('info_user');

                    $sendMailService->send(
                        'konatenhamed@ufrseg.enig-sarl.com',
                        $form->get('email')->getData(),
                        'Informations',
                        'content_mail',
                        $context
                    );
                } else {

                    $workflow->apply($demandeInscription, 'rejet');
                    $entityManager->persist($demandeInscription);
                    $entityManager->flush();
                }


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

        return $this->renderForm('demande_inscription/edit.html.twig', [
            'demande_inscription' => $demandeInscription,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/rejeter', name: 'app_demande_inscription_edit_rejeter', methods: ['GET', 'POST'])]
    public function editRejeter(
        Request $request,
        DemandeInscription $demandeInscription,
        EntityManagerInterface $entityManager,
        FormError $formError,
        FonctionRepository $fonctionRepository,
        GroupeRepository $groupeRepository,
        UserPasswordHasherInterface $userPasswordHasher,
        SendMailService $sendMailService
    ): Response {

        $form = $this->createForm(DemandeInscriptionRejeterType::class, $demandeInscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_demande_inscription_edit_rejeter', [
                'id' => $demandeInscription->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_demandes_index');
            $workflow = $this->workflow->get($demandeInscription, 'demande');

            if ($form->isValid()) {

                if ($form->getClickedButton()->getName() === 'rejeter') {


                    $workflow->apply($demandeInscription, 'rejet');
                    $entityManager->persist($demandeInscription);
                    $entityManager->flush();
                }


                $data = true;
                $message = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
                //return $this->redirect($redirect);
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

        return $this->renderForm('demande_inscription/rejeter.html.twig', [
            'demande_inscription' => $demandeInscription,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_demande_inscription_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, DemandeInscription $demandeInscription, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_demande_inscription_delete',
                    [
                        'id' => $demandeInscription->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($demandeInscription);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_demande_inscription_index');

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

        return $this->renderForm('demande_inscription/delete.html.twig', [
            'demande_inscription' => $demandeInscription,
            'form' => $form,
        ]);
    }
}
