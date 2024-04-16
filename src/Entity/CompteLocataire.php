<?php

namespace App\Entity;

use App\Repository\CompteLocataireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompteLocataireRepository::class)]
class CompteLocataire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'compteLocataires')]
    private ?Locataire $locataire = null;

    #[ORM\Column(length: 255)]
    private ?string $montantCaution = null;

    #[ORM\Column(length: 255)]
    private ?string $montantAvance = null;

    #[ORM\Column(length: 255)]
    private ?string $montantFraixAnnex = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateLimite = null;

    #[ORM\Column(length: 255)]
    private ?string $solde = null;

    #[ORM\Column(length: 255)]
    private ?string $montantVerse = null;

    #[ORM\Column(length: 255)]
    private ?string $montantAPayer = null;

    #[ORM\ManyToOne(inversedBy: 'compteLocataires')]
    private ?ContratLoc $contrat = null;

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

    public function getMontantCaution(): ?string
    {
        return $this->montantCaution;
    }

    public function setMontantCaution(string $montantCaution): static
    {
        $this->montantCaution = $montantCaution;

        return $this;
    }

    public function getMontantAvance(): ?string
    {
        return $this->montantAvance;
    }

    public function setMontantAvance(string $montantAvance): static
    {
        $this->montantAvance = $montantAvance;

        return $this;
    }

    public function getMontantFraixAnnex(): ?string
    {
        return $this->montantFraixAnnex;
    }

    public function setMontantFraixAnnex(string $montantFraixAnnex): static
    {
        $this->montantFraixAnnex = $montantFraixAnnex;

        return $this;
    }

    public function getDateLimite(): ?\DateTimeInterface
    {
        return $this->dateLimite;
    }

    public function setDateLimite(\DateTimeInterface $dateLimite): static
    {
        $this->dateLimite = $dateLimite;

        return $this;
    }

    public function getSolde(): ?string
    {
        return $this->solde;
    }

    public function setSolde(string $solde): static
    {
        $this->solde = $solde;

        return $this;
    }

    public function getMontantVerse(): ?string
    {
        return $this->montantVerse;
    }

    public function setMontantVerse(string $montantVerse): static
    {
        $this->montantVerse = $montantVerse;

        return $this;
    }

    public function getMontantAPayer(): ?string
    {
        return $this->montantAPayer;
    }

    public function setMontantAPayer(string $montantAPayer): static
    {
        $this->montantAPayer = $montantAPayer;

        return $this;
    }

    public function getContrat(): ?ContratLoc
    {
        return $this->contrat;
    }

    public function setContrat(?ContratLoc $contrat): static
    {
        $this->contrat = $contrat;

        return $this;
    }
}
