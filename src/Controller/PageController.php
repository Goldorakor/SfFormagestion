<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController
{
    #[Route('/page', name: 'app_page')]
    public function index(): Response
    {
        return $this->render('page/index.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

    #[Route('/parametres/termes_publiables/reglement_interieur', name: 'reglement_interieur')]
    public function regInterieur(): Response
    {
        return $this->render('pages/reglement_interieur.html.twig');
    }


    #[Route('/parametres/termes_publiables/conditions_generales_utilisation', name: 'conditions_generales_utilisation')]
    public function condGenUtilisation(): Response
    {
        return $this->render('pages/conditions_generales_utilisation.html.twig');
    }


    #[Route('/parametres/termes_publiables/conditions_generales_vente', name: 'conditions_generales_vente')]
    public function condGenVente(): Response
    {
        return $this->render('pages/conditions_generales_vente.html.twig');
    }
}








/*
    #[Route('/a-propos', name: 'page_apropos')]
    public function apropos(): Response
    {
        return $this->render('pages/a_propos.html.twig');
    }

    #[Route('/contact', name: 'page_contact')]
    public function contact(): Response
    {
        return $this->render('pages/contact.html.twig');
    }
*/