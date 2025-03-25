<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\BreadcrumbsGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


final class UtilisateurController extends AbstractController
{
    #[Route('/accueil/parametres/utilisateur', name: 'app_utilisateur')]
    public function index(UserRepository $userRepository, BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Liste des utilisateurs'], // Pas de route car c’est la page actuelle
        ]);

        $users = $userRepository->findAll();
        
        
        return $this->render('utilisateur/index.html.twig', [
            'users' => $users,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }



    #[Route('/accueil/parametres/utilisateur/new', name: 'new_utilisateur')] // 'new_utilisateur' est un nom cohérent qui décrit bien la fonction
    #[Route('/accueil/parametres/utilisateur/{id}/edit', name: 'edit_utilisateur')] // 'edit_utilisateur' est un nom cohérent qui décrit bien la fonction attendue
    #[IsGranted('ROLE_ADMIN')]
    public function new_edit(User $user = null, Request $request, EntityManagerInterface $entityManager, BreadcrumbsGenerator $breadcrumbsGenerator): Response // pour ajouter un apprenant à notre BDD
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Liste des utilisateurs', 'route' => 'app_utilisateur'], 
            ['label' => !$user ? "Créer un utilisateur" : "Modifier un utilisateur"], // Pas de route car c’est la page actuelle
        ]);
        // $variable = (condition) ? valeur_si_vrai : valeur_si_faux;
        // Si condition est vraie → la valeur après ? est assignée.
        // Si condition est fausse → la valeur après : est assignée.
        
        
        // 1. si pas de utilisateur, on crée un nouveau utilisateur (un objet utilisateur est bien créé ici) - s'il existe déjà, pas besoin de le créer
        if(!$user) {
            $user = new User();
        }

        // 2. on crée le formulaire à partir de UtilisateurType (on veut ce modèle là bien entendu)
        $form = $this->createForm(UtilisateurType::class, $user); // c'est bien la méthode createForm() qui permet de créer le formulaire

        
        // 4. le traitement s'effectue ici ! si le formulaire soumis est correct, on fera l'insertion en BDD
        $form->handleRequest($request);
        

        // bloc qui concerne la soumission
        if ($form->isSubmitted() && $form->isValid()) {
            
            $user = $form->getData(); // on récupère les données du formulaire dans notre objet utilisateur
            
            $entityManager->persist($user); // équivaut à la méthode prepare() en PDO

            $entityManager->flush(); // équivaut à la méthode execute() en PDOStatement

            // redirection vers la liste des utilisateurs (si formulaire soumis et formulaire valide)
            return $this->redirectToRoute('app_utilisateur');
        }
        // fin du bloc


        // 3. on affiche le formulaire créé dans la page dédiée à cet affichage -> {{ form(formAddUtilisateur) }} --> affichage par défaut 
        return $this->render('utilisateur/new.html.twig', [ // 'utilisateur/new.html.twig' -> vue dédiée à l'affichage du formulaire : on crée un nouveau fichier dans le dossier apprenant
            // 'form' => $form,  on fait passer une variable form qui prend la valeur $form
            // on change le nom pour éviter toute ambiguité 'form' -> 'formAddUtilisateur' comme expliqué dans new.html.twig
            'formAddUtilisateur' => $form,
            'edit' => $user->getId(), // comportement booléen -> permet dans la vue de faire la diff entre création d'un utilisateur et édition d'un utilisateur
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }




    #[IsGranted('ROLE_ADMIN')]
    #[Route('/accueil/parametres/utilisateur/{id}/delete', name: 'delete_utilisateur')]
    public function delete(User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user); // on enlève l'utilisateur ciblé de la collection des utilisateurs
        $entityManager->flush(); // on effectue la requête SQL : DELETE FROM

        return $this->redirectToRoute('app_utilisateur'); // après une suppression, on redirige vers la liste des utilisateurs
    }



    // méthode provisoire pour ajouter un utilisateur 01 avec rôle ADMIN
    #[Route('/ajout-user', name: 'app_ajout_user')]
    public function addUser(EntityManagerInterface $em, UserPasswordHasherInterface $hasher)
    {
        $user = new User();
        $user->setNom('Varol');
        $user->setPrenom('Nalan');
        $user->setEmail('varol.nalan@gmail.com');
        $user->setIsApproved(1);

        // Hash du mot de passe
        $hashedPassword = $hasher->hashPassword($user, 'varol12345678');
        $user->setPassword($hashedPassword);

        // Rôle facultatif
        $user->setRoles(['ROLE_ADMIN']);

        // Enregistrement
        $em->persist($user);
        $em->flush();

        return new Response('Utilisateur ajouté !');
    }

    /*

    Dans le navigateur, il faut aller à l’adresse :
    http://localhost:8000/ajout-user

    On devrait voir :
    Utilisateur ajouté !

    Et le user sera ajouté dans la base de données
    
    */



    // méthode provisoire pour ajouter un utilisateur 02 avec rôle USER
    #[Route('/ajout-user02', name: 'app_ajout_user02')]
    public function addUser02(EntityManagerInterface $em, UserPasswordHasherInterface $hasher)
    {
        $user = new User();
        $user->setNom('Heid');
        $user->setPrenom('Michael');
        $user->setEmail('heid.michael@gmail.com');
        $user->setIsApproved(1);

        // Hash du mot de passe
        $hashedPassword = $hasher->hashPassword($user, 'heid12345678');
        $user->setPassword($hashedPassword);

        // Rôle facultatif
        $user->setRoles(['ROLE_USER']);

        // Enregistrement
        $em->persist($user);
        $em->flush();

        return new Response('Utilisateur ajouté !');
    }

    /*

    Dans le navigateur, il faut aller à l’adresse :
    http://localhost:8000/ajout-user02

    On devrait voir :
    Utilisateur ajouté !

    Et le user sera ajouté dans la base de données
    
    */


}
