<?php

namespace App\Controller;

use App\Entity\Contratloc;
use App\Entity\FichierAdmin;
use App\Entity\Locataire;
use App\Repository\FichierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('fichier')]
class FichierController extends AbstractController
{
    #[Route('/{id}', name: 'fichier_index', methods: ['GET'])]
    public function show(Request $request, FichierAdmin $fichier)
    {

        $fileName = $fichier->getFileName();
        $filePath = $fichier->getPath();
        $download = $request->query->get('download');

        $file = $this->getUploadDir($filePath . '/' . $fileName);


        /*try {
            $file = $this->getUploadDir($filePath . '/' . $fileName);
        } catch (FileNotFoundException $e) {
            $file = $this->getUploadDir($fileName);
        } catch (FileNotFoundException $e) {
            $file = null;
        }*/

        if (!file_exists($file)) {
            return new Response('FichierAdmin invalide');
        }

        if ($download) {
            return $this->file($file);
        }

        return new BinaryFileResponse($file);
    }



    #[Route('/autre/{id}', name: 'fichier_index_autre', methods: ['GET'])]
    public function showAutre(Request $request, $id, FichierRepository $fichierRepository, Locataire $locataire)
    {
        $fichier = $fichierRepository->find($locataire->getInfoPiece()->getId());
        $fileName = $fichier->getFileName();
        $filePath = $fichier->getPath();
        $download = $request->query->get('download');

        $file = $this->getUploadDir($filePath . '/' . $fileName);


        /*try {
            $file = $this->getUploadDir($filePath . '/' . $fileName);
        } catch (FileNotFoundException $e) {
            $file = $this->getUploadDir($fileName);
        } catch (FileNotFoundException $e) {
            $file = null;
        }*/

        if (!file_exists($file)) {
            return new Response('FichierAdmin invalide');
        }

        if ($download) {
            return $this->file($file);
        }

        return new BinaryFileResponse($file);
    }


    #[Route('/fichierResilier/{id}', name: 'fichier_index_contrat_resilier', methods: ['GET'])]
    public function showContratResilier(Request $request, $id, FichierRepository $fichierRepository, Contratloc $contratloc)
    {
        $fichier = $fichierRepository->find($contratloc->getFichierResiliation()->getId());
        $fileName = $fichier->getFileName();
        $filePath = $fichier->getPath();
        $download = $request->query->get('download');

        $file = $this->getUploadDir($filePath . '/' . $fileName);


        /*try {
            $file = $this->getUploadDir($filePath . '/' . $fileName);
        } catch (FileNotFoundException $e) {
            $file = $this->getUploadDir($fileName);
        } catch (FileNotFoundException $e) {
            $file = null;
        }*/

        if (!file_exists($file)) {
            return new Response('FichierAdmin invalide');
        }

        if ($download) {
            return $this->file($file);
        }

        return new BinaryFileResponse($file);
    }

    /**
     * @return mixed
     */
    public function getUploadDir($path)
    {
        return $this->getParameter('upload_dir') . '/' . $path;
    }
}
