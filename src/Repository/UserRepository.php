<?php

namespace App\Repository;

use App\Entity\User;
use App\Services\App\AppSecurity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    private $role;

    public function __construct(ManagerRegistry $registry, AppSecurity $appSecurity)
    {
        parent::__construct($registry, User::class);
        $this->role = $appSecurity;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return User[] Returns an array of User objects
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
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * Vérifie un compte lors de la confirmation d'un compte ou la réinitialisation d'un mot de passe
     * @param int $id
     * @param string $token
     * @param bool $reset
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function findByIdAndToken(int $id, string $token, $reset = false): ?User
    {
        $qb = $this->createQueryBuilder('u');
        $qb
            ->innerJoin('u.department', 'd')
            ->addSelect('d')
            ->where(
                $qb->expr()->in('d.role', [$this->role->getRole('pro'), $this->role->getRole('particular')])
            )
            ->andWhere('u.id = :id');
        if ($reset) {
            $qb->andWhere($qb->expr()->andX(
                $qb->expr()->isNotNull('u.resetAt'),
                $qb->expr()->eq('u.resetToken', ':token')
            ));
        } else {
            $qb->andWhere('u.confirmationToken = :token');
        }
        $qb->setParameters(['id' => $id, 'token' => $token]);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Vérifie la validité d'un compte (email et confirmation du compte) afin de réinitialiser le mot de passe
     * @param string $email
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function forgetPassword(string $email): ?User
    {
        $qb = $this->createQueryBuilder('u');
        $qb->where($qb->expr()->andX(
            $qb->expr()->eq('u.email', ':email'),
            $qb->expr()->isNotNull('u.confirmationAt'))
        );
        $qb->setParameter('email' , $email);
        return $qb->getQuery()->getOneOrNullResult();
    }


    /**
     * Vérifie si le compte a le rôle administrateur ou modérateur
     *
     * @param string $confirm_token
     * @return User|string
     */
    public function checkUserAdmin(string $confirm_token): ?User
    {
        $qb = $this->createQueryBuilder('u');
        $qb
            ->innerJoin('u.department', 'd')
            ->addSelect('d')
            ->where(
                    $qb->expr()->in(
                        'd.role',
                        [$this->role->getRole('admin'), $this->role->getRole('moderateur')]
                    )
                )
            ->andWhere(
                $qb->expr()->andX(
                    $qb->expr()->eq('u.confirmationToken', ':confirToken'),
                    $qb->expr()->isNull('u.confirmationAt'),
                    $qb->expr()->isNotNull('u.createdAt')
                ))
            ->andWhere('u.enabled = :enable')
            ->setParameters(['confirToken' => $confirm_token, 'enable' => false])
        ;
        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return $e->getMessage();
        }
    }



    /**
     * Vérifie si l'utilisateur a la possibilité de réinitialiser son mot de passe en vérifiant certains critères
     * @param string $id
     * @param string $token
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function checkIfUserCanResetPassword(string $id, string $token) :?User
    {
        $qb = $this->createQueryBuilder('u');
        $qb->innerJoin('u.department', 'd')
            ->addSelect('d')
        ;
        return $qb
            ->where(
                $qb->expr()->in('d.role', [$this->role->getRole('pro'),$this->role->getRole('particular')])
            )
            ->andWhere($qb->expr()->andX(
                $qb->expr()->isNotNull('u.confirmationAt'),
                $qb->expr()->isNotNull('u.resetToken'),
                $qb->expr()->isNotNull('u.resetAt'),
                $qb->expr()->eq('u.id', ':id'),
                $qb->expr()->eq('u.resetToken', ':token'))
            )
            ->setParameters(['id' => $id, 'token' => $token])
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Vérifie si le compte d'un utilisateur est actif
     * @param string $email
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function findEnableUser(string $email) : ?User
    {
        $qb =  $this->createQueryBuilder('u');
        return $qb
            ->where('u.email = :email')
            ->andWhere(
                    $qb->expr()->isNotNull('u.confirmationAt')
                )
            ->andWhere('u.enabled = :info')
            ->setParameters(['email' => $email, 'info' => true])
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }


    /**
     * @param string $currentEmail
     * @param string $email
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function findUserEditProfile(string $currentEmail, string $email) : ?User
    {
        $qb =  $this->createQueryBuilder('u');
        return $qb
            ->where('u.email != :currentEmail')
            ->andWhere('u.email = :email')
            ->setParameters(['currentEmail' => $currentEmail, 'email' => $email])
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }



    /**
     * @return mixed
     * Retourne la liste des administrateurs du site
     */
    public function listeAdmins($users = false)
    {
        $qb = $this->createQueryBuilder('u');
        $qb
            ->innerJoin('u.department', 'dep')
            ->addSelect('dep');
            if(!$users){
                $qb->where(
                    $qb->expr()->in('dep.role', [$this->role->getRole('admin'), $this->role->getRole('moderateur')])
                );
            }else{
                $qb->where(
                    $qb->expr()->in('dep.role', [$this->role->getRole('particular'), $this->role->getRole('pro')])
                );
            }
        return
            $qb
                ->getQuery()
                ->getResult()
        ;
    }


    /**
     * @return mixed
     */
    public function getWalletNotNull()
    {
        $qb = $this->createQueryBuilder('u');
        return $qb
            ->innerJoin('u.department', 'dep')
            ->addSelect('dep')
            ->where($qb->expr()->isNotNull('u.wallet'))
            ->where(
                $qb->expr()->in('dep.role', [$this->role->getRole('pro'), $this->role->getRole('particular')])
            )
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param UserInterface $user
     * @return mixed|string
     */
    public function getUserEntity(UserInterface $user)
    {
        $qb = $this->createQueryBuilder('u');

        try {
            return $qb
                ->where('u.id = :user')
                ->setParameter('user', $user->getId())
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            return $e->getMessage();
        } catch (NonUniqueResultException $e) {
            return $e->getMessage();
        }

    }


    /**
     * Récupère la liste de tous les utilisateurs ayant le rôle admin du projet
     *
     * @return mixed
     */
    public function listeMainAdmins()
    {
        $qb = $this->createQueryBuilder('u');
        $qb
            ->innerJoin('u.department', 'dep')
            ->addSelect('dep')
            ->where($qb->expr()->in(
                'dep.role',
                [
                    $this->role->getRole('admin'),
                    $this->role->getRole('super_admin'),
                    $this->role->getRole('moderateur')
                ]
            ))
            ->andWhere('u.enabled = :info')
            ->setParameter('info', true);
        return $qb->getQuery()->getResult();
    }



}
