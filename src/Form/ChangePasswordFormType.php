<?php

namespace App\Form;

use App\Validator\PasswordConstraints; /* pour utiliser la regex */
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;


/* formulaire de changement de mot de passe */
class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
                'first_options' => [
                    'constraints' => array_merge([ /* array_merge() permet de garder les contraintes existantes (comme PasswordStrength) et d’ajouter celles définies dans PasswordConstraints */
                        new NotBlank([
                            'message' => 'Veuillez entrer un mot de passe',
                        ]),
                        /* on commente ici car on a centralisé les règles de validation dans validator/PasswordConstraints.php
                        new Length([
                            'min' => 12,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),*/
                        new PasswordStrength(),
                        new NotCompromisedPassword(),
                    ], PasswordConstraints::get()), // On ajoute les contraintes (Regex, longueur) définies dans Validator/PasswordConstraints.php
                    'label' => 'Nouveau mot de passe',
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                ],
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
