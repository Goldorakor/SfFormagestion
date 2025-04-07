<?php

namespace App\Form;

use App\Validator\PasswordConstraints; /* pour utiliser la regex */
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;


/* formulaire pour éditer son compte personnel */
class EditProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Nouvel email',
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false, // car ce n'est pas directement une propriété de User (un password en clair n'est pas stocké en BDD)
                'required' => false, // facultatif si l'utilisateur ne veut pas changer
                

                /*
                'first_options' => ['label' => 'Nouveau mot de passe'],
                */

                'first_options' => [
                    'label' => 'Nouveau mot de passe',
                    'constraints' => PasswordConstraints::get(), // On ajoute la contrainte Regex définie dans Validator/PasswordConstraints.php
                ],

                'second_options' => ['label' => 'Répéter le mot de passe'],
                'invalid_message' => 'Les mots de passe doivent correspondre.',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
