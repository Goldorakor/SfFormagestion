<?php

namespace App\Controller;

use App\Entity\Responsable;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ResponsableRepository;
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
}




