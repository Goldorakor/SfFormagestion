<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SondageController extends AbstractController
{
    #[Route('/sondage', name: 'app_sondage')]
    public function index(): Response
    {
        return $this->render('sondage/index.html.twig', [
            'controller_name' => 'SondageController',
        ]);
    }
}
