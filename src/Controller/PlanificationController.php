<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[IsGranted('IS_AUTHENTICATED_FULLY')] /* seul un utilisateur bien connecté peut accéder aux méthodes de ce contrôleur */
final class PlanificationController extends AbstractController
{
    #[Route('/planification', name: 'app_planification')]
    public function index(): Response
    {
        return $this->render('planification/index.html.twig', [
            'controller_name' => 'PlanificationController',
        ]);
    }
}
