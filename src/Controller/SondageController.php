<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[IsGranted('IS_AUTHENTICATED_FULLY')] /* seul un utilisateur bien connecté peut accéder aux méthodes de ce contrôleur */
final class SondageController extends AbstractController
{
    #[Route('/sondage', name: 'app_sondage')]
    public function index(): Response
    {
        return $this->render('sondage/index.html.twig', [
            'controller_name' => 'SondageController',
        ]);
    }

    
    
    // les deux méthodes suivantes ont été mises en place pour résoudre un gros souci de paramétrage de Symfony
    #[Route('/test-env-param', name: 'test_env_param')]
    public function testEnvParam(): Response
    {
        $testParam = $this->getParameter('test_param');
    dump($testParam); // Affiche la valeur du paramètre dans la barre de débogage
    return new Response('Test réussi, paramètre: ' . $testParam); // Affiche la valeur du paramètre dans la réponse
    }
    

    #[Route('/test-logo-directory', name: 'test_logo_directory')]
    public function testLogoDirectory(): Response
    {
        $logoDirectory = $this->getParameter('logos_directory');
        dump($logoDirectory); // Affiche le chemin du répertoire des logos
        return new Response('Répertoire des logos : ' . $logoDirectory); // Affiche le chemin du répertoire dans la réponse
    }

}
