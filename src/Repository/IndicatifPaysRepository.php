<?php

namespace App\Repository;

use App\Entity\IndicatifPays;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method IndicatifPays|null find($id, $lockMode = null, $lockVersion = null)
 * @method IndicatifPays|null findOneBy(array $criteria, array $orderBy = null)
 * @method IndicatifPays[]    findAll()
 * @method IndicatifPays[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IndicatifPaysRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IndicatifPays::class);
    }

    // /**
    //  * @return IndicatifPays[] Returns an array of IndicatifPays objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IndicatifPays
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
