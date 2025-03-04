<?php

namespace App\Entity;

use App\Repository\SondageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SondageRepository::class)]
class Sondage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $type_questionnaire = null;

    #[ORM\ManyToOne(inversedBy: 'sondages')]
    private ?Questionnaire $questionnaire = null;

    #[ORM\ManyToOne(inversedBy: 'sondages')]
    private ?Session $session = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeQuestionnaire(): ?string
    {
        return $this->type_questionnaire;
    }

    public function setTypeQuestionnaire(string $type_questionnaire): static
    {
        $this->type_questionnaire = $type_questionnaire;

        return $this;
    }

    public function getQuestionnaire(): ?Questionnaire
    {
        return $this->questionnaire;
    }

    public function setQuestionnaire(?Questionnaire $questionnaire): static
    {
        $this->questionnaire = $questionnaire;

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
        return $this->session->formation->nomFormation." : questionnaire ".$this->questionnaire->nomQuestionnaire;
    }
}
