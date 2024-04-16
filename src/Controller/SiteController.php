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

class SiteController extends AbstractController
{
    #[Route(path: '/immo-plus', name: 'app_immo_plus', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->render('site/base.html.twig');
    }
}
