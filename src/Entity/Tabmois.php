<?php

namespace App\Entity;

use App\Repository\TabmoisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TabmoisRepository::class)]
class Tabmois
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $LibMois = null;

    #[ORM\Column]
    private ?int $NumMois = null;

    #[ORM\Column(length: 255)]
    private ?string $Debut = null;

    #[ORM\Column(length: 255)]
    private ?string $Fin = null;

    #[ORM\OneToMany(mappedBy: 'mois', targetEntity: Campagne::class)]
    private Collection $campagnes;

    #[ORM\OneToMany(mappedBy: 'mois', targetEntity: Factureloc::class)]
    private Collection $facturelocs;

    public function __construct()
    {
        $this->campagnes = new ArrayCollection();
        $this->facturelocs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibMois(): ?string
    {
        return $this->LibMois;
    }

    public function setLibMois(string $LibMois): static
    {
        $this->LibMois = $LibMois;

        return $this;
    }

    public function getNumMois(): ?int
    {
        return $this->NumMois;
    }

    public function setNumMois(int $NumMois): static
    {
        $this->NumMois = $NumMois;

        return $this;
    }

    public function getDebut(): ?string
    {
        return $this->Debut;
    }

    public function setDebut(string $Debut): static
    {
        $this->Debut = $Debut;

        return $this;
    }

    public function getFin(): ?string
    {
        return $this->Fin;
    }

    public function setFin(string $Fin): static
    {
        $this->Fin = $Fin;

        return $this;
    }

    /**
     * @return Collection<int, Campagne>
     */
    public function getCampagnes(): Collection
    {
        return $this->campagnes;
    }

    public function addCampagne(Campagne $campagne): static
    {
        if (!$this->campagnes->contains($campagne)) {
            $this->campagnes->add($campagne);
            $campagne->setMois($this);
        }

        return $this;
    }

    public function removeCampagne(Campagne $campagne): static
    {
        if ($this->campagnes->removeElement($campagne)) {
            // set the owning side to null (unless already changed)
            if ($campagne->getMois() === $this) {
                $campagne->setMois(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Factureloc>
     */
    public function getFacturelocs(): Collection
    {
        return $this->facturelocs;
    }

    public function addFactureloc(Factureloc $factureloc): static
    {
        if (!$this->facturelocs->contains($factureloc)) {
            $this->facturelocs->add($factureloc);
            $factureloc->setMois($this);
        }

        return $this;
    }

    public function removeFactureloc(Factureloc $factureloc): static
    {
        if ($this->facturelocs->removeElement($factureloc)) {
            // set the owning side to null (unless already changed)
            if ($factureloc->getMois() === $this) {
                $factureloc->setMois(null);
            }
        }

        return $this;
    }
}
