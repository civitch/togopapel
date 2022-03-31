<?php

namespace App\Repository;

use App\Entity\DemandeCredit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DemandeCredit|null find($id, $lockMode = null, $lockVersion = null)
 * @method DemandeCredit|null findOneBy(array $criteria, array $orderBy = null)
 * @method DemandeCredit[]    findAll()
 * @method DemandeCredit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandeCreditRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeCredit::class);
    }

    // /**
    //  * @return DemandeCredit[] Returns an array of DemandeCredit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DemandeCredit
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
