<?php

namespace App\Controller;

use App\Entity\Module;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ModuleController extends AbstractController
{
    #[Route('/module', name: 'app_module')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $modules = $entityManager->getRepository(Module::class)->findAll();
        return $this->render('module/index.html.twig', [
            'modules' => $modules,
        ]);
    }
}



