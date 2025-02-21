<?php

namespace App\Controller;

use App\Entity\Apprenant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// classe ApprenantController hérite de la classe AbstractController, ce qui permet d'accéder à certaines méthodes pré-établies
final class ApprenantController extends AbstractController
{
    // url de la route : /apprenant
    // nom de la route : app_apprenant -> peut être changé mais doit être différent de tous les names de tous les controleurs !
    #[Route('/apprenant', name: 'app_apprenant')]
    // méthode index 
    public function index(EntityManagerInterface $entityManager): Response
    {
        $name = 'nom';
        $apprenants = $entityManager->getRepository(Apprenant::class)->findAll();

        // permet de faire le lien entre le controleur et la vue (vers la vue 'apprenant/index.html.twig')
        return $this->render('apprenant/index.html.twig', [
            // argument par défaut en guise d''exemple 
            // {{ controler_name }} dans apprenant/index.html.twig -> navigateur (http://127.0.0.1:8000/apprenant) affiche 'ApprenantController'
            'controller_name' => $name,
            'apprenants' => $apprenants,
        ]);
    }
}



// fetching : parcours de la BDD

// commentaires pour tester le push du pc du centre vers github