<?php

namespace App\Controller;

use App\Service\BreadcrumbsGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[IsGranted('IS_AUTHENTICATED_FULLY')] /* seul un utilisateur bien connecté peut accéder aux méthodes de ce contrôleur */
final class PageController extends AbstractController
{
    // méthode non utilisée pour le moment
    #[Route('/page', name: 'app_page')]
    public function index(): Response
    {
        return $this->render('page/index.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }




    #[Route('/accueil/parametres/termes_publiables/reglement_interieur', name: 'reglement_interieur_page')]
    public function regInterieur(BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Termes publiables', 'route' => 'termes_publiables'],
            ['label' => "Règlement intérieur"], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('page/reglement_interieur.html.twig',[
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }


    #[Route('/accueil/parametres/termes_publiables/conditions_generales_utilisation', name: 'conditions_generales_utilisation_page')]
    public function condGenUtilisation(BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Termes publiables', 'route' => 'termes_publiables'],
            ['label' => "Conditions générales d'utilisation"], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('page/conditions_generales_utilisation.html.twig',[
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }




    #[Route('/accueil/parametres/termes_publiables/conditions_generales_vente', name: 'conditions_generales_vente_page')]
    public function condGenVente(BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Termes publiables', 'route' => 'termes_publiables'],
            ['label' => 'Conditions générales de vente'], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('page/conditions_generales_vente.html.twig',[
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }




    #[Route('/accueil/parametres/termes_publiables', name: 'termes_publiables')]
    public function termesPubliables(BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Termes publiables'], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('page/termes_publiables.html.twig',[
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }




    #[Route('/accueil/parametres/modeles_documents', name: 'modeles_documents')]
    public function modelesDocuments(BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Liste des modèles de documents'], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('page/modeles_documents.html.twig',[
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }




    #[Route('/accueil/parametres', name: 'parametres')]
    public function parametres(BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres'], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('page/parametres.html.twig',[
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }




    #[Route('/accueil/suivis', name: 'suivis')]
    public function suivis(BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Suivis'], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('page/suivis.html.twig',[
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }




    #[Route('/accueil/creations', name: 'creations')]
    public function creations(BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Créations'], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('page/creations.html.twig',[
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }




    #[Route('/accueil', name: 'accueil')]
    public function accueil(BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil'], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('page/accueil.html.twig',[
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }



    
    // voie de garage pour les fonctionnalités en cours de développement ! 
    #[Route('/accueil/construction', name: 'construction')]
    public function construction(BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'En construction'], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('page/construction.html.twig',[
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }


    

    // voie de garage pour les tests d'affichage ! 
    #[Route('/accueil/affichage', name: 'affichage')]
    public function affichage(BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Affichage'], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('page/affichage.html.twig',[
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }

}


/*
    #[Route('/a-propos', name: 'page_apropos')]
    public function apropos(): Response
    {
        return $this->render('pages/a_propos.html.twig');
    }

    #[Route('/contact', name: 'page_contact')]
    public function contact(): Response
    {
        return $this->render('pages/contact.html.twig');
    }
*/
