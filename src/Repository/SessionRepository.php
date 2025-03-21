<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    //    /**
    //     * @return Session[] Returns an array of Session objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Session
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


    // méthode pour récupérer la liste des apprenants de chaque société ayant participé à une session déterminée (nom de la société suivi de la liste des salariés qui ont participé)
    // utile dans la vue de détails d'une session dans le suivi administratif
    public function findSocietesEtApprenantsBySession($sessionId)
    {
        return $this->getEntityManager()->createQuery("
            SELECT s.id AS societeId, s.raisonSociale, a.nom, a.prenom, a.email, a.metier
            FROM App\Entity\Inscription i
            JOIN i.apprenant a
            JOIN a.societe s
            WHERE i.session = :sessionId
            ORDER BY s.raisonSociale, a.nom
        ")
        ->setParameter('sessionId', $sessionId)
        ->getResult();
    }


    /*

    requête SQL liée à function findSocietesEtApprenantsBySession($sessionId)

    SELECT societe.id AS societeId, societe.raison_sociale, apprenant.nom, apprenant.prenom
    FROM inscription                                                                            inscription fait le lien entre la session et l’apprenant
    JOIN apprenant ON inscription.apprenant_id = apprenant.id                                   jointure entre inscription et apprenant
    JOIN societe ON apprenant.societe_id = societe.id                                           jointure en cascade entre apprenant et societe
    WHERE inscription.session_id = :sessionId                                                   filtre sur la session concernée
    ORDER BY societe.raison_sociale, apprenant.nom;                                             trie par raison sociale puis par nom d’apprenant

    */

}
