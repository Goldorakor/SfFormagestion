<?php

namespace App\Controller;

use App\Entity\Representant;
use App\Form\RepresentantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class RepresentantController extends AbstractController
{
    // je ne supprime pas cette méthode, peut-être servira-t-elle plus tard
    #[Route('/representant', name: 'app_representant')]
    public function index(): Response
    {
        return $this->render('representant/index.html.twig', [
            'controller_name' => 'RepresentantController',
        ]);
    }


    #[Route('/representant/new', name: 'new_representant')] // 'new_representant' est un nom cohérent qui décrit bien la fonction
    #[Route('/representant/{id}/edit', name: 'edit_representant')] // 'edit_representant' est un nom cohérent qui décrit bien la fonction attendue
    public function new_edit(Representant $representant = null, Request $request, EntityManagerInterface $entityManager): Response // pour ajouter un représentant à notre BDD
    {
        // 1. si pas de representant, on crée un nouveau representant (un objet representant est bien créé ici) - s'il existe déjà, pas besoin de le créer
        if(!$representant) {
            $representant = new Representant();
        }

        // 2. on crée le formulaire à partir de RepresentantType (on veut ce modèle là bien entendu)
        $form = $this->createForm(RepresentantType::class, $representant); // c'est bien la méthode createForm() qui permet de créer le formulaire

        
        // 4. le traitement s'effectue ici ! si le formulaire soumis est correct, on fera l'insertion en BDD
        $form->handleRequest($request);
        

        // bloc qui concerne la soumission
        if ($form->isSubmitted() && $form->isValid()) {
            
            $representant = $form->getData(); // on récupère les données du formulaire dans notre objet representant
            
            $entityManager->persist($representant); // équivaut à la méthode prepare() en PDO

            $entityManager->flush(); // équivaut à la méthode execute() en PDOStatement

            // redirection vers le formulaire du représentant (rempli, si tout fonctionne !) (si formulaire soumis et formulaire valide) -> pas le choix car il n'existe pas de liste de représentants, ni de vue de détails d'un représentant
            return $this->redirectToRoute('edit_representant');
        }
        // fin du bloc


        // 3. on affiche le formulaire créé dans la page dédiée à cet affichage -> {{ form(formAddRepresentant) }} --> affichage par défaut 
        return $this->render('representant/new.html.twig', [ // 'representant/new.html.twig' -> vue dédiée à l'affichage du formulaire : on crée un nouveau fichier dans le dossier representant
            // 'form' => $form,  on fait passer une variable form qui prend la valeur $form
            // on change le nom pour éviter toute ambiguité 'form' -> 'formAddRepresentant' comme expliqué dans new.html.twig
            'formAddRepresentant' => $form,
            'edit' => $representant->getId(), // comportement booléen -> permet dans la vue de faire la diff entre création d'un representant et édition d'un representant (peut servir plus tard donc on garde ce dispositif)
            'representant' => $representant ?? null, // rajout suite à un message d'erreur où il prétend que la variable $representant n'existe pas
        ]);
    }
}
