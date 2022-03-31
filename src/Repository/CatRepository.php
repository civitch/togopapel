<?php

namespace App\Repository;

use App\Entity\Cat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cat[]    findAll()
 * @method Cat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cat::class);
    }

    // /**
    //  * @return Cat[] Returns an array of Cat objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Cat
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    /**
     * @param string $faq
     * @return int|mixed|string|null
     */
    public function getCatFaq(string $faq)
    {
        $qb = $this->createQueryBuilder('c');
        try {
            return
                $qb
                    ->innerJoin('c.etiquettes', 'et')
                    ->innerJoin('et.postypes', 'post')
                    ->addSelect('et')
                    ->addSelect('post')
                    ->where('c.title = :title')
                    ->orderBy('et.id','ASC')
                    ->setParameter('title', $faq)
                    ->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return $e->getMessage();
        }
    }
}
