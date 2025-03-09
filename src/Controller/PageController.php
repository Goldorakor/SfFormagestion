<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
    public function regInterieur(): Response
    {
        return $this->render('page/reglement_interieur.html.twig');
    }


    #[Route('/accueil/parametres/termes_publiables/conditions_generales_utilisation', name: 'conditions_generales_utilisation_page')]
    public function condGenUtilisation(): Response
    {
        return $this->render('page/conditions_generales_utilisation.html.twig');
    }


    #[Route('/accueil/parametres/termes_publiables/conditions_generales_vente', name: 'conditions_generales_vente_page')]
    public function condGenVente(): Response
    {
        return $this->render('page/conditions_generales_vente.html.twig');
    }


    #[Route('/accueil/parametres/termes_publiables', name: 'termes_publiables')]
    public function termesPubliables(): Response
    {
        return $this->render('page/termes_publiables.html.twig');
    }


    #[Route('/accueil/parametres/modeles_documents', name: 'modeles_documents')]
    public function modelesDocuments(): Response
    {
        return $this->render('page/modeles_documents.html.twig');
    }


    #[Route('/accueil/parametres', name: 'parametres')]
    public function parametres(): Response
    {
        return $this->render('page/parametres.html.twig');
    }


    #[Route('/accueil/suivis', name: 'suivis')]
    public function suivis(): Response
    {
        return $this->render('page/suivis.html.twig');
    }


    #[Route('/accueil/creations', name: 'creations')]
    public function creations(): Response
    {
        return $this->render('page/creations.html.twig');
    }


    #[Route('/accueil', name: 'accueil')]
    public function accueil(): Response
    {
        return $this->render('page/accueil.html.twig');
    }

    // voie de garage pour les fonctionnalités en cours de développement ! 
    #[Route('/accueil/construction', name: 'construction')]
    public function construction(): Response
    {
        return $this->render('page/construction.html.twig');
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