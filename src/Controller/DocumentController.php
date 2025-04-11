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
    public function index(

    ): Response
    {
        return $this->render('document/index.html.twig', [
            'controller_name' => 'DocumentController',
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
            'controller_name' => 'DocumentController',
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
            'controller_name' => 'DocumentController',
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }








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


    // route pour générer une convocation PDF spécifique à une session et à un apprenant (paramètres dynamiques dans l'url)
    #[Route('/accueil/suivis/session/{sessionId}/apprenant/{apprenantId}/convocation_pdf', name: 'generer_convocation_pdf')]
     
    public function genererConvocationPdf(
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
        $now = new \DateTime();


        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Suivis', 'route' => 'suivis'],
            ['label' => 'Liste de suivi des sessions', 'route' => 'suivi_app_session'],
            ['label' => "Détails de suivi d'une session #".$session->getId(), 'route' => 'suivi_show_session', 'params' => ['id' => $session->getId()]],
            ['label' => "Convocation (PDF) apprenant #".$apprenant->getId(),], // Pas de route car c’est la page actuelle
        ]);

        // Récupérer le contenu HTML du template Twig
        $htmlContent = $this->renderView('document/convocation_pdf.html.twig', [
            'session' => $session,
            'societe' => $societe,
            'apprenant' => $apprenant,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
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
                'Content-Disposition' => 'inline; filename="convocation.pdf"', // 'Content-Disposition' => 'inline; filename="Convention_{{ societe.raisonSociale|slugify }}.pdf"',
            ]
        );
    }


    // route pour générer une convention spécifique à une session et à une société (paramètres dynamiques dans l'url)
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

    
    // route pour générer une convention PDF spécifique à une session et à une société (paramètres dynamiques dans l'url)
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


        // On protège les accès aux infos société
        $apprenantsSoc = $societe ? $sessionRepo->findApprenantsBySocieteBySession($sessionId, $societeId) : [];
        $responsableLegal = $societe ? $societeRepo->findUniqueRespLegal($societe->getId()) : null;
        $prixTotal = $societe ? $societeRepo->findPrixSociete($sessionId, $societeId) : null;

        $entreprise = $entrepriseRepo->findUniqueEntreprise(); // pour récupérer l'organisme de formation
        $representant = $representantRepo->findUniqueRepresentant(); // pour récupérer le représentant de l'organisme de formation
        $now = new \DateTime();


        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Suivis', 'route' => 'suivis'],
            ['label' => 'Liste de suivi des sessions', 'route' => 'suivi_app_session'],
            ['label' => "Détails de suivi d'une session #".$session->getId(), 'route' => 'suivi_show_session', 'params' => ['id' => $session->getId()]],
            ['label' => "Convention (PDF) société #".$societe->getId(),], // Pas de route car c’est la page actuelle
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
    public function feuilleEmargement(
        BreadcrumbsGenerator $breadcrumbsGenerator
    ): Response
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


    // route pour générer une attestation d'assiduité PDF spécifique à une session et à un apprenant (paramètres dynamiques dans l'url)
    #[Route('/accueil/suivis/session/{sessionId}/apprenant/{apprenantId}/attestation_fin_formation_pdf', name: 'generer_attestation_fin_formation_pdf')]
     
    public function genererAttestationFinFormationPdf(
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
        $now = new \DateTime();


        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Suivis', 'route' => 'suivis'],
            ['label' => 'Liste de suivi des sessions', 'route' => 'suivi_app_session'],
            ['label' => "Détails de suivi d'une session #".$session->getId(), 'route' => 'suivi_show_session', 'params' => ['id' => $session->getId()]],
            ['label' => "Attestation fin formation (PDF) apprenant #".$apprenant->getId(),], // Pas de route car c’est la page actuelle
        ]);

        // Récupérer le contenu HTML du template Twig
        $htmlContent = $this->renderView('document/attestation_fin_formation_pdf.html.twig', [
            'session' => $session,
            'societe' => $societe,
            'apprenant' => $apprenant,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
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
                'Content-Disposition' => 'inline; filename="attestation_fin_formation.pdf"', // 'Content-Disposition' => 'inline; filename="Convention_{{ societe.raisonSociale|slugify }}.pdf"',
            ]
        );
    }











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


    // route pour générer une attestation de présence PDF spécifique à une session et à un apprenant (paramètres dynamiques dans l'url)
    #[Route('/accueil/suivis/session/{sessionId}/apprenant/{apprenantId}/attestation_presence_pdf', name: 'generer_attestation_presence_pdf')]
     
    public function genererAttestationPresencePdf(
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
        $now = new \DateTime();


        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Suivis', 'route' => 'suivis'],
            ['label' => 'Liste de suivi des sessions', 'route' => 'suivi_app_session'],
            ['label' => "Détails de suivi d'une session #".$session->getId(), 'route' => 'suivi_show_session', 'params' => ['id' => $session->getId()]],
            ['label' => "Attestation présence (PDF) apprenant #".$apprenant->getId(),], // Pas de route car c’est la page actuelle
        ]);

        // Récupérer le contenu HTML du template Twig
        $htmlContent = $this->renderView('document/attestation_presence_pdf.html.twig', [
            'session' => $session,
            'societe' => $societe,
            'apprenant' => $apprenant,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
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
                'Content-Disposition' => 'inline; filename="attestation_presence.pdf"', // 'Content-Disposition' => 'inline; filename="Convention_{{ societe.raisonSociale|slugify }}.pdf"',
            ]
        );
    }













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


    // route pour générer une attestation d'assiduité PDF spécifique à une session et à un apprenant (paramètres dynamiques dans l'url)
    #[Route('/accueil/suivis/session/{sessionId}/apprenant/{apprenantId}/attestation_assiduite_pdf', name: 'generer_attestation_assiduite_pdf')]
    public function genererAttestationAssiduitePdf(
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
        $now = new \DateTime();


        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Suivis', 'route' => 'suivis'],
            ['label' => 'Liste de suivi des sessions', 'route' => 'suivi_app_session'],
            ['label' => "Détails de suivi d'une session #".$session->getId(), 'route' => 'suivi_show_session', 'params' => ['id' => $session->getId()]],
            ['label' => "Attestation assiduité (PDF) apprenant #".$apprenant->getId(),], // Pas de route car c’est la page actuelle
        ]);

        // Récupérer le contenu HTML du template Twig
        $htmlContent = $this->renderView('document/attestation_assiduite_pdf.html.twig', [
            'session' => $session,
            'societe' => $societe,
            'apprenant' => $apprenant,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
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
                'Content-Disposition' => 'inline; filename="attestation_assiduite.pdf"', // 'Content-Disposition' => 'inline; filename="Convention_{{ societe.raisonSociale|slugify }}.pdf"',
            ]
        );
    }





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


    // route pour générer un certificat de réalisation PDF spécifique à une session et à un apprenant (paramètres dynamiques dans l'url)
    #[Route('/accueil/suivis/session/{sessionId}/apprenant/{apprenantId}/certificat_realisation_pdf', name: 'generer_certificat_realisation_pdf')]
     
    public function genererCertificatRealisationPdf(
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
        $now = new \DateTime();


        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Suivis', 'route' => 'suivis'],
            ['label' => 'Liste de suivi des sessions', 'route' => 'suivi_app_session'],
            ['label' => "Détails de suivi d'une session #".$session->getId(), 'route' => 'suivi_show_session', 'params' => ['id' => $session->getId()]],
            ['label' => "Attestation présence (PDF) apprenant #".$apprenant->getId(),], // Pas de route car c’est la page actuelle
        ]);

        // Récupérer le contenu HTML du template Twig
        $htmlContent = $this->renderView('document/certificat_realisation_pdf.html.twig', [
            'session' => $session,
            'societe' => $societe,
            'apprenant' => $apprenant,
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
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
                'Content-Disposition' => 'inline; filename="certificat_realisation.pdf"', // 'Content-Disposition' => 'inline; filename="Convention_{{ societe.raisonSociale|slugify }}.pdf"',
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
            'controller_name' => 'DocumentController',
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }

}
