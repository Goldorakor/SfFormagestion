<?php

namespace App\Controller;

use App\Entity\Formation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class FormationController extends AbstractController
{
    #[Route('/formation', name: 'app_formation')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $formations = $entityManager->getRepository(Formation::class)->findAll();
        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }
}


