<?php

namespace App\Service;

use App\Entity\Campagne;
use App\Entity\ModuleGroupePermition;
use App\Entity\ConfigApp;
use App\Entity\Contratloc;
use App\Entity\Factureloc;
use App\Entity\Prestataire;
use App\Entity\UserFront;
use App\Repository\MaisonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

use Psr\Container\ContainerInterface;
use function PHPUnit\Framework\isEmpty;

class Menu
{

    private $em;
    private $route;
    private $container;
    private $security;
    private $repo;

    private $resp;
    private $tableau = [];
    private  const IN_MENU_PRINCIPAL = 1;


    public function __construct(EntityManagerInterface $em, RequestStack $requestStack, RouterInterface $router, Security $security, MaisonRepository $maisonRepository)
    {
        $this->em = $em;

        if ($requestStack->getCurrentRequest()) {
            $this->route = $requestStack->getCurrentRequest()->attributes->get('_route');
            $this->container = $router->getRouteCollection()->all();
            $this->security = $security;
            $this->repo = $maisonRepository;
        }
        //dd($this->security->getUser()->getGroupe()->getName());
        //foreach($this->container as $key => $value){

        //  if(str_contains($key,'index')){
        //   $this->tableau [] = [
        // $key => str_replace('_',' ',$key)
        //  ];
        //}

        //  }

        // dd( $this->tableau);
        // if($this->getPermission() == null){
        // dd($this->getPermission());
        // }
        //dd($this->getPermission());
        /* if(!$this->getPermission()){
            dd("rrrr");
        }*/
        //$this->getPermission();
    }
    public function getGroupeName()
    {
        return $this->security->getUser()->getGroupe()->getName();
    }
    public function getGroupeCode()
    {
        return $this->security->getUser()->getGroupe()->getCode();
    }
    public function getRoute()
    {
        return $this->route;
    }
    public function getNamePrestataire($reference)
    {
        return $this->em->getRepository(UserFront::class)->findOneBy(['reference' => $reference]);
    }
    public function listeModule()
    {

        return $this->em->getRepository(ModuleGroupePermition::class)->afficheModule($this->security->getUser()->getGroupe()->getId());
    }

    public function getAllFactureByAgentCampagne($agent, $campagne)
    {

        return $this->em->getRepository(Factureloc::class)->findAllFactureLocataireByAgentCampagne($agent, $campagne);
    }

    public function existeContrat($appart)
    {
        //dd($this->em->getRepository(Contratloc::class)->existeContrat(1));
        return $this->em->getRepository(Contratloc::class)->existeContrat($appart);
    }
    public function getCampagne($campagne)
    {
        //dd($this->em->getRepository(Contratloc::class)->existeContrat(1));


        $facturesSommeCommission = $this->em->getRepository(Factureloc::class)->getCampagne($campagne);

        $allMaisonsSommeCommission = [];
        $sommeCommission = 0;
        $allAgents = [];


        $j = 0;

        foreach ($facturesSommeCommission as $key => $value) {

            // if ($value->getStatut() == 'payer') {
            $allMaisonsSommeCommission[$j]['id'] = $value->getAppartement()->getMaisson()->getId();

            array_push($allAgents, $value->getAppartement()->getMaisson()->getIdAgent()->getId());
            // }

            //$allMaisons[$i]['maison'] = $value->getAppartement()->getMaisson()->getLibMaison();
            //$sommeCommission += $value->getAppartement()->getMaisson()->getMntCom();
            $j++;
        }

        for ($i = 0; $i < count(array_unique($allMaisonsSommeCommission, SORT_REGULAR)); $i++) {
            $sommeCommission += $this->repo->find($allMaisonsSommeCommission[$i])->getMntCom();
        }

        return $sommeCommission;
    }
    public function getAllFactureByAgentCampagneTotals($agent, $campagne)
    {

        return $this->em->getRepository(Factureloc::class)->getAllFactureByAgentCampagneTotals($agent, $campagne);
    }



    public function listeGroupeModule()
    {
        //dd($this->em->getRepository(ModuleGroupePermition::class)->affiche($this->security->getUser()->getGroupe()->getId()));

        return $this->em->getRepository(ModuleGroupePermition::class)->affiche($this->security->getUser()->getGroupe()->getId(), 1);
    }

    public function findParametre()
    {

        return $this->em->getRepository(ConfigApp::class)->findConfig();
    }
    public function getTest()
    {
        return "#DDAD59";
    }
    public function getPermission()
    {
        $repo = $this->em->getRepository(ModuleGroupePermition::class)->getPermission($this->security->getUser()->getGroupe()->getId(), $this->route);
        //dd($repo);
        if ($repo != null) {
            return $repo['code'];
        } else {
            return $repo;
        }
    }

    public function getPermissionIfDifferentNull($group, $route)
    {
        $repo = $this->em->getRepository(ModuleGroupePermition::class)->getPermission($group, $route);
        //dd($repo);
        if ($repo != null) {
            return $repo['code'];
        } else {
            return $repo;
        }
    }

    public function liste()
    {


        return  $repo = $this->em->getRepository(Groupe::class)->afficheGroupes();
    }

    public function listeParent()
    {

        return $this->em->getRepository(Groupe::class)->affiche();
    }
    //public function listeModule
    public function listeGroupe()
    {
        $array = [
            'module' => 'modules',
            'app_config_parametre_index' => 'Parametrage général',
            'app_utilisateur_groupe_index' => 'Gestion groupe utilisateur',
            'app_utilisateur_utilisateur_index' => 'Gestion des utilisateur',
            'app_demande_demande_index' => 'Gestion des demandes',
            'app_utilisateur_permition_index' => 'Gestion des rôles',
            'app_utilisateur_employe_index' => 'Gestion des employés',

        ];

        return $array;
    }
    //    public function verifyanddispatch() {
    //
    //
    //
    //    }
}
