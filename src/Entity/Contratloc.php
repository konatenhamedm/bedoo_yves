<?php

namespace App\Entity;

use App\Repository\ContratlocRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Nullable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContratlocRepository::class)]
class Contratloc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'Contratlocs')]
    private ?Locataire $locataire = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $DateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $DateFin = null;


    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: '1')]
    private ?int $NbMoisCaution = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: '0', nullable: true)]
    #[Assert\PositiveOrZero(message: 'Le montant de la caution doit être > 0 ', groups: ['niveau-frais'])]
    private ?string $MntCaution = null;



    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '1', nullable: true)]
    private ?int $NbMoisAvance = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: '0', nullable: true)]
    #[Assert\PositiveOrZero(message: 'Le montant avance doit être > 0 ', groups: ['niveau-frais'])]
    private ?string $MntAvance = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: '0', nullable: true)]
    private ?string $MntLoyer = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $AutreInfos = null;


    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?FichierAdmin $ScanContrat = null;



    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Le champs date debut est requis")]
    private ?string $Regime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $DateEntree = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $DateProchVers = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Nature = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: '0', nullable: true)]
    private ?string $MntLoyerPrec = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: '0', nullable: true)]
    private ?string $MntLoyerIni = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: '0', nullable: true)]
    private ?string $MntLoyerActu = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: '0', nullable: true)]
    private ?string $MntArriere = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $DejaLocataire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $StatutLoc = null;


    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: '0')]
    private ?string $Fraisanex = null;

    #[ORM\Column(nullable: true)]
    private ?int $Etat = null;

    #[ORM\Column(nullable: true)]
    private ?string $TotVerse = null;

    #[ORM\OneToMany(mappedBy: 'contrat', targetEntity: CompteLoc::class)]
    private Collection $compteLocs;

    #[ORM\OneToMany(mappedBy: 'contrat', targetEntity: Factureloc::class)]
    private Collection $facturelocs;

    #[ORM\OneToMany(mappedBy: 'contrat', targetEntity: Fincontrat::class)]
    private Collection $fincontrats;

    #[ORM\ManyToOne(inversedBy: 'contratlocs')]
    private ?Entreprise $entreprise = null;

    #[ORM\ManyToOne(inversedBy: 'contratlocs')]
    private ?Campagne $campagne = null;


    #[ORM\ManyToOne(inversedBy: 'appartContratlocs')]
    private ?Appartement $appart = null;

    #[ORM\OneToMany(mappedBy: 'contrat', targetEntity: CompteLocataire::class)]
    private Collection $compteLocataires;

    #[ORM\ManyToOne(inversedBy: 'contratlocs')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Motif $motif = null;


    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: '0', nullable: true)]
    private ?string $CautionRemise = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $details = null;

    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?FichierAdmin $FichierResiliation = null;



    public function __construct()
    {
        $this->compteLocs = new ArrayCollection();
        $this->facturelocs = new ArrayCollection();
        $this->fincontrats = new ArrayCollection();
        $this->compteLocataires = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
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



    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->DateDebut;
    }

    public function setDateDebut(\DateTimeInterface $DateDebut): static
    {
        $this->DateDebut = $DateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->DateFin;
    }

    public function setDateFin(\DateTimeInterface $DateFin): static
    {
        $this->DateFin = $DateFin;

        return $this;
    }

    public function getNbMoisCaution(): ?int
    {
        return $this->NbMoisCaution;
    }

    public function setNbMoisCaution(int $NbMoisCaution): static
    {
        $this->NbMoisCaution = $NbMoisCaution;

        return $this;
    }

    public function getMntCaution(): ?int
    {
        return $this->MntCaution;
    }

    public function setMntCaution(int $MntCaution): static
    {
        $this->MntCaution = $MntCaution;

        return $this;
    }

    public function getNbMoisAvance(): ?int
    {
        return $this->NbMoisAvance;
    }

    public function setNbMoisAvance(int $NbMoisAvance): static
    {
        $this->NbMoisAvance = $NbMoisAvance;

        return $this;
    }

    public function getMntAvance(): ?string
    {
        return $this->MntAvance;
    }

    public function setMntAvance(?string $MntAvance): static
    {
        $this->MntAvance = $MntAvance;

        return $this;
    }

    public function getMntLoyer(): ?string
    {
        return $this->MntLoyer;
    }

    public function setMntLoyer(?string $MntLoyer): static
    {
        $this->MntLoyer = $MntLoyer;

        return $this;
    }

    public function getAutreInfos(): ?string
    {
        return $this->AutreInfos;
    }

    public function setAutreInfos(?string $AutreInfos): static
    {
        $this->AutreInfos = $AutreInfos;

        return $this;
    }

    public function getScanContrat(): ?FichierAdmin
    {
        return $this->ScanContrat;
    }

    public function setScanContrat(FichierAdmin $ScanContrat): static
    {
        $this->ScanContrat = $ScanContrat;

        return $this;
    }

    public function getRegime(): ?string
    {
        return $this->Regime;
    }

    public function setRegime(?string $Regime): static
    {
        $this->Regime = $Regime;

        return $this;
    }

    public function getDateEntree(): ?\DateTimeInterface
    {
        return $this->DateEntree;
    }

    public function setDateEntree(\DateTimeInterface $DateEntree): static
    {
        $this->DateEntree = $DateEntree;

        return $this;
    }

    public function getDateProchVers(): ?\DateTimeInterface
    {
        return $this->DateProchVers;
    }

    public function setDateProchVers(\DateTimeInterface $DateProchVers): static
    {
        $this->DateProchVers = $DateProchVers;

        return $this;
    }

    public function getNature(): ?string
    {
        return $this->Nature;
    }

    public function setNature(?string $Nature): static
    {
        $this->Nature = $Nature;

        return $this;
    }

    public function getMntLoyerPrec(): ?string
    {
        return $this->MntLoyerPrec;
    }

    public function setMntLoyerPrec(?string $MntLoyerPrec): static
    {
        $this->MntLoyerPrec = $MntLoyerPrec;

        return $this;
    }

    public function getMntLoyerIni(): ?string
    {
        return $this->MntLoyerIni;
    }

    public function setMntLoyerIni(?string $MntLoyerIni): static
    {
        $this->MntLoyerIni = $MntLoyerIni;

        return $this;
    }

    public function getMntLoyerActu(): ?string
    {
        return $this->MntLoyerActu;
    }

    public function setMntLoyerActu(?string $MntLoyerActu): static
    {
        $this->MntLoyerActu = $MntLoyerActu;

        return $this;
    }

    public function getMntArriere(): ?string
    {
        return $this->MntArriere;
    }

    public function setMntArriere(?string $MntArriere): static
    {
        $this->MntArriere = $MntArriere;

        return $this;
    }

    public function getDejaLocataire(): ?string
    {
        return $this->DejaLocataire;
    }

    public function setDejaLocataire(?string $DejaLocataire): static
    {
        $this->DejaLocataire = $DejaLocataire;

        return $this;
    }

    public function getStatutLoc(): ?string
    {
        return $this->StatutLoc;
    }

    public function setStatutLoc(?string $StatutLoc): static
    {
        $this->StatutLoc = $StatutLoc;

        return $this;
    }

    public function getFraisanex(): ?string
    {
        return $this->Fraisanex;
    }

    public function setFraisanex(?string $Fraisanex): static
    {
        $this->Fraisanex = $Fraisanex;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->Etat;
    }

    public function setEtat(int $Etat): static
    {
        $this->Etat = $Etat;

        return $this;
    }

    public function getTotVerse(): ?string
    {
        return $this->TotVerse;
    }

    public function setTotVerse(?string $TotVerse): static
    {
        $this->TotVerse = $TotVerse;

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
            $compteLoc->setContrat($this);
        }

        return $this;
    }

    public function removeCompteLoc(CompteLoc $compteLoc): static
    {
        if ($this->compteLocs->removeElement($compteLoc)) {
            // set the owning side to null (unless already changed)
            if ($compteLoc->getContrat() === $this) {
                $compteLoc->setContrat(null);
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
            $factureloc->setContrat($this);
        }

        return $this;
    }

    public function removeFactureloc(Factureloc $factureloc): static
    {
        if ($this->facturelocs->removeElement($factureloc)) {
            // set the owning side to null (unless already changed)
            if ($factureloc->getContrat() === $this) {
                $factureloc->setContrat(null);
            }
        }

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
            $fincontrat->setContrat($this);
        }

        return $this;
    }

    public function removeFincontrat(Fincontrat $fincontrat): static
    {
        if ($this->fincontrats->removeElement($fincontrat)) {
            // set the owning side to null (unless already changed)
            if ($fincontrat->getContrat() === $this) {
                $fincontrat->setContrat(null);
            }
        }

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

    public function getCampagne(): ?Campagne
    {
        return $this->campagne;
    }

    public function setCampagne(?Campagne $campagne): static
    {
        $this->campagne = $campagne;

        return $this;
    }

    public function getAppart(): ?Appartement
    {
        return $this->appart;
    }

    public function setAppart(?Appartement $appart): static
    {
        $this->appart = $appart;

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
            $compteLocataire->setContrat($this);
        }

        return $this;
    }

    public function removeCompteLocataire(CompteLocataire $compteLocataire): static
    {
        if ($this->compteLocataires->removeElement($compteLocataire)) {
            // set the owning side to null (unless already changed)
            if ($compteLocataire->getContrat() === $this) {
                $compteLocataire->setContrat(null);
            }
        }

        return $this;
    }

    public function getMotif(): ?Motif
    {
        return $this->motif;
    }

    public function setMotif(?Motif $motif): static
    {
        $this->motif = $motif;

        return $this;
    }

    public function getCautionRemise(): ?string
    {
        return $this->CautionRemise;
    }

    public function setCautionRemise(string $CautionRemise): static
    {
        $this->CautionRemise = $CautionRemise;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(string $details): static
    {
        $this->details = $details;

        return $this;
    }

    public function getFichierResiliation(): ?FichierAdmin
    {
        return $this->FichierResiliation;
    }

    public function setFichierResiliation(?FichierAdmin $FichierResiliation): static
    {
        $this->FichierResiliation = $FichierResiliation;

        return $this;
    }
}
