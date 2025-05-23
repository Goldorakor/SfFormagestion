<?php

/* On ajoute un service pour générer les breadcrumbs */
/* Un service est une classe réutilisable qui effectue un traitement précis. Il est indépendant des requêtes HTTP et peut être utilisé dans un contrôleur, un autre service, un formulaire, etc */

namespace App\Service;

/* On importe l'interface UrlGeneratorInterface, qui est un service de Symfony 
permettant de générer des URL à partir des routes définies dans le projet. */
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/* Cette classe servira à générer le fil d'Ariane dynamiquement 
en fonction des routes passées en paramètre. */
class BreadcrumbsGenerator
{
    /* Déclarat° d’une ppté privée : $urlGenerator stockera l'instance 
    de UrlGeneratorInterface, ce qui nous permettra de générer les URL des pages */
    private UrlGeneratorInterface $urlGenerator;

    /* Injection de dépendance via le constructeur : 
    Lorsqu'un objet de la classe BreadcrumbsGenerator sera instancié, 
    Symfony injectera automatiquement le service UrlGeneratorInterface
    Ainsi, $this->urlGenerator pourra être utilisé pour générer des URLs dynamiquement. */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /* Déclaration de la méthode generate : 
    Cette méthode prend en entrée un tableau $routes, qui doit contenir les informations 
    sur les différentes étapes du fil d'Ariane. 
    Elle retourne un tableau contenant les éléments du fil d'Ariane. */
    public function generate(array $routes): array
    {
        /* Initialisation d’un tableau vide : 
        On initialise un tableau $breadcrumbs qui contiendra les différentes étapes du fil d'Ariane. */
        $breadcrumbs = [];

        /* Boucle sur les routes fournies : 
        On parcourt chaque élément du tableau $routes pour construire les données nécessaires au fil d’Ariane. */
        foreach ($routes as $route) {

            /* Pour chaque élément du tableau $routes, on ajoute un tableau associatif contenant :
            'label' : le texte qui sera affiché dans le fil d'Ariane (ex. "Accueil", "Produits", "Détail produit").
            'url' : l’URL associée. Si 'route' est définie, alors on utilise $this->urlGenerator->generate() 
            pour générer l’URL en fonction de la route Symfony.
            On passe aussi les éventuels paramètres avec ?? [] (si 'params' est défini, on l’utilise ; sinon, on passe un tableau vide).
            
            Si 'route' n'est pas définie, l'URL sera null, ce qui peut être utile pour l'étape active du fil d'Ariane.*/
            $breadcrumbs[] = [
                'label' => $route['label'],
                'url' => isset($route['route']) ? $this->urlGenerator->generate($route['route'], $route['params'] ?? []) : null
                /* isset($route['route']) : vérifie si la clé 'route' existe dans le tableau $route
                oui : $this->urlGenerator->generate($route['route'], $route['params'] ?? [])
                non : null */
                /* $this->urlGenerator->generate($route['route'], $route['params'] ?? [])
                l’appel au générateur d’URL de Symfony, avec deux arguments : le nom de la route et les paramètres éventuels de la route */
                /* $route['params'] ?? [] : Si 'params' existe dans $route, on l’utilise, Sinon, on utilise un tableau vide [] */
            ];
        }

        /* Retour du tableau des éléments du fil d'Ariane 
        Une fois la boucle terminée, on retourne la liste des éléments du fil d'Ariane sous forme d’un tableau. */
        return $breadcrumbs;
    }
}

/* 
Dans un contrôleur, on peut utiliser ce service ainsi :

$breadcrumbs = $breadcrumbsGenerator->generate([
    ['label' => 'Accueil', 'route' => 'app_home'],
    ['label' => 'Produits', 'route' => 'app_products'],
    ['label' => 'Ordinateurs portables'] // Pas de route car c’est la page actuelle
]);

Résultat retourné : un tableau de tableaux associatifs
[
    ['label' => 'Accueil', 'url' => '/'],
    ['label' => 'Produits', 'url' => '/produits'],
    ['label' => 'Ordinateurs portables', 'url' => null] // Page active sans lien
]
*/


/*
Contrôleur	                        Service
Lié à la requête HTTP	            Indépendant de la requête HTTP
Gère l’entrée/sortie utilisateur	Gère une logique métier réutilisable
Utilise les services	            Est utilisé par les contrôleurs ou d'autres services
*/
