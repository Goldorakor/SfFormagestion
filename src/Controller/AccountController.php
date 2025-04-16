<?php

namespace App\Controller;

use App\Form\EditProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;


#[IsGranted('IS_AUTHENTICATED_FULLY')] /* seul un utilisateur bien connecté peut accéder aux méthodes de ce contrôleur */
final class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig', [
        ]);
    }




    #[Route('/accueil/mon-profil', name: 'modifier_profil')]
    public function modifierProfil(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {

        // on passe toujours par $this->getUser() dans modifierProfil() : 
        // Symfony donne l’utilisateur connecté, donc pas possible de modifier un autre.
        // c'est le comportement espéré ! 
        $user = $this->getUser();

        $form = $this->createForm(EditProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mot de passe : uniquement si un nouveau est fourni
            $plainPassword = $form->get('plainPassword')->getData();

            if (!empty($plainPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $em->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès. Veuillez vous reconnecter.');
            return $this->redirectToRoute('app_logout'); /* on déconnecte l'utilisateur pour qu'il se reconnecte */
        }

        return $this->render('account/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}

/*

IS_AUTHENTICATED_ANONYMOUSLY        Tout le monde, même non connecté.
IS_AUTHENTICATED_REMEMBERED         Connexion avec cookie "remember me" acceptée.
IS_AUTHENTICATED_FULLY              Connexion complète requise (mot de passe saisi).
ROLE_USER ou ROLE_ADMIN             Vérifie un rôle spécifique (tu les gères dans roles de ton entité).

*/
