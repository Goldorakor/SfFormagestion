<?php

namespace App\Form;

use App\Entity\Apprenant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ApprenantInscritType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', EntityType::class, [
                'class' => Module::class,
                'placeholder' => 'Sélectionner un module',
                //'choice_label' => 'nom',  Ajuste selon le champ à afficher
                'label' => "Nom du module d'enseignement",
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée', // texte qui s'affiche devant le rectangle de saisie
            ])
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text', // pour forcer l'affichage sous forme d'un seul champ texte avec un format standardisé
            ])
            ->add('dateFin', DateTimeType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text', // pour forcer l'affichage sous forme d'un seul champ texte avec un format standardisé
            ]);
    }
}

/*

ce fichier est une formulaire qui sera employé dans un formulaire plus global (le formulaire de Session).

*/
