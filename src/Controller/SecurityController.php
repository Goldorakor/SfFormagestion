<?php

namespace App\Controller;

use LogicException;
use App\Entity\Entreprise;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager): Response
    {
        // Récupère la seule entreprise existante (ou null si elle n’existe pas)
        $entreprise = $entityManager->getRepository(Entreprise::class)->findOneBy([]);
        
        // Erreur de connexion éventuelle
        $error = $authenticationUtils->getLastAuthenticationError();

        // Dernier identifiant saisi par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();


        // dump($entreprise->getLogoFilename());
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'entreprise' => $entreprise, // il faut passer cette variable dans la vue pour afficher le logo de Forma'Toque
        ]);
    }
    

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
