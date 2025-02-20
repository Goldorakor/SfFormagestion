<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RepresentantController extends AbstractController
{
    #[Route('/representant', name: 'app_representant')]
    public function index(): Response
    {
        return $this->render('representant/index.html.twig', [
            'controller_name' => 'RepresentantController',
        ]);
    }
}
