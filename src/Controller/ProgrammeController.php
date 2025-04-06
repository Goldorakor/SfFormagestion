<?php

namespace App\Controller;

use App\Entity\Programme;
use App\Service\BreadcrumbsGenerator;
use App\Repository\ProgrammeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[IsGranted('IS_AUTHENTICATED_FULLY')] /* seul un utilisateur bien connecté peut accéder aux méthodes de ce contrôleur */
final class ProgrammeController extends AbstractController
{
    #[Route('/accueil/creations/programme', name: 'app_programme')]
    public function index(ProgrammeRepository $programmeRepository, BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Créations', 'route' => 'creations'],
            ['label' => 'Liste des programmes'], // Pas de route car c’est la page actuelle
        ]);
        
        
        // méthode choisie qui ne permet pas de trier la liste des programmes
        // $programmes = $programmeRepository->findAll();

        $programmes = $programmeRepository->findBy([], ["nomProgramme"=>"ASC"]);

        return $this->render('programme/index.html.twig', [
            'programmes' => $programmes,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }
}






