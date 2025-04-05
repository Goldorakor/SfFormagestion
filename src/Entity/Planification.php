<?php

namespace App\Entity;

use App\Repository\PlanificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlanificationRepository::class)]
class Planification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $duree = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\ManyToOne(inversedBy: 'planifications')]
    private ?Session $session = null;

    #[ORM\ManyToOne(inversedBy: 'planifications')]
    private ?Module $module = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

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

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): static
    {
        $this->module = $module;

        return $this;
    }


    // cette méthode permet de passer d'une durée exprimée en minutes à une durée exprimée en heures et en minutes :
    // $hours est le quotient de la division (intdiv) et $minutes est le reste de la division (% = opérateur modulo)
    // sprintf() est une fonction qui formate une chaîne de caractères : "%d" est un espace réservé qui sera remplacé par un nombre entier et justement les valeurs de $hours et $minutes sont insérées dans ces espaces réservés, dans l'ordre où elles apparaissent après la chaîne.
    public function getFormattedDuration02(): string
    {
        $hours = intdiv($this->duree, 60);
        $minutes = $this->duree % 60;

        if ($hours > 0 && $minutes > 0) {
            return sprintf("%d heure(s) et %d minute(s)", $hours, $minutes);
        } elseif ($hours > 0) {
            return sprintf("%d heure(s)", $hours);
        } else {
            return sprintf("%d minute(s)", $minutes);
        }
    }
    

    // cette méthode permet d'afficher le jour et l'heure de début et de fin de chaque module dans une planification
    public function getDatesModule(): string
    {
        return "du\u{00A0}".$this->dateDebut->format('d/m/Y') ." à\u{00A0}". $this->dateDebut->format('H:i')." au\u{00A0}". $this->dateFin->format('d/m/Y')." à\u{00A0}". $this->dateFin->format('H:i');
    }
    

    public function __toString()
    {
        return "de ".$this->dateDebut->format('d-m-Y H:i') ." à ". $this->dateFin->format('d-m-Y H:i');
    }
}


// {{ formation.dateMaj | date:"d-M-y HH:mm" }}



