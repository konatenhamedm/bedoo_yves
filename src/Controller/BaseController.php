<?php


namespace App\Controller;


use App\Controller\FileTrait;
use App\Repository\FacturelocRepository;
use App\Repository\TypeVersementsRepository;
use App\Repository\VersmtProprioRepository;
use App\Service\Menu;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\Registry;
use Symfony\Config\Security\PasswordHasherConfig;

class BaseController extends AbstractController
{

    use FileTrait;

    protected const UPLOAD_PATH = 'media_entreprise';
    protected $em;
    protected $security;
    protected $menu;
    protected  $entreprise;
    protected  $groupe;
    protected  $hasher;
    protected $workflow;
    protected $factureRepo;
    protected $versementRepo;
    protected $typeVersementsRepository;


    public function __construct(Registry $workflow, EntityManagerInterface $em, Menu $menu, Security $security, UserPasswordHasherInterface $hasher, TypeVersementsRepository $typeVersementsRepository, FacturelocRepository $facturelocRepository, VersmtProprioRepository $versementRepository)
    {
        $this->workflow = $workflow;
        $this->em = $em;
        $this->hasher = $hasher;
        $this->security = $security;
        $this->menu = $menu;
        if ($this->security->getUser()) {

            $this->entreprise = $this->security->getUser()->getEmploye()->getEntreprise();
            //$this->security = $security;
        } else {
            $this->redirectToRoute('app_login');
        }
        //$this->entreprise = $this->security->getUser()->getEmploye()->getEntreprise();
        $this->groupe = $this->security->getUser()->getGroupe()->getCode();
        $this->factureRepo = $facturelocRepository;
        $this->versementRepo = $versementRepository;
        $this->typeVersementsRepository = $typeVersementsRepository;
    }
}
