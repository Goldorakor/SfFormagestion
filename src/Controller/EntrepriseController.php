<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class EntrepriseController extends AbstractController
{
    // je ne supprime pas cette méthode, peut-être servira-t-elle plus tard
    #[Route('/entreprise', name: 'app_entreprise')]
    public function index(): Response
    {
        return $this->render('entreprise/index.html.twig', [
            'controller_name' => 'EntrepriseController',
        ]);
    }


    // cette section est construite de la même façon que son équivalente dans RepresentantController
    // Route unique pour créer ou éditer l'entreprise (on fusionne les deux noms)
    #[Route('/entreprise/new_edit', name: 'new_edit_entreprise')] // 'new_edit_entreprise' est un nom cohérent qui décrit bien la fonction attendue -> plus besoin d'injecter un id : on ne veut qu'une seule entreprise au maximum !
    public function new_edit(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, #[Autowire('%kernel.project_dir%/public/uploads/logos')] string $logosDirectory): Response // pour ajouter une entreprise à notre BDD
    {
        // On récupère la seule entreprise existante ou on en crée une si aucune n'existe.
        $entreprise = $entityManager->getRepository(Entreprise::class)->findOneBy([]) ?? new Entreprise();

        // 2. on crée le formulaire à partir de EntrepriseType (on veut ce modèle là bien entendu)
        $form = $this->createForm(EntrepriseType::class, $entreprise); // c'est bien la méthode createForm() qui permet de créer le formulaire

        // 4. le traitement s'effectue ici ! si le formulaire soumis est correct, on fera l'insertion en BDD
        $form->handleRequest($request);

        // bloc qui concerne la gestion du formulaire soumis
        if ($form->isSubmitted() && $form->isValid()) {
            
            $entreprise = $form->getData(); // on récupère les données du formulaire dans notre objet entreprise

            $logoFile = $form->get('logo')->getData();

            if ($logoFile) {

                $originalFilename = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $logoFile->guessExtension();

                try {
                    $logoFile->move($logosDirectory, $newFilename);

                } catch (FileException $e) {
                    $this->addFlash('error', "Erreur lors de l'upload du fichier");
                }

                $entreprise->setLogoFilename($newFilename);
            }
            
            $entityManager->persist($entreprise); // équivaut à la méthode prepare() en PDO

            $entityManager->flush(); // équivaut à la méthode execute() en PDOStatement

            // redirection après sauvegarde -> vers le formulaire de l'entreprise (rempli, si tout fonctionne !)
            return $this->redirectToRoute('new_edit_entreprise');
        }
        // fin du bloc

        // 3. on affiche le formulaire créé dans la page dédiée à cet affichage -> {{ form(formAddEntreprise) }} --> affichage par défaut 
        return $this->render('entreprise/new_edit.html.twig', [ // 'entreprise/new.html.twig' -> vue dédiée à l'affichage du formulaire : on crée un nouveau fichier dans le dossier entreprise
            // 'form' => $form,  on fait passer une variable form qui prend la valeur $form
            // on change le nom pour éviter toute ambiguité 'form' -> 'formAddEntreprise' comme expliqué dans new_edit.html.twig
            'formAddEntreprise' => $form,
            'edit' => $entreprise->getId() !== null, // comportement booléen -> si getId() retourne une valeur, on est en mode édition et si getId() est null, on est en mode création.
            'entreprise' => $entreprise // ?? null,  rajout suite à un message d'erreur où il prétend que la variable $entreprise n'existe pas
        ]);
    }
}
