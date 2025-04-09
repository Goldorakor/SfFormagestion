<?php

namespace App\Controller;

use App\Entity\Societe;
use App\Form\SocieteType;
use App\Entity\Responsabilite;
use App\Repository\SocieteRepository;
use App\Service\BreadcrumbsGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[IsGranted('IS_AUTHENTICATED_FULLY')] /* seul un utilisateur bien connecté peut accéder aux méthodes de ce contrôleur */
final class SocieteController extends AbstractController
{
    #[Route('/accueil/creations/societe', name: 'app_societe')]
    public function index(SocieteRepository $societeRepository, BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Créations', 'route' => 'creations'],
            ['label' => 'Liste des sociétés'], // Pas de route car c’est la page actuelle
        ]);
        
        
        // méthode choisie qui ne permet pas de trier la liste des sociétés
        // $societes = $societeRepository->findAll();

        $societes = $societeRepository->findBy([], ["raisonSociale"=>"ASC"]);
        // SELECT * FROM societe ORDER BY raisonSociale


        // un exemple si on voulait affiner le tri plus finement
        // $societes = $societeRepository->findBy(["villeSiege"=>"Strasbourg"], ["raisonSociale"=>"ASC"]);
        // SELECT * FROM societe WHERE ville = 'Strasbourg' ORDER BY raisonSociale

        return $this->render('societe/index.html.twig', [
            'societes' => $societes,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }



    #[Route('/admin/accueil/creations/societe/new', name: 'new_societe')] // 'new_societe' est un nom cohérent qui décrit bien la fonction
    #[Route('/admin/accueil/creations/societe/{id}/edit', name: 'edit_societe')] // 'edit_societe' est un nom cohérent qui décrit bien la fonction attendue
    #[IsGranted('ROLE_ADMIN')]
    public function new_edit(Societe $societe = null, Request $request, EntityManagerInterface $entityManager, BreadcrumbsGenerator $breadcrumbsGenerator): Response // pour ajouter un societe à notre BDD
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Créations', 'route' => 'creations'],
            ['label' => 'Liste des sociétés', 'route' => 'app_societe'], 
            ['label' => !$societe ? "Créer une société" : "Modifier une société"], // Pas de route car c’est la page actuelle
        ]);
        // $variable = (condition) ? valeur_si_vrai : valeur_si_faux;
        // Si condition est vraie → la valeur après ? est assignée.
        // Si condition est fausse → la valeur après : est assignée.
        
        
        // 1. si pas de societe, on crée un nouveau societe (un objet societe est bien créé ici) - s'il existe déjà, pas besoin de le créer
        if(!$societe) {
            $societe = new Societe();
        }

        // 2. on crée le formulaire à partir de SocieteType (on veut ce modèle là bien entendu)
        $form = $this->createForm(SocieteType::class, $societe); // c'est bien la méthode createForm() qui permet de créer le formulaire

        // 4. le traitement s'effectue ici ! si le formulaire soumis est correct, on fera l'insertion en BDD
        $form->handleRequest($request);


        // bloc qui concerne la soumission
        if ($form->isSubmitted() && $form->isValid()) {
            
            $societe = $form->getData(); // on récupère les données du formulaire dans notre objet Societe


            // Sauvegarde de la société
            // $entityManager->persist($societe);  équivaut à la méthode prepare() en PDO (on le fait plus bas)
            // $entityManager->flush(); équivaut à la méthode execute() en PDOStatement -> on exécute tout à la fin



            // Supprimer les anciennes responsabilités de cette société avant de les mettre à jour (sinon on a plein de doublons dans la table)
            // Toujours prendre cette méthode pour supprimer des enregistrements dans les tables associatives
            foreach ($societe->getResponsabilites()->toArray() as $responsabilite) {
                $entityManager->remove($responsabilite); // suppression réelle
                $societe->removeResponsabilite($responsabilite); // optionnel, pour tenir la collection à jour côté PHP
            }


            // Récupération des responsables sélectionnés
            $responsableLegal = $form->get('responsableLegal')->getData();
            $responsableAdministratif = $form->get('responsableAdministratif')->getData();
            $responsableRH = $form->get('responsableRH')->getData();



            // Création des responsabilités si un responsable est sélectionné
            if ($responsableLegal) { // vérifie si l'utilisateur a effectivement sélectionné un responsable légal via le formulaire -> si un responsable a été choisi, une nouvelle responsabilité est créée.
                $responsabiliteLegal = new Responsabilite();
                $responsabiliteLegal->setSociete($societe);
                $responsabiliteLegal->setResponsable($responsableLegal);
                $responsabiliteLegal->setTypeResponsable("légal");
                $entityManager->persist($responsabiliteLegal); // équivaut à la méthode prepare() en PDO
            }

            if ($responsableAdministratif) { // vérifie si l'utilisateur a effectivement sélectionné un responsable administratif via le formulaire -> si un responsable a été choisi, une nouvelle responsabilité est créée
                $responsabiliteAdmin = new Responsabilite();
                $responsabiliteAdmin->setSociete($societe);
                $responsabiliteAdmin->setResponsable($responsableAdministratif);
                $responsabiliteAdmin->setTypeResponsable("administratif");
                $entityManager->persist($responsabiliteAdmin); // équivaut à la méthode prepare() en PDO
            }

            if ($responsableRH) { // vérifie si l'utilisateur a effectivement sélectionné un responsable RH via le formulaire -> si un responsable a été choisi, une nouvelle responsabilité est créée
                $responsabiliteRH = new Responsabilite();
                $responsabiliteRH->setSociete($societe);
                $responsabiliteRH->setResponsable($responsableRH);
                $responsabiliteRH->setTypeResponsable("RH");
                $entityManager->persist($responsabiliteRH); // équivaut à la méthode prepare() en PDO -> Enregistrer toutes les modifications (societe et responsabilités)
            }


            // Enregistrement de la société et des responsabilités
            $entityManager->persist($societe);
            $entityManager->flush(); // équivaut à la méthode execute() en PDOStatement


            // redirection vers la liste des societes (si formulaire soumis et formulaire valide)
            return $this->redirectToRoute('app_societe');
        }
        // fin du bloc
        

        // 3. on affiche le formulaire créé dans la page dédiée à cet affichage -> {{ form(formAddSociete) }} --> affichage par défaut 
        return $this->render('societe/new.html.twig', [ // 'societe/new.html.twig' -> vue dédiée à l'affichage du formulaire : on crée un nouveau fichier dans le dossier societe
            // 'form' => $form,  on fait passer une variable form qui prend la valeur $form
            // on change le nom pour éviter toute ambiguité 'form' -> 'formAddSociete' comme expliqué dans new.html.twig
            'formAddSociete' => $form->createView(),
            'edit' => $societe->getId(), // comportement booléen
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }

    
    
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/accueil/creations/societe/{id}/delete', name: 'delete_societe')]
    public function delete(Societe $societe, EntityManagerInterface $entityManager): Response
    {
        
        // Societe.php : Collection $apprenants - Collection $formateurs - Collection $responsabilites
        // voilà les collections d'entités à traiter pour la question d'intégrité référentielle
        
        // Vérifier s'il y a des apprenants liés
        if (count($societe->getApprenants()) > 0) {
            $this->addFlash('warning', "Impossible de supprimer cette société : elle possède encore au moins un salarié. Supprimez-le ou supprimez ce lien d'abord.");
            return $this->redirectToRoute('show_societe', ['id' => $societe->getId()]); // on redirige immédiatement sur la vue de détails de la société (sans rien supprimer)
        }

        // Vérifier s'il y a des formateurs liés
        if (count($societe->getFormateurs()) > 0) {
            $this->addFlash('warning', "Impossible de supprimer cette société : elle possède encore au moins un formateur. Supprimez-le ou supprimez ce lien d'abord.");
            return $this->redirectToRoute('show_societe', ['id' => $societe->getId()]); // on redirige immédiatement sur la vue de détails de la société (sans rien supprimer)
        }

        // Vérifier s'il y a des responsabilités liées
        if (count($societe->getResponsabilites()) > 0) {
            $this->addFlash('warning', "Impossible de supprimer cette société : elle possède encore au moins un lien avec un responsable. Supprimez ce lien en éditant le profil de la société d'abord.");
            return $this->redirectToRoute('show_societe', ['id' => $societe->getId()]); // on redirige immédiatement sur la vue de détails de la société (sans rien supprimer)
        }

        
        $entityManager->remove($societe); // on enlève la societe ciblée de la collection des societes
        $entityManager->flush(); // on effectue la requête SQL : DELETE FROM

        return $this->redirectToRoute('app_societe'); // après une suppression, on redirige vers la liste des societes
    }


    
    #[Route('/accueil/creations/societe/{id}', name: 'show_societe')]
    public function show(Societe $societe, BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Créations', 'route' => 'creations'],
            ['label' => 'Liste des sociétés', 'route' => 'app_societe'], 
            ['label' => "Détails d'une société #".$societe->getId(), 'params' => ['id' => $societe->getId()]], // Société spécifique // Pas de route car c’est la page actuelle
        ]);
        
        
        return $this->render('societe/show.html.twig', [
            'societe' => $societe,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }
}
