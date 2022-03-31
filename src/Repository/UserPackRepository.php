<?php

namespace App\Repository;

use App\Entity\UserPack;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method UserPack|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserPack|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserPack[]    findAll()
 * @method UserPack[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserPackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPack::class);
    }

    // /**
    //  * @return UserPack[] Returns an array of UserPack objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserPack
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


}
