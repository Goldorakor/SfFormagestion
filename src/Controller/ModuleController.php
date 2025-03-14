<?php

namespace App\Controller;

use App\Entity\Module;
use App\Repository\ModuleRepository;
use App\Service\BreadcrumbsGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ModuleController extends AbstractController
{
    #[Route('/accueil/creations/module', name: 'app_module')]
    public function index(ModuleRepository $moduleRepository, BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Créations', 'route' => 'creations'],
            ['label' => 'Liste des modules'], // Pas de route car c’est la page actuelle
        ]);
        
        
        // méthode choisie qui ne permet pas de trier la liste des modules
        // $modules = $moduleRepository->findAll();

        $modules = $moduleRepository->findBy([], ["nomModule"=>"ASC"]);


        return $this->render('module/index.html.twig', [
            'modules' => $modules,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }


    /*
    #[Route('/module/new', name: 'new_module')] // 'new_module' est un nom cohérent qui décrit bien la fonction
    #[Route('/module/{id}/edit', name: 'edit_module')] // 'edit_module' est un nom cohérent qui décrit bien la fonction attendue
    public function new_edit(Module $module = null, Request $request, EntityManagerInterface $entityManager): Response // pour ajouter un module à notre BDD
    {
        // 1. si pas de module, on crée un nouveau module (un objet module est bien créé ici) - s'il existe déjà, pas besoin de le créer
        if(!$module) {
            $module = new Module();
        }

        // 2. on crée le formulaire à partir de ModuleType (on veut ce modèle là bien entendu)
        $form = $this->createForm(ModuleType::class, $module); // c'est bien la méthode createForm() qui permet de créer le formulaire

        // 4. le traitement s'effectue ici ! si le formulaire soumis est correct, on fera l'insertion en BDD
        $form->handleRequest($request);

        // bloc qui concerne la soumission
        if ($form->isSubmitted() && $form->isValid()) {
            
            $module = $form->getData(); // on récupère les données du formulaire dans notre objet module
            
            $entityManager->persist($module); // équivaut à la méthode prepare() en PDO

            $entityManager->flush(); // équivaut à la méthode execute() en PDOStatement

            // redirection vers la liste des modules (si formulaire soumis et formulaire valide)
            return $this->redirectToRoute('app_module');
        }

        // 3. on affiche le formulaire créé dans la page dédiée à cet affichage -> {{ form(formAddModule) }} --> affichage par défaut 
        return $this->render('module/new.html.twig', [ // 'module/new.html.twig' -> vue dédiée à l'affichage du formulaire : on crée un nouveau fichier dans le dossier module
            // 'form' => $form,  on fait passer une variable form qui prend la valeur $form
            // on change le nom pour éviter toute ambiguité 'form' -> 'formAddModule' comme expliqué dans new.html.twig
            'formAddModule' => $form,
            'edit' => $module->getId(), // comportement booléen
        ]);
    }




    #[Route('/module/{id}/delete', name: 'delete_module')]
    public function delete(Module $module, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($module); // on enlève le module ciblé de la collection des modules
        $entityManager->flush(); // on effectue la requête SQL : DELETE FROM

        return $this->redirectToRoute('app_module'); // après une suppression, on redirige vers la liste des modules
    }
    */



    // on a besoin de cette méthode pour afficher correctement certaines informations dans la vue de détails d'une session
    #[Route('/accueil/creations/module/{id}', name: 'show_module')]
    public function show(Module $module): Response
    {
        return $this->render('module/show.html.twig', [
            'module' => $module, // au singulier puisqu'on ne passe qu'un seul objet 'module' et pas une collection d'objets 'module'
        ]);
    }
 
}
