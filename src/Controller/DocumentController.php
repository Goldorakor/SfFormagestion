<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DocumentController extends AbstractController
{
    #[Route('/document', name: 'app_document')]
    public function index(): Response
    {
        return $this->render('document/index.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }


    #[Route('/accueil/parametres/modeles_documents/programme', name: 'programme')]
    public function programme(): Response
    {
        return $this->render('document/programme.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }


    #[Route('/accueil/parametres/modeles_documents/contrat_formateur', name: 'contrat_formateur')]
    public function contratFormateur(): Response
    {
        return $this->render('document/contrat_formateur.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }


    #[Route('/accueil/parametres/modeles_documents/convocation', name: 'convocation')]
    public function convocation(): Response
    {
        return $this->render('document/convocation.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }


    #[Route('/accueil/parametres/modeles_documents/convention', name: 'convention')]
    public function convention(): Response
    {
        return $this->render('document/convention.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }


    #[Route('/accueil/parametres/modeles_documents/feuille_emargement', name: 'feuille_emargement')]
    public function feuilleEmargement(): Response
    {
        return $this->render('document/feuille_emargement.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }


    #[Route('/accueil/parametres/modeles_documents/attestation_fin_formation', name: 'attestation_fin_formation')]
    public function attestationFinFormation(): Response
    {
        return $this->render('document/attestation_fin_formation.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }


    #[Route('/accueil/parametres/modeles_documents/attestation_presence', name: 'attestation_presence')]
    public function attestationPresence(): Response
    {
        return $this->render('document/attestation_presence.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }


    #[Route('/accueil/parametres/modeles_documents/releve_connexion', name: 'releve_connexion')]
    public function releveConnexion(): Response
    {
        return $this->render('document/releve_connexion.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }


    #[Route('/accueil/parametres/modeles_documents/releve_evaluation', name: 'releve_evaluation')]
    public function releveEvaluation(): Response
    {
        return $this->render('document/releve_evaluation.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }


    #[Route('/accueil/parametres/modeles_documents/attestation_assiduite', name: 'attestation_assiduite')]
    public function attestationAssiduite(): Response
    {
        return $this->render('document/attestation_assiduite.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }


    #[Route('/accueil/parametres/modeles_documents/certificat_realisation', name: 'certificat_realisation')]
    public function certificatRealisation(): Response
    {
        return $this->render('document/certificat_realisation.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }


    #[Route('/accueil/parametres/modeles_documents/contrat_client_particulier', name: 'contrat_client_particulier')]
    public function contratClientParticulier(): Response
    {
        return $this->render('document/contrat_client_particulier.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }


}
