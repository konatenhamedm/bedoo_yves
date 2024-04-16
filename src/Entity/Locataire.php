<?php

namespace App\Entity;

use App\Repository\LocataireRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Nullable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LocataireRepository::class)]
class Locataire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $NPrenoms = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: "Le champs date de naissance est requis")]
    private ?DateTimeInterface $DateNaiss = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Le champs  lieu de naissance est requis")]
    private ?string $LieuNaiss = null;


    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?FichierAdmin $InfoPiece = null;


    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Le champs profession est requis")]
    private ?string $Profession = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Ethnie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $NbEnfts = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $NbPersChge = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Pere = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Mere = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Le champs contact est requis")]
    private ?string $Contacts = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $NPConjointe = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ProfConj = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $EthnieConj = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ContactConj = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Le champs genre est requis")]
    private ?string $Genre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $VivezAvec = null;

    #[ORM\ManyToOne(inversedBy: 'locataires')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Sitmatri $situationMatri = null;

    #[ORM\OneToMany(mappedBy: 'locataire', targetEntity: Contratloc::class)]
    private Collection $Contratlocs;

    #[ORM\OneToMany(mappedBy: 'locataire', targetEntity: CompteLoc::class)]
    private Collection $compteLocs;

    #[ORM\OneToMany(mappedBy: 'locataire', targetEntity: Factureloc::class)]
    private Collection $facturelocs;

    #[ORM\ManyToOne(inversedBy: 'locataires')]
    private ?Entreprise $entreprise = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Le champs numéro de pièce est requis")]
    private ?string $numpiece = null;

    #[ORM\OneToMany(mappedBy: 'locataire', targetEntity: CompteLocataire::class)]
    private Collection $compteLocataires;

    #[ORM\OneToMany(mappedBy: 'locataire', targetEntity: VersmtProprio::class)]
    private Collection $versmtProprios;

    #[ORM\Column(length: 255)]
    private ?string $soldeRestant = null;

    #[ORM\OneToMany(mappedBy: 'locataire', targetEntity: CampagneContrat::class)]
    private Collection $campagneContrats;

    public function __construct()
    {
        $this->Contratlocs = new ArrayCollection();
        $this->compteLocs = new ArrayCollection();
        $this->facturelocs = new ArrayCollection();
        $this->compteLocataires = new ArrayCollection();
        $this->versmtProprios = new ArrayCollection();
        $this->soldeRestant = 0;
        $this->campagneContrats = new ArrayCollection();
    }

    public function getInfo()
    {
        $array = [];
        foreach ($this->getContratlocs() as $key => $value) {
            if ($value->getEtat() == 1) {
                $array = $value;
            }
        }
        //dd($array);
        return $array;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNPrenoms(): ?string
    {
        return $this->NPrenoms;
    }

    public function setNPrenoms(string $NPrenoms): static
    {
        $this->NPrenoms = $NPrenoms;

        return $this;
    }

    public function getDateNaiss(): ?\DateTimeInterface
    {
        return $this->DateNaiss;
    }

    public function setDateNaiss(\DateTimeInterface $DateNaiss): static
    {
        $this->DateNaiss = $DateNaiss;

        return $this;
    }

    public function getLieuNaiss(): ?string
    {
        return $this->LieuNaiss;
    }

    public function setLieuNaiss(string $LieuNaiss): static
    {
        $this->LieuNaiss = $LieuNaiss;

        return $this;
    }

    public function getInfoPiece(): ?FichierAdmin
    {
        return $this->InfoPiece;
    }

    public function setInfoPiece(FichierAdmin $InfoPiece): static
    {
        $this->InfoPiece = $InfoPiece;

        return $this;
    }



    public function getProfession(): ?string
    {
        return $this->Profession;
    }

    public function setProfession(string $Profession): static
    {
        $this->Profession = $Profession;

        return $this;
    }

    public function getEthnie(): ?string
    {
        return $this->Ethnie;
    }

    public function setEthnie(string $Ethnie): static
    {
        $this->Ethnie = $Ethnie;

        return $this;
    }

    public function getNbEnfts(): ?string
    {
        return $this->NbEnfts;
    }

    public function setNbEnfts(string $NbEnfts): static
    {
        $this->NbEnfts = $NbEnfts;

        return $this;
    }

    public function getNbPersChge(): ?string
    {
        return $this->NbPersChge;
    }

    public function setNbPersChge(string $NbPersChge): static
    {
        $this->NbPersChge = $NbPersChge;

        return $this;
    }

    public function getPere(): ?string
    {
        return $this->Pere;
    }

    public function setPere(string $Pere): static
    {
        $this->Pere = $Pere;

        return $this;
    }

    public function getMere(): ?string
    {
        return $this->Mere;
    }

    public function setMere(string $Mere): static
    {
        $this->Mere = $Mere;

        return $this;
    }

    public function getContacts(): ?string
    {
        return $this->Contacts;
    }

    public function setContacts(string $Contacts): static
    {
        $this->Contacts = $Contacts;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): static
    {
        $this->Email = $Email;

        return $this;
    }

    public function getNPConjointe(): ?string
    {
        return $this->NPConjointe;
    }

    public function setNPConjointe(string $NPConjointe): static
    {
        $this->NPConjointe = $NPConjointe;

        return $this;
    }

    public function getProfConj(): ?string
    {
        return $this->ProfConj;
    }

    public function setProfConj(string $ProfConj): static
    {
        $this->ProfConj = $ProfConj;

        return $this;
    }

    public function getEthnieConj(): ?string
    {
        return $this->EthnieConj;
    }

    public function setEthnieConj(string $EthnieConj): static
    {
        $this->EthnieConj = $EthnieConj;

        return $this;
    }

    public function getContactConj(): ?string
    {
        return $this->ContactConj;
    }

    public function setContactConj(string $ContactConj): static
    {
        $this->ContactConj = $ContactConj;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->Genre;
    }

    public function setGenre(string $Genre): static
    {
        $this->Genre = $Genre;

        return $this;
    }

    public function getVivezAvec(): ?string
    {
        return $this->VivezAvec;
    }

    public function setVivezAvec(string $VivezAvec): static
    {
        $this->VivezAvec = $VivezAvec;

        return $this;
    }

    public function getSituationMatri(): ?Sitmatri
    {
        return $this->situationMatri;
    }

    public function setSituationMatri(?Sitmatri $situationMatri): static
    {
        $this->situationMatri = $situationMatri;

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
            $Contratloc->setLocataire($this);
        }

        return $this;
    }

    public function removeContratloc(Contratloc $Contratloc): static
    {
        if ($this->Contratlocs->removeElement($Contratloc)) {
            // set the owning side to null (unless already changed)
            if ($Contratloc->getLocataire() === $this) {
                $Contratloc->setLocataire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CompteLoc>
     */
    public function getCompteLocs(): Collection
    {
        return $this->compteLocs;
    }

    public function addCompteLoc(CompteLoc $compteLoc): static
    {
        if (!$this->compteLocs->contains($compteLoc)) {
            $this->compteLocs->add($compteLoc);
            $compteLoc->setLocataire($this);
        }

        return $this;
    }

    public function removeCompteLoc(CompteLoc $compteLoc): static
    {
        if ($this->compteLocs->removeElement($compteLoc)) {
            // set the owning side to null (unless already changed)
            if ($compteLoc->getLocataire() === $this) {
                $compteLoc->setLocataire(null);
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
            $factureloc->setLocataire($this);
        }

        return $this;
    }

    public function removeFactureloc(Factureloc $factureloc): static
    {
        if ($this->facturelocs->removeElement($factureloc)) {
            // set the owning side to null (unless already changed)
            if ($factureloc->getLocataire() === $this) {
                $factureloc->setLocataire(null);
            }
        }

        return $this;
    }


    public function getNumpiece(): ?string
    {
        return $this->numpiece;
    }

    public function setNumpiece(string $numpiece): static
    {
        $this->numpiece = $numpiece;

        return $this;
    }
    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprise $entreprise): static
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    /**
     * @return Collection<int, CompteLocataire>
     */
    public function getCompteLocataires(): Collection
    {
        return $this->compteLocataires;
    }

    public function addCompteLocataire(CompteLocataire $compteLocataire): static
    {
        if (!$this->compteLocataires->contains($compteLocataire)) {
            $this->compteLocataires->add($compteLocataire);
            $compteLocataire->setLocataire($this);
        }

        return $this;
    }

    public function removeCompteLocataire(CompteLocataire $compteLocataire): static
    {
        if ($this->compteLocataires->removeElement($compteLocataire)) {
            // set the owning side to null (unless already changed)
            if ($compteLocataire->getLocataire() === $this) {
                $compteLocataire->setLocataire(null);
            }
        }

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
            $versmtProprio->setLocataire($this);
        }

        return $this;
    }

    public function removeVersmtProprio(VersmtProprio $versmtProprio): static
    {
        if ($this->versmtProprios->removeElement($versmtProprio)) {
            // set the owning side to null (unless already changed)
            if ($versmtProprio->getLocataire() === $this) {
                $versmtProprio->setLocataire(null);
            }
        }

        return $this;
    }

    public function getSoldeRestant(): ?string
    {
        return $this->soldeRestant;
    }

    public function setSoldeRestant(string $soldeRestant): static
    {
        $this->soldeRestant = $soldeRestant;

        return $this;
    }

    /**
     * @return Collection<int, CampagneContrat>
     */
    public function getCampagneContrats(): Collection
    {
        return $this->campagneContrats;
    }

    public function addCampagneContrat(CampagneContrat $campagneContrat): static
    {
        if (!$this->campagneContrats->contains($campagneContrat)) {
            $this->campagneContrats->add($campagneContrat);
            $campagneContrat->setLocataire($this);
        }

        return $this;
    }

    public function removeCampagneContrat(CampagneContrat $campagneContrat): static
    {
        if ($this->campagneContrats->removeElement($campagneContrat)) {
            // set the owning side to null (unless already changed)
            if ($campagneContrat->getLocataire() === $this) {
                $campagneContrat->setLocataire(null);
            }
        }

        return $this;
    }
}
