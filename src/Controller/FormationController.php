<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class FormationController extends AbstractController
{
    #[Route('/accueil/creations/formation', name: 'app_formation')]
    public function index(FormationRepository $formationRepository): Response
    {
        // méthode choisie qui ne permet pas de trier la liste des formations
        // $formations = $formationRepository->findAll();

        $formations = $formationRepository->findBy([], ["nomFormation"=>"ASC"]);

        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }
}


