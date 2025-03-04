<?php

namespace App\Entity;

use App\Repository\ResponsableRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResponsableRepository::class)]
class Responsable
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

    #[ORM\Column(length: 50)]
    private ?string $fonction = null;

    /**
     * @var Collection<int, Responsabilite>
     */
    #[ORM\OneToMany(targetEntity: Responsabilite::class, mappedBy: 'responsable')]
    private Collection $responsabilites;

    public function __construct()
    {
        $this->responsabilites = new ArrayCollection();
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

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(string $fonction): static
    {
        $this->fonction = $fonction;

        return $this;
    }

    /**
     * @return Collection<int, Responsabilite>
     */
    public function getResponsabilites(): Collection
    {
        return $this->responsabilites;
    }

    public function addResponsabilite(Responsabilite $responsabilite): static
    {
        if (!$this->responsabilites->contains($responsabilite)) {
            $this->responsabilites->add($responsabilite);
            $responsabilite->setResponsable($this);
        }

        return $this;
    }

    public function removeResponsabilite(Responsabilite $responsabilite): static
    {
        if ($this->responsabilites->removeElement($responsabilite)) {
            // set the owning side to null (unless already changed)
            if ($responsabilite->getResponsable() === $this) {
                $responsabilite->setResponsable(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->prenom." ".$this->nom;
    }
}
