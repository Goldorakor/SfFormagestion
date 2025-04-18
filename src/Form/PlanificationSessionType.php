<?php

namespace App\Form;

use App\Entity\Module;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;


class PlanificationSessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('module', EntityType::class, [
                'class' => Module::class,
                'placeholder' => 'Sélectionner un module',
                //'choice_label' => 'nom',  Ajuste selon le champ à afficher
                'label' => "Nom du module d'enseignement",
                'attr' => ['class' => 'form-cell module-nomModule'] /* pour décider de la taille du champ en css */
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée (en minutes)', // texte qui s'affiche devant le rectangle de saisie
                'attr' => [
                    'placeholder' => 'Durée (en minutes)',  // ici, placeholder est un attribut HTML : tous les attributs HTML personnalisés (comme class, placeholder, style, etc.) doivent être mis dans le tableau attr dans un formulaire Symfony
                    'class' => 'form-cell module-duree' /* pour décider de la taille du champ en css */
                ],
            ])
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text', // pour forcer l'affichage sous forme d'un seul champ texte avec un format standardisé
                'attr' => [
                    'class' => 'form-cell module-dateDebut' /* pour décider de la taille du champ en css */
                ],
            ])
            ->add('dateFin', DateTimeType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text', // pour forcer l'affichage sous forme d'un seul champ texte avec un format standardisé
                'attr' => [
                    'class' => 'form-cell module-dateFin' /* pour décider de la taille du champ en css */
                ],
            ]);
    }
}

/*

ce fichier est une formulaire qui sera employé dans un formulaire plus global (le formulaire de Session).

*/
