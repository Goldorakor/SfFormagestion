<?php

namespace App\Controller;

use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Service\PdfGenerator;
use App\Repository\SessionRepository;
use App\Repository\SocieteRepository;
use App\Service\BreadcrumbsGenerator;
use App\Repository\ResponsableRepository;
use App\Repository\ApprenantRepository;
use App\Repository\EntrepriseRepository;
use App\Repository\RepresentantRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[IsGranted('IS_AUTHENTICATED_FULLY')] /* seul un utilisateur bien connecté peut accéder aux méthodes de ce contrôleur */
final class DocumentController extends AbstractController
{
    // cette méthode aurait pu servir à afficher la page "liste des modèles de documents" (comme le cas Apprenant par exemple)
    // mais cette page est statique et elle est donc traitée par une méthode dans PageController.php :
    // #[Route('/accueil/parametres/modeles_documents', name: 'modeles_documents')]
    #[Route('/document', name: 'app_document')]
    public function index(

    ): Response
    {
        return $this->render('document/index.html.twig', [
        ]);
    }




    #[Route('/accueil/parametres/modeles_documents/programme', name: 'programme')]
    public function programme(
        BreadcrumbsGenerator $breadcrumbsGenerator
    ): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Liste des modèles de documents', 'route' => 'modeles_documents'],
            ['label' => 'Programme'], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('document/programme.html.twig', [
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }




    #[Route('/accueil/parametres/modeles_documents/contrat_formateur', name: 'contrat_formateur')]
    public function contratFormateur(
        BreadcrumbsGenerator $breadcrumbsGenerator
    ): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Liste des modèles de documents', 'route' => 'modeles_documents'],
            ['label' => 'Contrat formateur'], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('document/contrat_formateur.html.twig', [
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }




    /*
    *
    * route pour générer une convocation spécifique à une session et à un apprenant (paramètres dynamiques dans l'url)
    *
    */
    #[Route('/accueil/suivis/session/{sessionId}/apprenant/{apprenantId}/convocation', name: 'generer_convocation')]
    public function genererConvocation(
        int $sessionId,
        int $apprenantId,
        SessionRepository $sessionRepo,
        SocieteRepository $societeRepo,
        ApprenantRepository $apprenantRepo,
        EntrepriseRepository $entrepriseRepo,
        RepresentantRepository $representantRepo,
        BreadcrumbsGenerator $breadcrumbsGenerator,
    ): Response
    {
        // on récupère la session, la société et toutes les données nécessaires
        $session = $sessionRepo->find($sessionId);
        $apprenant = $apprenantRepo->find($apprenantId);
        $societe = $apprenant->getSociete();

        // S’il y a une société, on récupère son id et le responsable légal
        $responsableLegal = null;
        if ($societe !== null) {
            $societeId = $societe->getId();
            $responsableLegal = $societeRepo->findUniqueRespLegal($societeId); // pour récupérer le responsable légal de la société
        }

        $entreprise = $entrepriseRepo->findUniqueEntreprise(); // pour récupérer l'organisme de formation
        $representant = $representantRepo->findUniqueRepresentant(); // pour récupérer le représentant de l'organisme de formation
        
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Suivis', 'route' => 'suivis'],
            ['label' => 'Liste de suivi des sessions', 'route' => 'suivi_app_session'],
            ['label' => "Détails de suivi d'une session #".$session->getId(), 'route' => 'suivi_show_session', 'params' => ['id' => $session->getId()]],
            ['label' => "Convocation apprenant #".$apprenant->getId(),], // Pas de route car c’est la page actuelle
        ]);

        $now = new \DateTime();

        return $this->render('document/convocation.html.twig', [
            'session' => $session,
            'societe' => $societe,
            'apprenant' => $apprenant,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
            'now' => $now,
            'entreprise' => $entreprise,
            'representant' => $representant,
            'responsableLegal' => $responsableLegal,
        ]);
    }




    /*
    *
    * route pour générer une convocation PDF spécifique à une session et à un apprenant (paramètres dynamiques dans l'url)
    *
    */
    #[Route('/accueil/suivis/session/{sessionId}/apprenant/{apprenantId}/convocation_pdf', name: 'generer_convocation_pdf')]
     
