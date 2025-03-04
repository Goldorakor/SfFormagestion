<?php

namespace App\Controller;

use App\Entity\Responsable;
use App\Form\ResponsableType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ResponsableRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ResponsableController extends AbstractController
{
    #[Route('/responsable', name: 'app_responsable')]
    public function index(ResponsableRepository $responsableRepository): Response
    {
        // méthode choisie qui ne permet pas de trier la liste des responsables
        // $responsables = $responsableRepository->findAll();

        $responsables = $responsableRepository->findBy([], ["nom"=>"ASC"]);

        return $this->render('responsable/index.html.twig', [
            'responsables' => $responsables,
        ]);
    }


    

    #[Route('/responsable/new', name: 'new_responsable')] // 'new_responsable' est un nom cohérent qui décrit bien la fonction
    #[Route('/responsable/{id}/edit', name: 'edit_responsable')] // 'edit_responsable' est un nom cohérent qui décrit bien la fonction attendue
    public function new_edit(Responsable $responsable = null, Request $request, EntityManagerInterface $entityManager): Response // pour ajouter un responsable à notre BDD
    {
        // 1. si pas de responsable, on crée un nouveau responsable (un objet responsable est bien créé ici) - s'il existe déjà, pas besoin de le créer
        if(!$responsable) {
            $responsable = new Responsable();
        }

        // 2. on crée le formulaire à partir de ResponsableType (on veut ce modèle là bien entendu)
        $form = $this->createForm(ResponsableType::class, $responsable); // c'est bien la méthode createForm() qui permet de créer le formulaire

        // 4. le traitement s'effectue ici ! si le formulaire soumis est correct, on fera l'insertion en BDD
        $form->handleRequest($request);


        // bloc qui concerne la soumission
        if ($form->isSubmitted() && $form->isValid()) {
            
            $responsable = $form->getData(); // on récupère les données du formulaire dans notre objet responsable
            
            $entityManager->persist($responsable); // équivaut à la méthode prepare() en PDO

            $entityManager->flush(); // équivaut à la méthode execute() en PDOStatement

            // redirection vers la liste des responsables (si formulaire soumis et formulaire valide)
            return $this->redirectToRoute('app_responsable');
        }
        // fin du bloc
        

        // 3. on affiche le formulaire créé dans la page dédiée à cet affichage -> {{ form(formAddResponsable) }} --> affichage par défaut 
        return $this->render('responsable/new.html.twig', [ // 'responsable/new.html.twig' -> vue dédiée à l'affichage du formulaire : on crée un nouveau fichier dans le dossier responsable
            // 'form' => $form,  on fait passer une variable form qui prend la valeur $form
            // on change le nom pour éviter toute ambiguité 'form' -> 'formAddResponsable' comme expliqué dans new.html.twig
            'formAddResponsable' => $form,
            'edit' => $responsable->getId(), // comportement booléen
        ]);
    }



    #[Route('/responsable/{id}/delete', name: 'delete_responsable')]
    public function delete(Responsable $responsable, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($responsable); // on enlève le responsable ciblé de la collection des responsables
        $entityManager->flush(); // on effectue la requête SQL : DELETE FROM

        return $this->redirectToRoute('app_responsable'); // après une suppression, on redirige vers la liste des responsables
    }


    
    #[Route('/responsable/{id}', name: 'show_responsable')]
    public function show(Responsable $responsable): Response
    {
       
        // on crée un tableau vide, pour y ranger les responsabilités par société -> on ne veut pas une simple liste mais trier les responsabilités en fonction des sociétés (on fait des groupes)
        $responsabilitesParSociete = [];

        // On parcourt toutes les responsabilités du responsable
        foreach ($responsable->getResponsabilites() as $responsabilite) { // $responsable->getResponsabilites() retourne la collection de responsabilités du responsable. On boucle sur chaque responsabilité
            $societe = $responsabilite->getSociete(); // On récupère l’objet 'Societe' correspondant à la responsabilité en cours (chaque responsabilité est liée à une société)

            // On regroupe par société
            $responsabilitesParSociete[$societe->getId()]['societe'] = $societe; // On stocke l’objet société sous la clé correspondant à son id -> on ne crée qu’une seule entrée par société.
            $responsabilitesParSociete[$societe->getId()]['responsabilites'][] = $responsabilite; // On ajoute la responsabilité actuelle dans le tableau responsabilites associé à la société -> toutes les responsabilités d'une même société sont stockées ensemble. 
        }
        
        
        return $this->render('responsable/show.html.twig', [
            'responsable' => $responsable,
            'responsabilitesParSociete' => $responsabilitesParSociete, // besoin de passer cette variable -> pour afficher les responsabilités d'un responsable, organisées par société. Le tableau $responsabilitesParSociete contenant les responsabilités organisées par société.
        ]);
    }
}



/*
IMPORTANT
Le tableau $responsabilitesParSociete a cette structure :
[
    1 => [
        'societe' => Societe { id: 1, raisonSociale: "Société A" },
        'responsabilites' => [
            Responsabilite { typeResponsable: "Légal" },
            Responsabilite { typeResponsable: "Administratif" },
        ]
    ],
    2 => [
        'societe' => Societe { id: 2, raisonSociale: "Société B" },
        'responsabilites' => [
            Responsabilite { typeResponsable: "RH" },
        ]
    ]
]
    */
