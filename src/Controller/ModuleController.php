<?php

namespace App\Controller;

use App\Entity\Module;
use App\Repository\ModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ModuleController extends AbstractController
{
    #[Route('/module', name: 'app_module')]
    public function index(ModuleRepository $moduleRepository): Response
    {
        // méthode choisie qui ne permet pas de trier la liste des modules
        // $modules = $moduleRepository->findAll();

        $modules = $moduleRepository->findBy([], ["nomModule"=>"ASC"]);


        return $this->render('module/index.html.twig', [
            'modules' => $modules,
        ]);
    }
}






