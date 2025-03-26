<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
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
                'multiple' => false, // true sinon message "Warning: Array to string conversion" ('roles' est un tableau) => ok car le CallbackTransformer gère la conversion vers array
                'required' => true, // Un rôle doit être sélectionné
                'choice_attr' => function($choice, $key, $index) {
                    if ($choice === 'ROLE_ADMIN') {
                        return ['data-role' => 'admin'];
                    }
                    return [];
                }
            ])

            ->add('enregistrer', SubmitType::class)
        ;

        // Transformer la string en array pour la BDD => 'multiple' => false, désormais possible 
        $builder->get('roles')
        ->addModelTransformer(new CallbackTransformer(
            function ($rolesArray)
            {
                // Transforme l'array en string (à l'affichage)
                return $rolesArray[0] ?? null;
            },

            function ($roleString)
            {
                // Transforme la string en array (à la soumission)
                return [$roleString];
            }
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

}

/*

'choice_attr' => function($choice, $key, $index)


1 . $choice : Ce paramètre contient la valeur réelle que Symfony va soumettre si l’option est sélectionnée.

Label : Utilisateur	=> $choice : 'ROLE_USER'
Label : Administrateur	=> $choice : 'ROLE_ADMIN'

on peut donc tester $choice === 'ROLE_ADMIN' ou $choice === 'ROLE_USER'.


2. $key : C’est le texte affiché dans la liste déroulante ou à côté du bouton radio/checkbox.
Avec le tableau choices, ça donne :

$key : Utilisateur => $choice : 'ROLE_USER'
$key : Administrateur => $choice : 'ROLE_ADMIN'

Exemple : si on veut styliser l’option avec le texte "Administrateur", on peut faire :

if ($key === 'Administrateur') { ... }


3. $index : Il représente l’index numérique ou la clé de l’élément dans la liste de choix. Dans ton exemple, comme choices est un tableau associatif simple, $index sera :

$key : Utilisateur => $choice : 'ROLE_USER' =>	$index : 0
$key : Administrateur => $choice : 'ROLE_ADMIN' =>	$index : 1




Avec ton choice_attr, si on écrit :

'choice_attr' => function($choice, $key, $index) {
    if ($choice === 'ROLE_ADMIN') {
        return ['data-role' => 'admin'];
    }
    return [];
}

Symfony va générer :

<option value="ROLE_USER">Utilisateur</option>
<option value="ROLE_ADMIN" data-role="admin">Administrateur</option>

UTILE pour :
ajouter des classes CSS ciblées : return ['class' => 'text-danger'];

utiliser data-* dans ton JavaScript : document.querySelector('[data-role="admin"]').style.fontWeight = 'bold';

*/
