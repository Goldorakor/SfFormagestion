<?php

/* création d'une classe PHP PasswordConstraints.php pour regrouper les règles */
/* une classe utilitaire => une classe statique qui sert uniquement à regrouper de la logique réutilisable sans avoir besoin de créer une instance (new) pour l'utiliser */
namespace App\Validator;

use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints as Assert;

class PasswordConstraints
{
    public static function get(): array
    {
        return [
            /* new Assert\NotBlank(['message' => 'Le mot de passe ne peut pas être vide.']), dans éditer profil, il peut être vide ! */
            new Length([
                'min' => 12,
                'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                'max' => 4096,
            ]),
            new Regex([
                'pattern' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{12,}$/',
                'message' => 'Le mot de passe doit contenir au moins 12 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.',
            ]),
        ];
    }
}
