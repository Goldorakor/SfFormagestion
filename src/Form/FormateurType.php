<?php

namespace App\Form;

use App\Entity\Societe;
use App\Entity\Formateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FormateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
            ->add('nom', TextType::class, [
                'label' => 'Nom', // texte qui s'affiche devant le rectangle de saisie
            ])

            ->add('prenom', TextType::class, [
                'label' => 'Prénom', // texte qui s'affiche devant le rectangle de saisie
            ])

            ->add('sexe', ChoiceType::class, [
                'label' => 'Civilité', // texte qui s'affiche devant le rectangle de saisie
                'choices' => [
                    'Féminin' => 'F',
                    'Masculin' => 'M',
                ],
                'placeholder' => 'Sélectionner la civilité',
                'expanded' => false,
                'multiple' => false, // true sinon message "Warning: Array to string conversion" ('roles' est un tableau) => ok car le CallbackTransformer gère la conversion vers array
                'required' => true, // Un rôle doit être sélectionné
            ])

            ->add('tel', TextType::class, [
                'label' => 'Téléphone', // texte qui s'affiche devant le rectangle de saisie
            ])

            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail', // texte qui s'affiche devant le rectangle de saisie
            ])
            
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut', // texte qui s'affiche devant le rectangle de saisie
                'choices' => [
                    'Indépendant' => 'indépendant',
                    'Employé' => 'salarié',
                ],
                'placeholder' => 'Sélectionner le statut',
                'expanded' => false,
                'multiple' => false, // true sinon message "Warning: Array to string conversion" ('roles' est un tableau) => ok car le CallbackTransformer gère la conversion vers array
                'required' => true, // Un rôle doit être sélectionné
            ])

            ->add('societe', EntityType::class, [
                'class' => Societe::class,
                //'choice_label' => 'raisonSociale',  Affiche la raison sociale -> on supprime pour qu'il affiche le __toString de la société
                'placeholder' => 'Sélectionner une société',
                'label' => 'Société',
                'required' => false, // Rendre le champ optionnel car parfois la société n'est pas exigée
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
            'data_class' => Formateur::class,
        ]);
    }
}
