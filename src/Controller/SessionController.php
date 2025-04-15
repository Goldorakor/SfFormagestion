<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Sondage;
use App\Entity\Apprenant;
use App\Entity\Module;
use App\Form\SessionType;
use App\Entity\Encadrement;
use App\Entity\Inscription;
use App\Entity\Planification;
use App\Repository\SessionRepository;
use App\Repository\SocieteRepository;
use App\Service\BreadcrumbsGenerator;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InscriptionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[IsGranted('IS_AUTHENTICATED_FULLY')] /* seul un utilisateur bien connecté peut accéder aux méthodes de ce contrôleur */
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



    #[Route('/admin/accueil/creations/session/new', name: 'new_session')] // 'new_session' est un nom cohérent qui décrit bien la fonction
    #[Route('/admin/accueil/creations/session/{id}/edit', name: 'edit_session')] // 'edit_session' est un nom cohérent qui décrit bien la fonction attendue
    #[IsGranted('ROLE_ADMIN')]
    public function new_edit( // pour ajouter une session à notre BDD ou éditer une session existante
        Session $session = null,
        Request $request,
        EntityManagerInterface $entityManager,
        BreadcrumbsGenerator $breadcrumbsGenerator,
        InscriptionRepository $inscriptionRepository,
    ): Response 
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
        $modif = true;
        if(!$session) {
            $modif = false;
            $session = new Session();
        }

        // On stocke les inscriptions d’origine pour comparaison (si édition)
        // Enregistrer les données existantes pour nettoyage éventuel
        $inscriptionsExistantes = $session->getInscriptions()->toArray();
        $planificationsExistantes = $session->getPlanifications()->toArray();

        $formOptions = [];


        // Pré-remplir les champs référents si on est en mode édition
        foreach ($session->getEncadrements() as $encadrement) {
            if ($encadrement->getTypeReferent() === 'pédagogique') {
                $formOptions['referentPedagogique'] = $encadrement->getFormateur();
            } elseif ($encadrement->getTypeReferent() === 'administratif') {
                $formOptions['referentAdministratif'] = $encadrement->getFormateur();
            }
        }


        // Pré-remplir les champs questionnaires si on est en mode édition
        foreach ($session->getSondages() as $sondage) {
            if ($sondage->getTypeQuestionnaire() === 'de préformation') {
                $formOptions['questionnairePrefor'] = $sondage->getQuestionnaire();
            } elseif ($sondage->getTypeQuestionnaire() === 'à chaud') {
                $formOptions['questionnaireChaud'] = $sondage->getQuestionnaire();
            } elseif ($sondage->getTypeQuestionnaire() === 'à froid') {
                $formOptions['questionnaireFroid'] = $sondage->getQuestionnaire();
            }
        }


        /* Récupération des inscriptions liées à la session
        $inscriptions = $inscriptionRepository->findBy(['session' => $session]);
        foreach ($inscriptions as $inscription) {
            $session->addInscription($inscription); // utile pour les avoir à disposition sur $session
        }*/

        // Préparer les données pour le champ non mappé 'apprenantsInscrits'
        $apprenantsInscritsData = [];
        foreach ($session->getInscriptions() as $inscription) {
            $apprenantsInscritsData[] = [
                'apprenant' => $inscription->getApprenant(),
                'prix' => $inscription->getPrix() ?? null,
            ];
        }

        // Préparer les données pour le champ non mappé 'planificationSessions'
        $planificationSessionsData = [];
        foreach ($session->getPlanifications() as $planification) {
            $planificationSessionsData[] = [
                'module' => $planification->getModule(),
                'duree' => $planification->getDuree(),
                'dateDebut' => $planification->getDateDebut(),
                'dateFin' => $planification->getDateFin(),
            ];
        }


        // 2. on crée le formulaire à partir de SessionType (on veut ce modèle là bien entendu)
        $form = $this->createForm(SessionType::class, $session, [ // c'est bien la méthode createForm() qui permet de créer le formulaire
            //'data' => $session,
            'referentPedagogique' => $formOptions['referentPedagogique'] ?? null,
            'referentAdministratif' => $formOptions['referentAdministratif'] ?? null,
            'questionnairePrefor' => $formOptions['questionnairePrefor'] ?? null,
            'questionnaireChaud' => $formOptions['questionnaireChaud'] ?? null,
            'questionnaireFroid' => $formOptions['questionnaireFroid'] ?? null,
            'apprenantsInscrits' => $apprenantsInscritsData,
            'planificationSessions' => $planificationSessionsData,
        ]); 

        // 4. le traitement s'effectue ici ! si le formulaire soumis est correct, on fera l'insertion en BDD
        $form->handleRequest($request);


        // bloc qui concerne la soumission
        if ($form->isSubmitted() && $form->isValid()) {
            
            // On supprime les anciens 'encadrements'
            if ($modif) {
                foreach ($session->getEncadrements() as $encadrement) {
                    $entityManager->remove($encadrement);
                }
            }

            // Récupération des formateurs sélectionnés
            $referentPedagogique = $form->get('referentPedagogique')->getData();
            $referentAdministratif = $form->get('referentAdministratif')->getData();

            if ($referentPedagogique) {
                $encadrementPedago = new Encadrement();
                $encadrementPedago->setSession($session);
                $encadrementPedago->setFormateur($referentPedagogique);
                $encadrementPedago->setTypeReferent('pédagogique');
                $entityManager->persist($encadrementPedago);
            }
            
            if ($referentAdministratif) {
                $encadrementAdmin = new Encadrement();
                $encadrementAdmin->setSession($session);
                $encadrementAdmin->setFormateur($referentAdministratif);
                $encadrementAdmin->setTypeReferent('administratif');
                $entityManager->persist($encadrementAdmin);
            }



            // On supprime les anciens 'sondages'
            if ($modif) {
                foreach ($session->getSondages() as $sondage) {
                    $entityManager->remove($sondage);
                }
            }

            // Récupération des questionnaires sélectionnés
            $questionnairePrefor = $form->get('questionnairePrefor')->getData();
            $questionnaireChaud = $form->get('questionnaireChaud')->getData();
            $questionnaireFroid = $form->get('questionnaireFroid')->getData();

            if ($questionnairePrefor) {
                $sondage = new Sondage();
                $sondage->setSession($session);
                $sondage->setQuestionnaire($questionnairePrefor);
                $sondage->setTypeQuestionnaire('de préformation');
                $entityManager->persist($sondage);
            }
            
            if ($questionnaireChaud) {
                $sondage = new Sondage();
                $sondage->setSession($session);
                $sondage->setQuestionnaire($questionnaireChaud);
                $sondage->setTypeQuestionnaire('à chaud');
                $entityManager->persist($sondage);
            }
            
            if ($questionnaireFroid) {
                $sondage = new Sondage();
                $sondage->setSession($session);
                $sondage->setQuestionnaire($questionnaireFroid);
                $sondage->setTypeQuestionnaire('à froid');
                $entityManager->persist($sondage);
            }


            // Suppression des anciennes inscriptions (avant enregistrement des nouvelles)
            foreach ($inscriptionsExistantes as $inscription) {
                $session->removeInscription($inscription);

                $apprenant = $inscription->getApprenant();
                if ($apprenant) {
                    $apprenant->removeInscription($inscription);
                }

                $entityManager->remove($inscription);
            }


            $apprenantsInscrits = $request->request->all('session')['apprenantsInscrits'] ?? [];

            foreach ($apprenantsInscrits as $apprenantData) {
                $apprenantId = $apprenantData['apprenant'] ?? null;
                $prix = $apprenantData['prix'] ?? null;

                if ($apprenantId && $prix !== null) {
                    $apprenant = $entityManager->getRepository(Apprenant::class)->find($apprenantId);
                    if ($apprenant) {
                        $inscription = new Inscription();
                        $inscription->setSession($session);
                        $inscription->setApprenant($apprenant);
                        $inscription->setPrix($prix);

                        // Ajout aux collections (relations bidirectionnelles)
                        $session->addInscription($inscription);
                        $apprenant->addInscription($inscription);

                        $entityManager->persist($inscription);
                    }
                }
            }



            // Suppression des anciennes planifications (avant enregistrement des nouvelles)
            foreach ($planificationsExistantes as $planification) {
                $session->removePlanification($planification);

                $module = $planification->getModule();
                if ($module) {
                    $module->removePlanification($planification);
                }

                $entityManager->remove($planification);
            }


            $planificationSessions = $request->request->all('session')['planificationSessions'] ?? [];

            foreach ($planificationSessions as $planifData) {
                $moduleId = $planifData['module'] ?? null;
                $duree = $planifData['duree'] ?? null;
                $dateDebut = $planifData['dateDebut'] ?? null;
                $dateFin = $planifData['dateFin'] ?? null;

                if ($moduleId && $duree !== null && $dateDebut && $dateFin) {
                    $module = $entityManager->getRepository(Module::class)->find($moduleId);
                    if ($module) {
                        $planification = new Planification();
                        $planification->setSession($session);
                        $planification->setModule($module);
                        $planification->setDuree($duree);
                        $planification->setDateDebut(new \DateTime($dateDebut));
                        $planification->setDateFin(new \DateTime($dateFin));

                        // Ajout aux collections (relations bidirectionnelles)
                        $session->addPlanification($planification);
                        $module->addPlanification($planification);

                        $entityManager->persist($planification);
                    }
                }
            }


            // Enregistrement de la session et des encadrements, inscriptions, sondages, planifications
            $entityManager->persist($session);
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
            'formAddSession' => $form->createView(),
            'edit' => $session->getId(), // comportement booléen
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);

    }



    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/accueil/creations/session/{id}/delete', name: 'delete_session')]
    public function delete(Session $session, EntityManagerInterface $entityManager): Response
    {
        // Session.php : Collection $inscriptions - Collection $encadrements - Collection $planifications - Collection $sondages
        // voilà les collections d'entités à traiter pour la question d'intégrité référentielle
        
        // Vérifier s'il y a des inscriptions liées
        if (count($session->getInscriptions()) > 0) {
            $this->addFlash('warning', "Impossible de supprimer cette session : elle possède encore au moins une inscription d'apprenant. Supprimez-la en éditant la session.");
            return $this->redirectToRoute('show_session', ['id' => $session->getId()]); // on redirige immédiatement sur la vue de détails de la société (sans rien supprimer)
        }

        // Vérifier s'il y a des encadrements liés
        if (count($session->getEncadrements()) > 0) {
            $this->addFlash('warning', "Impossible de supprimer cette session : elle possède encore au moins un encadrement de formateur. Supprimez-le en éditant la session.");
            return $this->redirectToRoute('show_session', ['id' => $session->getId()]); // on redirige immédiatement sur la vue de détails de la société (sans rien supprimer)
        }

        // Vérifier s'il y a des planifications liées
        if (count($session->getPlanifications()) > 0) {
            $this->addFlash('warning', "Impossible de supprimer cette session : elle possède encore au moins une planification de module. Supprimez-la en éditant la session.");
            return $this->redirectToRoute('show_session', ['id' => $session->getId()]); // on redirige immédiatement sur la vue de détails de la société (sans rien supprimer)
        }

        // Vérifier s'il y a des sondages liés
        if (count($session->getSondages()) > 0) {
            $this->addFlash('warning', "Impossible de supprimer cette session : elle possède encore au moins un sondage à travers un questionnaire. Supprimez-le en éditant la session.");
            return $this->redirectToRoute('show_session', ['id' => $session->getId()]); // on redirige immédiatement sur la vue de détails de la société (sans rien supprimer)
        }
        
        
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
            ['label' => "Détails d'une session #".$session->getId(), 'params' => ['id' => $session->getId()]], 
            // Session spécifique // Pas de route car c’est la page actuelle
        ]);
        
        
        return $this->render('session/show.html.twig', [
            'session' => $session,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }



    /*
    on crée cette méthode pour la partie suivi de notre outil :
    pour différencier la liste des sessions de la partie 'créations' de la liste des sessions de la partie 'suivis',
    on ajoute un préfixe suivi à tous les nommages de la partie 'suivis'
    */
    #[Route('/accueil/suivis/session', name: 'suivi_app_session')]
    public function suivi_index(SessionRepository $sessionRepository, BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Suivis', 'route' => 'suivis'],
            ['label' => 'Liste de suivi des sessions'], // Pas de route car c’est la page actuelle
        ]);
        

        $sessions = $sessionRepository->findBy([], ["titreSession"=>"ASC"]);

        return $this->render('session/suivi_index.html.twig', [
            'sessions' => $sessions,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }



    /*
    on crée cette méthode pour afficher le détail d'une session dans la partie 'suivis' :
    dans cette vue de détails, on pourra placer nos boutons pour générer chacun des documents administratifs nécessaires
    Avant la vue, il faut un contrôleur pour aller récupérer les données via mes méthodes de repository et les passer à Twig
    */
    #[Route('/accueil/suivis/session/{id}', name: 'suivi_show_session')]
    public function suivi_show(Session $session, BreadcrumbsGenerator $breadcrumbsGenerator, SessionRepository $sessionRepo, SocieteRepository $societeRepo): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Suivis', 'route' => 'suivis'],
            ['label' => 'Liste de suivi des sessions', 'route' => 'suivi_app_session'], 
            ['label' => "Détails de suivi d'une session #".$session->getId(), 'params' => ['id' => $session->getId()]], 
            // Session spécifique // Pas de route car c’est la page actuelle
        ]);


        // Récupérer les sociétés et les apprenants salariés liés à la session
        $societesEtApprenants = $sessionRepo->findSocietesEtApprenantsBySession($session->getId());

        // Récupérer les apprenants non salariés liés à la session (les particuliers)
        $apprenantsParticuliers = $sessionRepo->findApprenantsParticuliersBySession($session->getId());

        // Récupérer le total payé par chaque société
        $prixParSociete = []; // On prépare un tableau associatif (societeId => total payé) qui contiendra la somme des paiements par société, pour le moment c'est vide
        
    
        // On parcourt toutes les lignes récupérées par la DQL qui liste les sociétés et leurs apprenants
        foreach ($societesEtApprenants as $ligne) {

            /*
            $ligne contient : 
            - 'societeId' => l'ID de la société
            - 'raisonSociale' => le nom de la société
            - 'nom' => nom de l'apprenant
            - 'prenom' => prénom de l'apprenant
            - 'email' => email de l'apprenant
            - 'metier' => métier de l'apprenant
            */


            // On vérifie si on n'a pas encore calculé le total payé pour cette société
            if (!isset($prixParSociete[$ligne['societeId']])) {

                // Si ce n’est pas encore fait, on récupère le total payé par cette société grâce à la méthode dans SocieteRepository
                $prix = $societeRepo->findPrixSociete($session->getId(), $ligne['societeId']);

                // On stocke ce total dans le tableau associatif en liant le total à l'ID de la société
                // On sécurise avec l'opérateur "??" au cas où la requête ne retourne rien (NULL)
                $prixParSociete[$ligne['societeId']] = $prix['totalPaye'] ?? 0;
            }
        }

        /*

        tableau associatif a cette forme :

        [
            1 => 5000,   // société Id 1 a payé 5000 euros
            2 => 3500,   // société Id 2 a payé 3500 euros
            // etc.
        ]

        */

        return $this->render('session/suivi_show.html.twig', [
            'session' => $session,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
            'societesEtApprenants' => $societesEtApprenants, // on a besoin  de cette variable pour récupérer les sociétés (et la liste de leurs apprenants) qui participent à la session
            'prixParSociete' => $prixParSociete, // on a besoin de cette variable pour le prix total de chaque société
            'apprenantsParticuliers' => $apprenantsParticuliers // on a besoin de cette variable pour récupérer les particuliers qui participent à la session
        ]);
    }

    /*
    La partie 'suivis' ne nécessite pas de méthodes d'ajout ou de suppression de sessions :
    ce travail ne s'effectue que dans la partie 'creations' -> 'suivis' communique avec la BDD pour venir récupérer des infos, rien d'autre
    */

}
