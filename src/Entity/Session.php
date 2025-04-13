<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titreSession = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $accroche = null;

    #[ORM\Column]
    private ?int $nbPlaces = null;

    #[ORM\Column]
    private ?int $nbModules = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    private ?Formation $formation = null;

    /**
     * @var Collection<int, Encadrement>
     */
    #[ORM\OneToMany(targetEntity: Encadrement::class, mappedBy: 'session')]
    private Collection $encadrements;

    /**
     * @var Collection<int, Inscription>
     */
    #[ORM\OneToMany(targetEntity: Inscription::class, mappedBy: 'session', cascade: ['persist'], orphanRemoval: true)]
    private Collection $inscriptions;

    /**
     * @var Collection<int, Sondage>
     */
    #[ORM\OneToMany(targetEntity: Sondage::class, mappedBy: 'session')]
    private Collection $sondages;

    /**
     * @var Collection<int, Planification>
     */
    #[ORM\OneToMany(targetEntity: Planification::class, mappedBy: 'session', cascade: ['persist'], orphanRemoval: true)]
    private Collection $planifications;


    public function __construct()
    {
        $this->encadrements = new ArrayCollection();
        $this->inscriptions = new ArrayCollection();
        $this->sondages = new ArrayCollection();
        $this->planifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitreSession(): ?string
    {
        return $this->titreSession;
    }

    public function setTitreSession(string $titreSession): static
    {
        $this->titreSession = $titreSession;

        return $this;
    }

    public function getAccroche(): ?string
    {
        return $this->accroche;
    }

    public function setAccroche(?string $accroche): static
    {
        $this->accroche = $accroche;

        return $this;
    }

    public function getNbPlaces(): ?int
    {
        return $this->nbPlaces;
    }

    public function setNbPlaces(int $nbPlaces): static
    {
        $this->nbPlaces = $nbPlaces;

        return $this;
    }

    public function getNbModules(): ?int
    {
        return $this->nbModules;
    }

    public function setNbModules(int $nbModules): static
    {
        $this->nbModules = $nbModules;

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

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): static
    {
        $this->formation = $formation;

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
            $encadrement->setSession($this);
        }

        return $this;
    }

    public function removeEncadrement(Encadrement $encadrement): static
    {
        if ($this->encadrements->removeElement($encadrement)) {
            // set the owning side to null (unless already changed)
            if ($encadrement->getSession() === $this) {
                $encadrement->setSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Inscription>
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscription $inscription): static
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions->add($inscription);
            $inscription->setSession($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription): static
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getSession() === $this) {
                $inscription->setSession(null);
            }
        }

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
            $sondage->setSession($this);
        }

        return $this;
    }

    public function removeSondage(Sondage $sondage): static
    {
        if ($this->sondages->removeElement($sondage)) {
            // set the owning side to null (unless already changed)
            if ($sondage->getSession() === $this) {
                $sondage->setSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Planification>
     */
    public function getPlanifications(): Collection
    {
        return $this->planifications;
    }

    public function addPlanification(Planification $planification): static
    {
        if (!$this->planifications->contains($planification)) {
            $this->planifications->add($planification);
            $planification->setSession($this);
        }

        return $this;
    }

    public function removePlanification(Planification $planification): static
    {
        if ($this->planifications->removeElement($planification)) {
            // set the owning side to null (unless already changed)
            if ($planification->getSession() === $this) {
                $planification->setSession(null);
            }
        }

        return $this;
    }


    // cette méthode permet d'obtenir le prix total payé par tous les apprenants inscrits à une session.
    // il existe aussi un moyen d'obtenir le prix total directement dans la vue (voir mon commentaire dans la vue de détails de session)
    public function getTotalPrice(): string
    {
        $somme = 0;
    
        foreach ($this->inscriptions as $inscription) {
            $somme += $inscription->getPrix(); 
        }

        return $somme." euros";
    }


    // cette méthode permet de passer d'une durée exprimée en minutes à une durée exprimée en heures et en minutes :
    // $hours est le quotient de la division (intdiv) et $minutes est le reste de la division (% = opérateur modulo)
    // sprintf() est une fonction qui formate une chaîne de caractères : "%d" est un espace réservé qui sera remplacé par un nombre entier et justement les valeurs de $hours et $minutes sont insérées dans ces espaces réservés, dans l'ordre où elles apparaissent après la chaîne.
    /*
    public function getFormattedDuration(): string
    {
        $hours = intdiv($this->dureeTotale, 60);
        $minutes = $this->dureeTotale % 60;

        if ($hours > 0 && $minutes > 0) {
            return sprintf("%d heures et %d minutes", $hours, $minutes);
        } elseif ($hours > 0) {
            return sprintf("%d heures", $hours);
        } else {
            return sprintf("%d minutes", $minutes);
        }
    }
    */


    // Cette méthode permet d'additionner toutes les durées (dans la table planification) pour tous les modules enseignés dans une session donnée
    // On initialise à zéro puis on ajoute pour cet objet Session là toutes les durées des planifications qui le concernent
    // Ensuite, on reprend la méthode au dessus pour convertir proprement en heures et minutes (repris de l'entité Planification)
    public function getFormattedDuration(): string
    {
        $somme = 0;
    
        foreach ($this->planifications as $planification) {
            $somme += $planification->getDuree();
        }

        $hours = intdiv($somme, 60);
        $minutes = $somme % 60;

        // return sprintf("%d heures et %d minutes", $hours, $minutes); -> affiche " x heures et 0 minutes" -> avec des conditions, on évite le souci.
        if ($hours > 0 && $minutes > 0) {
            return sprintf("%d heures et %d minutes", $hours, $minutes);
        } elseif ($hours > 0) {
            return sprintf("%d heures", $hours);
        } else {
            return sprintf("%d minutes", $minutes);
        }

    }


    /* cette méthode permet d'afficher le jour et l'heure de début et de fin de la session de formation
    public function getDatesSession(): string
    {
        return "du ".$this->dateDebut->format('d/m/Y') ." à ". $this->dateDebut->format('H:i')." au ". $this->dateFin->format('d/m/Y')." à ". $this->dateFin->format('H:i');
    }
    */

    // cette méthode permet d'afficher le jour et l'heure de début et de fin de la session de formation
    // mieux que la version précédente du dessus car je tiens compte des sessions qui se déroulent sur une seule journée
    public function getDatesSession(): string
    {
        
        if ($this->dateDebut->format('d/m/Y') == $this->dateFin->format('d/m/Y')) {
            return "le ".$this->dateDebut->format('d/m/Y') ." de ". $this->dateDebut->format('H:i')." à ". $this->dateFin->format('H:i');
        } else {
            return "du ".$this->dateDebut->format('d/m/Y') ." à ". $this->dateDebut->format('H:i')." au ". $this->dateFin->format('d/m/Y')." à ". $this->dateFin->format('H:i');
        }
    }
    

    public function __toString()
    {
        return $this->titreSession;
    }

}
