<?php

namespace App\Repository;

use App\Entity\Societe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Societe>
 */
class SocieteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Societe::class);
    }

    //    /**
    //     * @return Societe[] Returns an array of Societe objects
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

    //    public function findOneBySomeField($value): ?Societe
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }



    // méthode pour récupérer le prix total payé par une société donnée pour une session déterminée (prix global que paie une société pour ses salariés inscrits)
    // utile dans le document convention mais aussi dans la vue de détails d'une session dans le suivi administratif
    public function findPrixSociete($session_id, $societe_id)
    {

        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery("
            SELECT s.raisonSociale, SUM(i.prix) AS totalPaye
            FROM App\Entity\Societe s
            JOIN s.apprenants a
            JOIN a.inscriptions i
            WHERE i.session = :sessionId
            AND s.id = :societeId
            GROUP BY s.id
        ");
        $query->setParameter('sessionId', $session_id);
        $query->setParameter('societeId', $societe_id);

        /* return $query->getResult();   -> [
            ["raisonSociale" => "Nom de la société", "totalPaye" => 5000]
        ]*/
        return $query->getOneOrNullResult();  // -> ["raisonSociale" => "Nom de la société", "totalPaye" => 5000]

    }

    /* 

    requête SQL liée à function findPrixSociete($session_id, $societe_id)

    SELECT s.raison_sociale, SUM(i.prix) AS total_paye
    FROM societe s
    JOIN apprenant a ON s.id = a.societe_id
    JOIN inscription i ON a.id = i.apprenant_id
    WHERE i.session_id = :sessionId
    AND s.id = :societeId
    GROUP BY s.id;
    
    */



    // méthode pour récupérer le prix total payé par une société donnée pour une session déterminée (prix global que paie une société pour ses salariés inscrits)
    // utile dans le document convention mais aussi dans la vue de détails d'une session dans le suivi administratif
    public function findUniqueRespLegal($societe_id)
    {

        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery("
            SELECT s.raisonSociale, r.sexe, r.prenom, r.nom
            FROM App\Entity\Societe s
            JOIN s.responsabilites resp
            JOIN resp.responsable r
            WHERE s.id = :societeId
            AND resp.type_responsable = :typeResponsable
        ");
        $query->setParameter('societeId', $societe_id);
        $query->setParameter('typeResponsable', 'légal');

        /* return $query->getResult();   -> [
            ["raisonSociale" => "Nom de la société", "sexe" => "M", "prenom" => "prénom du resp", "nom" => "nom du resp"]
        ]*/
        return $query->getOneOrNullResult();  // -> ["raisonSociale" => "Nom de la société", "sexe" => "M", "prenom" => "prénom du resp", "nom" => "nom du resp"]

    }

    /* 

    requête SQL liée à function findRespLegal($societe_id)
    testée dans HeidiSQL => impeccable

    SELECT s.raison_sociale, r.sexe, r.prenom, r.nom
    FROM societe s
    JOIN responsabilite resp ON s.id = resp.societe_id
    JOIN responsable r ON resp.responsable_id = r.id
    WHERE s.id = :societeId 
    AND resp.type_responsable = "légal";
    
    */

}
