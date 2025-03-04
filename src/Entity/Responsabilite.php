<?php

namespace App\Entity;

use App\Repository\ResponsabiliteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResponsabiliteRepository::class)]
class Responsabilite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $type_responsable = null;

    #[ORM\ManyToOne(inversedBy: 'responsabilites')]
    private ?Responsable $responsable = null;

    #[ORM\ManyToOne(inversedBy: 'responsabilites')]
    private ?Societe $societe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeResponsable(): ?string
    {
        return $this->type_responsable;
    }

    public function setTypeResponsable(string $type_responsable): static
    {
        $this->type_responsable = $type_responsable;

        return $this;
    }

    public function getResponsable(): ?Responsable
    {
        return $this->responsable;
    }

    public function setResponsable(?Responsable $responsable): static
    {
        $this->responsable = $responsable;

        return $this;
    }

    public function getSociete(): ?Societe
    {
        return $this->societe;
    }

    public function setSociete(?Societe $societe): static
    {
        $this->societe = $societe;

        return $this;
    }

    public function __toString()
    {
        return $this->responsable->prenom." ".$this->responsable->nom." : référent ".$this->type_referent;
    }
}
