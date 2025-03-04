<?php

namespace App\Entity;

use App\Repository\SocieteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SocieteRepository::class)]
class Societe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $raisonSociale = null;

    #[ORM\Column(length: 20)]
    private ?string $statutJuri = null;

    #[ORM\Column(length: 100)]
    private ?string $adresseSiege = null;

    #[ORM\Column(length: 20)]
    private ?string $codePostalSiege = null;

    #[ORM\Column(length: 50)]
    private ?string $villeSiege = null;

    #[ORM\Column(length: 50)]
    private ?string $paysSiege = null;

    #[ORM\Column(length: 100)]
    private ?string $adresseFac = null;

    #[ORM\Column(length: 20)]
    private ?string $codePostalFac = null;

    #[ORM\Column(length: 50)]
    private ?string $villeFac = null;

    #[ORM\Column(length: 50)]
    private ?string $paysFac = null;

    #[ORM\Column(length: 20)]
    private ?string $tel = null;

    #[ORM\Column(length: 100)]
    private ?string $email = null;

    #[ORM\Column(length: 20)]
    private ?string $numSiret = null;

    /**
     * @var Collection<int, Apprenant>
     */
    #[ORM\OneToMany(targetEntity: Apprenant::class, mappedBy: 'societe')]
    #[ORM\OrderBy(["nom"=>"ASC"])]  // ajout du formateur -> permet de trier la collection d'apprenants de la société par ordre alphabétique
    private Collection $apprenants;

    /**
     * @var Collection<int, Formateur>
     */
    #[ORM\OneToMany(targetEntity: Formateur::class, mappedBy: 'societe')]
    private Collection $formateurs;

    /**
     * @var Collection<int, Responsabilite>
     */
    #[ORM\OneToMany(targetEntity: Responsabilite::class, mappedBy: 'societe')]
    private Collection $responsabilites;

    public function __construct()
    {
        $this->apprenants = new ArrayCollection();
        $this->formateurs = new ArrayCollection();
        $this->responsabilites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaisonSociale(): ?string
    {
        return $this->raisonSociale;
    }

    public function setRaisonSociale(string $raisonSociale): static
    {
        $this->raisonSociale = $raisonSociale;

        return $this;
    }

    public function getStatutJuri(): ?string
    {
        return $this->statutJuri;
    }

    public function setStatutJuri(string $statutJuri): static
    {
        $this->statutJuri = $statutJuri;

        return $this;
    }

    public function getAdresseSiege(): ?string
    {
        return $this->adresseSiege;
    }

    public function setAdresseSiege(string $adresseSiege): static
    {
        $this->adresseSiege = $adresseSiege;

        return $this;
    }

    public function getCodePostalSiege(): ?string
    {
        return $this->codePostalSiege;
    }

    public function setCodePostalSiege(string $codePostalSiege): static
    {
        $this->codePostalSiege = $codePostalSiege;

        return $this;
    }

    public function getVilleSiege(): ?string
    {
        return $this->villeSiege;
    }

    public function setVilleSiege(string $villeSiege): static
    {
        $this->villeSiege = $villeSiege;

        return $this;
    }

    public function getPaysSiege(): ?string
    {
        return $this->paysSiege;
    }

    public function setPaysSiege(string $paysSiege): static
    {
        $this->paysSiege = $paysSiege;

        return $this;
    }

    public function getAdresseFac(): ?string
    {
        return $this->adresseFac;
    }

    public function setAdresseFac(string $adresseFac): static
    {
        $this->adresseFac = $adresseFac;

        return $this;
    }

    public function getCodePostalFac(): ?string
    {
        return $this->codePostalFac;
    }

    public function setCodePostalFac(string $codePostalFac): static
    {
        $this->codePostalFac = $codePostalFac;

        return $this;
    }

    public function getVilleFac(): ?string
    {
        return $this->villeFac;
    }

    public function setVilleFac(string $villeFac): static
    {
        $this->villeFac = $villeFac;

        return $this;
    }

    public function getPaysFac(): ?string
    {
        return $this->paysFac;
    }

    public function setPaysFac(string $paysFac): static
    {
        $this->paysFac = $paysFac;

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

    public function getNumSiret(): ?string
    {
        return $this->numSiret;
    }

    public function setNumSiret(string $numSiret): static
    {
        $this->numSiret = $numSiret;

        return $this;
    }

    /**
     * @return Collection<int, Apprenant>
     */
    public function getApprenants(): Collection
    {
        return $this->apprenants;
    }

    public function addApprenant(Apprenant $apprenant): static
    {
        if (!$this->apprenants->contains($apprenant)) {
            $this->apprenants->add($apprenant);
            $apprenant->setSociete($this);
        }

        return $this;
    }

    public function removeApprenant(Apprenant $apprenant): static
    {
        if ($this->apprenants->removeElement($apprenant)) {
            // set the owning side to null (unless already changed)
            if ($apprenant->getSociete() === $this) {
                $apprenant->setSociete(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Formateur>
     */
    public function getFormateurs(): Collection
    {
        return $this->formateurs;
    }

    public function addFormateur(Formateur $formateur): static
    {
        if (!$this->formateurs->contains($formateur)) {
            $this->formateurs->add($formateur);
            $formateur->setSociete($this);
        }

        return $this;
    }

    public function removeFormateur(Formateur $formateur): static
    {
        if ($this->formateurs->removeElement($formateur)) {
            // set the owning side to null (unless already changed)
            if ($formateur->getSociete() === $this) {
                $formateur->setSociete(null);
            }
        }

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
            $responsabilite->setSociete($this);
        }

        return $this;
    }

    public function removeResponsabilite(Responsabilite $responsabilite): static
    {
        if ($this->responsabilites->removeElement($responsabilite)) {
            // set the owning side to null (unless already changed)
            if ($responsabilite->getSociete() === $this) {
                $responsabilite->setSociete(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->statutJuri." ".$this->raisonSociale;
    }
}
