<?php

namespace App\Repository;

use App\Entity\Option;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Option|null find($id, $lockMode = null, $lockVersion = null)
 * @method Option|null findOneBy(array $criteria, array $orderBy = null)
 * @method Option[]    findAll()
 * @method Option[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Option::class);
    }

    // /**
    //  * @return Option[] Returns an array of Option objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Option
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    /**
     * Récupère les options de la page d'accueil
     * @return int|mixed|string
     * @throws NonUniqueResultException
     */
    public function getOptionHome($main = false)
    {
        $qb = $this->createQueryBuilder('o');
        if($main)
        {
            return
                $qb
                    ->where('o.label = :main')
                    ->setParameter('main', Option::LABEL['mainhome'])
                    ->getQuery()
                    ->getOneOrNullResult()
            ;
        }
        else{
            return
                $qb
                    ->where($qb->expr()->in('o.label', [Option::LABEL['pays'], Option::LABEL['social'], Option::LABEL['app']]))
                    ->getQuery()
                    ->getResult()
                ;
        }
    }

}
