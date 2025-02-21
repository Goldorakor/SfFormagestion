<?php

namespace App\Entity;

use App\Repository\EncadrementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EncadrementRepository::class)]
class Encadrement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $type_referent = null;

    #[ORM\ManyToOne(inversedBy: 'encadrements')]
    private ?Formateur $formateur = null;

    #[ORM\ManyToOne(inversedBy: 'encadrements')]
    private ?Session $session = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeReferent(): ?string
    {
        return $this->type_referent;
    }

    public function setTypeReferent(string $type_referent): static
    {
        $this->type_referent = $type_referent;

        return $this;
    }

    public function getFormateur(): ?Formateur
    {
        return $this->formateur;
    }

    public function setFormateur(?Formateur $formateur): static
    {
        $this->formateur = $formateur;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): static
    {
        $this->session = $session;

        return $this;
    }

    public function __toString()
    {
        return $this->formateur->prenom." ".$this->formateur->nom." : ".$this->type_referent;
    }
}
