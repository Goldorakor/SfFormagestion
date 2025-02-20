<?php

namespace App\Controller;

use App\Entity\Societe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SocieteController extends AbstractController
{
    #[Route('/societe', name: 'app_societe')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $societes = $entityManager->getRepository(Societe::class)->findAll();
        return $this->render('societe/index.html.twig', [
            'societes' => $societes,
        ]);
    }
}

