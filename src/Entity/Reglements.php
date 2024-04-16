<?php

namespace App\Entity;

use App\Repository\ReglementsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReglementsRepository::class)]
class Reglements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reglements')]
    private ?Factureloc $numFact = null;

    #[ORM\ManyToOne(inversedBy: 'reglements')]
    private ?Fournisseurs $fournisseur = null;

    #[ORM\Column]
    private ?int $MontantVerse = null;

    #[ORM\Column]
    private ?int $date = null;

    #[ORM\Column(length: 255)]
    private ?string $numchq = null;

    #[ORM\ManyToOne(inversedBy: 'reglements')]
    private ?TypeVersements $typeversement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumFact(): ?factureloc
    {
        return $this->numFact;
    }

    public function setNumFact(?factureloc $numFact): static
    {
        $this->numFact = $numFact;

        return $this;
    }

    public function getFournisseur(): ?Fournisseurs
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?Fournisseurs $fournisseur): static
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    public function getMontantVerse(): ?int
    {
        return $this->MontantVerse;
    }

    public function setMontantVerse(int $MontantVerse): static
    {
        $this->MontantVerse = $MontantVerse;

        return $this;
    }

    public function getDate(): ?int
    {
        return $this->date;
    }

    public function setDate(int $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getNumchq(): ?string
    {
        return $this->numchq;
    }

    public function setNumchq(string $numchq): static
    {
        $this->numchq = $numchq;

        return $this;
    }

    public function getTypeversement(): ?TypeVersements
    {
        return $this->typeversement;
    }

    public function setTypeversement(?TypeVersements $typeversement): static
    {
        $this->typeversement = $typeversement;

        return $this;
    }
}
