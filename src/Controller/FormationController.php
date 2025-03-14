<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Service\BreadcrumbsGenerator;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class FormationController extends AbstractController
{
    #[Route('/accueil/creations/formation', name: 'app_formation')]
    public function index(FormationRepository $formationRepository, BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Créations', 'route' => 'creations'],
            ['label' => 'Liste des formations'], // Pas de route car c’est la page actuelle
        ]);
        
        
        // méthode choisie qui ne permet pas de trier la liste des formations
        // $formations = $formationRepository->findAll();

        $formations = $formationRepository->findBy([], ["nomFormation"=>"ASC"]);

        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }
}


