<?php

namespace App\Entity;

use App\Repository\CampagneContratRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CampagneContratRepository::class)]
class CampagneContrat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;




    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '0')]
    private ?string $loyer = null;

    private ?string $locataire_hide = null;

    public function getLocataireHide(): ?string
    {
        return $this->locataire_hide;
    }

    public function setLocataireHide(?string $locataire_hide): void
    {
        $this->locataire_hide = $locataire_hide;
    }



    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateLimite = null;

    #[ORM\ManyToOne(inversedBy: 'campagneContrats')]
    private ?Campagne $campagne = null;



    #[ORM\ManyToOne(inversedBy: 'campagneContrats')]
    private ?Maison $maison = null;

    #[ORM\ManyToOne(inversedBy: 'campagneContrats')]
    private ?Locataire $locataire = null;

    #[ORM\ManyToOne(inversedBy: 'campagneContrats')]
    private ?Proprio $proprietaire = null;

    #[ORM\ManyToOne(inversedBy: 'campagneContrats')]
    private ?Appartement $numAppartement = null;

    public function getId(): ?int
    {
        return $this->id;
    }







    public function getLoyer(): ?string
    {
        return $this->loyer;
    }

    public function setLoyer(string $loyer): static
    {
        $this->loyer = $loyer;

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

    public function getCampagne(): ?Campagne
    {
        return $this->campagne;
    }

    public function setCampagne(?Campagne $campagne): static
    {
        $this->campagne = $campagne;

        return $this;
    }


    public function getMaison(): ?Maison
    {
        return $this->maison;
    }

    public function setMaison(?Maison $maison): static
    {
        $this->maison = $maison;

        return $this;
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

    public function getProprietaire(): ?Proprio
    {
        return $this->proprietaire;
    }

    public function setProprietaire(?Proprio $proprietaire): static
    {
        $this->proprietaire = $proprietaire;

        return $this;
    }

    public function getNumAppartement(): ?Appartement
    {
        return $this->numAppartement;
    }

    public function setNumAppartement(?Appartement $numAppartement): static
    {
        $this->numAppartement = $numAppartement;

        return $this;
    }
}
