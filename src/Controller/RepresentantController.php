<?php

namespace App\Controller;

use App\Entity\Representant;
use App\Form\RepresentantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class RepresentantController extends AbstractController
{
    // je ne supprime pas cette méthode, peut-être servira-t-elle plus tard
    #[Route('/representant', name: 'app_representant')]
    public function index(): Response
    {
        return $this->render('representant/index.html.twig', [
            'controller_name' => 'RepresentantController',
        ]);
    }


    // https://symfony.com/doc/current/controller/upload_file.html -> on récupère la partie de code qui permet définir la méthode ci-dessous

    // Route unique pour créer ou éditer le représentant (on fusionne les deux noms)
    #[Route('/representant/new_edit', name: 'new_edit_representant')] // 'new_edit_representant' est un nom cohérent qui décrit bien la fonction attendue -> plus besoin d'injecter un id : on ne veut qu'un seul représentant au maximum !
    public function new_edit(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, #[Autowire('%env(TAMPON_DIRECTORY)%')] string $tamponsDirectory): Response // pour ajouter un représentant à notre BDD
    // attention : #[Autowire('%kernel.project_dir%/public/uploads/tampons')] => #[Autowire('%env(TAMPON_DIRECTORY)%')] : stockage en dehors de public - voir le fichier .env

    {
    
        // On récupère le seul représentant existant ou on en crée un si aucun n'existe.
        $representant = $entityManager->getRepository(Representant::class)->findOneBy([]) ?? new Representant();

        // 2. on crée le formulaire à partir de RepresentantType (on veut ce modèle là bien entendu)
        $form = $this->createForm(RepresentantType::class, $representant); // c'est bien la méthode createForm() qui permet de créer le formulaire
        
        // 4. le traitement s'effectue ici ! si le formulaire soumis est correct, on fera l'insertion en BDD
        $form->handleRequest($request);
        
        // bloc qui concerne la soumission
        if ($form->isSubmitted() && $form->isValid()) {
            
            $representant = $form->getData(); // on récupère les données du formulaire dans notre objet representant

            $tamponFile = $form->get('tampon')->getData();

            if ($tamponFile) {

                // vérification de l'extension du fichier (on vérifie déjà dans RepresentantType.php mais une vérification supplémentaire au niveau du contrôleur empêche toute tentative de contournement en manipulant l’extension d’un fichier malveillant)
                $allowedExtensions = ['jpg', 'jpeg', 'png']; // liste des extensions autorisées
                $extension = $tamponFile->guessExtension(); // Récupère l'extension réelle du fichier

                if (!in_array($extension, $allowedExtensions)) { // si l'extension ne coïncide pas avec la liste d'extensions autorisées
                    $this->addFlash('error', 'Format de fichier non autorisé.');
                    return $this->redirectToRoute('new_edit_representant'); // on est redirigé vers le formulaire
                }
                
                $originalFilename = pathinfo($tamponFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $tamponFile->guessExtension();

                try {
                    $tamponFile->move($tamponsDirectory, $newFilename);
                    
                } catch (FileException $e) {
                    $this->addFlash('error', "Erreur lors de l'upload du fichier");
                }

                $representant->setTamponFilename($newFilename);

            }

            $entityManager->persist($representant); // équivaut à la méthode prepare() en PDO

            $entityManager->flush(); // équivaut à la méthode execute() en PDOStatement

            // redirection après sauvegarde -> vers le formulaire de l'entreprise (rempli, si tout fonctionne !)
            return $this->redirectToRoute('new_edit_representant');
        }
        // fin du bloc

        // 3. on affiche le formulaire créé dans la page dédiée à cet affichage -> {{ form(formAddRepresentant) }} --> affichage par défaut 
        return $this->render('representant/new_edit.html.twig', [ // 'representant/new.html.twig' -> vue dédiée à l'affichage du formulaire : on crée un nouveau fichier dans le dossier representant
            // 'form' => $form,  on fait passer une variable form qui prend la valeur $form
            // on change le nom pour éviter toute ambiguité 'form' -> 'formAddRepresentant' comme expliqué dans new_edit.html.twig
            'formAddRepresentant' => $form,
            'edit' => $representant->getId() !== null, // comportement booléen -> si getId() retourne une valeur, on est en mode édition et si getId() est null, on est en mode création.
            'representant' => $representant // ?? null,  rajout suite à un message d'erreur où il prétend que la variable $representant n'existe pas
        ]);
    }


    // les fichiers stockés en dehors de 'public' ne sont pas directement accessibles par url. Pour les afficher, il faut créer une route spéciale qui lit les fichiers et les sert via Symfony.
    #[Route('/tampon/{filename}', name: 'tampon_display')]
    public function displayTampon(string $filename): Response
    {
        $tamponsDirectory = $this->getParameter('kernel.project_dir') . '/var/uploads/tampons/';
        $filePath = $tamponsDirectory . $filename;

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException("Le fichier n'existe pas.");
        }

        return new BinaryFileResponse($filePath);
    }
 
}


/*
Une fois cette route en place, pour afficher un tampon dans le template Twig, on peut faire :

<img src="{{ path('tampon_display', { filename: entreprise.tamponFilename }) }}" alt="tampon du représentant">

Ça génère une URL comme /tampon/mon-tampon.png, qui affichera correctement l’image !
*/


/*
if ($representant->getTampon()) {
    $oldFilePath = $this->getParameter('tampons_directory') . '/' . $representant->getTampon();
    if (file_exists($oldFilePath)) {
        unlink($oldFilePath);
    }
}

$representant->setTampon($newFilename);

*/
