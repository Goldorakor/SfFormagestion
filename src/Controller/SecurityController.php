<?php

namespace App\Controller;

use LogicException;
use App\Entity\Entreprise;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(
        Request $request,
        AuthenticationUtils $authenticationUtils,
        EntityManagerInterface $entityManager,
        #[Autowire(service: 'limiter.login_limiter')] RateLimiterFactory $loginLimiter
    ): Response {

        // Appliquer la limitation du nombre de requêtes (grâce au rate-limiter)
        $limiter = $loginLimiter->create($request->getClientIp());
        $limit = $limiter->consume(); // Consomme 1 "jeton"

        if (!$limit->isAccepted()) {
            // Trop de tentatives : refuser l'accès
            return new Response('Trop de tentatives, réessayez plus tard.', Response::HTTP_TOO_MANY_REQUESTS);
        }


        // Vérifie si le champ honey pot est rempli
        $data = $request->request->get('login_form');
        if (!empty($data['hp'])) {
            // Si le champ est rempli, rediriger ou afficher un message
            return new Response('Un bot a été détecté, veuillez réessayer plus tard.', Response::HTTP_FORBIDDEN);
        }
        

        // Récupère la seule entreprise existante
        $entreprise = $entityManager->getRepository(Entreprise::class)->findOneBy([]);

        // Gestion normale du formulaire de connexion
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'entreprise' => $entreprise,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
