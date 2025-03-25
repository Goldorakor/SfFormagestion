<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'placeholder' => 'Sélectionner un rôle',
                //'multiple' => true,  permet à un utilisateur d'avoir plusieurs rôles  =>  $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
                //'expanded' => true,   Si on veut des boutons radio plutôt qu'une liste déroulante  =>  nécessaire si un utilisateur peut avoir plusieurs rôles
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
