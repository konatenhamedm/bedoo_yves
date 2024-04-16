<?php

namespace App\Entity;

use App\Repository\PaysRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaysRepository::class)]
class Pays
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'pays', targetEntity: Ville::class)]
    private Collection $villes;

    #[ORM\OneToMany(mappedBy: 'pays', targetEntity: Entreprise::class)]
    private Collection $entreprises;

    #[ORM\OneToMany(mappedBy: 'pays', targetEntity: DemandeInscription::class)]
    private Collection $demandeInscriptions;

    public function __construct()
    {
        $this->villes = new ArrayCollection();
        $this->entreprises = new ArrayCollection();
        $this->demandeInscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, Ville>
     */
    public function getVilles(): Collection
    {
        return $this->villes;
    }

    public function addVille(Ville $ville): static
    {
        if (!$this->villes->contains($ville)) {
            $this->villes->add($ville);
            $ville->setPays($this);
        }

        return $this;
    }

    public function removeVille(Ville $ville): static
    {
        if ($this->villes->removeElement($ville)) {
            // set the owning side to null (unless already changed)
            if ($ville->getPays() === $this) {
                $ville->setPays(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Entreprise>
     */
    public function getEntreprises(): Collection
    {
        return $this->entreprises;
    }

    public function addEntreprise(Entreprise $entreprise): static
    {
        if (!$this->entreprises->contains($entreprise)) {
            $this->entreprises->add($entreprise);
            $entreprise->setPays($this);
        }

        return $this;
    }

    public function removeEntreprise(Entreprise $entreprise): static
    {
        if ($this->entreprises->removeElement($entreprise)) {
            // set the owning side to null (unless already changed)
            if ($entreprise->getPays() === $this) {
                $entreprise->setPays(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DemandeInscription>
     */
    public function getDemandeInscriptions(): Collection
    {
        return $this->demandeInscriptions;
    }

    public function addDemandeInscription(DemandeInscription $demandeInscription): static
    {
        if (!$this->demandeInscriptions->contains($demandeInscription)) {
            $this->demandeInscriptions->add($demandeInscription);
            $demandeInscription->setPays($this);
        }

        return $this;
    }

    public function removeDemandeInscription(DemandeInscription $demandeInscription): static
    {
        if ($this->demandeInscriptions->removeElement($demandeInscription)) {
            // set the owning side to null (unless already changed)
            if ($demandeInscription->getPays() === $this) {
                $demandeInscription->setPays(null);
            }
        }

        return $this;
    }
}
