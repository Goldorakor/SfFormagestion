<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Apprenant;
use App\Entity\Formateur;
use App\Entity\Formation;
use App\Entity\Questionnaire;
use App\Form\ApprenantInscritType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('titreSession', TextType::class, [
                'label' => 'Titre', // texte qui s'affiche devant le rectangle de saisie
                'attr' => ['placeholder' => 'Intitulé de la session'],
            ])

            ->add('accroche', TextareaType::class, [
                'label' => 'Accroche', // texte qui s'affiche devant le rectangle de saisie
                'required' => false,   // champ non obligatoire
                'attr' => ['placeholder' => "Phrase d'accroche optionnelle"],
            ])

            ->add('nbPlaces', IntegerType::class, [
                'label' => 'Places max.', // texte qui s'affiche devant le rectangle de saisie
                'attr' => ['placeholder' => 'Nombre de participants max.'],
            ])

            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text', // pour forcer l'affichage sous forme d'un seul champ texte avec un format standardisé
            ])

            ->add('dateFin', DateTimeType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text', // pour forcer l'affichage sous forme d'un seul champ texte avec un format standardisé
            ])

            ->add('formation', EntityType::class, [
                'class' => Formation::class,
                'choice_label' => 'nomFormation', // on ne le supprime pas car on ne veut pas spécialement afficher le __toString dans ce cas précis
                'placeholder' => 'Sélectionner une formation',
                'label' => 'Formation associée à la session',
            ])

            // Champ pour le référent pédagogique => champ non mappé directement (à traiter dans le contrôleur) !!
            ->add('referentPedagogique', EntityType::class, [ // nom à utiliser dans le SessionController
                'class' => Formateur::class,
                //'choice_label' => 'nom',  Affiche le nom du formateur -> on supprime pour qu'il affiche le __toString du formateur
                'placeholder' => 'Sélectionner un formateur',
                'mapped' => false, // On ne mappe pas à l'entité Session directement -> ces 2 champs ne sont pas directement liés à l'entité Session. Ils seront utilisés pour créer des entrées dans la table Encadrement
                'label' => 'Référent pédagogique',
                'required' => false, // Rend le champ optionnel -> pas ici car on veut tjrs un référent pédagogique
                'data' => $options['referentPedagogique'], // préremplissage ici
            ])

            // Champ pour le référent administratif => champ non mappé directement (à traiter dans le contrôleur) !!
            ->add('referentAdministratif', EntityType::class, [ // nom à utiliser dans le SessionController
                'class' => Formateur::class,
                //'choice_label' => 'nom',  Affiche le nom du formateur -> on supprime pour qu'il affiche le __toString du formateur
                'placeholder' => 'Sélectionner un formateur',
                'mapped' => false, // On ne mappe pas à l'entité Session directement -> ces 2 champs ne sont pas directement liés à l'entité Session. Ils seront utilisés pour créer des entrées dans la table Encadrement
                'label' => 'Référent administratif',
                'required' => false, // Rend le champ optionnel -> pas ici car on veut tjrs un référent administratif
                'data' => $options['referentAdministratif'], // préremplissage ici
            ])

            // Champ pour le questionnaire de préformation
            ->add('questionnairePrefor', EntityType::class, [ // nom à utiliser dans le SessionController
                'class' => Questionnaire::class,
                'choice_label' => 'nomQuestionnaire', // pas besoin d'afficher le __toString du questionnaire
                'placeholder' => 'Sélectionner un questionnaire de préformation',
                'mapped' => false, // On ne mappe pas à l'entité Session directement -> ces 3 champs ne sont pas directement liés à l'entité Session. Ils seront utilisés pour créer des entrées dans la table Sondage
                'label' => 'Questionnaire de préformation',
                'required' => false, // Rend le champ optionnel -> pas ici car on veut tjrs un questionnaire de préformation
                'data' => $options['questionnairePrefor'],
            ])

            // Champ pour le questionnaire à chaud
            ->add('questionnaireChaud', EntityType::class, [ // nom à utiliser dans le SessionController
                'class' => Questionnaire::class,
                'choice_label' => 'nomQuestionnaire', // pas besoin d'afficher le __toString du questionnaire
                'placeholder' => 'Sélectionner un questionnaire à chaud',
                'mapped' => false, // On ne mappe pas à l'entité Session directement -> ces 3 champs ne sont pas directement liés à l'entité Session. Ils seront utilisés pour créer des entrées dans la table Sondage
                'label' => 'Questionnaire à chaud',
                'required' => false, // Rend le champ optionnel -> pas ici car on veut tjrs un questionnaire à chaud
                'data' => $options['questionnaireChaud'],
            ])

            // Champ pour le questionnaire à froid
            ->add('questionnaireFroid', EntityType::class, [ // nom à utiliser dans le SessionController
                'class' => Questionnaire::class,
                'choice_label' => 'nomQuestionnaire', // pas besoin d'afficher le __toString du questionnaire
                'placeholder' => 'Sélectionner un questionnaire à froid',
                'mapped' => false, // On ne mappe pas à l'entité Session directement -> ces 3 champs ne sont pas directement liés à l'entité Session. Ils seront utilisés pour créer des entrées dans la table Sondage
                'label' => 'Questionnaire à froid',
                'required' => false, // Rend le champ optionnel -> pas ici car on veut tjrs un questionnaire à chaud
                'data' => $options['questionnaireFroid'],
            ])

            // Champ pour inscrire les apprenants avec leur prix
            ->add('apprenantsInscrits', CollectionType::class, [
                'entry_type' => ApprenantInscritType::class, // Formulaire imbriqué pour chaque apprenant (spécifie que chaque élément est un sous-formulaire ApprenantType)
                'allow_add' => true, // Permet d'ajouter dynamiquement des apprenants
                'allow_delete' => true, // Permet de supprimer un apprenant inscrit
                /*'entry_options' => [
                    'label' => false,    -> Supprime les labels des champs à l'intérieur de chaque répétition de la collection.
                ],*/
                'mapped' => false, // Ce champ ne mappe pas directement avec l'entité Session
                'by_reference' => false, // Utilisé pour les ajouts dynamiques
                'prototype' => true, //  Ajout du prototype pour le JavaScript
                'required' => false, // rend le champ optionnel
                'label' => false, // Empêche l'affichage automatique du label (Apprenants inscrits) -> supprime le label global de la collection
                'data' => $options['apprenantsInscrits'] ?? [], // pour récup les données initiales lors de l'édition d'une session
            ])

            // Champ pour inscrire les modules avec leur durée, leur date de début et leur date de fin
            ->add('planificationSessions', CollectionType::class, [
                'entry_type' => PlanificationSessionType::class, // Formulaire imbriqué pour chaque apprenant
                'allow_add' => true, // Permet d'ajouter dynamiquement des apprenants
                'allow_delete' => true, // Permet de supprimer un apprenant inscrit
                /*'entry_options' => [    -> Supprime les labels des champs à l'intérieur de chaque répétition de la collection.
                    'label' => false,
                ],*/
                'mapped' => false, // Ce champ ne mappe pas directement avec l'entité Session
                'by_reference' => false, // Utilisé pour les ajouts dynamiques
                'prototype' => true, //  Ajout du prototype pour le JavaScript
                'required' => false, // rend le champ optionnel
                'label' => false, // Empêche l'affichage automatique du label (Planification sessions) -> supprime le label global de la collection
                'data' => $options['planificationSessions'] ?? [], // pour récup les données initiales lors de l'édition d'une session
            ])

            ->add('enregistrer', SubmitType::class, [
                'label' => '<i class="fa-solid fa-download"></i> Enregistrer',
                'label_html' => true,
                'attr' => ['class' => 'btn btn-rose'],
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
            'referentPedagogique' => null, // pour édition de session
            'referentAdministratif' => null,
            'questionnairePrefor' => null,
            'questionnaireChaud' => null,
            'questionnaireFroid' => null,
            'apprenantsInscrits' => [], 
            'planificationSessions' => [], // pour édition de session
        ]);
    }
}
