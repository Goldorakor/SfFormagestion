<?php

// src/Command/CreateUserCommand.php
namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserCommand extends Command
{
    private $entityManager;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure()
    {
        $this->setName('app:create-user')
            ->setDescription('Crée un utilisateur avec un mot de passe haché et tous les attributs');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Créer l'utilisateur
        $user = new User();
        
        // Définir les attributs de l'utilisateur
        $user->setEmail('heidmichael@gmail.com');
        $user->setNom('heid');
        $user->setPrenom('michael');
        $user->setPassword('12345678'); // Mot de passe en clair, il sera haché automatiquement

        // Utilisation du password hasher pour hacher le mot de passe
        $encodedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($encodedPassword); // Hachage du mot de passe

        // Ajouter les rôles (exemple : ROLE_USER)
        $user->setRoles(['ROLE_USER']);
        
        // Définir l'attribut isApproved si nécessaire
        $user->setIsApproved(true);  // ou false selon ton besoin

        // Persister l'utilisateur dans la base de données
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('Utilisateur créé avec succès !');

        return Command::SUCCESS;
    }
}




/*
Pour la création d'une commande Symfony pour ajouter un utilisateur :
Si on veut créer un utilisateur via la ligne de commande (par exemple, pour un utilisateur administrateur à la création du projet), on peut créer une commande Symfony dédiée.

dans l'invite de commandes : php bin/console app:create-user

On se sert de ce profil pour vérifier le bon fonctionnement de notre système de login
*/
