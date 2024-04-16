<?php

namespace App\Entity;

use App\Repository\SessionsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionsRepository::class)]
class Sessions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $EXPIRE = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $DONNEES = null;

    #[ORM\Column(length: 255)]
    private ?string $SAVE = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEXPIRE(): ?int
    {
        return $this->EXPIRE;
    }

    public function setEXPIRE(int $EXPIRE): static
    {
        $this->EXPIRE = $EXPIRE;

        return $this;
    }

    public function getDONNEES(): ?string
    {
        return $this->DONNEES;
    }

    public function setDONNEES(string $DONNEES): static
    {
        $this->DONNEES = $DONNEES;

        return $this;
    }

    public function getSAVE(): ?string
    {
        return $this->SAVE;
    }

    public function setSAVE(string $SAVE): static
    {
        $this->SAVE = $SAVE;

        return $this;
    }
}
