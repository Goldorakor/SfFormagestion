<?php

namespace App\Controller;

use App\Entity\Societe;
use App\Repository\SocieteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SocieteController extends AbstractController
{
    #[Route('/societe', name: 'app_societe')]
    public function index(SocieteRepository $societeRepository): Response
    {
        // méthode choisie qui ne permet pas de trier la liste des sociétés
        // $societes = $societeRepository->findAll();

        $societes = $societeRepository->findBy([], ["raisonSociale"=>"ASC"]);

        return $this->render('societe/index.html.twig', [
            'societes' => $societes,
        ]);
    }
}

