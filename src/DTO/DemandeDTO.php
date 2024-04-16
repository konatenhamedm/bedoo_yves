<?php

namespace App\DTO;

use App\Entity\Civilite;
use App\Entity\Filiere;
use App\Entity\Genre;
use App\Entity\Niveau;
use App\Entity\Pays;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;

class DemandeDTO
{
    #[Assert\NotBlank(message: 'Veuillez renseigner email')]
    private ?string $email = null;

    #[Assert\NotBlank(message: 'Veuillez renseigner votre denomination')]
    private ?string $denomination;

    #[Assert\NotBlank(message: 'Veuillez renseigner votre contact')]
    private ?string $contact;


    #[Assert\NotBlank(message: 'Veuillez renseigner votre adresse')]
    private ?string $adresse;

    #[Assert\NotBlank(message: 'Veuillez renseigner votre ville')]
    private ?string $ville;
    #[Assert\NotBlank(message: 'Veuillez renseigner votre siteWeb')]
    private ?string $siteWeb;
    #[Assert\NotBlank(message: 'Veuillez renseigner votre siteWeb')]
    private ?Pays $pays;

    /**
     * Get the value of email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of denomination
     */
    public function getDenomination()
    {
        return $this->denomination;
    }

    /**
     * Set the value of denomination
     *
     * @return  self
     */
    public function setDenomination(?string $denomination): self
    {
        $this->denomination = $denomination;

        return $this;
    }

    /**
     * Get the value of contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set the value of contact
     *
     * @return  self
     */
    public function setContact(?string $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get the value of adresse
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set the value of adresse
     *
     * @return  self
     */
    public function setAdresse(?string $adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get the value of ville
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set the value of ville
     *
     * @return  self
     */
    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get the value of siteWeb
     */
    public function getSiteWeb()
    {
        return $this->siteWeb;
    }

    /**
     * Set the value of siteWeb
     *
     * @return  self
     */
    public function setSiteWeb(?string $siteWeb): self
    {
        $this->siteWeb = $siteWeb;

        return $this;
    }

    /**
     * Get the value of pays
     */
    public function getPays(): ?Pays
    {
        return $this->pays;
    }

    /**
     * Set the value of pays
     *
     * @return  self
     */
    public function setPays(?Pays $pays): self
    {
        $this->pays = $pays;

        return $this;
    }
}
