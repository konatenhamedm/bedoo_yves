<?php

namespace App\Entity;

use App\Repository\CompteLocRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompteLocRepository::class)]
class CompteLoc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'compteLocs')]
    private ?Locataire $locataire = null;

    #[ORM\ManyToOne(inversedBy: 'compteLocs')]
    private ?Contratloc $contrat = null;

    #[ORM\Column]
    private ?int $MntCaution = null;

    #[ORM\Column]
    private ?int $MntAvance = null;

    #[ORM\Column]
    private ?int $MntFraisAnex = null;

    #[ORM\Column]
    private ?int $DateLimite = null;

    #[ORM\Column]
    private ?int $Solde = null;

    #[ORM\Column]
    private ?int $MntVerse = null;

    #[ORM\Column]
    private ?int $MntAPayer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocataire(): ?Locataire
    {
        return $this->locataire;
    }

    public function setLocataire(?Locataire $locataire): static
    {
        $this->locataire = $locataire;

        return $this;
    }

    public function getContrat(): ?Contratloc
    {
        return $this->contrat;
    }

    public function setContrat(?Contratloc $contrat): static
    {
        $this->contrat = $contrat;

        return $this;
    }

    public function getMntCaution(): ?int
    {
        return $this->MntCaution;
    }

    public function setMntCaution(int $MntCaution): static
    {
        $this->MntCaution = $MntCaution;

        return $this;
    }

    public function getMntAvance(): ?int
    {
        return $this->MntAvance;
    }

    public function setMntAvance(int $MntAvance): static
    {
        $this->MntAvance = $MntAvance;

        return $this;
    }

    public function getMntFraisAnex(): ?int
    {
        return $this->MntFraisAnex;
    }

    public function setMntFraisAnex(int $MntFraisAnex): static
    {
        $this->MntFraisAnex = $MntFraisAnex;

        return $this;
    }

    public function getDateLimite(): ?int
    {
        return $this->DateLimite;
    }

    public function setDateLimite(int $DateLimite): static
    {
        $this->DateLimite = $DateLimite;

        return $this;
    }

    public function getSolde(): ?int
    {
        return $this->Solde;
    }

    public function setSolde(int $Solde): static
    {
        $this->Solde = $Solde;

        return $this;
    }

    public function getMntVerse(): ?int
    {
        return $this->MntVerse;
    }

    public function setMntVerse(int $MntVerse): static
    {
        $this->MntVerse = $MntVerse;

        return $this;
    }

    public function getMntAPayer(): ?int
    {
        return $this->MntAPayer;
    }

    public function setMntAPayer(int $MntAPayer): static
    {
        $this->MntAPayer = $MntAPayer;

        return $this;
    }
}
