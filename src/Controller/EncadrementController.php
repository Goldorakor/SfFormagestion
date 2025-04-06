<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[IsGranted('IS_AUTHENTICATED_FULLY')] /* seul un utilisateur bien connecté peut accéder aux méthodes de ce contrôleur */
final class EncadrementController extends AbstractController
{
    #[Route('/encadrement', name: 'app_encadrement')]
    public function index(): Response
    {
        return $this->render('encadrement/index.html.twig', [
            'controller_name' => 'EncadrementController',
        ]);
    }
}
