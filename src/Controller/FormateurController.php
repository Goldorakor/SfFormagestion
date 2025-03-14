<?php

namespace App\Controller;

use DateTime;
use App\Entity\Formateur;
use App\Form\FormateurType;
use App\Service\BreadcrumbsGenerator;
use App\Repository\FormateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class FormateurController extends AbstractController
{
    #[Route('/accueil/creations/formateur', name: 'app_formateur')]
    public function index(FormateurRepository $formateurRepository, BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Créations', 'route' => 'creations'],
            ['label' => 'Liste des formateurs'], // Pas de route car c’est la page actuelle
        ]);

        
        // méthode choisie qui ne permet pas de trier la liste des formateurs
        // $formateurs = $formateurRepository->findAll();
        
        $formateurs = $formateurRepository->findBy([], ["nom"=>"ASC"]);
        return $this->render('formateur/index.html.twig', [
            'formateurs' => $formateurs,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }




    #[Route('/accueil/creations/formateur/new', name: 'new_formateur')] // 'new_formateur' est un nom cohérent qui décrit bien la fonction
    #[Route('/accueil/creations/formateur/{id}/edit', name: 'edit_formateur')] // 'edit_formateur' est un nom cohérent qui décrit bien la fonction attendue
    public function new_edit(Formateur $formateur = null, Request $request, EntityManagerInterface $entityManager, BreadcrumbsGenerator $breadcrumbsGenerator): Response // pour ajouter un formateur à notre BDD
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Créations', 'route' => 'creations'],
            ['label' => 'Liste des formateurs', 'route' => 'app_formateur'], 
            ['label' => !$formateur ? "Créer un formateur" : "Modifier un formateur"], // Pas de route car c’est la page actuelle
        ]);
        // $variable = (condition) ? valeur_si_vrai : valeur_si_faux;
        // Si condition est vraie → la valeur après ? est assignée.
        // Si condition est fausse → la valeur après : est assignée.
        
        
        // 1. si pas de formateur, on crée un nouveau formateur (un objet formateur est bien créé ici) - s'il existe déjà, pas besoin de le créer
        if(!$formateur) {
            $formateur = new Formateur();
        }

        // 2. on crée le formulaire à partir de FormateurType (on veut ce modèle là bien entendu)
        $form = $this->createForm(FormateurType::class, $formateur); // c'est bien la méthode createForm() qui permet de créer le formulaire

        // 4. le traitement s'effectue ici ! si le formulaire soumis est correct, on fera l'insertion en BDD
        $form->handleRequest($request);


        // bloc qui concerne la soumission
        if ($form->isSubmitted() && $form->isValid()) {
            
            $formateur = $form->getData(); // on récupère les données du formulaire dans notre objet formateur
            
            $entityManager->persist($formateur); // équivaut à la méthode prepare() en PDO

            $entityManager->flush(); // équivaut à la méthode execute() en PDOStatement

            // redirection vers la liste des formateurs (si formulaire soumis et formulaire valide)
            return $this->redirectToRoute('app_formateur');
        }
        // fin du bloc


        // 3. on affiche le formulaire créé dans la page dédiée à cet affichage -> {{ form(formAddFormateur) }} --> affichage par défaut 
        return $this->render('formateur/new.html.twig', [ // 'formateur/new.html.twig' -> vue dédiée à l'affichage du formulaire : on crée un nouveau fichier dans le dossier formateur
            // 'form' => $form,  on fait passer une variable form qui prend la valeur $form
            // on change le nom pour éviter toute ambiguité 'form' -> 'formAddFormateur' comme expliqué dans new.html.twig
            'formAddFormateur' => $form,
            'edit' => $formateur->getId(), // comportement booléen
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }


    

    #[Route('/accueil/creations/formateur/{id}/delete', name: 'delete_formateur')]
    public function delete(Formateur $formateur, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($formateur); // on enlève le formateur ciblé de la collection des formateurs
        $entityManager->flush(); // on effectue la requête SQL : DELETE FROM

        return $this->redirectToRoute('app_formateur'); // après une suppression, on redirige vers la liste des formateurs
    }


    
    #[Route('/accueil/creations/formateur/{id}', name: 'show_formateur')]
    public function show(Formateur $formateur, BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Créations', 'route' => 'creations'],
            ['label' => 'Liste des formateurs', 'route' => 'app_formateur'], 
            ['label' => "Détails du formateur #".$formateur->getId(), 'params' => ['id' => $formateur->getId()]], // Formateur spécifique // Pas de route car c’est la page actuelle
        ]);
        
        
        $now = new DateTime(); // on a besoin de créer cet objet DateTime pour savoir si une session est à venir, en cours ou terminée dans la vue de détails du formateur (repère temporel)
        
        return $this->render('formateur/show.html.twig', [
            'formateur' => $formateur,
            'now' => $now,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }

}


