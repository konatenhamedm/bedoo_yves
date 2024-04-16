<?php

namespace App\Controller;

use App\Entity\Factureloc;
use App\Entity\Pays;
use App\Entity\VersmtProprio;
use App\Form\VersmtProprioType;
use App\Repository\ContratlocRepository;
use App\Repository\FacturelocRepository;
use App\Repository\LocataireRepository;
use App\Repository\PaysRepository;
use App\Repository\ProprioRepository;
use App\Repository\TabmoisRepository;
use App\Repository\VersmtProprioRepository;
use App\Service\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HomeController extends AbstractController
{
    #[Route(path: '/home', name: 'app_default')]
    public function index(Request $request): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/error_page', name: 'page_error_index', methods: ['GET', 'POST'])]
    public function errorIndex(Request $request): Response
    {
        return $this->render('error.html.twig', []);
    }

    private $em;
    public function __construct(EntityManagerInterface $em)
    {

        $this->em = $em;
    }

    private function numero()
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(Pays::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0) {
            $nb = 1;
        } else {
            $nb = $nb + 1;
        }
        return (date("y") . 'PYS' . date("m", strtotime("now")) . str_pad($nb, 3, '0', STR_PAD_LEFT));
    }

    #[Route(path: '/site/save/data', name: 'site_save_data')]
    public function savedata(Request $request, PaysRepository $paysRepository, EntityManagerInterface $em): Response
    {


        $tab = [
            ["val0" => "Afghanistan "],
            ["val0" => "Afrique du Sud "],
            ["val0" => "Åland (les Îles)"],
            ["val0" => "Albanie "],
            ["val0" => "Algérie "],
            ["val0" => "Allemagne "],
            ["val0" => "Andorre "],
            ["val0" => "Angola "],
            ["val0" => "Anguilla"],
            ["val0" => "Antarctique "],
            ["val0" => "Antigua-et-Barbuda"],
            ["val0" => "Arabie saoudite "],
            ["val0" => "Argentine "],
            ["val0" => "Arménie "],
            ["val0" => "Aruba"],
            ["val0" => "Australie "],
            ["val0" => "Autriche "],
            ["val0" => "Azerbaïdjan "],
            ["val0" => "Bahamas "],
            ["val0" => "Bahreïn"],
            ["val0" => "Bangladesh "],
            ["val0" => "Barbade "],
            ["val0" => "Bélarus "],
            ["val0" => "Belgique "],
            ["val0" => "Belize "],
            ["val0" => "Bénin "],
            ["val0" => "Bermudes "],
            ["val0" => "Bhoutan "],
            ["val0" => "Bolivie (État plurinational de)"],
            ["val0" => "Bonaire, Saint-Eustache et Saba"],
            ["val0" => "Bosnie-Herzégovine "],
            ["val0" => "Botswana "],
            ["val0" => "Bouvet (l'Île)"],
            ["val0" => "Brésil "],
            ["val0" => "Brunéi Darussalam "],
            ["val0" => "Bulgarie "],
            ["val0" => "Burkina Faso "],
            ["val0" => "Burundi "],
            ["val0" => "Cabo Verde"],
            ["val0" => "Caïmans (les Îles)"],
            ["val0" => "Cambodge "],
            ["val0" => "Cameroun "],
            ["val0" => "Canada "],
            ["val0" => "Chili "],
            ["val0" => "Chine "],
            ["val0" => "Christmas (l'Île)"],
            ["val0" => "Chypre"],
            ["val0" => "Cocos (les Îles) / Keeling (les Îles)"],
            ["val0" => "Colombie "],
            ["val0" => "Comores "],
            ["val0" => "Congo "],
            ["val0" => "Congo (la République démocratique du)"],
            ["val0" => "Cook (les Îles)"],
            ["val0" => "Corée (la République de)"],
            ["val0" => "Corée (la République populaire démocratique de)"],
            ["val0" => "Costa Rica "],
            ["val0" => "Côte d'Ivoire "],
            ["val0" => "Croatie "],
            ["val0" => "Cuba"],
            ["val0" => "Curaçao"],
            ["val0" => "Danemark "],
            ["val0" => "Djibouti"],
            ["val0" => "dominicaine (la République)"],
            ["val0" => "Dominique "],
            ["val0" => "Égypte "],
            ["val0" => "El Salvador"],
            ["val0" => "Émirats arabes unis "],
            ["val0" => "Équateur "],
            ["val0" => "Érythrée "],
            ["val0" => "Espagne "],
            ["val0" => "Estonie "],
            ["val0" => "Eswatini "],
            ["val0" => "États-Unis d'Amérique "],
            ["val0" => "Éthiopie "],
            ["val0" => "Falkland (les Îles) /Malouines (les Îles)"],
            ["val0" => "Féroé (les Îles)"],
            ["val0" => "Fidji "],
            ["val0" => "Finlande "],
            ["val0" => "France "],
            ["val0" => "Gabon "],
            ["val0" => "Gambie "],
            ["val0" => "Géorgie "],
            ["val0" => "Géorgie du Sud-et-les Îles Sandwich du Sud "],
            ["val0" => "Ghana "],
            ["val0" => "Gibraltar"],
            ["val0" => "Grèce "],
            ["val0" => "Grenade "],
            ["val0" => "Groenland "],
            ["val0" => "Guadeloupe "],
            ["val0" => "Guam"],
            ["val0" => "Guatemala "],
            ["val0" => "Guernesey"],
            ["val0" => "Guinée "],
            ["val0" => "Guinée équatoriale "],
            ["val0" => "Guinée-Bissau "],
            ["val0" => "Guyana "],
            ["val0" => "Guyane française (la)"],
            ["val0" => "Haïti"],
            ["val0" => "Heard-et-Îles MacDonald (l'Île)"],
            ["val0" => "Honduras "],
            ["val0" => "Hong Kong"],
            ["val0" => "Hongrie "],
            ["val0" => "Île de Man"],
            ["val0" => "Îles mineures éloignées des États-Unis "],
            ["val0" => "Inde "],
            ["val0" => "Indien (le Territoire britannique de l'océan)"],
            ["val0" => "Indonésie "],
            ["val0" => "Iran (République Islamique d')"],
            ["val0" => "Iraq "],
            ["val0" => "Irlande "],
            ["val0" => "Islande "],
            ["val0" => "Israël"],
            ["val0" => "Italie "],
            ["val0" => "Jamaïque "],
            ["val0" => "Japon "],
            ["val0" => "Jersey"],
            ["val0" => "Jordanie "],
            ["val0" => "Kazakhstan "],
            ["val0" => "Kenya "],
            ["val0" => "Kirghizistan "],
            ["val0" => "Kiribati"],
            ["val0" => "Koweït "],
            ["val0" => "Lao (la République démocratique populaire)"],
            ["val0" => "Lesotho "],
            ["val0" => "Lettonie "],
            ["val0" => "Liban "],
            ["val0" => "Libéria "],
            ["val0" => "Libye "],
            ["val0" => "Liechtenstein "],
            ["val0" => "Lituanie "],
            ["val0" => "Luxembourg "],
            ["val0" => "Macao"],
            ["val0" => "Macédoine du Nord "],
            ["val0" => "Madagascar"],
            ["val0" => "Malaisie "],
            ["val0" => "Malawi "],
            ["val0" => "Maldives "],
            ["val0" => "Mali "],
            ["val0" => "Malte"],
            ["val0" => "Mariannes du Nord (les Îles)"],
            ["val0" => "Maroc "],
            ["val0" => "Marshall (les Îles)"],
            ["val0" => "Martinique "],
            ["val0" => "Maurice"],
            ["val0" => "Mauritanie "],
            ["val0" => "Mayotte"],
            ["val0" => "Mexique "],
            ["val0" => "Micronésie (États fédérés de)"],
            ["val0" => "Moldavie (la République de)"],
            ["val0" => "Monaco"],
            ["val0" => "Mongolie "],
            ["val0" => "Monténégro "],
            ["val0" => "Montserrat"],
            ["val0" => "Mozambique "],
            ["val0" => "Myanmar "],
            ["val0" => "Namibie "],
            ["val0" => "Nauru"],
            ["val0" => "Népal "],
            ["val0" => "Nicaragua "],
            ["val0" => "Niger "],
            ["val0" => "Nigéria "],
            ["val0" => "Niue"],
            ["val0" => "Norfolk (l'Île)"],
            ["val0" => "Norvège "],
            ["val0" => "Nouvelle-Calédonie "],
            ["val0" => "Nouvelle-Zélande "],
            ["val0" => "Oman"],
            ["val0" => "Ouganda "],
            ["val0" => "Ouzbékistan "],
            ["val0" => "Pakistan "],
            ["val0" => "Palaos "],
            ["val0" => "Palestine, État de"],
            ["val0" => "Panama "],
            ["val0" => "Papouasie-Nouvelle-Guinée "],
            ["val0" => "Paraguay "],
            ["val0" => "Pays-Bas "],
            ["val0" => "Pérou "],
            ["val0" => "Philippines "],
            ["val0" => "Pitcairn"],
            ["val0" => "Pologne "],
            ["val0" => "Polynésie française "],
            ["val0" => "Porto Rico"],
            ["val0" => "Portugal "],
            ["val0" => "Qatar "],
            ["val0" => "République arabe syrienne "],
            ["val0" => "République centrAfriqueine "],
            ["val0" => "Réunion "],
            ["val0" => "Roumanie "],
            ["val0" => "Royaume-Uni de Grande-Bretagne et d'Irlande du Nord "],
            ["val0" => "Russie (la Fédération de)"],
            ["val0" => "Rwanda "],
            ["val0" => "Sahara occidental"],
            ["val0" => "Saint-Barthélemy"],
            ["val0" => "Sainte-Hélène, Ascension et Tristan da Cunha"],
            ["val0" => "Sainte-Lucie"],
            ["val0" => "Saint-Kitts-et-Nevis"],
            ["val0" => "Saint-Marin"],
            ["val0" => "Saint-Martin (partie française)"],
            ["val0" => "Saint-Martin (partie néerlandaise)"],
            ["val0" => "Saint-Pierre-et-Miquelon"],
            ["val0" => "Saint-Siège "],
            ["val0" => "Saint-Vincent-et-les Grenadines"],
            ["val0" => "Salomon (les Îles)"],
            ["val0" => "Samoa "],
            ["val0" => "Samoa américaines "],
            ["val0" => "Sao Tomé-et-Principe"],
            ["val0" => "Sénégal "],
            ["val0" => "Serbie "],
            ["val0" => "Seychelles "],
            ["val0" => "Sierra Leone "],
            ["val0" => "Singapour"],
            ["val0" => "Slovaquie "],
            ["val0" => "Slovénie "],
            ["val0" => "Somalie "],
            ["val0" => "Soudan "],
            ["val0" => "Soudan du Sud "],
            ["val0" => "Sri Lanka"],
            ["val0" => "Suède "],
            ["val0" => "Suisse "],
            ["val0" => "Suriname "],
            ["val0" => "Svalbard et l'Île Jan Mayen "],
            ["val0" => "Tadjikistan "],
            ["val0" => "Taïwan (Province de Chine)"],
            ["val0" => "Tanzanie (la République-Unie de)"],
            ["val0" => "Tchad "],
            ["val0" => "Tchéquie "],
            ["val0" => "Terres australes françaises "],
            ["val0" => "Thaïlande "],
            ["val0" => "Timor-Leste "],
            ["val0" => "Togo "],
            ["val0" => "Tokelau "],
            ["val0" => "Tonga "],
            ["val0" => "Trinité-et-Tobago "],
            ["val0" => "Tunisie "],
            ["val0" => "Turkménistan "],
            ["val0" => "Turks-et-Caïcos (les Îles)"],
            ["val0" => "Turquie "],
            ["val0" => "Tuvalu "],
            ["val0" => "Ukraine "],
            ["val0" => "Uruguay "],
            ["val0" => "Vanuatu "],
            ["val0" => "Venezuela (République bolivarienne du)"],
            ["val0" => "Vierges britanniques (les Îles)"],
            ["val0" => "Vierges des États-Unis (les Îles)"],
            ["val0" => "Viet Nam "],
            ["val0" => "Wallis-et-Futuna"],
            ["val0" => "Yémen "],
            ["val0" => "Zambie "],
            ["val0" => "Zimbabwe"],
        ];

        foreach ($tab as $item) {

            $pays = new Pays();
            $pays->setCode($this->numero());
            $pays->setLibelle($item['val0']);
            $paysRepository->save($pays, true);
        }

        return $this->json('success');
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

    #[Route('/versement/get_numero', name: 'app_get_numero', methods: ['GET'])]
    public function existe(Request $request): Response
    {
        $response = new Response();
        $format = "";
        $numero = $this->numeroVersement();

        if ($request->isXmlHttpRequest()) {

            $arrayCollection[] = array(
                'numero' =>  $numero,

                // ... Same for each property you want
            );
            $data = json_encode($arrayCollection); // formater le résultat de la requête en json
            //dd($data);
            $response->headers->set('Content-TypeActe', 'application/json');
            $response->setContent($data);
        }
        return $this->json([
            'code' => 200,
            'message' => 'ça marche bien',
            'numero' => $numero,
        ], 200);
    }

    #[Route('/new/{id}', name: 'app_comptabilite_versmt_proprio_new',  methods: ['GET', 'POST'])]
    #[Route('/{id}/new', name: 'app_achat_demande_new_user', methods: ['GET', 'POST'])]
    public function new(Request $request, VersmtProprioRepository $versmtProprioRepository, FormError $formError, ?int $id, FacturelocRepository $facturelocRepository, TabmoisRepository $tabmoisRepository, LocataireRepository $locataireRepository, ContratlocRepository $contratlocRepository): Response
    {



        $versmtProprio = new VersmtProprio();
        $form = $this->createForm(VersmtProprioType::class, $versmtProprio, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilite_versmt_proprio_new', ['id' => $id])
        ]);
        $form->handleRequest($request);

        // $locataire = $locataireRepository->find($id);
        $factures = $facturelocRepository->findAllFactureLocataire($id);
        $montantAvance = (int)$locataireRepository->getMontanSommeContratActif($id)[0]['soldeRestant'];
        // dd($factures);
        // $proprioId = $proprio;


        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $date = $form->get('dateVersement')->getData();

            $montant = (int) $form->get('montant')->getData() + $montantAvance;

            //dd($tabmoisRepository->findOneBy(array('NumMois' => (int)$date->format('m'))));

            // dd($montant);
            $response = [];

            $redirect = $this->generateUrl('app_comptabilite_versmt_proprio_index', ['id' => $id]);



            if ($form->isValid()) {
                $last_key = count($factures);
                $i = 1;

                foreach ($factures as $key => $facture) {
                    //dd($montant);
                    $versement = new VersmtProprio();
                    $versement->setLibelle($tabmoisRepository->findOneBy(array('NumMois' => (int)$date->format('m')))->getLibMois() . ' ' . $date->format('Y'));
                    $versement->setDateVersement($form->get('dateVersement')->getData());
                    $versement->setProprio($facture->getAppartement()->getMaisson()->getProprio());
                    $versement->setMaison($facture->getAppartement()->getMaisson());
                    $versement->setLocataire($locataireRepository->find($id));
                    $versement->setNumero($form->get('numero')->getData());
                    $versement->setNumeroRecu($this->numeroVersements());

                    $versement->setTypeVersement($form->get('type_versement')->getData());

                    if ($montant >= $facture->getSoldeFactLoc()) {
                        // dd('dd');
                        $facture->setSoldeFactLoc(0);
                        $facture->setStatut(Factureloc::ETATS_STATUT['payer']);
                        $facture->setEncaisse(Factureloc::ETATS['oui']);

                        $versement->setMontant($facture->getMntFact());
                        $versmtProprioRepository->save($versement, true);
                        $facture->setVersement($versement);
                        $facturelocRepository->save($facture, true);

                        $montant = $montant - $facture->getSoldeFactLoc();

                        if ($i == $last_key) {

                            if ($montant >= 0) {
                                $locataire = $locataireRepository->find($facture->getLocataire()->getId());
                                /*   $contrat = $contratlocRepository->find($facture->getContrat()->getId()); */
                                $locataire->setSoldeRestant($montant);
                                $locataireRepository->save($locataire, true);
                            }
                            //dd('ff');

                        }
                    } else {

                        if ($i == $last_key) {
                            //dd('ff');
                            $locataire = $locataireRepository->find($facture->getLocataire()->getId());
                            /*   $contrat = $contratlocRepository->find($facture->getContrat()->getId()); */
                            $locataire->setSoldeRestant($montant);
                            $locataireRepository->save($locataire, true);
                        }
                    }




                    $i++;
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

        return $this->renderForm('comptabilite/versmt_proprio/new.html.twig', [
            'versmt_proprio' => $versmtProprio,
            'form' => $form,
            // 'id' => $id
        ]);
    }


    #[Route(path: '/print-iframe', name: 'default_print_iframe', methods: ["DELETE", "GET"], condition: "request.query.get('r')", options: ["expose" => true])]
    public function defaultPrintIframe(Request $request, UrlGeneratorInterface $urlGenerator)
    {
        $all = $request->query->all();
        //print-iframe?r=foo_bar_foo&params[']
        $routeName = $request->query->get('r');
        $title = $request->query->get('title');
        $params = $all['params'] ?? [];
        $stacked = $params['stacked'] ?? false;
        $redirect = isset($params['redirect']) ? $urlGenerator->generate($params['redirect'], $params) : '';
        $iframeUrl = $urlGenerator->generate($routeName, $params);

        $isFacture = isset($params['mode']) && $params['mode'] == 'facture' && $routeName == 'facturation_facture_print';

        return $this->render('home/iframe.html.twig', [
            'iframe_url' => $iframeUrl,
            'id' => $params['id'] ?? null,
            'stacked' => $stacked,
            'redirect' => $redirect,
            'title' => $title,
            'facture' => 0/*$isFacture*/
        ]);
    }
}
