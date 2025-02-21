<?php

namespace App\Controller;

use App\Entity\Programme;
use App\Repository\ProgrammeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProgrammeController extends AbstractController
{
    #[Route('/programme', name: 'app_programme')]
    public function index(ProgrammeRepository $programmeRepository): Response
    {
        // méthode choisie qui ne permet pas de trier la liste des programmes
        // $programmes = $programmeRepository->findAll();

        $programmes = $programmeRepository->findBy([], ["nomProgramme"=>"ASC"]);

        return $this->render('programme/index.html.twig', [
            'programmes' => $programmes,
        ]);
    }
}






