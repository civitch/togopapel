<?php

namespace App\Repository;

use App\Entity\Annonce;
use App\Entity\Conversation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Conversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversation[]    findAll()
 * @method Conversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    // /**
    //  * @return Conversation[] Returns an array of Conversation objects
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
    public function findOneBySomeField($value): ?Conversation
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
     * Récupère la liste des conversations de l'utilisateur connecté
     *
     * @param UserInterface $user
     * @return mixed
     */
    public function getConversationByUser(UserInterface $user)
    {
        $qb = $this->createQueryBuilder('c');
        return
            $qb
                ->innerJoin('c.messages', 'm')
                ->addSelect('m')
                ->where(
                    $qb->expr()->orX(
                        $qb->expr()->eq('m.receiver', ':user'),
                        $qb->expr()->eq('m.sender', ':user')
                    )
                )
                ->setParameter('user', $user)
                ->getQuery()
                ->getResult()
            ;
    }


    /**
     * @param $userOne
     * @param $userTwo
     * @param Annonce $annonce
     * @return mixed
     */
    public function checkIfConversationExist(UserInterface $userOne, UserInterface $userTwo, Annonce $annonce)
    {
        //SELECT c.id FROM conversation AS c
        // INNER JOIN annonce AS a
        // INNER JOIN message AS m
        // ON c.annonce_id = a.id
        // AND m.conversation_id = c.id
        // WHERE a.id = 47
        // AND (m.sender_id = 7 OR m.receiver_id = 7)
        // AND (m.sender_id = 3 OR m.receiver_id = 3)

        $qb = $this->createQueryBuilder('c');
        return
            $qb
                ->innerJoin('c.annonce', 'ann')
                ->innerJoin('c.messages', 'mess')
                ->addSelect('ann')
                ->addSelect('mess')
                ->where('ann.id = :idAnn')
                ->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->eq('mess.sender', ':userOne'),
                        $qb->expr()->eq('mess.receiver', ':userOne')
                    )
                )
                ->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->eq('mess.sender', ':userTwo'),
                        $qb->expr()->eq('mess.receiver', ':userTwo')
                    )
                )
                ->setParameters(['idAnn' => $annonce->getId(), 'userOne' => $userOne, 'userTwo' => $userTwo])
                ->getQuery()
                ->getResult()
            ;
    }

     



}
