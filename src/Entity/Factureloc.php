<?php

namespace App\Entity;

use App\Repository\FacturelocRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FacturelocRepository::class)]
class Factureloc
{

    const ETATS = [
        'oui' => 'oui',
        'non' => 'non',

    ];
    const ETATS_STATUT = [
        'payer' => 'payer',
        'impayer' => 'impayer',

    ];
    const ETATS_STATUT_REMISE = [
        'remis' => 'remis',

    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'facturelocs')]
    private ?Campagne $compagne = null;

    #[ORM\ManyToOne(inversedBy: 'facturelocs')]
    private ?Tabmois $mois = null;

    #[ORM\ManyToOne(inversedBy: 'facturelocs')]
    private ?Contratloc $contrat = null;

    #[ORM\ManyToOne(inversedBy: 'facturelocs')]
    private ?Locataire $locataire = null;

    #[ORM\ManyToOne(inversedBy: 'facturelocs')]
    private ?Appartement $appartement = null;

    #[ORM\Column(length: 255)]
    private ?string $LibFacture = null;

    #[ORM\Column]
    private ?int $MntFact = null;

    #[ORM\Column]
    private ?int $SoldeFactLoc = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $DateEmission = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $DateLimite = null;

    #[ORM\OneToMany(mappedBy: 'numFact', targetEntity: Reglements::class)]
    private Collection $reglements;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(length: 255)]
    private ?string $encaisse = null;

    #[ORM\ManyToOne(inversedBy: 'facturelocs')]
    private ?VersmtProprio $versement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $remiseProprio = null;


    public function __construct()
    {
        $this->reglements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompagne(): ?Campagne
    {
        return $this->compagne;
    }

    public function setCompagne(?Campagne $compagne): static
    {
        $this->compagne = $compagne;

        return $this;
    }

    public function getMois(): ?Tabmois
    {
        return $this->mois;
    }

    public function setMois(?Tabmois $mois): static
    {
        $this->mois = $mois;

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

    public function getLocataire(): ?Locataire
    {
        return $this->locataire;
    }

    public function setLocataire(?Locataire $locataire): static
    {
        $this->locataire = $locataire;

        return $this;
    }

    public function getAppartement(): ?appartement
    {
        return $this->appartement;
    }

    public function setAppartement(?appartement $appartement): static
    {
        $this->appartement = $appartement;

        return $this;
    }

    public function getLibFacture(): ?string
    {
        return $this->LibFacture;
    }

    public function setLibFacture(string $LibFacture): static
    {
        $this->LibFacture = $LibFacture;

        return $this;
    }

    public function getMntFact(): ?int
    {
        return $this->MntFact;
    }

    public function setMntFact(int $MntFact): static
    {
        $this->MntFact = $MntFact;

        return $this;
    }

    public function getSoldeFactLoc(): ?int
    {
        return $this->SoldeFactLoc;
    }

    public function setSoldeFactLoc(int $SoldeFactLoc): static
    {
        $this->SoldeFactLoc = $SoldeFactLoc;

        return $this;
    }

    public function getDateEmission(): ?\DateTimeInterface
    {
        return $this->DateEmission;
    }

    public function setDateEmission(\DateTimeInterface $DateEmission): static
    {
        $this->DateEmission = $DateEmission;

        return $this;
    }

    public function getDateLimite(): ?\DateTimeInterface
    {
        return $this->DateLimite;
    }

    public function setDateLimite(\DateTimeInterface $DateLimite): static
    {
        $this->DateLimite = $DateLimite;

        return $this;
    }

    /**
     * @return Collection<int, Reglements>
     */
    public function getReglements(): Collection
    {
        return $this->reglements;
    }

    public function addReglement(Reglements $reglement): static
    {
        if (!$this->reglements->contains($reglement)) {
            $this->reglements->add($reglement);
            $reglement->setNumFact($this);
        }

        return $this;
    }

    public function removeReglement(Reglements $reglement): static
    {
        if ($this->reglements->removeElement($reglement)) {
            // set the owning side to null (unless already changed)
            if ($reglement->getNumFact() === $this) {
                $reglement->setNumFact(null);
            }
        }

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getEncaisse(): ?string
    {
        return $this->encaisse;
    }

    public function setEncaisse(string $encaisse): static
    {
        $this->encaisse = $encaisse;

        return $this;
    }

    public function getVersement(): ?VersmtProprio
    {
        return $this->versement;
    }

    public function setVersement(?VersmtProprio $versement): static
    {
        $this->versement = $versement;

        return $this;
    }

    public function getRemiseProprio(): ?string
    {
        return $this->remiseProprio;
    }

    public function setRemiseProprio(string $remiseProprio): static
    {
        $this->remiseProprio = $remiseProprio;

        return $this;
    }
}
