<?php

namespace App\Controller;

use App\Entity\Responsable;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ResponsableRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ResponsableController extends AbstractController
{
    #[Route('/responsable', name: 'app_responsable')]
    public function index(ResponsableRepository $responsableRepository): Response
    {
        $responsables = $responsableRepository->findAll();
        return $this->render('responsable/index.html.twig', [
            'responsables' => $responsables,
        ]);
    }
}




