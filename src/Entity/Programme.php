<?php

namespace App\Entity;

use App\Repository\ProgrammeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgrammeRepository::class)]
class Programme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomProgramme = null;

    #[ORM\Column(length: 20)]
    private ?string $refProgramme = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $prerequis = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $objectifsPeda = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenusPeda = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $aptitude = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $competences = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $delaiAcces = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $moyensEnca = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $methodes = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $moyensTech = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $niveau = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $modalitesAcces = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $modalitesEval = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $accessibilite = null;

    #[ORM\Column]
    private ?float $tauxReussite = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $debouches = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $cible = null;

    /**
     * @var Collection<int, Formation>
     */
    #[ORM\OneToMany(targetEntity: Formation::class, mappedBy: 'programme')]
    private Collection $formations;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateMaj = null;

    public function __construct()
    {
        $this->formations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomProgramme(): ?string
    {
        return $this->nomProgramme;
    }

    public function setNomProgramme(string $nomProgramme): static
    {
        $this->nomProgramme = $nomProgramme;

        return $this;
    }

    public function getRefProgramme(): ?string
    {
        return $this->refProgramme;
    }

    public function setRefProgramme(string $refProgramme): static
    {
        $this->refProgramme = $refProgramme;

        return $this;
    }

    public function getPrerequis(): ?string
    {
        return $this->prerequis;
    }

    public function setPrerequis(string $prerequis): static
    {
        $this->prerequis = $prerequis;

        return $this;
    }

    public function getObjectifsPeda(): ?string
    {
        return $this->objectifsPeda;
    }

    public function setObjectifsPeda(string $objectifsPeda): static
    {
        $this->objectifsPeda = $objectifsPeda;

        return $this;
    }

    public function getContenusPeda(): ?string
    {
        return $this->contenusPeda;
    }

    public function setContenusPeda(string $contenusPeda): static
    {
        $this->contenusPeda = $contenusPeda;

        return $this;
    }

    public function getAptitude(): ?string
    {
        return $this->aptitude;
    }

    public function setAptitude(string $aptitude): static
    {
        $this->aptitude = $aptitude;

        return $this;
    }

    public function getCompetences(): ?string
    {
        return $this->competences;
    }

    public function setCompetences(string $competences): static
    {
        $this->competences = $competences;

        return $this;
    }

    public function getDelaiAcces(): ?string
    {
        return $this->delaiAcces;
    }

    public function setDelaiAcces(string $delaiAcces): static
    {
        $this->delaiAcces = $delaiAcces;

        return $this;
    }

    public function getMoyensEnca(): ?string
    {
        return $this->moyensEnca;
    }

    public function setMoyensEnca(string $moyensEnca): static
    {
        $this->moyensEnca = $moyensEnca;

        return $this;
    }

    public function getMethodes(): ?string
    {
        return $this->methodes;
    }

    public function setMethodes(string $methodes): static
    {
        $this->methodes = $methodes;

        return $this;
    }

    public function getMoyensTech(): ?string
    {
        return $this->moyensTech;
    }

    public function setMoyensTech(string $moyensTech): static
    {
        $this->moyensTech = $moyensTech;

        return $this;
    }

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(string $niveau): static
    {
        $this->niveau = $niveau;

        return $this;
    }

    public function getModalitesAcces(): ?string
    {
        return $this->modalitesAcces;
    }

    public function setModalitesAcces(string $modalitesAcces): static
    {
        $this->modalitesAcces = $modalitesAcces;

        return $this;
    }

    public function getModalitesEval(): ?string
    {
        return $this->modalitesEval;
    }

    public function setModalitesEval(string $modalitesEval): static
    {
        $this->modalitesEval = $modalitesEval;

        return $this;
    }

    public function getAccessibilite(): ?string
    {
        return $this->accessibilite;
    }

    public function setAccessibilite(string $accessibilite): static
    {
        $this->accessibilite = $accessibilite;

        return $this;
    }

    public function getTauxReussite(): ?float
    {
        return $this->tauxReussite;
    }

    public function setTauxReussite(float $tauxReussite): static
    {
        $this->tauxReussite = $tauxReussite;

        return $this;
    }

    public function getDebouches(): ?string
    {
        return $this->debouches;
    }

    public function setDebouches(string $debouches): static
    {
        $this->debouches = $debouches;

        return $this;
    }

    public function getCible(): ?string
    {
        return $this->cible;
    }

    public function setCible(string $cible): static
    {
        $this->cible = $cible;

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    public function addFormation(Formation $formation): static
    {
        if (!$this->formations->contains($formation)) {
            $this->formations->add($formation);
            $formation->setProgramme($this);
        }

        return $this;
    }

    public function removeFormation(Formation $formation): static
    {
        if ($this->formations->removeElement($formation)) {
            // set the owning side to null (unless already changed)
            if ($formation->getProgramme() === $this) {
                $formation->setProgramme(null);
            }
        }

        return $this;
    }

    public function getDateMaj(): ?\DateTimeInterface
    {
        return $this->dateMaj;
    }

    public function setDateMaj(\DateTimeInterface $dateMaj): static
    {
        $this->dateMaj = $dateMaj;

        return $this;
    }

    public function __toString()
    {
        return $this->nomProgramme;
    }
}
