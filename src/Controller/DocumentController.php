<?php

namespace App\Controller;

use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Service\PdfGenerator;
use App\Repository\SessionRepository;
use App\Repository\SocieteRepository;
use App\Service\BreadcrumbsGenerator;
// use App\Repository\ResponsableRepository;
use App\Repository\ApprenantRepository;
use App\Repository\EntrepriseRepository;
use App\Repository\RepresentantRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[IsGranted('IS_AUTHENTICATED_FULLY')] /* seul un utilisateur bien connecté peut accéder aux méthodes de ce contrôleur */
final class DocumentController extends AbstractController
{
    // cette méthode aurait pu servir à afficher la page "liste des modèles de documents" (comme le cas Apprenant par exemple)
    // mais cette page est statique et elle est donc traitée par une méthode dans PageController.php :
    // #[Route('/accueil/parametres/modeles_documents', name: 'modeles_documents')]
    #[Route('/document', name: 'app_document')]
    public function index(): Response
    {
        return $this->render('document/index.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }


    #[Route('/accueil/parametres/modeles_documents/programme', name: 'programme')]
    public function programme(BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Liste des modèles de documents', 'route' => 'modeles_documents'],
            ['label' => 'Programme'], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('document/programme.html.twig', [
            'controller_name' => 'DocumentController',
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }


    #[Route('/accueil/parametres/modeles_documents/contrat_formateur', name: 'contrat_formateur')]
    public function contratFormateur(BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Liste des modèles de documents', 'route' => 'modeles_documents'],
            ['label' => 'Contrat formateur'], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('document/contrat_formateur.html.twig', [
            'controller_name' => 'DocumentController',
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }


    #[Route('/accueil/parametres/modeles_documents/convocation', name: 'convocation')]
    public function convocation(BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Liste des modèles de documents', 'route' => 'modeles_documents'],
            ['label' => 'Convocation'], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('document/convocation.html.twig', [
            'controller_name' => 'DocumentController',
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }


    /* 
    
    Je n'affiche pas le modèle convention en twig : AUCUN INTERET

    route pour la partie modèle de convention qu'on peut configurer (afficher ou modifier le modèle de convention)
    #[Route('/accueil/parametres/modeles_documents/convention', name: 'modele_convention')]
    public function voirModeleConvention(
        BreadcrumbsGenerator $breadcrumbsGenerator,
    ): Response
    {
        
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Liste des modèles de documents', 'route' => 'modeles_documents'],
            ['label' => 'Convention'], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('document/convention.html.twig', [
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);

    }
    
    */   


    // route pour générer une convention spécifique à une session et une société (paramètres dynamiques dans l'url)
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
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Liste des modèles de documents', 'route' => 'modeles_documents'],
            ['label' => 'Convention'], // Pas de route car c’est la page actuelle
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


    
    // la troisième méthode sur le document "convention" : la génération d'un document au format pdf
    #[Route('/accueil/suivis/session/{sessionId}/societe/{societeId}/convention_pdf', name: 'generer_convention_pdf')]
     
    public function genererConventionPdf(
        int $sessionId,
        int $societeId,
        SessionRepository $sessionRepo,
        SocieteRepository $societeRepo,
        EntrepriseRepository $entrepriseRepo,
        RepresentantRepository $representantRepo,
        BreadcrumbsGenerator $breadcrumbsGenerator,
        ): Response
    {
        
        // Optionnel : configurer DomPDF
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true); // Nécessaire si on a des images avec des URL absolues

        $dompdf = new Dompdf($pdfOptions);
        
        // Chemin vers le logo
        $path = $this->getParameter('kernel.project_dir') . '/var/uploads/logos/logo-formatoque-67d018f6254f1.png';
        
        // On récupère l'extension du fichier (png, jpg, etc.)
        $type = pathinfo($path, PATHINFO_EXTENSION);
        
        // On lit le contenu du fichier
        $data = file_get_contents($path);
        
        // On encode l'image en base64
        $logo_base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);


        // Chemin vers le tampon
        $path02 = $this->getParameter('kernel.project_dir') . '/var/uploads/tampons/tampon-formatoque-67d01558ce68c.jpg';
        
        // On récupère l'extension du fichier (png, jpg, etc.)
        $type02 = pathinfo($path02, PATHINFO_EXTENSION);
        
        // On lit le contenu du fichier
        $data02 = file_get_contents($path02);
        
        // On encode l'image en base64
        $tampon_base64 = 'data:image/' . $type02 . ';base64,' . base64_encode($data02);



        // on récupère la session, la société et  les autres données nécessaires
        $session = $sessionRepo->find($sessionId);
        $societe = $societeRepo->find($societeId);
        $apprenantsSoc = $sessionRepo->findApprenantsBySocieteBySession($sessionId, $societeId);
        $entreprise = $entrepriseRepo->findUniqueEntreprise(); // pour récupérer l'organisme de formation
        $representant = $representantRepo->findUniqueRepresentant(); // pour récupérer le représentant de l'organisme de formation
        $responsableLegal = $societeRepo->findUniqueRespLegal($societeId); // pour récupérer le responsable légal de la société
        $prixTotal = $societeRepo->findPrixSociete($sessionId, $societeId); // Récupération du prix total payé par la société pour la session donnée
        $now = new \DateTime();

        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Liste des modèles de documents', 'route' => 'modeles_documents'],
            ['label' => 'Convention (PDF)'], // Pas de route car c’est la page actuelle
        ]);

        // Récupérer le contenu HTML du template Twig
        $htmlContent = $this->renderView('document/convention_pdf.html.twig', [
            'session' => $session,
            'societe' => $societe,
            'breadcrumbs' => $breadcrumbs,
            'prix_total' => $prixTotal,
            'apprenants_soc' => $apprenantsSoc,
            'now' => $now,
            'entreprise' => $entreprise,
            'representant' => $representant,
            'responsableLegal' => $responsableLegal,
            'pdfMode' => true,
            'logoBase64' => $logo_base64,
            'tamponBase64' => $tampon_base64,
        ]);

        // Chargement et génération du PDF
        $dompdf->loadHtml($htmlContent);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Envoi du PDF au navigateur
        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="convention.pdf"', // 'Content-Disposition' => 'inline; filename="Convention_{{ societe.raisonSociale|slugify }}.pdf"',
            ]
        );
    }




    #[Route('/accueil/parametres/modeles_documents/feuille_emargement', name: 'feuille_emargement')]
    public function feuilleEmargement(BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Liste des modèles de documents', 'route' => 'modeles_documents'],
            ['label' => "Feuille d'émargement"], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('document/feuille_emargement.html.twig', [
            'controller_name' => 'DocumentController',
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }


    #[Route('/accueil/parametres/modeles_documents/attestation_fin_formation', name: 'attestation_fin_formation')]
    public function attestationFinFormation(BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Liste des modèles de documents', 'route' => 'modeles_documents'],
            ['label' => 'Attestation de fin de formation'], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('document/attestation_fin_formation.html.twig', [
            'controller_name' => 'DocumentController',
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }


    #[Route('/accueil/parametres/modeles_documents/attestation_presence', name: 'attestation_presence')]
    public function attestationPresence(BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Liste des modèles de documents', 'route' => 'modeles_documents'],
            ['label' => 'Attestation de présence'], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('document/attestation_presence.html.twig', [
            'controller_name' => 'DocumentController',
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }


    #[Route('/accueil/parametres/modeles_documents/releve_connexion', name: 'releve_connexion')]
    public function releveConnexion(BreadcrumbsGenerator $breadcrumbsGenerator): Response
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


    #[Route('/accueil/parametres/modeles_documents/releve_evaluation', name: 'releve_evaluation')]
    public function releveEvaluation(BreadcrumbsGenerator $breadcrumbsGenerator): Response
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


    #[Route('/accueil/parametres/modeles_documents/attestation_assiduite', name: 'attestation_assiduite')]
    public function attestationAssiduite(BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Liste des modèles de documents', 'route' => 'modeles_documents'],
            ['label' => "Attestation d'assiduité"], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('document/attestation_assiduite.html.twig', [
            'controller_name' => 'DocumentController',
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }


    #[Route('/accueil/parametres/modeles_documents/certificat_realisation', name: 'certificat_realisation')]
    public function certificatRealisation(BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Liste des modèles de documents', 'route' => 'modeles_documents'],
            ['label' => 'Certificat de réalisation'], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('document/certificat_realisation.html.twig', [
            'controller_name' => 'DocumentController',
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }


    #[Route('/accueil/parametres/modeles_documents/contrat_client_particulier', name: 'contrat_client_particulier')]
    public function contratClientParticulier(BreadcrumbsGenerator $breadcrumbsGenerator): Response
    {
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Liste des modèles de documents', 'route' => 'modeles_documents'],
            ['label' => 'Contrat client particulier'], // Pas de route car c’est la page actuelle
        ]);
        
        return $this->render('document/contrat_client_particulier.html.twig', [
            'controller_name' => 'DocumentController',
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }

}