    public function genererConvocationPdf(
        int $sessionId,
        int $apprenantId,
        SessionRepository $sessionRepo,
        SocieteRepository $societeRepo,
        ApprenantRepository $apprenantRepo,
        EntrepriseRepository $entrepriseRepo,
        RepresentantRepository $representantRepo,
        PdfGenerator $pdfGenerator, /* on injecte le service pour pouvoir en bénéficier */
        SluggerInterface $slugger, /* on injecte le service pour pouvoir en bénéficier */
    ): Response
    {
        $entreprise = $entrepriseRepo->findUniqueEntreprise();
        // on récupère les infos de l'organisme de formation (l’entreprise)

        $representant = $representantRepo->findUniqueRepresentant();
        // on récupère le représentant de l'organisme de formation
        
        // on récupère les chemins absolus des images (logo et tampon)
        $logoFilename = $entreprise->getLogoFilename();
        $logoPath = $this->getParameter('kernel.project_dir') . '/var/uploads/logos/'.$logoFilename;
        $tamponFilename = $representant->getTamponFilename();
        $tamponPath = $this->getParameter('kernel.project_dir') . '/var/uploads/tampons/'.$tamponFilename;
        
        // On récupère les extensions des fichiers (png, jpg, etc.)
        $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
        $tamponType = pathinfo($tamponPath, PATHINFO_EXTENSION);
        
        // On lit les contenus des fichiers
        $logoData = file_get_contents($logoPath);
        $tamponData = file_get_contents($tamponPath);
        
        // On encode les images en base64 : utile pour l’afficher dans un PDF
        $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
        $tamponBase64 = 'data:image/' . $tamponType . ';base64,' . base64_encode($tamponData);

        // on récupère la session de formation et la société concernée via leurs IDs
        $session = $sessionRepo->find($sessionId);
        $apprenant = $apprenantRepo->find($apprenantId);
        $societe = $apprenant->getSociete();

        // On se protège d'une possible erreur
        if (!$session || !$apprenant) {
            throw $this->createNotFoundException('Session ou apprenant introuvable.');
        }
        
        // S’il y a une société, on récupère son id et le responsable légal
        $responsableLegal = null;
        if ($societe !== null) {
            $societeId = $societe->getId();
            $responsableLegal = $societeRepo->findUniqueRespLegal($societeId); // pour récupérer le responsable légal de la société
        }

        $now = new \DateTime();

        // Récupérer le contenu HTML du template Twig
        $htmlContent = $this->renderView('document/convocation_pdf.html.twig', [
            'session' => $session,
            'societe' => $societe,
            'apprenant' => $apprenant,
            'now' => $now,
            'entreprise' => $entreprise,
            'representant' => $representant,
            'responsableLegal' => $responsableLegal,
            'pdfMode' => true,
            'logoBase64' => $logo_base64,
            'tamponBase64' => $tampon_base64,
        ]);

        // On génère le contenu PDF à l’aide du service PdfGenerator
        $pdfContent = $pdfGenerator->getPdfContent($htmlContent);

        $slug = $slugger->slug($apprenant->__toString())->lower(); // pour avoir une identité de l'apprenant propre et normalisé

        // On retourne une réponse HTTP contenant le fichier PDF
        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Convocation_' . $slug . '.pdf"', // nom de ficher avec nom de société propre et normalisé
            ]
        );
    }




    /*
    *
    * route pour générer une convention spécifique à une session et à une société (paramètres dynamiques dans l'url)
    *
    */
    #[Route('/accueil/suivis/session/{sessionId}/societe/{societeId}/convention', name: 'generer_convention')]
    public function genererConvention(
        int $sessionId,
        int $societeId,
        SessionRepository $sessionRepo,
        SocieteRepository $societeRepo,
        EntrepriseRepository $entrepriseRepo,
        RepresentantRepository $representantRepo,
        BreadcrumbsGenerator $breadcrumbsGenerator,
    ): Response
    {
        // on récupère la session, la société et toutes les données nécessaires
        $session = $sessionRepo->find($sessionId);
        $societe = $societeRepo->find($societeId);
        $apprenantsSoc = $sessionRepo->findApprenantsBySocieteBySession($sessionId, $societeId);
        $entreprise = $entrepriseRepo->findUniqueEntreprise(); // pour récupérer l'organisme de formation
        $representant = $representantRepo->findUniqueRepresentant(); // pour récupérer le représentant de l'organisme de formation
        $responsableLegal = $societeRepo->findUniqueRespLegal($societeId); // pour récupérer le responsable légal de la société
        
        // Récupération du prix total payé par la société pour la session donnée
        $prixTotal = $societeRepo->findPrixSociete($sessionId, $societeId);
        
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Suivis', 'route' => 'suivis'],
            ['label' => 'Liste de suivi des sessions', 'route' => 'suivi_app_session'],
            ['label' => "Détails de suivi d'une session #".$session->getId(), 'route' => 'suivi_show_session', 'params' => ['id' => $session->getId()]],
            ['label' => "Convention société #".$societe->getId(),], // Pas de route car c’est la page actuelle
        ]);

        $now = new \DateTime();

        return $this->render('document/convention.html.twig', [
            'session' => $session,
            'societe' => $societe,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
            'prix_total' => $prixTotal, // on envoie le prix total à la vue
            'apprenants_soc' => $apprenantsSoc,
            'now' => $now,
            'entreprise' => $entreprise,
            'representant' => $representant,
            'responsableLegal' => $responsableLegal
        ]);

        /*
        <p>Prix total payé par la société : {{ prix_total[0].totalPaye ?? 'Non disponible' }} €</p>
        -> La requête retourne un tableau d'un seul élément (getResult()), donc on accède à la première ligne avec [0] et à la colonne totalPaye.
        */
    }


    

    /*
    *
    * route pour générer une convention PDF spécifique à une session et à une société (paramètres dynamiques dans l'url)
    *
    */
    /* déclaration d'une route Symfony :  L’URL contient deux paramètres dynamiques : sessionId et societeId.*/
    /* Le nom de la route (generer_convention_pdf) pourra être utilisé dans Twig ou dans les redirections */
    #[Route('/accueil/suivis/session/{sessionId}/societe/{societeId}/convention_pdf', name: 'generer_convention_pdf')] 
     
    /* Déclaration de la méthode et injection des dépendances
    *  Cette méthode est appelée quand la route est visitée. Elle attend en paramètres : 
    *  Les deux ID passés dans l’URL et plusieurs repositories injectés automatiquement
    *  (grâce à l’autowiring) pour interagir avec la base de données. 
    */
    public function genererConventionPdf(
        int $sessionId,
        int $societeId,
        SessionRepository $sessionRepo,
        SocieteRepository $societeRepo,
        EntrepriseRepository $entrepriseRepo,
        RepresentantRepository $representantRepo,
        PdfGenerator $pdfGenerator, /* on injecte le service pour pouvoir en bénéficier */
        SluggerInterface $slugger, /* on injecte le service pour pouvoir en bénéficier */
    ): Response
    {
        $entreprise = $entrepriseRepo->findUniqueEntreprise();
        // on récupère les infos de l'organisme de formation (l’entreprise)

        $representant = $representantRepo->findUniqueRepresentant();
        // on récupère le représentant de l'organisme de formation
        
        // on récupère les chemins absolus des images (logo et tampon)
        $logoFilename = $entreprise->getLogoFilename();
        $logoPath = $this->getParameter('kernel.project_dir') . '/var/uploads/logos/'.$logoFilename;
        $tamponFilename = $representant->getTamponFilename();
        $tamponPath = $this->getParameter('kernel.project_dir') . '/var/uploads/tampons/'.$tamponFilename;
        
        // On récupère les extensions des fichiers (png, jpg, etc.)
        $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
        $tamponType = pathinfo($tamponPath, PATHINFO_EXTENSION);
        
        // On lit les contenus des fichiers
        $logoData = file_get_contents($logoPath);
        $tamponData = file_get_contents($tamponPath);
        
        // On encode les images en base64 : utile pour l’afficher dans un PDF
        $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
        $tamponBase64 = 'data:image/' . $tamponType . ';base64,' . base64_encode($tamponData);

        // on récupère la session de formation et la société concernée via leurs IDs
        $session = $sessionRepo->find($sessionId);
        $societe = $societeRepo->find($societeId);

        // On se protège d'une possible erreur
        if (!$session || !$societe) {
            throw $this->createNotFoundException('Session ou société introuvable.');
        }

        $apprenantsSoc = $sessionRepo->findApprenantsBySocieteBySession($sessionId, $societeId);
        // On récupère les apprenants inscrits à cette session dans cette société

        $responsableLegal = $societeRepo->findUniqueRespLegal($societeId);
        // on récupère Le responsable légal de la société

        $prixTotal = $societeRepo->findPrixSociete($sessionId, $societeId);
        // on récupère le prix total que la société doit payer pour la session

        $now = new \DateTime();
        // on récupère la date actuelle

        /*  on génère du contenu HTML via Twig (le futur contenu du PDF) :
        toutes les variables nécessaires sont passées à Twig =>
        données sur la session, la société, le prix, les images encodées, etc. */
        $htmlContent = $this->renderView('document/convention_pdf.html.twig', [
            'session' => $session,
            'societe' => $societe,
            'prix_total' => $prixTotal,
            'apprenants_soc' => $apprenantsSoc,
            'now' => $now,
            'entreprise' => $entreprise,
            'representant' => $representant,
            'responsableLegal' => $responsableLegal,
            'pdfMode' => true,
            'logoBase64' => $logoBase64,
            'tamponBase64' => $tamponBase64,
        ]);

        // On génère le contenu PDF à l’aide du service PdfGenerator
        $pdfContent = $pdfGenerator->getPdfContent($htmlContent);

        $slug = $slugger->slug($societe->getRaisonSociale())->lower(); // pour avoir un nom de société propre et normalisé

        // On retourne une réponse HTTP contenant le fichier PDF
        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Convention_' . $slug . '.pdf"', // nom de ficher avec nom de société propre et normalisé
            ]
        );
    }




    /*
    *
    * route pour générer une feuille d'émargement spécifique à une session et à une société (paramètres dynamiques dans l'url)
    *
    */
    #[Route('/accueil/suivis/session/{sessionId}/societe/{societeId}/feuille_emargement', name: 'generer_feuille_emargement')]
    public function genererFeuilleEmargement(
        int $sessionId,
        int $societeId,
        SessionRepository $sessionRepo,
        SocieteRepository $societeRepo,
        EntrepriseRepository $entrepriseRepo,
        RepresentantRepository $representantRepo,
        BreadcrumbsGenerator $breadcrumbsGenerator,
    ): Response
    {
        
        // on récupère la session, la société et toutes les données nécessaires
        $session = $sessionRepo->find($sessionId);
        $societe = $societeRepo->find($societeId);
        $apprenantsSoc = $sessionRepo->findApprenantsBySocieteBySession($sessionId, $societeId);
        $entreprise = $entrepriseRepo->findUniqueEntreprise(); // pour récupérer l'organisme de formation
        $representant = $representantRepo->findUniqueRepresentant(); // pour récupérer le représentant de l'organisme de formation
        $responsableLegal = $societeRepo->findUniqueRespLegal($societeId); // pour récupérer le responsable légal de la société

        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Suivis', 'route' => 'suivis'],
            ['label' => 'Liste de suivi des sessions', 'route' => 'suivi_app_session'],
            ['label' => "Détails de suivi d'une session #".$session->getId(), 'route' => 'suivi_show_session', 'params' => ['id' => $session->getId()]],
            ['label' => "Feuille émargement #".$societe->getId(),], // Pas de route car c’est la page actuelle
        ]);

        $now = new \DateTime();

        /* partie pour récupérer les demi-journées qui composent la session */

        // On initialise un tableau vide pour stocker les demi-journées regroupées par date et moment (matin/après-midi)
        $demiJournees = [];

        // On parcourt toutes les planifications de la session (chaque planification correspond à un module, enseigné à un moment donné)
        foreach ($session->getPlanifications() as $planif) {
            // On récupère la date au format d/m/Y (ex : 2025-04-12)
            $date = $planif->getDateDebut()->format('Y-m-d');
            // On récupère la date sous forme d'un objet DateTime qu'on passera à la vue
            $dateTwig = $planif->getDateDebut();
            // On extrait l'heure pour déterminer le moment de la journée (matin ou après-midi)
            $hour = (int)$planif->getDateDebut()->format('H');
            // Si l'heure est avant 12h, c'est le matin ; sinon, c'est l'après-midi
            $moment = ($hour < 12) ? 'matin' : 'après-midi';
            // On crée une clé unique pour chaque demi-journée, par exemple : "2025-04-12-matin"
            $key = $date . '-' . $moment;

            // Si cette demi-journée n'existe pas encore dans le tableau, on l'initialise
            if (!isset($demiJournees[$key])) {
                $demiJournees[$key] = [
                    'date' => $date,
                    'moment' => $moment,
                    'modules' => [] // On prépare un tableau pour stocker les noms des modules
                ];
            }

            // On ajoute le nom du module à la liste des modules de cette demi-journée
            $demiJournees[$key]['modules'][] = $planif->getModule()->getNomModule();
        }

        // On trie les demi-journées pour que les matinées apparaissent avant les après-midis d'un même jour
        uksort($demiJournees, function ($a, $b) { // les arguments $a et $b représentent les clés du tableau $demiJournees
            // On sépare chaque clé en deux parties : date et moment
            [$dateA, $momentA] = explode('-', $a); // pour découper la chaîne $a en deux parties : une date et un moment 
            [$dateB, $momentB] = explode('-', $b); // pour découper la chaîne $b en deux parties : une date et un moment 
        
            if ($dateA === $dateB) {
                // Si la date est la même, on place le matin avant l'après-midi
                return ($momentA === $momentB) ? 0 : (($momentA === 'matin') ? -1 : 1);
            }
            // 0 : les deux éléments comparés sont considérés comme égaux, donc leur ordre relatif ne change pas.
            // -1 :  premier élément doit venir avant le second élément.
            // 1 : le premier élément doit venir après le second élément.
        
            // Sinon, on trie simplement par date (ordre chronologique)
            return strcmp($dateA, $dateB);
        });
        /* fin de la partie pour récupérer les demi-journées qui composent la session */
        
        return $this->render('document/feuille_emargement.html.twig', [
            'session' => $session,
            'societe' => $societe,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
            'apprenants_soc' => $apprenantsSoc,
            'now' => $now,
            'entreprise' => $entreprise,
            'representant' => $representant,
            'responsableLegal' => $responsableLegal,
            'demi_journees' => $demiJournees,
        ]);

        /*
        <p>Prix total payé par la société : {{ prix_total[0].totalPaye ?? 'Non disponible' }} €</p>
        -> La requête retourne un tableau d'un seul élément (getResult()), donc on accède à la première ligne avec [0] et à la colonne totalPaye.
        */
    }


  

    /*
    *
    * route pour générer une feuille d'émargement PDF spécifique à une session et à une société (paramètres dynamiques dans l'url)
    *
    */
    #[Route('/accueil/suivis/session/{sessionId}/societe/{societeId}/feuille_emargement_pdf', name: 'generer_feuille_emargement_pdf')]
     
    public function genererFeuilleEmargementPdf(
        int $sessionId,
        int $societeId,
        SessionRepository $sessionRepo,
        SocieteRepository $societeRepo,
        EntrepriseRepository $entrepriseRepo,
        RepresentantRepository $representantRepo,
        PdfGenerator $pdfGenerator, /* on injecte le service pour pouvoir en bénéficier */
        SluggerInterface $slugger, /* on injecte le service pour pouvoir en bénéficier */
    ): Response
    {
        $entreprise = $entrepriseRepo->findUniqueEntreprise();
        // on récupère les infos de l'organisme de formation (l’entreprise)

        $representant = $representantRepo->findUniqueRepresentant();
        // on récupère le représentant de l'organisme de formation
        
        // on récupère les chemins absolus des images (logo et tampon)
        $logoFilename = $entreprise->getLogoFilename();
        $logoPath = $this->getParameter('kernel.project_dir') . '/var/uploads/logos/'.$logoFilename;
        $tamponFilename = $representant->getTamponFilename();
        $tamponPath = $this->getParameter('kernel.project_dir') . '/var/uploads/tampons/'.$tamponFilename;
        
        // On récupère les extensions des fichiers (png, jpg, etc.)
        $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
        $tamponType = pathinfo($tamponPath, PATHINFO_EXTENSION);
        
        // On lit les contenus des fichiers
        $logoData = file_get_contents($logoPath);
        $tamponData = file_get_contents($tamponPath);
        
        // On encode les images en base64 : utile pour l’afficher dans un PDF
        $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
        $tamponBase64 = 'data:image/' . $tamponType . ';base64,' . base64_encode($tamponData);

        // on récupère la session de formation et la société concernée via leurs IDs
        $session = $sessionRepo->find($sessionId);
        $societe = $societeRepo->find($societeId);

        // On se protège d'une possible erreur
        if (!$session || !$societe) {
            throw $this->createNotFoundException('Session ou société introuvable.');
        }

        $apprenantsSoc = $sessionRepo->findApprenantsBySocieteBySession($sessionId, $societeId);
        // On récupère les apprenants inscrits à cette session dans cette société

        $responsableLegal = $societeRepo->findUniqueRespLegal($societeId);
        // on récupère Le responsable légal de la société

        $now = new \DateTime();
        // on récupère la date actuelle

        /* partie pour récupérer les demi-journées qui composent la session */
        // On initialise un tableau vide pour stocker les demi-journées regroupées par date et moment (matin/après-midi)
        $demiJournees = [];

        // On parcourt toutes les planifications de la session (chaque planification correspond à un module, enseigné à un moment donné)
        foreach ($session->getPlanifications() as $planif) {
            // On récupère la date au format d/m/Y (ex : 12/04/2025)
            $date = $planif->getDateDebut()->format('Y-m-d');

            // On récupère la date sous forme d'un objet DateTime qu'on passera à la vue
            $dateTwig = $planif->getDateDebut();

            // On extrait l'heure pour déterminer le moment de la journée (matin ou après-midi)
            $hour = (int)$planif->getDateDebut()->format('H');

            // Si l'heure est avant 12h, c'est le matin ; sinon, c'est l'après-midi
            $moment = ($hour < 12) ? 'matin' : 'après-midi';
            // On crée une clé unique pour chaque demi-journée, par exemple : "2025-04-12-matin"
            $key = $date . '-' . $moment;

            // Si cette demi-journée n'existe pas encore dans le tableau, on l'initialise
            if (!isset($demiJournees[$key])) {
                $demiJournees[$key] = [
                    'date' => $date,
                    'moment' => $moment,
                    'modules' => [] // On prépare un tableau pour stocker les noms des modules
                ];
            }

            // On ajoute le nom du module à la liste des modules de cette demi-journée
            $demiJournees[$key]['modules'][] = $planif->getModule()->getNomModule();
        }

        // On trie les demi-journées pour que les matinées apparaissent avant les après-midis d'un même jour
        uksort($demiJournees, function ($a, $b) { // les arguments $a et $b représentent les clés du tableau $demiJournees
            // On sépare chaque clé en deux parties : date et moment
            [$dateA, $momentA] = explode('-', $a); // pour découper la chaîne $a en deux parties : une date et un moment 
            [$dateB, $momentB] = explode('-', $b); // pour découper la chaîne $b en deux parties : une date et un moment 
        
            if ($dateA === $dateB) {
                // Si la date est la même, on place le matin avant l'après-midi
                return ($momentA === $momentB) ? 0 : (($momentA === 'matin') ? -1 : 1);
            }
            // 0 : les deux éléments comparés sont considérés comme égaux, donc leur ordre relatif ne change pas.
            // -1 :  premier élément doit venir avant le second élément.
            // 1 : le premier élément doit venir après le second élément.
        
            // Sinon, on trie simplement par date (ordre chronologique)
            return strcmp($dateA, $dateB);
        });
        /* fin de la partie pour récupérer les demi-journées qui composent la session */

        // Récupérer le contenu HTML du template Twig
        $htmlContent = $this->renderView('document/feuille_emargement_pdf.html.twig', [
            'session' => $session,
            'societe' => $societe,
            'apprenants_soc' => $apprenantsSoc,
            'now' => $now,
            'entreprise' => $entreprise,
            'representant' => $representant,
            'responsableLegal' => $responsableLegal,
            'pdfMode' => true,
            'logoBase64' => $logo_base64,
            'tamponBase64' => $tampon_base64,
            'demi_journees' => $demiJournees,
        ]);

        // On génère le contenu PDF à l’aide du service PdfGenerator
        $pdfContent = $pdfGenerator->getPdfContent($htmlContent);

        $slug = $slugger->slug($societe->getRaisonSociale())->lower(); // pour avoir un nom de société propre et normalisé

        // On retourne une réponse HTTP contenant le fichier PDF
        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Feuille_emargement_' . $slug . '.pdf"', // nom de ficher avec nom de société propre et normalisé
            ]
        );
    }




    /*
    *
    * route pour générer une feuille d'émargement spécifique à une session et à une société (paramètres dynamiques dans l'url)
    *
    */
    #[Route('/accueil/suivis/session/{sessionId}/apprenant/{apprenantId}/feuille_emargement_solo', name: 'generer_feuille_emargement_solo')]
    public function genererFeuilleEmargementSolo(
        int $sessionId,
        int $apprenantId,
        SessionRepository $sessionRepo,
        ApprenantRepository $apprenantRepo,
        EntrepriseRepository $entrepriseRepo,
        RepresentantRepository $representantRepo,
        BreadcrumbsGenerator $breadcrumbsGenerator,
    ): Response
    {
        
        // on récupère la session, la société et toutes les données nécessaires
        $session = $sessionRepo->find($sessionId);
        $apprenant = $apprenantRepo->find($apprenantId);
        $entreprise = $entrepriseRepo->findUniqueEntreprise(); // pour récupérer l'organisme de formation
        $representant = $representantRepo->findUniqueRepresentant(); // pour récupérer le représentant de l'organisme de formation
        

        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Suivis', 'route' => 'suivis'],
            ['label' => 'Liste de suivi des sessions', 'route' => 'suivi_app_session'],
            ['label' => "Détails de suivi d'une session #".$session->getId(), 'route' => 'suivi_show_session', 'params' => ['id' => $session->getId()]],
            ['label' => "Feuille émargement #".$apprenant->getId(),], // Pas de route car c’est la page actuelle
        ]);

        $now = new \DateTime();

        /* partie pour récupérer les demi-journées qui composent la session */

        // On initialise un tableau vide pour stocker les demi-journées regroupées par date et moment (matin/après-midi)
        $demiJournees = [];

        // On parcourt toutes les planifications de la session (chaque planification correspond à un module, enseigné à un moment donné)
        foreach ($session->getPlanifications() as $planif) {
            // On récupère la date au format d/m/Y (ex : 2025-04-12)
            $date = $planif->getDateDebut()->format('Y-m-d');
            // On récupère la date sous forme d'un objet DateTime qu'on passera à la vue
            $dateTwig = $planif->getDateDebut();
            // On extrait l'heure pour déterminer le moment de la journée (matin ou après-midi)
            $hour = (int)$planif->getDateDebut()->format('H');
            // Si l'heure est avant 12h, c'est le matin ; sinon, c'est l'après-midi
            $moment = ($hour < 12) ? 'matin' : 'après-midi';
            // On crée une clé unique pour chaque demi-journée, par exemple : "2025-04-12-matin"
            $key = $date . '-' . $moment;

            // Si cette demi-journée n'existe pas encore dans le tableau, on l'initialise
            if (!isset($demiJournees[$key])) {
                $demiJournees[$key] = [
                    'date' => $date,
                    'moment' => $moment,
                    'modules' => [] // On prépare un tableau pour stocker les noms des modules
                ];
            }

            // On ajoute le nom du module à la liste des modules de cette demi-journée
            $demiJournees[$key]['modules'][] = $planif->getModule()->getNomModule();
        }

        // On trie les demi-journées pour que les matinées apparaissent avant les après-midis d'un même jour
        uksort($demiJournees, function ($a, $b) { // les arguments $a et $b représentent les clés du tableau $demiJournees
            // On sépare chaque clé en deux parties : date et moment
            [$dateA, $momentA] = explode('-', $a); // pour découper la chaîne $a en deux parties : une date et un moment 
            [$dateB, $momentB] = explode('-', $b); // pour découper la chaîne $b en deux parties : une date et un moment 
        
            if ($dateA === $dateB) {
                // Si la date est la même, on place le matin avant l'après-midi
                return ($momentA === $momentB) ? 0 : (($momentA === 'matin') ? -1 : 1);
            }
            // 0 : les deux éléments comparés sont considérés comme égaux, donc leur ordre relatif ne change pas.
            // -1 :  premier élément doit venir avant le second élément.
            // 1 : le premier élément doit venir après le second élément.
        
            // Sinon, on trie simplement par date (ordre chronologique)
            return strcmp($dateA, $dateB);
        });
        /* fin de la partie pour récupérer les demi-journées qui composent la session */
        
        return $this->render('document/feuille_emargement_solo.html.twig', [
            'session' => $session,
            'apprenant' => $apprenant,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
            'now' => $now,
            'entreprise' => $entreprise,
            'representant' => $representant,
            'demi_journees' => $demiJournees,
        ]);

        /*
        <p>Prix total payé par la société : {{ prix_total[0].totalPaye ?? 'Non disponible' }} €</p>
        -> La requête retourne un tableau d'un seul élément (getResult()), donc on accède à la première ligne avec [0] et à la colonne totalPaye.
        */
    }



    
    /*
    *
    * route pour générer une feuille d'émargement PDF spécifique à une session et à une société (paramètres dynamiques dans l'url)
    *
    */
    #[Route('/accueil/suivis/session/{sessionId}/apprenant/{apprenantId}/feuille_emargement_solo_pdf', name: 'generer_feuille_emargement_solo_pdf')]
     
    public function genererFeuilleEmargementSoloPdf(
        int $sessionId,
        int $apprenantId,
        SessionRepository $sessionRepo,
        ApprenantRepository $apprenantRepo,
        EntrepriseRepository $entrepriseRepo,
        RepresentantRepository $representantRepo,
        PdfGenerator $pdfGenerator, /* on injecte le service pour pouvoir en bénéficier */
        SluggerInterface $slugger, /* on injecte le service pour pouvoir en bénéficier */
    ): Response
    {
        $entreprise = $entrepriseRepo->findUniqueEntreprise();
        // on récupère les infos de l'organisme de formation (l’entreprise)

        $representant = $representantRepo->findUniqueRepresentant();
        // on récupère le représentant de l'organisme de formation
        
        // on récupère les chemins absolus des images (logo et tampon)
        $logoFilename = $entreprise->getLogoFilename();
        $logoPath = $this->getParameter('kernel.project_dir') . '/var/uploads/logos/'.$logoFilename;
        $tamponFilename = $representant->getTamponFilename();
        $tamponPath = $this->getParameter('kernel.project_dir') . '/var/uploads/tampons/'.$tamponFilename;
        
        // On récupère les extensions des fichiers (png, jpg, etc.)
        $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
        $tamponType = pathinfo($tamponPath, PATHINFO_EXTENSION);
        
        // On lit les contenus des fichiers
        $logoData = file_get_contents($logoPath);
        $tamponData = file_get_contents($tamponPath);
        
        // On encode les images en base64 : utile pour l’afficher dans un PDF
        $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
        $tamponBase64 = 'data:image/' . $tamponType . ';base64,' . base64_encode($tamponData);

        // on récupère la session de formation et la société concernée via leurs IDs
        $session = $sessionRepo->find($sessionId);
        $apprenant = $apprenantRepo->find($apprenantId);
        /*$societe = $apprenant->getSociete();*/

        // On se protège d'une possible erreur
        if (!$session || !$apprenant) {
            throw $this->createNotFoundException('Session ou apprenant introuvable.');
        }

        $now = new \DateTime();
        
        /* partie pour récupérer les demi-journées qui composent la session */

        // On initialise un tableau vide pour stocker les demi-journées regroupées par date et moment (matin/après-midi)
        $demiJournees = [];

        // On parcourt toutes les planifications de la session (chaque planification correspond à un module, enseigné à un moment donné)
        foreach ($session->getPlanifications() as $planif) {
            // On récupère la date au format d/m/Y (ex : 12/04/2025)
            $date = $planif->getDateDebut()->format('Y-m-d');
            // On récupère la date sous forme d'un objet DateTime qu'on passera à la vue
            $dateTwig = $planif->getDateDebut();
            // On extrait l'heure pour déterminer le moment de la journée (matin ou après-midi)
            $hour = (int)$planif->getDateDebut()->format('H');
            // Si l'heure est avant 12h, c'est le matin ; sinon, c'est l'après-midi
            $moment = ($hour < 12) ? 'matin' : 'après-midi';
            // On crée une clé unique pour chaque demi-journée, par exemple : "2025-04-12-matin"
            $key = $date . '-' . $moment;

            // Si cette demi-journée n'existe pas encore dans le tableau, on l'initialise
            if (!isset($demiJournees[$key])) {
                $demiJournees[$key] = [
                    'date' => $date,
                    'moment' => $moment,
                    'modules' => [] // On prépare un tableau pour stocker les noms des modules
                ];
            }

            // On ajoute le nom du module à la liste des modules de cette demi-journée
            $demiJournees[$key]['modules'][] = $planif->getModule()->getNomModule();
        }

        // On trie les demi-journées pour que les matinées apparaissent avant les après-midis d'un même jour
        uksort($demiJournees, function ($a, $b) { // les arguments $a et $b représentent les clés du tableau $demiJournees
            // On sépare chaque clé en deux parties : date et moment
            [$dateA, $momentA] = explode('-', $a); // pour découper la chaîne $a en deux parties : une date et un moment 
            [$dateB, $momentB] = explode('-', $b); // pour découper la chaîne $b en deux parties : une date et un moment 
        
            if ($dateA === $dateB) {
                // Si la date est la même, on place le matin avant l'après-midi
                return ($momentA === $momentB) ? 0 : (($momentA === 'matin') ? -1 : 1);
            }
            // 0 : les deux éléments comparés sont considérés comme égaux, donc leur ordre relatif ne change pas.
            // -1 :  premier élément doit venir avant le second élément.
            // 1 : le premier élément doit venir après le second élément.
        
            // Sinon, on trie simplement par date (ordre chronologique)
            return strcmp($dateA, $dateB);
        });
        /* fin de la partie pour récupérer les demi-journées qui composent la session */

        // Récupérer le contenu HTML du template Twig
        $htmlContent = $this->renderView('document/feuille_emargement_solo_pdf.html.twig', [
            'session' => $session,
            'apprenant' => $apprenant,
            'now' => $now,
            'entreprise' => $entreprise,
            'representant' => $representant,
            'pdfMode' => true,
            'logoBase64' => $logo_base64,
            'tamponBase64' => $tampon_base64,
            'demi_journees' => $demiJournees,
        ]);

        // On génère le contenu PDF à l’aide du service PdfGenerator
        $pdfContent = $pdfGenerator->getPdfContent($htmlContent);

        $slug = $slugger->slug($apprenant->__toString())->lower(); // pour avoir une identité de l'apprenant propre et normalisé

        // On retourne une réponse HTTP contenant le fichier PDF
        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Feuille_emargement_solo_' . $slug . '.pdf"', // nom de ficher avec nom de société propre et normalisé
            ]
        );
    }


    

    /*
    *
    * route pour générer une attestation de fin de formation spécifique à une session et à un apprenant (paramètres dynamiques dans l'url)
    *
    */
    #[Route('/accueil/suivis/session/{sessionId}/apprenant/{apprenantId}/attestation_fin_formation', name: 'generer_attestation_fin_formation')]
    public function genererAttestationFinFormation(
        int $sessionId,
        int $apprenantId,
        SessionRepository $sessionRepo,
        SocieteRepository $societeRepo,
        ApprenantRepository $apprenantRepo,
        EntrepriseRepository $entrepriseRepo,
        RepresentantRepository $representantRepo,
        BreadcrumbsGenerator $breadcrumbsGenerator,
    ): Response
    {
        // on récupère la session, la société et toutes les données nécessaires
        $session = $sessionRepo->find($sessionId);
        $apprenant = $apprenantRepo->find($apprenantId);
        $societe = $apprenant->getSociete();

        // S’il y a une société, on récupère son id et le responsable légal
        $responsableLegal = null;
        if ($societe !== null) {
            $societeId = $societe->getId();
            $responsableLegal = $societeRepo->findUniqueRespLegal($societeId); // pour récupérer le responsable légal de la société
        }

        $entreprise = $entrepriseRepo->findUniqueEntreprise(); // pour récupérer l'organisme de formation
        $representant = $representantRepo->findUniqueRepresentant(); // pour récupérer le représentant de l'organisme de formation
        
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Suivis', 'route' => 'suivis'],
            ['label' => 'Liste de suivi des sessions', 'route' => 'suivi_app_session'],
            ['label' => "Détails de suivi d'une session #".$session->getId(), 'route' => 'suivi_show_session', 'params' => ['id' => $session->getId()]],
            ['label' => "Attestation fin formation apprenant #".$apprenant->getId(),], // Pas de route car c’est la page actuelle
        ]);

        $now = new \DateTime();
        
        return $this->render('document/attestation_fin_formation.html.twig', [
            'session' => $session,
            'societe' => $societe,
            'apprenant' => $apprenant,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
            'now' => $now,
            'entreprise' => $entreprise,
            'representant' => $representant,
            'responsableLegal' => $responsableLegal,
        ]);
    }




    /*
    *
    * route pour générer une attestation de fin de formation PDF spécifique à une session et à un apprenant (paramètres dynamiques dans l'url)
    *
    */
    #[Route('/accueil/suivis/session/{sessionId}/apprenant/{apprenantId}/attestation_fin_formation_pdf', name: 'generer_attestation_fin_formation_pdf')]
     
    public function genererAttestationFinFormationPdf(
        int $sessionId,
        int $apprenantId,
        SessionRepository $sessionRepo,
        SocieteRepository $societeRepo,
        ApprenantRepository $apprenantRepo,
        EntrepriseRepository $entrepriseRepo,
        RepresentantRepository $representantRepo,
        PdfGenerator $pdfGenerator, /* on injecte le service pour pouvoir en bénéficier */
        SluggerInterface $slugger, /* on injecte le service pour pouvoir en bénéficier */
    ): Response
    {
        $entreprise = $entrepriseRepo->findUniqueEntreprise();
        // on récupère les infos de l'organisme de formation (l’entreprise)

        $representant = $representantRepo->findUniqueRepresentant();
        // on récupère le représentant de l'organisme de formation
        
        // on récupère les chemins absolus des images (logo et tampon)
        $logoFilename = $entreprise->getLogoFilename();
        $logoPath = $this->getParameter('kernel.project_dir') . '/var/uploads/logos/'.$logoFilename;
        $tamponFilename = $representant->getTamponFilename();
        $tamponPath = $this->getParameter('kernel.project_dir') . '/var/uploads/tampons/'.$tamponFilename;
        
        // On récupère les extensions des fichiers (png, jpg, etc.)
        $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
        $tamponType = pathinfo($tamponPath, PATHINFO_EXTENSION);
        
        // On lit les contenus des fichiers
        $logoData = file_get_contents($logoPath);
        $tamponData = file_get_contents($tamponPath);
        
        // On encode les images en base64 : utile pour l’afficher dans un PDF
        $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
        $tamponBase64 = 'data:image/' . $tamponType . ';base64,' . base64_encode($tamponData);

        // on récupère la session de formation et la société concernée via leurs IDs
        $session = $sessionRepo->find($sessionId);
        $apprenant = $apprenantRepo->find($apprenantId);
        $societe = $apprenant->getSociete();

        // On se protège d'une possible erreur
        if (!$session || !$apprenant) {
            throw $this->createNotFoundException('Session ou apprenant introuvable.');
        }
        
        // S’il y a une société, on récupère son id et le responsable légal
        $responsableLegal = null;
        if ($societe !== null) {
            $societeId = $societe->getId();
            $responsableLegal = $societeRepo->findUniqueRespLegal($societeId); // pour récupérer le responsable légal de la société
        }

        $now = new \DateTime();

        // Récupérer le contenu HTML du template Twig
        $htmlContent = $this->renderView('document/attestation_fin_formation_pdf.html.twig', [
            'session' => $session,
            'societe' => $societe,
            'apprenant' => $apprenant,
            'now' => $now,
            'entreprise' => $entreprise,
            'representant' => $representant,
            'responsableLegal' => $responsableLegal,
            'pdfMode' => true,
            'logoBase64' => $logo_base64,
            'tamponBase64' => $tampon_base64,
        ]);

        // On génère le contenu PDF à l’aide du service PdfGenerator
        $pdfContent = $pdfGenerator->getPdfContent($htmlContent);

        $slug = $slugger->slug($apprenant->__toString())->lower(); // pour avoir une identité de l'apprenant propre et normalisé

        // On retourne une réponse HTTP contenant le fichier PDF
        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Attestation_fin_formation_solo_' . $slug . '.pdf"', // nom de ficher avec nom de société propre et normalisé
            ]
        );
    }



    /*
    *
    * route pour générer une attestation de présence spécifique à une session et à un apprenant (paramètres dynamiques dans l'url)
    *
    */
    #[Route('/accueil/suivis/session/{sessionId}/apprenant/{apprenantId}/attestation_presence', name: 'generer_attestation_presence')]
    public function genererAttestationPresence(
        int $sessionId,
        int $apprenantId,
        SessionRepository $sessionRepo,
        SocieteRepository $societeRepo,
        ApprenantRepository $apprenantRepo,
        EntrepriseRepository $entrepriseRepo,
        RepresentantRepository $representantRepo,
        BreadcrumbsGenerator $breadcrumbsGenerator,
    ): Response
    {
        // on récupère la session, la société et toutes les données nécessaires
        $session = $sessionRepo->find($sessionId);
        $apprenant = $apprenantRepo->find($apprenantId);
        $societe = $apprenant->getSociete();

        // S’il y a une société, on récupère son id et le responsable légal
        $responsableLegal = null;
        if ($societe !== null) {
            $societeId = $societe->getId();
            $responsableLegal = $societeRepo->findUniqueRespLegal($societeId); // pour récupérer le responsable légal de la société
        }

        
        $entreprise = $entrepriseRepo->findUniqueEntreprise(); // pour récupérer l'organisme de formation
        $representant = $representantRepo->findUniqueRepresentant(); // pour récupérer le représentant de l'organisme de formation
        

        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Suivis', 'route' => 'suivis'],
            ['label' => 'Liste de suivi des sessions', 'route' => 'suivi_app_session'],
            ['label' => "Détails de suivi d'une session #".$session->getId(), 'route' => 'suivi_show_session', 'params' => ['id' => $session->getId()]],
            ['label' => "Attestation présence apprenant #".$apprenant->getId(),], // Pas de route car c’est la page actuelle
        ]);


        $now = new \DateTime();

        
        return $this->render('document/attestation_presence.html.twig', [
            'session' => $session,
            'societe' => $societe,
            'apprenant' => $apprenant,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
            'now' => $now,
            'entreprise' => $entreprise,
            'representant' => $representant,
            'responsableLegal' => $responsableLegal,
        ]);
    }




    /*
    *
    * route pour générer une attestation de présence PDF spécifique à une session et à un apprenant (paramètres dynamiques dans l'url)
    *
    */
    #[Route('/accueil/suivis/session/{sessionId}/apprenant/{apprenantId}/attestation_presence_pdf', name: 'generer_attestation_presence_pdf')]
     
    public function genererAttestationPresencePdf(
        int $sessionId,
        int $apprenantId,
        SessionRepository $sessionRepo,
        SocieteRepository $societeRepo,
        ApprenantRepository $apprenantRepo,
        EntrepriseRepository $entrepriseRepo,
        RepresentantRepository $representantRepo,
        PdfGenerator $pdfGenerator, /* on injecte le service pour pouvoir en bénéficier */
        SluggerInterface $slugger, /* on injecte le service pour pouvoir en bénéficier */
    ): Response
    {
        $entreprise = $entrepriseRepo->findUniqueEntreprise();
        // on récupère les infos de l'organisme de formation (l’entreprise)

        $representant = $representantRepo->findUniqueRepresentant();
        // on récupère le représentant de l'organisme de formation
        
        // on récupère les chemins absolus des images (logo et tampon)
        $logoFilename = $entreprise->getLogoFilename();
        $logoPath = $this->getParameter('kernel.project_dir') . '/var/uploads/logos/'.$logoFilename;
        $tamponFilename = $representant->getTamponFilename();
        $tamponPath = $this->getParameter('kernel.project_dir') . '/var/uploads/tampons/'.$tamponFilename;
        
        // On récupère les extensions des fichiers (png, jpg, etc.)
        $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
        $tamponType = pathinfo($tamponPath, PATHINFO_EXTENSION);
        
        // On lit les contenus des fichiers
        $logoData = file_get_contents($logoPath);
        $tamponData = file_get_contents($tamponPath);
        
        // On encode les images en base64 : utile pour l’afficher dans un PDF
        $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
        $tamponBase64 = 'data:image/' . $tamponType . ';base64,' . base64_encode($tamponData);

        // on récupère la session de formation et la société concernée via leurs IDs
        $session = $sessionRepo->find($sessionId);
        $apprenant = $apprenantRepo->find($apprenantId);
        $societe = $apprenant->getSociete();

        // On se protège d'une possible erreur
        if (!$session || !$apprenant) {
            throw $this->createNotFoundException('Session ou apprenant introuvable.');
        }
        
        // S’il y a une société, on récupère son id et le responsable légal
        $responsableLegal = null;
        if ($societe !== null) {
            $societeId = $societe->getId();
            $responsableLegal = $societeRepo->findUniqueRespLegal($societeId); // pour récupérer le responsable légal de la société
        }

        $now = new \DateTime();

        // Récupérer le contenu HTML du template Twig
        $htmlContent = $this->renderView('document/attestation_presence_pdf.html.twig', [
            'session' => $session,
            'societe' => $societe,
            'apprenant' => $apprenant,
            'now' => $now,
            'entreprise' => $entreprise,
            'representant' => $representant,
            'responsableLegal' => $responsableLegal,
            'pdfMode' => true,
            'logoBase64' => $logo_base64,
            'tamponBase64' => $tampon_base64,
        ]);

        // On génère le contenu PDF à l’aide du service PdfGenerator
        $pdfContent = $pdfGenerator->getPdfContent($htmlContent);

        $slug = $slugger->slug($apprenant->__toString())->lower(); // pour avoir une identité de l'apprenant propre et normalisé

        // On retourne une réponse HTTP contenant le fichier PDF
        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Attestation_presence_' . $slug . '.pdf"', // nom de ficher avec nom de société propre et normalisé
            ]
        );
    }




    /*
    #[Route('/accueil/parametres/modeles_documents/releve_connexion', name: 'releve_connexion')]
    public function releveConnexion(
        BreadcrumbsGenerator $breadcrumbsGenerator
    ): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Liste des modèles de documents', 'route' => 'modeles_documents'],
            ['label' => "Relevé de connexion"], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('document/releve_connexion.html.twig', [
            'controller_name' => 'DocumentController',
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }
    */

    /*
    #[Route('/accueil/parametres/modeles_documents/releve_evaluation', name: 'releve_evaluation')]
    public function releveEvaluation(
        BreadcrumbsGenerator $breadcrumbsGenerator
    ): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Liste des modèles de documents', 'route' => 'modeles_documents'],
            ['label' => "Relevé d'évaluation"], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('document/releve_evaluation.html.twig', [
            'controller_name' => 'DocumentController',
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }
    */




    /*
    *
    * route pour générer une attestation d'assiduité spécifique à une session et à un apprenant (paramètres dynamiques dans l'url)
    *
    */
    #[Route('/accueil/suivis/session/{sessionId}/apprenant/{apprenantId}/attestation_assiduite', name: 'generer_attestation_assiduite')]
    public function genererAttestationAssiduite(
        int $sessionId,
        int $apprenantId,
        SessionRepository $sessionRepo,
        SocieteRepository $societeRepo,
        ApprenantRepository $apprenantRepo,
        EntrepriseRepository $entrepriseRepo,
        RepresentantRepository $representantRepo,
        BreadcrumbsGenerator $breadcrumbsGenerator,
    ): Response
    {
        // on récupère la session, la société et toutes les données nécessaires
        $session = $sessionRepo->find($sessionId);
        $apprenant = $apprenantRepo->find($apprenantId);
        $societe = $apprenant->getSociete();

        // S’il y a une société, on récupère son id et le responsable légal
        $responsableLegal = null;
        if ($societe !== null) {
            $societeId = $societe->getId();
            $responsableLegal = $societeRepo->findUniqueRespLegal($societeId); // pour récupérer le responsable légal de la société
        }

        
        $entreprise = $entrepriseRepo->findUniqueEntreprise(); // pour récupérer l'organisme de formation
        $representant = $representantRepo->findUniqueRepresentant(); // pour récupérer le représentant de l'organisme de formation
        

        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Suivis', 'route' => 'suivis'],
            ['label' => 'Liste de suivi des sessions', 'route' => 'suivi_app_session'],
            ['label' => "Détails de suivi d'une session #".$session->getId(), 'route' => 'suivi_show_session', 'params' => ['id' => $session->getId()]],
            ['label' => "Attestation assiduité apprenant #".$apprenant->getId(),], // Pas de route car c’est la page actuelle
        ]);


        $now = new \DateTime();

        
        return $this->render('document/attestation_assiduite.html.twig', [
            'session' => $session,
            'societe' => $societe,
            'apprenant' => $apprenant,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
            'now' => $now,
            'entreprise' => $entreprise,
            'representant' => $representant,
            'responsableLegal' => $responsableLegal,
        ]);
    }




    /*
    *
    * route pour générer une attestation d'assiduité PDF spécifique à une session et à un apprenant (paramètres dynamiques dans l'url)
    *
    */
    #[Route('/accueil/suivis/session/{sessionId}/apprenant/{apprenantId}/attestation_assiduite_pdf', name: 'generer_attestation_assiduite_pdf')]
    public function genererAttestationAssiduitePdf(
        int $sessionId,
        int $apprenantId,
        SessionRepository $sessionRepo,
        SocieteRepository $societeRepo,
        ApprenantRepository $apprenantRepo,
        EntrepriseRepository $entrepriseRepo,
        RepresentantRepository $representantRepo,
        PdfGenerator $pdfGenerator, /* on injecte le service pour pouvoir en bénéficier */
        SluggerInterface $slugger, /* on injecte le service pour pouvoir en bénéficier */
    ): Response
    {
        $entreprise = $entrepriseRepo->findUniqueEntreprise();
        // on récupère les infos de l'organisme de formation (l’entreprise)

        $representant = $representantRepo->findUniqueRepresentant();
        // on récupère le représentant de l'organisme de formation
        
        // on récupère les chemins absolus des images (logo et tampon)
        $logoFilename = $entreprise->getLogoFilename();
        $logoPath = $this->getParameter('kernel.project_dir') . '/var/uploads/logos/'.$logoFilename;
        $tamponFilename = $representant->getTamponFilename();
        $tamponPath = $this->getParameter('kernel.project_dir') . '/var/uploads/tampons/'.$tamponFilename;
        
        // On récupère les extensions des fichiers (png, jpg, etc.)
        $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
        $tamponType = pathinfo($tamponPath, PATHINFO_EXTENSION);
        
        // On lit les contenus des fichiers
        $logoData = file_get_contents($logoPath);
        $tamponData = file_get_contents($tamponPath);
        
        // On encode les images en base64 : utile pour l’afficher dans un PDF
        $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
        $tamponBase64 = 'data:image/' . $tamponType . ';base64,' . base64_encode($tamponData);

        // on récupère la session de formation et la société concernée via leurs IDs
        $session = $sessionRepo->find($sessionId);
        $apprenant = $apprenantRepo->find($apprenantId);
        $societe = $apprenant->getSociete();

        // On se protège d'une possible erreur
        if (!$session || !$apprenant) {
            throw $this->createNotFoundException('Session ou apprenant introuvable.');
        }
        
        // S’il y a une société, on récupère son id et le responsable légal
        $responsableLegal = null;
        if ($societe !== null) {
            $societeId = $societe->getId();
            $responsableLegal = $societeRepo->findUniqueRespLegal($societeId); // pour récupérer le responsable légal de la société
        }

        $now = new \DateTime();

        // Récupérer le contenu HTML du template Twig
        $htmlContent = $this->renderView('document/attestation_assiduite_pdf.html.twig', [
            'session' => $session,
            'societe' => $societe,
            'apprenant' => $apprenant,
            'now' => $now,
            'entreprise' => $entreprise,
            'representant' => $representant,
            'responsableLegal' => $responsableLegal,
            'pdfMode' => true,
            'logoBase64' => $logo_base64,
            'tamponBase64' => $tampon_base64,
        ]);

        // On génère le contenu PDF à l’aide du service PdfGenerator
        $pdfContent = $pdfGenerator->getPdfContent($htmlContent);

        $slug = $slugger->slug($apprenant->__toString())->lower(); // pour avoir une identité de l'apprenant propre et normalisé

        // On retourne une réponse HTTP contenant le fichier PDF
        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Attestation_assiduite_' . $slug . '.pdf"', // nom de ficher avec nom de société propre et normalisé
            ]
        );
    }

    
    /*
    *
    * route pour générer un certificat de réalisation spécifique à une session et à un apprenant (paramètres dynamiques dans l'url)
    *
    */
    #[Route('/accueil/suivis/session/{sessionId}/apprenant/{apprenantId}/certificat_realisation', name: 'generer_certificat_realisation')]
    public function genererCertificatRealisation(
        int $sessionId,
        int $apprenantId,
        SessionRepository $sessionRepo,
        SocieteRepository $societeRepo,
        ApprenantRepository $apprenantRepo,
        EntrepriseRepository $entrepriseRepo,
        RepresentantRepository $representantRepo,
        BreadcrumbsGenerator $breadcrumbsGenerator,
    ): Response
    {
        // on récupère la session, la société et toutes les données nécessaires
        $session = $sessionRepo->find($sessionId);
        $apprenant = $apprenantRepo->find($apprenantId);
        $societe = $apprenant->getSociete();

        // S’il y a une société, on récupère son id et le responsable légal
        $responsableLegal = null;
        if ($societe !== null) {
            $societeId = $societe->getId();
            $responsableLegal = $societeRepo->findUniqueRespLegal($societeId); // pour récupérer le responsable légal de la société
        }

        $entreprise = $entrepriseRepo->findUniqueEntreprise(); // pour récupérer l'organisme de formation
        $representant = $representantRepo->findUniqueRepresentant(); // pour récupérer le représentant de l'organisme de formation
        
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Suivis', 'route' => 'suivis'],
            ['label' => 'Liste de suivi des sessions', 'route' => 'suivi_app_session'],
            ['label' => "Détails de suivi d'une session #".$session->getId(), 'route' => 'suivi_show_session', 'params' => ['id' => $session->getId()]],
            ['label' => "Certificat réalisation apprenant #".$apprenant->getId(),], // Pas de route car c’est la page actuelle
        ]);

        $now = new \DateTime();

        return $this->render('document/certificat_realisation.html.twig', [
            'session' => $session,
            'societe' => $societe,
            'apprenant' => $apprenant,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
            'now' => $now,
            'entreprise' => $entreprise,
            'representant' => $representant,
            'responsableLegal' => $responsableLegal,
        ]);
    }




    /*
    *
    * route pour générer un certificat de réalisation PDF spécifique à une session et à un apprenant (paramètres dynamiques dans l'url)
    *
    */
    #[Route('/accueil/suivis/session/{sessionId}/apprenant/{apprenantId}/certificat_realisation_pdf', name: 'generer_certificat_realisation_pdf')]
     
    public function genererCertificatRealisationPdf(
        int $sessionId,
        int $apprenantId,
        SessionRepository $sessionRepo,
        SocieteRepository $societeRepo,
        ApprenantRepository $apprenantRepo,
        EntrepriseRepository $entrepriseRepo,
        RepresentantRepository $representantRepo,
        PdfGenerator $pdfGenerator, /* on injecte le service pour pouvoir en bénéficier */
        SluggerInterface $slugger, /* on injecte le service pour pouvoir en bénéficier */
    ): Response
    {
        $entreprise = $entrepriseRepo->findUniqueEntreprise();
        // on récupère les infos de l'organisme de formation (l’entreprise)

        $representant = $representantRepo->findUniqueRepresentant();
        // on récupère le représentant de l'organisme de formation
        
        // on récupère les chemins absolus des images (logo et tampon)
        $logoFilename = $entreprise->getLogoFilename();
        $logoPath = $this->getParameter('kernel.project_dir') . '/var/uploads/logos/'.$logoFilename;
        $tamponFilename = $representant->getTamponFilename();
        $tamponPath = $this->getParameter('kernel.project_dir') . '/var/uploads/tampons/'.$tamponFilename;
        
        // On récupère les extensions des fichiers (png, jpg, etc.)
        $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
        $tamponType = pathinfo($tamponPath, PATHINFO_EXTENSION);
        
        // On lit les contenus des fichiers
        $logoData = file_get_contents($logoPath);
        $tamponData = file_get_contents($tamponPath);
        
        // On encode les images en base64 : utile pour l’afficher dans un PDF
        $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
        $tamponBase64 = 'data:image/' . $tamponType . ';base64,' . base64_encode($tamponData);

        // on récupère la session de formation et la société concernée via leurs IDs
        $session = $sessionRepo->find($sessionId);
        $apprenant = $apprenantRepo->find($apprenantId);
        $societe = $apprenant->getSociete();

        // On se protège d'une possible erreur
        if (!$session || !$apprenant) {
            throw $this->createNotFoundException('Session ou apprenant introuvable.');
        }
        
        // S’il y a une société, on récupère son id et le responsable légal
        $responsableLegal = null;
        if ($societe !== null) {
            $societeId = $societe->getId();
            $responsableLegal = $societeRepo->findUniqueRespLegal($societeId); // pour récupérer le responsable légal de la société
        }

        $now = new \DateTime();

        // Récupérer le contenu HTML du template Twig
        $htmlContent = $this->renderView('document/certificat_realisation_pdf.html.twig', [
            'session' => $session,
            'societe' => $societe,
            'apprenant' => $apprenant,
            'now' => $now,
            'entreprise' => $entreprise,
            'representant' => $representant,
            'responsableLegal' => $responsableLegal,
            'pdfMode' => true,
            'logoBase64' => $logo_base64,
            'tamponBase64' => $tampon_base64,
        ]);

        // On génère le contenu PDF à l’aide du service PdfGenerator
        $pdfContent = $pdfGenerator->getPdfContent($htmlContent);

        $slug = $slugger->slug($apprenant->__toString())->lower(); // pour avoir une identité de l'apprenant propre et normalisé

        // On retourne une réponse HTTP contenant le fichier PDF
        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Certificat_realisation_' . $slug . '.pdf"', // nom de ficher avec nom de société propre et normalisé
            ]
        );
    }




    #[Route('/accueil/parametres/modeles_documents/contrat_client_particulier', name: 'contrat_client_particulier')]
    public function contratClientParticulier(
        BreadcrumbsGenerator $breadcrumbsGenerator
    ): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Liste des modèles de documents', 'route' => 'modeles_documents'],
            ['label' => 'Contrat client particulier'], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('document/contrat_client_particulier.html.twig', [
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }

}
