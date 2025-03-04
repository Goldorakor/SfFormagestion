<?php

namespace App\Entity;

use App\Repository\QuestionnaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionnaireRepository::class)]
class Questionnaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $url_stockage = null;

    /**
     * @var Collection<int, Sondage>
     */
    #[ORM\OneToMany(targetEntity: Sondage::class, mappedBy: 'questionnaire')]
    private Collection $sondages;

    #[ORM\Column(length: 255)]
    private ?string $nomQuestionnaire = null;

    public function __construct()
    {
        $this->sondages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrlStockage(): ?string
    {
        return $this->url_stockage;
    }

    public function setUrlStockage(string $url_stockage): static
    {
        $this->url_stockage = $url_stockage;

        return $this;
    }

    /**
     * @return Collection<int, Sondage>
     */
    public function getSondages(): Collection
    {
        return $this->sondages;
    }

    public function addSondage(Sondage $sondage): static
    {
        if (!$this->sondages->contains($sondage)) {
            $this->sondages->add($sondage);
            $sondage->setQuestionnaire($this);
        }

        return $this;
    }

    public function removeSondage(Sondage $sondage): static
    {
        if ($this->sondages->removeElement($sondage)) {
            // set the owning side to null (unless already changed)
            if ($sondage->getQuestionnaire() === $this) {
                $sondage->setQuestionnaire(null);
            }
        }

        return $this;
    }

    public function getNomQuestionnaire(): ?string
    {
        return $this->nomQuestionnaire;
    }

    public function setNomQuestionnaire(string $nomQuestionnaire): static
    {
        $this->nomQuestionnaire = $nomQuestionnaire;

        return $this;
    }

    public function __toString()
    {
        return $this->nomQuestionnaire;
    }
}
