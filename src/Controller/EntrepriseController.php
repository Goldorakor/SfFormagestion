<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use App\Controller\EntrepriseController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class EntrepriseController extends AbstractController
{
    private $logosDirectory;

    // Injection via le constructeur
    public function __construct(ParameterBagInterface $params)
    {
        $this->logosDirectory = $params->get('logos_directory');
    }

    #[Route('/entreprise/new_edit', name: 'new_edit_entreprise')]
    public function newEdit(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        // On récupère la seule entreprise existante ou on en crée une nouvelle si aucune n'existe.
        $entreprise = $entityManager->getRepository(Entreprise::class)->findOneBy([]) ?? new Entreprise();

        // Création du formulaire
        $form = $this->createForm(EntrepriseType::class, $entreprise);

        // Traitement du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entreprise = $form->getData(); // On récupère les données du formulaire

            // Traitement du logo s'il y en a un
            $logoFile = $form->get('logo')->getData();

            if ($logoFile) {
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                $extension = $logoFile->guessExtension();

                if (!in_array($extension, $allowedExtensions)) {
                    $this->addFlash('error', 'Format de fichier non autorisé.');
                    return $this->redirectToRoute('new_edit_entreprise');
                }

                $originalFilename = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $logoFile->guessExtension();

                try {
                    // Déplacement du fichier vers le répertoire des logos
                    $logoFile->move($this->logosDirectory, $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', "Erreur lors de l'upload du fichier");
                }

                $entreprise->setLogoFilename($newFilename);
            }

            $entityManager->persist($entreprise);
            $entityManager->flush();

            return $this->redirectToRoute('new_edit_entreprise');
        }

        return $this->render('entreprise/new_edit.html.twig', [
            'formAddEntreprise' => $form,
            'edit' => $entreprise->getId() !== null,
            'entreprise' => $entreprise
        ]);
    }

    // Route pour afficher le logo
    #[Route('/logo/{filename}', name: 'logo_display')]
    public function displayLogo(string $filename): Response
    {
        $logosDirectory = $this->getParameter('kernel.project_dir') . '/var/uploads/logos/';
        $filePath = $logosDirectory . $filename;

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException("Le fichier n'existe pas.");
        }

        return new BinaryFileResponse($filePath);
    }
}
