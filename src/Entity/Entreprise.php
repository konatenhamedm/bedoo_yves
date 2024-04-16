<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
#[ORM\Table(name: '_admin_param_entreprise')]
class Entreprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $denomination = null;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: Employe::class)]
    private Collection $employes;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: ConfigApp::class)]
    private Collection $configApps;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: Proprio::class)]
    private Collection $Proprios;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Sigle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Agrements = null;


    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $situation_geo = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Le champs  contact  est requis")]
    private ?string $contacts = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mobile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fax = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Le champs  email  est requis")]
    private ?string $email = null;

    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?FichierAdmin $logo = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $site_web = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotNull(message: "Le champs  directeur  est requis")]
    private ?string $Directeur = null;



    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Le champs  ville  est requis")]
    private ?string $ville = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;


    #[ORM\ManyToOne(inversedBy: 'entreprises')]
    private ?Pays $pays = null;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: Fonction::class)]
    private Collection $fonctions;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: Locataire::class)]
    private Collection $locataires;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: Contratloc::class)]
    private Collection $contratlocs;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: Campagne::class)]
    private Collection $campagnes;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: Quartier::class)]
    private Collection $quartiers;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: JoursMoisEntreprise::class)]
    private Collection $joursMoisEntreprises;


    public function __construct()
    {
        $this->employes = new ArrayCollection();
        $this->configApps = new ArrayCollection();
        $this->Proprios = new ArrayCollection();
        $this->groupes = new ArrayCollection();
        $this->fonctions = new ArrayCollection();
        $this->locataires = new ArrayCollection();
        $this->contratlocs = new ArrayCollection();
        $this->campagnes = new ArrayCollection();
        $this->quartiers = new ArrayCollection();
        $this->joursMoisEntreprises = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDenomination(): ?string
    {
        return $this->denomination;
    }

    public function setDenomination(string $denomination): self
    {
        $this->denomination = $denomination;

        return $this;
    }

    /**
     * @return Collection<int, Employe>
     */
    public function getEmployes(): Collection
    {
        return $this->employes;
    }

    public function addEmploye(Employe $employe): self
    {
        if (!$this->employes->contains($employe)) {
            $this->employes->add($employe);
            $employe->setEntreprise($this);
        }

        return $this;
    }

    public function removeEmploye(Employe $employe): self
    {
        if ($this->employes->removeElement($employe)) {
            // set the owning side to null (unless already changed)
            if ($employe->getEntreprise() === $this) {
                $employe->setEntreprise(null);
            }
        }

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, ConfigApp>
     */
    public function getConfigApps(): Collection
    {
        return $this->configApps;
    }

    public function addConfigApp(ConfigApp $configApp): static
    {
        if (!$this->configApps->contains($configApp)) {
            $this->configApps->add($configApp);
            $configApp->setEntreprise($this);
        }

        return $this;
    }

    public function removeConfigApp(ConfigApp $configApp): static
    {
        if ($this->configApps->removeElement($configApp)) {
            // set the owning side to null (unless already changed)
            if ($configApp->getEntreprise() === $this) {
                $configApp->setEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Proprio>
     */
    public function getProprios(): Collection
    {
        return $this->Proprios;
    }

    public function addProprio(Proprio $Proprio): static
    {
        if (!$this->Proprios->contains($Proprio)) {
            $this->Proprios->add($Proprio);
            $Proprio->setEntreprise($this);
        }

        return $this;
    }

    public function removeProprio(Proprio $Proprio): static
    {
        if ($this->Proprios->removeElement($Proprio)) {
            // set the owning side to null (unless already changed)
            if ($Proprio->getEntreprise() === $this) {
                $Proprio->setEntreprise(null);
            }
        }

        return $this;
    }


    public function getSigle(): ?string
    {
        return $this->Sigle;
    }

    public function setSigle(string $Sigle): static
    {
        $this->Sigle = $Sigle;

        return $this;
    }

    public function getAgrements(): ?string
    {
        return $this->Agrements;
    }

    public function setAgrements(string $Agrements): static
    {
        $this->Agrements = $Agrements;

        return $this;
    }


    public function getSituationGeo(): ?string
    {
        return $this->situation_geo;
    }

    public function setSituationGeo(string $situation_geo): static
    {
        $this->situation_geo = $situation_geo;

        return $this;
    }

    public function getContacts(): ?string
    {
        return $this->contacts;
    }

    public function setContacts(string $contacts): static
    {
        $this->contacts = $contacts;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }



    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): static
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(string $fax): static
    {
        $this->fax = $fax;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getLogo(): ?FichierAdmin
    {
        return $this->logo;
    }

    public function setLogo(FichierAdmin $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getSiteWeb(): ?string
    {
        return $this->site_web;
    }

    public function setSiteWeb(string $site_web): static
    {
        $this->site_web = $site_web;

        return $this;
    }

    public function getDirecteur(): ?string
    {
        return $this->Directeur;
    }

    public function setDirecteur(string $Directeur): static
    {
        $this->Directeur = $Directeur;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }



    public function getPays(): ?Pays
    {
        return $this->pays;
    }

    public function setPays(?Pays $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * @return Collection<int, Fonction>
     */
    public function getFonctions(): Collection
    {
        return $this->fonctions;
    }

    public function addFonction(Fonction $fonction): static
    {
        if (!$this->fonctions->contains($fonction)) {
            $this->fonctions->add($fonction);
            $fonction->setEntreprise($this);
        }

        return $this;
    }

    public function removeFonction(Fonction $fonction): static
    {
        if ($this->fonctions->removeElement($fonction)) {
            // set the owning side to null (unless already changed)
            if ($fonction->getEntreprise() === $this) {
                $fonction->setEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Locataire>
     */
    public function getLocataires(): Collection
    {
        return $this->locataires;
    }

    public function addLocataire(Locataire $locataire): static
    {
        if (!$this->locataires->contains($locataire)) {
            $this->locataires->add($locataire);
            $locataire->setEntreprise($this);
        }

        return $this;
    }

    public function removeLocataire(Locataire $locataire): static
    {
        if ($this->locataires->removeElement($locataire)) {
            // set the owning side to null (unless already changed)
            if ($locataire->getEntreprise() === $this) {
                $locataire->setEntreprise(null);
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
            $contratloc->setEntreprise($this);
        }

        return $this;
    }

    public function removeContratloc(Contratloc $contratloc): static
    {
        if ($this->contratlocs->removeElement($contratloc)) {
            // set the owning side to null (unless already changed)
            if ($contratloc->getEntreprise() === $this) {
                $contratloc->setEntreprise(null);
            }
        }

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
            $campagne->setEntreprise($this);
        }

        return $this;
    }

    public function removeCampagne(Campagne $campagne): static
    {
        if ($this->campagnes->removeElement($campagne)) {
            // set the owning side to null (unless already changed)
            if ($campagne->getEntreprise() === $this) {
                $campagne->setEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Quartier>
     */
    public function getQuartiers(): Collection
    {
        return $this->quartiers;
    }

    public function addQuartier(Quartier $quartier): static
    {
        if (!$this->quartiers->contains($quartier)) {
            $this->quartiers->add($quartier);
            $quartier->setEntreprise($this);
        }

        return $this;
    }

    public function removeQuartier(Quartier $quartier): static
    {
        if ($this->quartiers->removeElement($quartier)) {
            // set the owning side to null (unless already changed)
            if ($quartier->getEntreprise() === $this) {
                $quartier->setEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, JoursMoisEntreprise>
     */
    public function getJoursMoisEntreprises(): Collection
    {
        return $this->joursMoisEntreprises;
    }

    public function addJoursMoisEntreprise(JoursMoisEntreprise $joursMoisEntreprise): static
    {
        if (!$this->joursMoisEntreprises->contains($joursMoisEntreprise)) {
            $this->joursMoisEntreprises->add($joursMoisEntreprise);
            $joursMoisEntreprise->setEntreprise($this);
        }

        return $this;
    }

    public function removeJoursMoisEntreprise(JoursMoisEntreprise $joursMoisEntreprise): static
    {
        if ($this->joursMoisEntreprises->removeElement($joursMoisEntreprise)) {
            // set the owning side to null (unless already changed)
            if ($joursMoisEntreprise->getEntreprise() === $this) {
                $joursMoisEntreprise->setEntreprise(null);
            }
        }

        return $this;
    }
}
