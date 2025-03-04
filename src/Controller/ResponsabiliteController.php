<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ResponsabiliteController extends AbstractController
{
    #[Route('/responsabilite', name: 'app_responsabilite')]
    public function index(): Response
    {
        return $this->render('responsabilite/index.html.twig', [
            'controller_name' => 'ResponsabiliteController',
        ]);
    }
}
