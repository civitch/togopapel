<?php

namespace App\Repository;

use App\Entity\Etiquette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Etiquette|null find($id, $lockMode = null, $lockVersion = null)
 * @method Etiquette|null findOneBy(array $criteria, array $orderBy = null)
 * @method Etiquette[]    findAll()
 * @method Etiquette[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtiquetteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etiquette::class);
    }

    // /**
    //  * @return Etiquette[] Returns an array of Etiquette objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Etiquette
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

}
