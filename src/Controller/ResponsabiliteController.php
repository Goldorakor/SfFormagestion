<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[IsGranted('IS_AUTHENTICATED_FULLY')] /* seul un utilisateur bien connecté peut accéder aux méthodes de ce contrôleur */
final class ResponsabiliteController extends AbstractController
{
    #[Route('/responsabilite', name: 'app_responsabilite')]
    public function index(): Response
    {
        return $this->render('responsabilite/index.html.twig', [
        ]);
    }
}
