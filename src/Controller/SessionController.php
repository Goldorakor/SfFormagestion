<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Sondage;
use App\Form\SessionType;
use App\Entity\Encadrement;
use App\Entity\Inscription;
use App\Entity\Planification;
use App\Repository\SessionRepository;
use App\Service\BreadcrumbsGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SessionController extends AbstractController
{
    #[Route('/accueil/creations/session', name: 'app_session')]
    public function index(SessionRepository $sessionRepository, BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Créations', 'route' => 'creations'],
            ['label' => 'Liste des sessions'], // Pas de route car c’est la page actuelle
        ]);
        
        
        // méthode choisie qui ne permet pas de trier la liste des sessions
        // $sessions = $sessionRepository->findAll();

        $sessions = $sessionRepository->findBy([], ["titreSession"=>"ASC"]);

        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }



    #[Route('/accueil/creations/session/new', name: 'new_session')] // 'new_session' est un nom cohérent qui décrit bien la fonction
    #[Route('/accueil/creations/session/{id}/edit', name: 'edit_session')] // 'edit_session' est un nom cohérent qui décrit bien la fonction attendue
    public function new_edit(Session $session = null, Request $request, EntityManagerInterface $entityManager, BreadcrumbsGenerator $breadcrumbsGenerator): Response // pour ajouter un session à notre BDD
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Créations', 'route' => 'creations'],
            ['label' => 'Liste des sessions', 'route' => 'app_session'], 
            ['label' => !$session ? "Créer une session" : "Modifier une session"], // Pas de route car c’est la page actuelle
        ]);
        // $variable = (condition) ? valeur_si_vrai : valeur_si_faux;
        // Si condition est vraie → la valeur après ? est assignée.
        // Si condition est fausse → la valeur après : est assignée.
        
        
        // 1. si pas de session, on crée une nouvelle session (un objet session est bien créé ici) - s'il existe déjà, pas besoin de le créer
        if(!$session) {
            $session = new Session();
        }

        // 2. on crée le formulaire à partir de SessionType (on veut ce modèle là bien entendu)
        $form = $this->createForm(SessionType::class, $session); // c'est bien la méthode createForm() qui permet de créer le formulaire

        // 4. le traitement s'effectue ici ! si le formulaire soumis est correct, on fera l'insertion en BDD
        $form->handleRequest($request);


        // bloc qui concerne la soumission
        if ($form->isSubmitted() && $form->isValid()) {
            
            $session = $form->getData(); // on récupère les données du formulaire dans notre objet session
            
            $entityManager->persist($session); // équivaut à la méthode prepare() en PDO

            // $entityManager->flush();  équivaut à la méthode execute() en PDOStatement -> on n'envoie rien en BDD à ce stade car on enverra tout en BDD à la fin et pas uniquement ce qui concerne Session


            // Récupération des formateurs sélectionnés
            $referentPedagogique = $form->get('referentPedagogique')->getData();
            $referentAdministratif = $form->get('referentAdministratif')->getData();


            // Création des encadrements si un formateur est sélectionné
            if ($referentPedagogique) { // vérifie si l'utilisateur a effectivement sélectionné un référent pédagogique via le formulaire -> si un formateur a été choisi, un nouvel encadrement est créé.
                $encadrementPeda = new Encadrement();
                $encadrementPeda->setSession($session);
                $encadrementPeda->setFormateur($referentPedagogique);
                $encadrementPeda->setTypeReferent("pédagogique");
                $entityManager->persist($encadrementPeda); // équivaut à la méthode prepare() en PDO
            }

            if ($referentAdministratif) { // vérifie si l'utilisateur a effectivement sélectionné un référent administratif via le formulaire -> si un formateur a été choisi, un nouvel encadrement est créé.
                $encadrementAdmin = new Encadrement();
                $encadrementAdmin->setSession($session);
                $encadrementAdmin->setFormateur($referentAdministratif);
                $encadrementAdmin->setTypeReferent("administratif");
                $entityManager->persist($encadrementAdmin); // équivaut à la méthode prepare() en PDO
            }

            
            // Récupération des questionnaires sélectionnés
            $questionnairePrefor = $form->get('questionnairePrefor')->getData();
            $questionnaireChaud = $form->get('questionnaireChaud')->getData();
            $questionnaireFroid = $form->get('questionnaireFroid')->getData();


            // Création des sondages si un questionnaire est sélectionné
            if ($questionnairePrefor) { // vérifie si l'utilisateur a effectivement sélectionné un questionnaire de préformation via le formulaire -> si un questionnaire a été choisi, un nouveau sondage est créé.
                $sondagePrefor = new Sondage();
                $sondagePrefor->setSession($session);
                $sondagePrefor->setQuestionnaire($questionnairePrefor);
                $sondagePrefor->setTypeQuestionnaire("de préformation");
                $entityManager->persist($sondagePrefor); // équivaut à la méthode prepare() en PDO
            }

            if ($questionnaireChaud) { // vérifie si l'utilisateur a effectivement sélectionné un questionnaire à chaud via le formulaire -> si un questionnaire a été choisi, un nouveau sondage est créé.
                $sondageChaud = new Sondage();
                $sondageChaud->setSession($session);
                $sondageChaud->setQuestionnaire($questionnaireChaud);
                $sondageChaud->setTypeQuestionnaire("à chaud");
                $entityManager->persist($sondageChaud); // équivaut à la méthode prepare() en PDO
            }

            if ($questionnaireFroid) { // vérifie si l'utilisateur a effectivement sélectionné un questionnaire à froid via le formulaire -> si un questionnaire a été choisi, un nouveau sondage est créé.
                $sondageFroid = new Sondage();
                $sondageFroid->setSession($session);
                $sondageFroid->setQuestionnaire($questionnaireFroid);
                $sondageFroid->setTypeQuestionnaire("à froid");
                $entityManager->persist($sondageFroid); // équivaut à la méthode prepare() en PDO
            }


            // Récupération des apprenants et de leurs prix (apprenant inscrit transporte les deux informations)
            $apprenantsInscrits = $form->get('apprenantsInscrits')->getData();


            foreach ($apprenantsInscrits as $apprenantData) {
                $apprenant = $apprenantData['apprenant'] ?? null; // Récupérer l'apprenant sélectionné
                $prix = $apprenantData['prix'] ?? null; // Récupérer le prix associé
            
                if ($apprenant && $prix !== null) { // Vérifie que l'apprenant est sélectionné ET que le prix est renseigné
                    $inscription = new Inscription();
                    $inscription->setSession($session);
                    $inscription->setApprenant($apprenant);
                    $inscription->setPrix($prix);
                    $entityManager->persist($inscription);
                }
            }


            // Récupération des apprenants et de leurs prix (apprenant inscrit transporte les deux informations)
            $planificationSessions = $form->get('planificationSessions')->getData();


            foreach ($planificationSessions as $moduleData) {
                $module = $moduleData['module'] ?? null; // Récupérer le module sélectionné
                $duree = $moduleData['duree'] ?? null; // Récupérer la durée associée
                $dateDebut = $moduleData['dateDebut'] ?? null; // Récupérer la date de début associée
                $dateFin = $moduleData['dateFin'] ?? null; // Récupérer la date de fin associée
            
                if ($apprenant && $prix !== null) { // Vérifie que le module est sélectionné ET que la durée est renseignée ET que les dates de début et de fin sont remplies
                    $planification = new Planification();
                    $planification->setSession($session);
                    $planification->setModule($module);
                    $planification->setDuree($duree);
                    $planification->setDateDebut($dateDebut);
                    $planification->setDateFin($dateFin);
                    $entityManager->persist($planification);
                }
            }


            // Enregistrement de la session, des encadrements et des sondages
            $entityManager->flush(); // équivaut à la méthode execute() en PDOStatement -> maintenant, on envoie la totalité des informations en BDD


            // redirection vers la liste des sessions (si formulaire soumis et formulaire valide)
            return $this->redirectToRoute('app_session');
        }
        // fin du bloc
        

        // 3. on affiche le formulaire créé dans la page dédiée à cet affichage -> {{ form(formAddSession) }} --> affichage par défaut 
        return $this->render('session/new.html.twig', [ // 'session/new.html.twig' -> vue dédiée à l'affichage du formulaire : on crée un nouveau fichier dans le dossier session
            // 'form' => $form,  on fait passer une variable form qui prend la valeur $form
            // on change le nom pour éviter toute ambiguité 'form' -> 'formAddSession' comme expliqué dans new.html.twig
            'formAddSession' => $form,
            'edit' => $session->getId(), // comportement booléen
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);

    }


    #[Route('/accueil/creations/session/{id}/delete', name: 'delete_session')]
    public function delete(Session $session, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($session); // on enlève la session ciblée de la collection des sessions
        $entityManager->flush(); // on effectue la requête SQL : DELETE FROM

        return $this->redirectToRoute('app_session'); // après une suppression, on redirige vers la liste des sessions
    }

    
    #[Route('/accueil/creations/session/{id}', name: 'show_session')]
    public function show(Session $session, BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Créations', 'route' => 'creations'],
            ['label' => 'Liste des sessions', 'route' => 'app_session'], 
            ['label' => "Détails d'une session' #".$session->getId(), 'params' => ['id' => $session->getId()]], // Session spécifique // Pas de route car c’est la page actuelle
        ]);
        
        
        return $this->render('session/show.html.twig', [
            'session' => $session,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }
}
