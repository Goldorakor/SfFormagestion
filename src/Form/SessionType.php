<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Formateur;
use App\Entity\Formation;
use App\Entity\Questionnaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titreSession', TextType::class, [
                'label' => 'Titre', // texte qui s'affiche devant le rectangle de saisie
            ])
            ->add('accroche', TextareaType::class, [
                'label' => 'Accroche', // texte qui s'affiche devant le rectangle de saisie
            ])
            ->add('nbPlaces', IntegerType::class, [
                'label' => 'Places max.', // texte qui s'affiche devant le rectangle de saisie
            ])
            ->add('dateDebut', null, [
                'label' => 'Date de début de session',
                'widget' => 'single_text', // pour forcer l'affichage sous forme d'un seul champ texte avec un format standardisé
            ], DateTimeType::class)
            ->add('dateFin', null, [
                'label' => 'Date de fin de session',
                'widget' => 'single_text', // pour forcer l'affichage sous forme d'un seul champ texte avec un format standardisé
            ], DateTimeType::class)
            ->add('formation', EntityType::class, [
                'class' => Formation::class,
                'choice_label' => 'nomFormation', // on ne le supprime pas car on ne veut pas spécialement afficher le __toString dans ce cas précis
                'placeholder' => 'Sélectionner une formation',
                'label' => 'Formation associée à la session',
            ])

            // Champ pour le référent pédagogique
            ->add('referentPedagogique', EntityType::class, [ // nom à utiliser dans le SessionController
                'class' => Formateur::class,
                //'choice_label' => 'nom',  Affiche le nom du formateur -> on supprime pour qu'il affiche le __toString du formateur
                'placeholder' => 'Sélectionner un formateur',
                'mapped' => false, // On ne mappe pas à l'entité Session directement -> ces 2 champs ne sont pas directement liés à l'entité Session. Ils seront utilisés pour créer des entrées dans la table Encadrement
                'label' => 'Référent pédagogique',
                //'required' => false,  Rend le champ optionnel -> pas ici car on veut tjrs un référent pédagogique
            ])

            // Champ pour le référent administratif
            ->add('referentAdministratif', EntityType::class, [ // nom à utiliser dans le SessionController
                'class' => Formateur::class,
                //'choice_label' => 'nom',  Affiche le nom du formateur -> on supprime pour qu'il affiche le __toString du formateur
                'placeholder' => 'Sélectionner un formateur',
                'mapped' => false, // On ne mappe pas à l'entité Session directement -> ces 2 champs ne sont pas directement liés à l'entité Session. Ils seront utilisés pour créer des entrées dans la table Encadrement
                'label' => 'Référent administratif',
                //'required' => false,  Rend le champ optionnel -> pas ici car on veut tjrs un référent administratif
            ])

            // Champ pour le questionnaire de préformation
            ->add('questionnairePrefor', EntityType::class, [ // nom à utiliser dans le SessionController
                'class' => Questionnaire::class,
                'choice_label' => 'nomQuestionnaire', // pas besoin d'afficher le __toString du questionnaire
                'placeholder' => 'Sélectionner un questionnaire de préformation',
                'mapped' => false, // On ne mappe pas à l'entité Session directement -> ces 3 champs ne sont pas directement liés à l'entité Session. Ils seront utilisés pour créer des entrées dans la table Sondage
                'label' => 'Questionnaire de préformation',
                //'required' => false,  Rend le champ optionnel -> pas ici car on veut tjrs un questionnaire de préformation
            ])

            // Champ pour le questionnaire à chaud
            ->add('questionnaireChaud', EntityType::class, [ // nom à utiliser dans le SessionController
                'class' => Questionnaire::class,
                'choice_label' => 'nomQuestionnaire', // pas besoin d'afficher le __toString du questionnaire
                'placeholder' => 'Sélectionner un questionnaire à chaud',
                'mapped' => false, // On ne mappe pas à l'entité Session directement -> ces 3 champs ne sont pas directement liés à l'entité Session. Ils seront utilisés pour créer des entrées dans la table Sondage
                'label' => 'Questionnaire à chaud',
                //'required' => false,  Rend le champ optionnel -> pas ici car on veut tjrs un questionnaire à chaud
            ])

            // Champ pour le questionnaire à froid
            ->add('questionnaireFroid', EntityType::class, [ // nom à utiliser dans le SessionController
                'class' => Questionnaire::class,
                'choice_label' => 'nomQuestionnaire', // pas besoin d'afficher le __toString du questionnaire
                'placeholder' => 'Sélectionner un questionnaire à froid',
                'mapped' => false, // On ne mappe pas à l'entité Session directement -> ces 3 champs ne sont pas directement liés à l'entité Session. Ils seront utilisés pour créer des entrées dans la table Sondage
                'label' => 'Questionnaire à froid',
                //'required' => false,  Rend le champ optionnel -> pas ici car on veut tjrs un questionnaire à chaud
            ])


            ->add('enregistrer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}



/*
les "places" de DateTimeType::class sont peut-être celles-là.

->add('dateDebut', null, [
    'widget' => 'single_text',
], DateTimeType::class)
->add('dateFin', null, [
    'widget' => 'single_text',
], DateTimeType::class)


les anciennes "places" de DateTimeType::class sont celles-là.

->add('dateDebut', DateTimeType::class, null, [
    'widget' => 'single_text',
])
->add('dateFin', DateTimeType::class, null, [
     'widget' => 'single_text',
 ])

*/
