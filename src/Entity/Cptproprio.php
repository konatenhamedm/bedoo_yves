<?php

namespace App\Entity;

use App\Repository\CptProprioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CptProprioRepository::class)]
class CptProprio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'cptProprios')]
    private ?Proprio $Proprio = null;

    #[ORM\Column]
    private ?int $Pourcentage = null;

    #[ORM\Column]

    private ?int $TotalDu = null;

    #[ORM\Column]
    private ?int $TotalPaye = null;

    #[ORM\OneToMany(mappedBy: 'compte_Proprio', targetEntity: VersmtProprio::class)]
    private Collection $versmtProprios;

    public function __construct()
    {
        $this->versmtProprios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProprio(): ?Proprio
    {
        return $this->Proprio;
    }

    public function setProprio(?Proprio $Proprio): static
    {
        $this->Proprio = $Proprio;

        return $this;
    }

    public function getPourcentage(): ?int
    {
        return $this->Pourcentage;
    }

    public function setPourcentage(int $Pourcentage): static
    {
        $this->Pourcentage = $Pourcentage;

        return $this;
    }

    public function getTotalDu(): ?int
    {
        return $this->TotalDu;
    }

    public function setTotalDu(int $TotalDu): static
    {
        $this->TotalDu = $TotalDu;

        return $this;
    }

    public function getTotalPaye(): ?int
    {
        return $this->TotalPaye;
    }

    public function setTotalPaye(int $TotalPaye): static
    {
        $this->TotalPaye = $TotalPaye;

        return $this;
    }

    /**
     * @return Collection<int, VersmtProprio>
     */
    public function getVersmtProprios(): Collection
    {
        return $this->versmtProprios;
    }

    public function addVersmtProprio(VersmtProprio $versmtProprio): static
    {
        if (!$this->versmtProprios->contains($versmtProprio)) {
            $this->versmtProprios->add($versmtProprio);
            $versmtProprio->setCompteProprio($this);
        }

        return $this;
    }

    public function removeVersmtProprio(VersmtProprio $versmtProprio): static
    {
        if ($this->versmtProprios->removeElement($versmtProprio)) {
            // set the owning side to null (unless already changed)
            if ($versmtProprio->getCompteProprio() === $this) {
                $versmtProprio->setCompteProprio(null);
            }
        }

        return $this;
    }
}
