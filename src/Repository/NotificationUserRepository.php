<?php

namespace App\Repository;

use App\Entity\NotificationUser;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method NotificationUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotificationUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotificationUser[]    findAll()
 * @method NotificationUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationUserRepository extends ServiceEntityRepository
{
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, NotificationUser::class);
        $this->paginator = $paginator;
    }

    // /**
    //  * @return NotificationUser[] Returns an array of NotificationUser objects
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
    public function findOneBySomeField($value): ?NotificationUser
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    /**
     * Liste des notifications par utilisateur connecté sur le header
     *
     * @param UserInterface $user
     * @return int|mixed|string
     */
    public function getNotificationByUSer(UserInterface $user)
    {
        return
            $this->createQueryBuilder('n')
            ->innerJoin('n.user', 'user')
            ->innerJoin('n.notification', 'notif')
            ->addSelect('user')
            ->addSelect('notif')
            ->where('n.user = :info')
            ->orderBy('n.createdAt', 'DESC')
            ->setParameter('info', $user)
            ->setFirstResult(0)
            ->setMaxResults(4)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Nombre de notifications
     *
     * @param UserInterface $user
     * @return int|mixed|string
     */
    public function getCountNotification(UserInterface $user)
    {
        $qb = $this->createQueryBuilder('n');
        return
            $qb
                ->innerJoin('n.user', 'user')
                ->innerJoin('n.notification', 'notif')
                ->addSelect('user')
                ->addSelect('notif')
                ->where('n.user = :info')
                ->andWhere($qb->expr()->isNull('n.readAt'))
                ->setParameter('info', $user)
                ->getQuery()
                ->getResult()
            ;
    }

    /**
     * Liste de toutes les notifications
     *
     * @param UserInterface $user
     * @param int $page
     * @param int $limit
     * @return int|mixed|string
     */
    public function getAll(UserInterface $user, int $page, int $limit): PaginationInterface
    {
        $qb = $this->createQueryBuilder('n');
        $qb
            ->innerJoin('n.user', 'user')
            ->innerJoin('n.notification', 'notif')
            ->addSelect('user')
            ->addSelect('notif')
            ->where('n.user = :info')
            ->setParameter('info', $user)
        ;
        return $this->paginateItems($qb->getQuery(), $page, $limit);
    }


    /**
     * @param Query $query
     * @param int $page
     * @param int $limit
     * @return PaginationInterface
     */
    private function paginateItems(Query $query, int $page, int $limit): PaginationInterface
    {
        return $this->paginator->paginate($query, $page, $limit);
    }
}
