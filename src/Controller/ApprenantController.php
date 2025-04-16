<?php

namespace App\Controller;

use DateTime;
use App\Entity\Apprenant;
use App\Form\ApprenantType;
use App\Service\BreadcrumbsGenerator;
use App\Repository\ApprenantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[IsGranted('IS_AUTHENTICATED_FULLY')] /* seul un utilisateur bien connecté peut accéder aux méthodes de ce contrôleur */
final class ApprenantController extends AbstractController // classe ApprenantController hérite de la classe AbstractController, ce qui permet d'accéder à certaines méthodes pré-établies
{
    // url de la route : /apprenant
    // nom de la route : app_apprenant -> peut être changé mais doit être différent de tous les names de tous les controleurs !
    #[Route('/accueil/creations/apprenant', name: 'app_apprenant')]
    public function index(ApprenantRepository $apprenantRepository, BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Créations', 'route' => 'creations'],
            ['label' => 'Liste des apprenants'], // Pas de route car c’est la page actuelle
        ]);
        
        $apprenants = $apprenantRepository->findBy([], ["nom"=>"ASC"]);
        // SELECT * FROM apprenant ORDER BY nom

        // un exemple si on voulait affiner le tri plus finement
        // $apprenants = $societeRepository->findBy(["ville"=>"Strasbourg"], ["nom"=>"ASC"]);
        // SELECT * FROM apprenant WHERE ville = 'Strasbourg' ORDER BY nom

        // permet de faire le lien entre le controleur et la vue (vers la vue 'apprenant/index.html.twig')
        return $this->render('apprenant/index.html.twig', [
            'apprenants' => $apprenants,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }




    #[Route('/admin/accueil/creations/apprenant/new', name: 'new_apprenant')] // 'new_apprenant' est un nom cohérent qui décrit bien la fonction
    #[Route('/admin/accueil/creations/apprenant/{id}/edit', name: 'edit_apprenant')] // 'edit_apprenant' est un nom cohérent qui décrit bien la fonction attendue
    #[IsGranted('ROLE_ADMIN')]
    public function new_edit(
        Apprenant $apprenant = null,
        Request $request,
        EntityManagerInterface $entityManager,
        BreadcrumbsGenerator $breadcrumbsGenerator
        ): Response // pour ajouter un apprenant à notre BDD
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Créations', 'route' => 'creations'],
            ['label' => 'Liste des apprenants', 'route' => 'app_apprenant'], 
            ['label' => !$apprenant ? "Créer un apprenant" : "Modifier un apprenant"], // Pas de route car c’est la page actuelle
        ]);
        // $variable = (condition) ? valeur_si_vrai : valeur_si_faux;
        // Si condition est vraie → la valeur après ? est assignée.
        // Si condition est fausse → la valeur après : est assignée.
        
        
        // 1. si pas de apprenant, on crée un nouveau apprenant (un objet apprenant est bien créé ici) - s'il existe déjà, pas besoin de le créer
        if(!$apprenant) {
            $apprenant = new Apprenant();
        }

        // 2. on crée le formulaire à partir de ApprenantType (on veut ce modèle là bien entendu)
        $form = $this->createForm(ApprenantType::class, $apprenant); // c'est bien la méthode createForm() qui permet de créer le formulaire

        
        // 4. le traitement s'effectue ici ! si le formulaire soumis est correct, on fera l'insertion en BDD
        $form->handleRequest($request);
        

        // bloc qui concerne la soumission
        if ($form->isSubmitted() && $form->isValid()) { // vérifie si le formulaire a bien été soumis et vérifie que le formulaire est valide, c'est-à-dire que les données soumises respectent les contraintes définies dans l'entité (par exemple, des champs requis ou des formats valides)
            
            $apprenant = $form->getData(); // on récupère les données du formulaire dans notre objet apprenant
            // Cette méthode récupère les données soumises dans le formulaire et les stocke dans l'entité liée, ici dans l'objet $apprenant. Cela signifie que toutes les valeurs saisies dans le formulaire sont maintenant dans l'objet apprenant, qui est une instance de l'entité correspondante
            // Pourquoi getData() ?
            // l’objet $apprenant n’existe pas encore. Il est créé automatiquement par le formulaire au moment de la soumission.
            // getData() récupère cet objet Apprenant complet et rempli à partir des données du formulaire.
            // C’est grâce au data_class dans le FormType que Symfony sait quelle entité remplir.
            // pas besoin d’appeler getDoctrine() ici, car on récupère directement l’objet généré et rempli par le formulaire..

            $entityManager->persist($apprenant); // équivaut à la méthode prepare() en PDO
            // indique à Doctrine que l'entité apprenant doit être persistée, c'est-à-dire qu'elle doit être ajoutée à la session de Doctrine pour qu'elle soit enregistrée dans la base de données lorsque l'opération de flush() sera effectuée

            $entityManager->flush(); // équivaut à la méthode execute() en PDOStatement
            // Cette méthode exécute réellement la requête SQL pour persister les modifications (ici, l'ajout ou la mise à jour de l'entité apprenant dans la base de données). flush() va enregistrer toutes les entités persistées dans la base de données à ce moment-là.

            // redirection vers la liste des apprenants (si formulaire soumis et formulaire valide)
            return $this->redirectToRoute('app_apprenant');
        }
        // fin du bloc


        // 3. on affiche le formulaire créé dans la page dédiée à cet affichage -> {{ form(formAddApprenant) }} --> affichage par défaut 
        return $this->render('apprenant/new.html.twig', [ // 'apprenant/new.html.twig' -> vue dédiée à l'affichage du formulaire : on crée un nouveau fichier dans le dossier apprenant
            // 'form' => $form,  on fait passer une variable form qui prend la valeur $form
            // on change le nom pour éviter toute ambiguité 'form' -> 'formAddApprenant' comme expliqué dans new.html.twig
            'formAddApprenant' => $form->createView(), // dans la vue Twig, Symfony attend toujours l’objet FormView (celui renvoyé par createView()), pas l’objet Form lui-même
            'edit' => $apprenant->getId(), // comportement booléen -> permet dans la vue de faire la diff entre création d'un apprenant et édition d'un apprenant
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }




    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/accueil/creations/apprenant/{id}/delete', name: 'delete_apprenant')]
    public function delete(Apprenant $apprenant, EntityManagerInterface $entityManager): Response
    {
        
        // Apprenant.php : Collection $inscriptions
        // voilà la collection d'entités à traiter pour la question d'intégrité référentielle
        
        // Vérifier s'il y a des inscriptions liées
        if (count($apprenant->getInscriptions()) > 0) {
            $this->addFlash('warning', "Impossible de supprimer cet apprenant : il possède encore au moins un lien avec une session (à travers une inscription). Supprimez ce lien en éditant le profil de la session d'abord.");
            return $this->redirectToRoute('show_apprenant', ['id' => $apprenant->getId()]); // on redirige immédiatement sur la vue de détails de l'apprenant (sans rien supprimer)
        }
        
        $entityManager->remove($apprenant); // on enlève l'apprenant ciblé de la collection des apprenants
        $entityManager->flush(); // on effectue la requête SQL : DELETE FROM

        return $this->redirectToRoute('app_apprenant'); // après une suppression, on redirige vers la liste des apprenants
    }



    
    #[Route('/accueil/creations/apprenant/{id}', name: 'show_apprenant')]
    public function show(Apprenant $apprenant, BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Créations', 'route' => 'creations'],
            ['label' => 'Liste des apprenants', 'route' => 'app_apprenant'], 
            ['label' => "Détails de l'apprenant #".$apprenant->getId(), 'params' => ['id' => $apprenant->getId()]], // Apprenant spécifique // Pas de route car c’est la page actuelle
        ]);
        
        
        $now = new DateTime(); // on a besoin de créer cet objet DateTime pour savoir si une session est à venir, en cours ou terminée dans la vue de détails de l'apprenant (repère temporel)
        
        return $this->render('apprenant/show.html.twig', [
            'apprenant' => $apprenant, // au singulier puisqu'on ne passe qu'un seul objet 'apprenant' et pas une collection d'objets 'apprenant'
            'now' => $now,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }
}

// fetching : parcours de la BDD





// Symfony redirigera tous les utilisateurs vers la même page après connexion, et tu pourras ensuite gérer les fonctionnalités accessibles en fonction des rôles directement dans tes templates ou tes contrôleurs.

/*
Gérer les fonctionnalités selon les rôles

Dans tes templates Twig, tu peux utiliser :

{% if is_granted('ROLE_ADMIN') %}
    <a href="{{ path('admin_dashboard') }}">Accès admin</a>
{% endif %}

Et dans tes contrôleurs, tu peux restreindre l’accès avec :

if (!$this->isGranted('ROLE_ADMIN')) {
    throw $this->createAccessDeniedException('Accès refusé.');
}

Comme ça, les deux rôles accèdent aux mêmes pages, mais avec des fonctionnalités adaptées !
*/
