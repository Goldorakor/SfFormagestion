<?php

namespace App\Form;

use App\Entity\Societe;
use App\Entity\Responsable;
use App\Form\ResponsabiliteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class SocieteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('raisonSociale', TextType::class, [
                'label' => 'Nom de la société', // texte qui s'affiche devant le rectangle de saisie
            ])
            ->add('statutJuri', TextType::class, [
                'label' => 'Statut juridique', // on ajoute ce libellé pour avoir 'Statut juridique' comme texte devant ce champ du formulaire
            ])
            ->add('adresseSiege', TextType::class, [
                'label' => 'Rue',
            ])
            ->add('codePostalSiege', TextType::class, [
                'label' => 'Code postal',
            ])
            ->add('villeSiege', TextType::class, [
                'label' => 'Ville',
            ])
            ->add('paysSiege', TextType::class, [
                'label' => 'PAYS',
            ])
            ->add('adresseFac', TextType::class, [
                'label' => 'Rue',
            ])
            ->add('codePostalFac', TextType::class, [
                'label' => 'Code postal',
            ])
            ->add('villeFac', TextType::class, [
                'label' => 'Ville',
            ])
            ->add('paysFac', TextType::class, [
                'label' => 'PAYS',
            ])
            ->add('tel', TextType::class, [
                'label' => 'Téléphone',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
            ])
            ->add('numSiret', TextType::class, [
                'label' => 'Numéro SIRET',
            ])

            /*
            ->add('responsabilites', CollectionType::class, [
                'entry_type' => ResponsabiliteType::class, // Chaque élément de la collection est un formulaire de type ResponsabiliteType -> Utilisation du formulaire des responsabilités créé précédemment
                'allow_add' => true,  // Permet d'ajouter des responsabilités dynamiquement
                'allow_delete' => true, // Permet de supprimer des responsabilités
                'by_reference' => false, // Obligatoire pour une relation bidirectionnelle -> 
                'prototype' => true, // Permet d'ajouter dynamiquement des éléments avec JS
            ])
            */

            // Champ pour le responsable légal
            ->add('responsableLegal', EntityType::class, [ // nom à utiliser dans le SocieteController
                'class' => Responsable::class,
                //'choice_label' => 'nom',  Affiche le nom du responsable -> on supprime pour qu'il affiche le __toString du responsable
                'placeholder' => 'Sélectionner un responsable',
                'mapped' => false, // On ne mappe pas à l'entité Societe directement -> ces champs ne sont pas directement liés à l'entité Societe. Ils seront utilisés pour créer des entrées dans la table Responsabilite
                'label' => 'Responsable légal',
                'required' => false, // Rendre le champ optionnel car parfois le resp légal n'est pas exigé
            ])

            // Champ pour le responsable administratif
            ->add('responsableAdministratif', EntityType::class, [ // nom à utiliser dans le SocieteController
                'class' => Responsable::class,
                //'choice_label' => 'nom',  Affiche le nom du responsable -> on supprime pour qu'il affiche le __toString du responsable
                'placeholder' => 'Sélectionner un responsable',
                'mapped' => false,
                'label' => 'Responsable administratif',
                'required' => false, // Rendre le champ optionnel car parfois le resp admin n'est pas exigé
            ])

            // Champ pour le responsable RH
            ->add('responsableRH', EntityType::class, [ // nom à utiliser dans le SocieteController
                'class' => Responsable::class,
                //'choice_label' => 'nom',  Affiche le nom du responsable -> on supprime pour qu'il affiche le __toString du responsable
                'placeholder' => 'Sélectionner un responsable',
                'mapped' => false,
                'label' => 'Responsable RH',
                'required' => false, // Rendre le champ optionnel car parfois le resp RH n'est pas exigé
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
            'data_class' => Societe::class,
        ]);
    }
}

/*
importance de 'by_reference' => false

Symfony doit gérer les ajouts/suppressions correctement dans une relation bidirectionnelle.
Par défaut, Symfony et Doctrine tentent d’ajouter les objets à une collection par référence.
Cela signifie qu’au lieu d’appeler les méthodes addResponsabilite() et removeResponsabilite() sur l’objet Societe, Symfony essaierait simplement d’ajouter/supprimer des objets directement dans la collection.

Or, dans ton cas, Societe possède une relation bidirectionnelle avec Responsabilite. Cela veut dire que :

Societe a une collection de Responsabilite (OneToMany).
Responsabilite contient une référence vers Societe (ManyToOne).
Il faut que Doctrine soit informé des modifications via les méthodes addResponsabilite() et removeResponsabilite() pour bien gérer la relation.


en résumé
Avec by_reference => true (par défaut) : Symfony modifie directement la collection, mais Doctrine ne suit pas bien les relations bidirectionnelles.
Avec by_reference => false : Symfony utilise bien addResponsabilite() et removeResponsabilite(), garantissant une gestion propre de la relation et évitant les bugs.

*/
