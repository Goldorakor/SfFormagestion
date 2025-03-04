<?php

namespace App\Entity;

use App\Repository\FormateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormateurRepository::class)]
class Formateur
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

    #[ORM\Column(length: 20)]
    private ?string $tel = null;

    #[ORM\Column(length: 100)]
    private ?string $email = null;

    #[ORM\Column(length: 20)]
    private ?string $statut = null;

    #[ORM\ManyToOne(inversedBy: 'formateurs')]
    private ?Societe $societe = null;

    /**
     * @var Collection<int, Encadrement>
     */
    #[ORM\OneToMany(targetEntity: Encadrement::class, mappedBy: 'formateur')]
    private Collection $encadrements;

    public function __construct()
    {
        $this->encadrements = new ArrayCollection();
    }

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

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): static
    {
        $this->tel = $tel;

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

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

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

    /**
     * @return Collection<int, Encadrement>
     */
    public function getEncadrements(): Collection
    {
        return $this->encadrements;
    }

    public function addEncadrement(Encadrement $encadrement): static
    {
        if (!$this->encadrements->contains($encadrement)) {
            $this->encadrements->add($encadrement);
            $encadrement->setFormateur($this);
        }

        return $this;
    }

    public function removeEncadrement(Encadrement $encadrement): static
    {
        if ($this->encadrements->removeElement($encadrement)) {
            // set the owning side to null (unless already changed)
            if ($encadrement->getFormateur() === $this) {
                $encadrement->setFormateur(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->prenom." ".$this->nom;
    }
}
