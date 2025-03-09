<?php

namespace App\Controller;

use DateTime;
use App\Entity\Apprenant;
use App\Form\ApprenantType;
use App\Repository\ApprenantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// classe ApprenantController hérite de la classe AbstractController, ce qui permet d'accéder à certaines méthodes pré-établies
final class ApprenantController extends AbstractController
{
    // url de la route : /apprenant
    // nom de la route : app_apprenant -> peut être changé mais doit être différent de tous les names de tous les controleurs !
    #[Route('/accueil/creations/apprenant', name: 'app_apprenant')]

    // méthode index (première façon de faire)
    // public function index(EntityManagerInterface $entityManager): Response

    // méthode index (deuxième façon de faire)
    public function index(ApprenantRepository $apprenantRepository): Response
    {
        // exemple du début -> désuet pour la suite
        $name = 'nom';

        // méthode index (première façon de faire)
        // $apprenants = $entityManager->getRepository(Apprenant::class)->findAll();

        // méthode index (deuxième façon de faire)
        // $apprenants = $apprenantRepository->findAll();

        // méthode index (troisième façon de faire)
        $apprenants = $apprenantRepository->findBy([], ["nom"=>"ASC"]);
        // SELECT * FROM apprenant ORDER BY nom


        // un exemple si on voulait affiner le tri plus finement
        // $apprenants = $societeRepository->findBy(["ville"=>"Strasbourg"], ["nom"=>"ASC"]);
        // SELECT * FROM apprenant WHERE ville = 'Strasbourg' ORDER BY nom



        // permet de faire le lien entre le controleur et la vue (vers la vue 'apprenant/index.html.twig')
        return $this->render('apprenant/index.html.twig', [
            // argument par défaut en guise d''exemple 
            // {{ controler_name }} dans apprenant/index.html.twig -> navigateur (http://127.0.0.1:8000/apprenant) affiche 'ApprenantController'
            'controller_name' => $name,
            'apprenants' => $apprenants,
        ]);
    }




    #[Route('/accueil/creations/apprenant/new', name: 'new_apprenant')] // 'new_apprenant' est un nom cohérent qui décrit bien la fonction
    #[Route('/accueil/creations/apprenant/{id}/edit', name: 'edit_apprenant')] // 'edit_apprenant' est un nom cohérent qui décrit bien la fonction attendue
    public function new_edit(Apprenant $apprenant = null, Request $request, EntityManagerInterface $entityManager): Response // pour ajouter un apprenant à notre BDD
    {
        // 1. si pas de apprenant, on crée un nouveau apprenant (un objet apprenant est bien créé ici) - s'il existe déjà, pas besoin de le créer
        if(!$apprenant) {
            $apprenant = new Apprenant();
        }

        // 2. on crée le formulaire à partir de ApprenantType (on veut ce modèle là bien entendu)
        $form = $this->createForm(ApprenantType::class, $apprenant); // c'est bien la méthode createForm() qui permet de créer le formulaire

        
        // 4. le traitement s'effectue ici ! si le formulaire soumis est correct, on fera l'insertion en BDD
        $form->handleRequest($request);
        

        // bloc qui concerne la soumission
        if ($form->isSubmitted() && $form->isValid()) {
            
            $apprenant = $form->getData(); // on récupère les données du formulaire dans notre objet apprenant
            
            $entityManager->persist($apprenant); // équivaut à la méthode prepare() en PDO

            $entityManager->flush(); // équivaut à la méthode execute() en PDOStatement

            // redirection vers la liste des apprenants (si formulaire soumis et formulaire valide)
            return $this->redirectToRoute('app_apprenant');
        }
        // fin du bloc


        // 3. on affiche le formulaire créé dans la page dédiée à cet affichage -> {{ form(formAddApprenant) }} --> affichage par défaut 
        return $this->render('apprenant/new.html.twig', [ // 'apprenant/new.html.twig' -> vue dédiée à l'affichage du formulaire : on crée un nouveau fichier dans le dossier apprenant
            // 'form' => $form,  on fait passer une variable form qui prend la valeur $form
            // on change le nom pour éviter toute ambiguité 'form' -> 'formAddApprenant' comme expliqué dans new.html.twig
            'formAddApprenant' => $form,
            'edit' => $apprenant->getId(), // comportement booléen -> permet dans la vue de faire la diff entre création d'un apprenant et édition d'un apprenant
        ]);
    }




    #[Route('/accueil/creations/apprenant/{id}/delete', name: 'delete_apprenant')]
    public function delete(Apprenant $apprenant, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($apprenant); // on enlève l'apprenant ciblé de la collection des apprenants
        $entityManager->flush(); // on effectue la requête SQL : DELETE FROM

        return $this->redirectToRoute('app_apprenant'); // après une suppression, on redirige vers la liste des apprenants
    }



    
    #[Route('/accueil/creations/apprenant/{id}', name: 'show_apprenant')]
    public function show(Apprenant $apprenant): Response
    {
        $now = new DateTime(); // on a besoin de créer cet objet DateTime pour savoir si une session est à venir, en cours ou terminée dans la vue de détails de l'apprenant (repère temporel)
        
        return $this->render('apprenant/show.html.twig', [
            'apprenant' => $apprenant, // au singulier puisqu'on ne passe qu'un seul objet 'apprenant' et pas une collection d'objets 'apprenant'
            'now' => $now,
        ]);
    }
}

// fetching : parcours de la BDD





// Symfony redirigera tous les utilisateurs vers la même page après connexion, et tu pourras ensuite gérer les fonctionnalités accessibles en fonction des rôles directement dans tes templates ou tes contrôleurs.

/*
Gérer les fonctionnalités selon les rôles

Dans tes templates Twig, tu peux utiliser :

{% if is_granted('ROLE_ADMIN') %}
    <a href="{{ path('admin_dashboard') }}">Accès admin</a>
{% endif %}

Et dans tes contrôleurs, tu peux restreindre l’accès avec :

if (!$this->isGranted('ROLE_ADMIN')) {
    throw $this->createAccessDeniedException('Accès refusé.');
}

Comme ça, les deux rôles accèdent aux mêmes pages, mais avec des fonctionnalités adaptées ! 🎯
*/



