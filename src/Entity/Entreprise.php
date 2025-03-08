<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
class Entreprise
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

    #[ORM\Column(length: 20)]
    private ?string $tel = null;

    #[ORM\Column(length: 100)]
    private ?string $email = null;

    #[ORM\Column(length: 20)]
    private ?string $numSiret = null;

    #[ORM\Column(length: 30)]
    private ?string $numRCS = null;

    #[ORM\Column(length: 30)]
    private ?string $numTVA = null;

    #[ORM\Column(length: 30)]
    private ?string $numDeclaActivite = null;

    #[ORM\Column(length: 30)]
    private ?string $prefectureRegion = null;

    #[ORM\Column(length: 100)]
    private ?string $tribunal = null;

    #[ORM\Column(length: 255)]
    private ?string $logoFilename = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Representant $representant = null;

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

    public function getNumRCS(): ?string
    {
        return $this->numRCS;
    }

    public function setNumRCS(string $numRCS): static
    {
        $this->numRCS = $numRCS;

        return $this;
    }

    public function getNumTVA(): ?string
    {
        return $this->numTVA;
    }

    public function setNumTVA(string $numTVA): static
    {
        $this->numTVA = $numTVA;

        return $this;
    }

    public function getNumDeclaActivite(): ?string
    {
        return $this->numDeclaActivite;
    }

    public function setNumDeclaActivite(string $numDeclaActivite): static
    {
        $this->numDeclaActivite = $numDeclaActivite;

        return $this;
    }

    public function getPrefectureRegion(): ?string
    {
        return $this->prefectureRegion;
    }

    public function setPrefectureRegion(string $prefectureRegion): static
    {
        $this->prefectureRegion = $prefectureRegion;

        return $this;
    }

    public function getTribunal(): ?string
    {
        return $this->tribunal;
    }

    public function setTribunal(string $tribunal): static
    {
        $this->tribunal = $tribunal;

        return $this;
    }

    public function getLogoFilename(): ?string
    {
        return $this->logoFilename;
    }

    public function setLogoFilename(string $logoFilename): static
    {
        $this->logoFilename = $logoFilename;

        return $this;
    }

    public function getRepresentant(): ?Representant
    {
        return $this->representant;
    }

    public function setRepresentant(Representant $representant): static
    {
        $this->representant = $representant;

        return $this;
    }

    public function __toString()
    {
        return $this->statutJuri." ".$this->raisonSociale;
    }
}
