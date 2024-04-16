<?php

namespace App\Entity;

use App\Repository\VersmtProprioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VersmtProprioRepository::class)]
class VersmtProprio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\ManyToOne(inversedBy: 'versmtProprios')]
    private ?Proprio $Proprio = null;

    #[ORM\ManyToOne(inversedBy: 'versmtProprios')]
    private ?TypeVersements $type_versement = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateVersement = null;


    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: '0')]
    private ?string $montant = null;

    #[ORM\ManyToOne(inversedBy: 'versmtProprios')]
    private ?Locataire $locataire = null;

    #[ORM\Column(length: 255)]
    private ?string $numero = null;

    #[ORM\ManyToOne(inversedBy: 'versmtProprios')]
    private ?Maison $maison = null;

    #[ORM\Column(length: 10)]
    private ?string $numeroRecu = null;

    #[ORM\OneToMany(mappedBy: 'versement', targetEntity: Factureloc::class)]
    private Collection $facturelocs;

    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?FichierAdmin $preuve = null;

    public function __construct()
    {
        $this->facturelocs = new ArrayCollection();
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

    public function getTypeVersement(): ?TypeVersements
    {
        return $this->type_versement;
    }

    public function setTypeVersement(?TypeVersements $type_versement): static
    {
        $this->type_versement = $type_versement;

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

    public function getDateVersement(): ?\DateTimeInterface
    {
        return $this->dateVersement;
    }

    public function setDateVersement(\DateTimeInterface $dateVersement): static
    {
        $this->dateVersement = $dateVersement;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): static
    {
        $this->montant = $montant;

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

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
    {
        $this->numero = $numero;

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

    public function getNumeroRecu(): ?string
    {
        return $this->numeroRecu;
    }

    public function setNumeroRecu(string $numeroRecu): static
    {
        $this->numeroRecu = $numeroRecu;

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
            $factureloc->setVersement($this);
        }

        return $this;
    }

    public function removeFactureloc(Factureloc $factureloc): static
    {
        if ($this->facturelocs->removeElement($factureloc)) {
            // set the owning side to null (unless already changed)
            if ($factureloc->getVersement() === $this) {
                $factureloc->setVersement(null);
            }
        }

        return $this;
    }

    public function getPreuve(): ?FichierAdmin
    {
        return $this->preuve;
    }

    public function setPreuve(FichierAdmin $preuve): static
    {
        $this->preuve = $preuve;

        return $this;
    }
}
