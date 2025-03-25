<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // On ajoute uniquement les champs que l'on veut afficher
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail', // texte qui s'affiche devant le rectangle de saisie
            ])

            ->add('nom', TextType::class, [
                'label' => 'Nom', // texte qui s'affiche devant le rectangle de saisie
            ])
            
            ->add('prenom', TextType::class, [
                'label' => 'Prénom', // texte qui s'affiche devant le rectangle de saisie
            ])

            ->add('roles', ChoiceType::class, [
                'label' => 'Rôle',
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'placeholder' => 'Sélectionner un rôle',
                'expanded' => false,
                'required' => true, // Un rôle doit être sélectionné
                'choice_attr' => function($choice, $key, $value) {
                    // Si c'est ROLE_ADMIN, ajouter l'attribut de sélection de ROLE_USER par défaut
                    if ($value === 'ROLE_ADMIN') {
                        return ['data-role' => 'admin']; // Tu peux ajouter un attribut personnalisé si nécessaire
                    }
                    return [];
                }
            ])

            ->add('enregistrer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
