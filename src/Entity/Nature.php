<?php

namespace App\Entity;

use App\Repository\NatureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NatureRepository::class)]
class Nature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $LibNature = null;

    #[ORM\OneToMany(mappedBy: 'Nature', targetEntity: Contratloc::class)]
    private Collection $Contratlocs;

    public function __construct()
    {
        $this->Contratlocs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibNature(): ?string
    {
        return $this->LibNature;
    }

    public function setLibNature(string $LibNature): static
    {
        $this->LibNature = $LibNature;

        return $this;
    }

    /**
     * @return Collection<int, Contratloc>
     */
    public function getContratlocs(): Collection
    {
        return $this->Contratlocs;
    }

    public function addContratloc(Contratloc $Contratloc): static
    {
        if (!$this->Contratlocs->contains($Contratloc)) {
            $this->Contratlocs->add($Contratloc);
            $Contratloc->setNature($this);
        }

        return $this;
    }

    public function removeContratloc(Contratloc $Contratloc): static
    {
        if ($this->Contratlocs->removeElement($Contratloc)) {
            // set the owning side to null (unless already changed)
            if ($Contratloc->getNature() === $this) {
                $Contratloc->setNature(null);
            }
        }

        return $this;
    }
}
