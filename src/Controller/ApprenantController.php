<?php

namespace App\Controller;

use App\Entity\Apprenant;
use App\Repository\ApprenantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// classe ApprenantController hérite de la classe AbstractController, ce qui permet d'accéder à certaines méthodes pré-établies
final class ApprenantController extends AbstractController
{
    // url de la route : /apprenant
    // nom de la route : app_apprenant -> peut être changé mais doit être différent de tous les names de tous les controleurs !
    #[Route('/apprenant', name: 'app_apprenant')]

    // méthode index (première façon de faire)
    // public function index(EntityManagerInterface $entityManager): Response

    // méthode index (deuxième façon de faire)
    public function index(ApprenantRepository $apprenantRepository): Response
    {
        // exemple du début -> désuet pour la suite
        $name = 'nom';

        // méthode index (première façon de faire)
        // $apprenants = $entityManager->getRepository(Apprenant::class)->findAll();

        // méthode index (deuxième façon de faire)
        // $apprenants = $apprenantRepository->findAll();

        // méthode index (troisième façon de faire)
        $apprenants = $apprenantRepository->findBy([], ["nom"=>"ASC"]);
        // SELECT * FROM apprenant ORDER BY nom



        // permet de faire le lien entre le controleur et la vue (vers la vue 'apprenant/index.html.twig')
        return $this->render('apprenant/index.html.twig', [
            // argument par défaut en guise d''exemple 
            // {{ controler_name }} dans apprenant/index.html.twig -> navigateur (http://127.0.0.1:8000/apprenant) affiche 'ApprenantController'
            'controller_name' => $name,
            'apprenants' => $apprenants,
        ]);
    }
}




































// fetching : parcours de la BDD