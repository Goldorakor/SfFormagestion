<?php

namespace App\Controller;

use App\Entity\Programme;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProgrammeController extends AbstractController
{
    #[Route('/programme', name: 'app_programme')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $programmes = $entityManager->getRepository(Programme::class)->findAll();
        return $this->render('programme/index.html.twig', [
            'programmes' => $programmes,
        ]);
    }
}


