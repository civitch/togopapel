<?php

namespace App\Repository;

use App\Entity\NewsLetterTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NewsLetterTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method NewsLetterTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method NewsLetterTemplate[]    findAll()
 * @method NewsLetterTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsLetterTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsLetterTemplate::class);
    }

    // /**
    //  * @return NewsLetterTemplate[] Returns an array of NewsLetterTemplate objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NewsLetterTemplate
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
