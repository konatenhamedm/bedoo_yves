<?php

namespace App\Entity;

use App\Repository\MotifRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MotifRepository::class)]
class Motif
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $LibMotif = null;

    #[ORM\OneToMany(mappedBy: 'motif', targetEntity: Fincontrat::class)]
    private Collection $fincontrats;

    #[ORM\OneToMany(mappedBy: 'motif', targetEntity: Contratloc::class)]
    private Collection $contratlocs;

    public function __construct()
    {
        $this->fincontrats = new ArrayCollection();
        $this->contratlocs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibMotif(): ?string
    {
        return $this->LibMotif;
    }

    public function setLibMotif(string $LibMotif): static
    {
        $this->LibMotif = $LibMotif;

        return $this;
    }

    /**
     * @return Collection<int, Fincontrat>
     */
    public function getFincontrats(): Collection
    {
        return $this->fincontrats;
    }

    public function addFincontrat(Fincontrat $fincontrat): static
    {
        if (!$this->fincontrats->contains($fincontrat)) {
            $this->fincontrats->add($fincontrat);
            $fincontrat->setMotif($this);
        }

        return $this;
    }

    public function removeFincontrat(Fincontrat $fincontrat): static
    {
        if ($this->fincontrats->removeElement($fincontrat)) {
            // set the owning side to null (unless already changed)
            if ($fincontrat->getMotif() === $this) {
                $fincontrat->setMotif(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Contratloc>
     */
    public function getContratlocs(): Collection
    {
        return $this->contratlocs;
    }

    public function addContratloc(Contratloc $contratloc): static
    {
        if (!$this->contratlocs->contains($contratloc)) {
            $this->contratlocs->add($contratloc);
            $contratloc->setMotif($this);
        }

        return $this;
    }

    public function removeContratloc(Contratloc $contratloc): static
    {
        if ($this->contratlocs->removeElement($contratloc)) {
            // set the owning side to null (unless already changed)
            if ($contratloc->getMotif() === $this) {
                $contratloc->setMotif(null);
            }
        }

        return $this;
    }
}
