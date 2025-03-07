<?php

namespace App\Entity;

use App\Repository\RepresentantRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RepresentantRepository::class)]
class Representant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(length: 20)]
    private ?string $sexe = null;

    #[ORM\Column(length: 50)]
    private ?string $fonction = null;

    #[ORM\Column(length: 255)]
    private ?string $tamponFilename = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(string $fonction): static
    {
        $this->fonction = $fonction;

        return $this;
    }

    public function getTamponFilename(): ?string
    {
        return $this->tamponFilename;
    }

    public function setTamponFilename(string $tamponFilename): static
    {
        $this->tamponFilename = $tamponFilename;

        return $this;
    }

    public function __toString()
    {
        return $this->prenom." ".$this->nom;
    }
}
