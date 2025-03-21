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

    SELECT societe.raison_sociale, SUM(inscription.prix) AS total_paye
    FROM societe
    JOIN apprenant ON societe.id = apprenant.societe_id
    JOIN inscription ON apprenant.id = inscription.apprenant_id
    WHERE inscription.session_id = :sessionId
    AND societe.id = :societeId
    GROUP BY societe.id;
    
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

    SELECT societe.raison_sociale, responsable.sexe, responsable.prenom, responsable.nom
    FROM societe
    JOIN responsabilite ON societe.id = responsabilite.societe_id
    JOIN responsable ON responsabilite.responsable_id = responsable.id
    WHERE societe.id = :societeId 
    AND responsabilite.type_responsable = "légal";
    
    */

}
