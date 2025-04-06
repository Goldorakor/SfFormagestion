<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[IsGranted('IS_AUTHENTICATED_FULLY')] /* seul un utilisateur bien connecté peut accéder aux méthodes de ce contrôleur */
final class QuestionnaireController extends AbstractController
{
    #[Route('/questionnaire', name: 'app_questionnaire')]
    public function index(): Response
    {
        return $this->render('questionnaire/index.html.twig', [
            'controller_name' => 'QuestionnaireController',
        ]);
    }
}
