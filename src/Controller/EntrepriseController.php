<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use App\Service\BreadcrumbsGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
// use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


#[IsGranted('IS_AUTHENTICATED_FULLY')] /* seul un utilisateur bien connecté peut accéder aux méthodes de ce contrôleur */
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


    // https://symfony.com/doc/current/controller/upload_file.html -> on récupère la partie de code qui permet définir la méthode ci-dessous

    // Route unique pour créer ou éditer l'entreprise (on fusionne les deux noms) et afficher la vue de détails
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/entreprise/new_edit', name: 'new_edit_entreprise')] // 'new_edit_entreprise' est un nom cohérent qui décrit bien la fonction attendue -> plus besoin d'injecter un id : on ne veut qu'une seule entreprise au maximum !
    public function newEdit(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, BreadcrumbsGenerator $breadcrumbsGenerator): Response // pour ajouter un représentant à notre BDD
    // attention : #[Autowire('%kernel.project_dir%/public/uploads/logos')] => #[Autowire('%env(LOGO_DIRECTORY)%')] : stockage en dehors de public - voir le fichier .env

    {
    
        // pour construire notre fil d'Ariane
        $breadcrumbs = $breadcrumbsGenerator->generate([
            ['label' => 'Accueil', 'route' => 'accueil'],
            ['label' => 'Paramètres', 'route' => 'parametres'],
            ['label' => 'Infos sur la société'], // Pas de route car c’est la page actuelle
        ]);
        
        
        $logosDirectory = $this->getParameter('logos_directory'); // on récupère le bon chemin d'upload
        
        // dump($logosDirectory); die();  Vérification du chemin
        
        
        // On récupère la seule entreprise existante ou on en crée une si aucune n'existe.
        $entreprise = $entityManager->getRepository(Entreprise::class)->findOneBy([]) ?? new Entreprise();

        // 2. on crée le formulaire à partir de EntrepriseType (on veut ce modèle là bien entendu)
        $form = $this->createForm(EntrepriseType::class, $entreprise); // c'est bien la méthode createForm() qui permet de créer le formulaire
        
        // 4. le traitement s'effectue ici ! si le formulaire soumis est correct, on fera l'insertion en BDD
        $form->handleRequest($request);
        
        // bloc qui concerne la soumission
        if ($form->isSubmitted() && $form->isValid()) {
            
            $entreprise = $form->getData(); // on récupère les données du formulaire dans notre objet representant

            $logoFile = $form->get('logo')->getData();

            if ($logoFile) {


                // Si un logo existe déjà, on supprime l'ancien fichier du dossier
                if ($entreprise->getLogoFilename()) {
                    $oldFilePath = $logosDirectory . $entreprise->getLogoFilename();

                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);  // Supprimer le fichier logo existant
                    }
                }


                // https://symfony.com/doc/current/controller/upload_file.html
                // vérification de l'extension du fichier (on vérifie déjà dans RepresentantType.php mais une vérification supplémentaire au niveau du contrôleur empêche toute tentative de contournement en manipulant l’extension d’un fichier malveillant)
                $allowedExtensions = ['jpg', 'jpeg', 'png']; // liste des extensions autorisées
                $extension = $logoFile->guessExtension(); // Récupère l'extension réelle du fichier

                if (!in_array($extension, $allowedExtensions)) { // si l'extension ne coïncide pas avec la liste d'extensions autorisées
                    $this->addFlash('error', 'Format de fichier non autorisé.');
                    return $this->redirectToRoute('new_edit_representant'); // on est redirigé vers le formulaire
                }
                
                $originalFilename = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $logoFile->guessExtension();

                /*
                uniqid() génère un identifiant unique pour chaque fichier.
                pathinfo() récupère le nom original du fichier sans son extension.
                slug() transforme une chaine de texte en une version sécurisée et lisible pour l'URL : supprime les caractères spéciaux, remplace les espaces par des tirets, met en minuscules
                */

                try {
                    $logoFile->move($logosDirectory, $newFilename);

                    // Si le fichier a bien été déplacé, on met à jour le nom dans l'entité
                    $entreprise->setLogoFilename($newFilename);
                    
                } catch (FileException $e) {
                    $this->addFlash('error', "Erreur lors de l'upload du fichier");
                    return $this->redirectToRoute('new_edit_entreprise'); // en cas d'erreur, on est redirigé sur le formulaire (mais sans validation)
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
        return $this->render('entreprise/new_edit.html.twig', [ // 'entreprise/new.html.twig' -> vue dédiée à l'affichage du formulaire : on crée un nouveau fichier dans le dossier representant
            // 'form' => $form,  on fait passer une variable form qui prend la valeur $form
            // on change le nom pour éviter toute ambiguité 'form' -> 'formAddEntreprise' comme expliqué dans new_edit.html.twig
            'formAddEntreprise' => $form->createView(),
            'edit' => $entreprise->getId() !== null, // comportement booléen -> si getId() retourne une valeur, on est en mode édition et si getId() est null, on est en mode création.
            'entreprise' => $entreprise, // ?? null,  rajout suite à un message d'erreur où il prétend que la variable $entreprise n'existe pas
            'breadcrumbs' => $breadcrumbs, // on passe cette variable à la vue pour afficher le fil d'Ariane
        ]);
    }


    // les fichiers stockés en dehors de 'public' ne sont pas directement accessibles par url. Pour les afficher, il faut créer une route spéciale qui lit les fichiers et les sert via Symfony.
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


/*
Une fois cette route en place, pour afficher un logo dans le template Twig, on peut faire :

<img src="{{ path('logo_display', { filename: entreprise.logoFilename }) }}" alt="logo du représentant">

Ça génère une URL comme /logo/mon-logo.png, qui affichera correctement l’image !
*/


/*
if ($entreprise->getLogo()) {
    $oldFilePath = $this->getParameter('logos_directory') . '/' . $entreprise->getLogo();
    if (file_exists($oldFilePath)) {
        unlink($oldFilePath);
    }
}

$representant->setTampon($newFilename);

*/
