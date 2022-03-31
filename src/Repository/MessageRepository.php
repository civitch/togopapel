<?php

namespace App\Repository;

use App\Entity\Annonce;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    // /**
    //  * @return Message[] Returns an array of Message objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Message
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    /**
     * Affiche la liste des message de l'utilisateur connecté en fonction de l'a
     *
     * @param Annonce $annonce
     * @param UserInterface $user
     * @return mixed
     */
    public function getMessagesByUser(UserInterface $user, Annonce $annonce)
    {
        $qb = $this->createQueryBuilder('m');
        return
            $qb
                ->innerJoin('m.annonce', 'a')
                ->addSelect('a')
                ->where(
                    $qb->expr()->orX(
                        $qb->expr()->eq('m.receiver', ':user'),
                        $qb->expr()->eq('m.sender', ':user')
                    )
                )
                ->andWhere('a.id = :id')
                ->setParameters(['user' => $user, 'id' => $annonce])
                ->getQuery()
                ->getResult()
        ;
    }

    /**
     * Retourne la liste des messages liée à une conversation
     *
     * @param UserInterface $user
     * @param Conversation $conversation
     * @return mixed
     */
    public function getMessageByConversation(UserInterface $user,  Conversation $conversation)
    {
        $qb = $this->createQueryBuilder('m');
        return
            $qb
                ->innerJoin('m.conversation', 'con')
                ->addSelect('con')
                ->where('m.conversation = :conv')
                ->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->eq('m.receiver', ':user'),
                        $qb->expr()->eq('m.sender', ':user')
                    )
                )
                ->setParameters(['user' => $user, 'conv' => $conversation])
                ->getQuery()
                ->getResult()
        ;
    }
}
