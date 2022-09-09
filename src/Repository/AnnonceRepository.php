<?php

namespace App\Repository;

use App\Entity\Annonce;
use App\Entity\AnnonceSearch;
use App\Entity\Picture;
use App\Entity\User;
use App\Services\App\AppSecurity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    private $paginator;

    private $appSecurity;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator, AppSecurity $appSecurity)
    {
        parent::__construct($registry, Annonce::class);
        $this->paginator = $paginator;
        $this->appSecurity = $appSecurity;
    }

    // /**
    //  * @return Annonce[] Returns an array of Annonce objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Annonce
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    /**
     * Récupère les annonces en fonction de l'utilisateur connecté
     *
     * @param UserInterface $user
     * @return mixed
     */
    public function getAnnoncesByOwner(UserInterface $user, $enabled = false)
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->where($qb->expr()->eq('a.user', ':user'))
            ->orderBy("a.createdAt", 'DESC')
            ->setParameter('user', $user)
        ;
        if($enabled){
            $qb
                ->andWhere('a.enabled = :status')
                ->setParameter('status', Annonce::STATUS['enabled'])
            ;
        }
        return $qb->getQuery()->getResult();
    }


    /**
     * Récupérer la liste des favoris pour l'utilisateur connecté
     *
     * @param $user UserInterface
     * @return mixed
     */
    public function getFavorisByUser(UserInterface $user)
    {
        $qb = $this->createQueryBuilder('a');
        return $qb
            ->innerJoin('a.favoris', 'fav')
            ->addSelect('fav')
            ->where('fav = :user')
            ->andWhere('a.enabled = :status')
            ->setParameters(['user' => $user, 'status' => Annonce::STATUS['enabled']])
            ->getQuery()->getResult()
        ;
    }


    /**
     * Retourne les annonces en fonction des données de recherche
     *
     * @param AnnonceSearch $search
     * @param int $page
     * @param int $limit
     * @return PaginationInterface
     */
    public function getSearchAnnnonce(AnnonceSearch $search, int $page, int $limit, $details = false): PaginationInterface
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->andWhere('a.enabled = :status')
            ->orderBy('a.createdAt', 'DESC')
            ->setParameter('status', Annonce::STATUS['enabled'])
        ;

        if($details)
        {
            if($search->isParticulier() && $search->isProfesional())
            {
                $qb
                    ->innerJoin('a.user', 'user')
                    ->innerJoin('user.department', 'dep')
                    ->addSelect('dep')
                    ->addSelect('user')
                    ->andWhere($qb->expr()->in('dep.role', [$this->appSecurity->getRole('particular'), $this->appSecurity->getRole('pro')]))
                ;
            }
            elseif($search->isParticulier())
            {
                $qb
                    ->innerJoin('a.user', 'user')
                    ->innerJoin('user.department', 'dep')
                    ->addSelect('dep')
                    ->addSelect('user')
                    ->andWhere('dep.role = :depart')
                    ->setParameter('depart', $this->appSecurity->getRole('particular'))
                ;
            }
            elseif($search->isProfesional())
            {
                $qb
                    ->innerJoin('a.user', 'user')
                    ->innerJoin('user.department', 'dep')
                    ->addSelect('dep')
                    ->addSelect('user')
                    ->andWhere('dep.role = :depro')
                    ->setParameter('depro', $this->appSecurity->getRole('pro'))
                ;
            }

            if($search->getTitle()){
                $qb
                    ->andWhere($qb->expr()->like('a.title', ':title'))
                    ->orWhere($qb->expr()->like('a.description', ':title'))
                    ->setParameter('title', '%'.$search->getTitle().'%')
                ;
            }

            if(empty($search->getVille()))
            {
                $qb
                    ->innerJoin('a.ville', 'vil')
                    ->addSelect('vil')
                ;
            }

            if($search->getVille())
            {
                $qb
                    ->innerJoin('a.ville', 'vil')
                    ->addSelect('vil')
                    ->andWhere('vil.id = :vile')
                    ->setParameter('vile', $search->getVille())
                ;
            }

            if(empty($search->getCategorie()))
            {
                $qb
                    ->innerJoin('a.categorie', 'cat')
                    ->addSelect('cat')
                ;
            }

            if($search->getCategorie()){
                $qb
                    ->innerJoin('a.categorie', 'cat')
                    ->addSelect('cat')
                    ->andWhere('cat.id = :cate')
                    ->setParameter('cate', $search->getCategorie())
                ;
            }

            if ($search->getPriceMax()) {
                $qb
                    ->andWhere('a.price <= :maxprice')
                    ->setParameter('maxprice', $search->getPriceMax());
            }

            if ($search->getPriceMin()) {
                $qb
                    ->andWhere('a.price >= :minprice')
                    ->setParameter('minprice', $search->getPriceMin());
            }

            if($search->getType()){
                $qb
                    ->andWhere('a.type = :type')
                    ->setParameter('type', true)
                ;
            }
            else{
                $qb
                    ->andWhere('a.type = :type')
                    ->setParameter('type', false)
                ;
            }
        }
        $annonces = $this->paginateItems($qb->getQuery(), $page, $limit);
        $this->hydratePicture($annonces);
        return $annonces;

    }

    /**
     * Récupère les annonces en fonction du slug d'une catégorie
     *
     * @param int $page
     * @param string $slug
     * @param int $limit
     * @return PaginationInterface
     */
    public function getAnnoncesByCatSlug(int $page, string $slug, int $limit): PaginationInterface
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->innerJoin('a.categorie', 'cate')
            ->addSelect('cate')
            ->where('cate.slug = :slugin')
            ->andWhere('a.enabled = :status')
            ->setParameters(['slugin' => $slug, 'status' => Annonce::STATUS['enabled']]);

        $annonces = $this->paginateItems($qb->getQuery(), $page, $limit);
        $this->hydratePicture($annonces);
        return $annonces;
    }


    /**
     * Récupère les annonces en fonction du slug de la ville
     *
     * @param int $page
     * @param string $slug
     * @param int $limit
     * @return PaginationInterface
     */
    public function getAnnoncesByVilleSlug(int $page, string $slug, int $limit): PaginationInterface
    {
        $qb = $this->createQueryBuilder('a');
        $qb
                ->innerJoin('a.ville', 'ville')
                ->addSelect('ville')
                ->where('ville.slug = :slugin')
                ->andWhere('a.enabled = :status')
                ->setParameters(['slugin' => $slug, 'status' => Annonce::STATUS['enabled']]);

        $annonces = $this->paginateItems($qb->getQuery(), $page, $limit);
        $this->hydratePicture($annonces);
        return $annonces;
    }


    /**
     * Retourne les annonces en fonction du slug de la Région
     *
     * @param int $page
     * @param string $slug
     * @return PaginationInterface
     */
    public function getAnnoncesBySlugRegion(int $page, string $slug, int $limit): PaginationInterface
    {
        //SELECT * FROM annonce AS a
        //INNER JOIN ville AS v
        //INNER JOIN region AS r
        //ON r.id = v.region_id AND v.id = a.ville_id
        //WHERE r.slug = "plateaux"
        $qb = $this->createQueryBuilder('a');
        $query = $qb
                    ->innerJoin('a.ville', 'ville')
                    ->innerJoin('ville.region', 'region')
                    ->addSelect('ville')
                    ->addSelect('region')
                    ->where('region.slug = :slugin')
                    ->andWhere('a.enabled = :status')
                    ->setParameters(['slugin' => $slug, 'status' => Annonce::STATUS['enabled']]);

        $annonces = $this->paginateItems($query->getQuery(), $page, $limit);
        $this->hydratePicture($annonces);
        return $annonces;
    }


    /**
     * Récupère les annonces en fonction du slug de la rubrique
     *
     * @param int $page
     * @param string $slug
     * @param int $limit
     * @return PaginationInterface
     */
    public function getAnnoncesBySlugRubrique(int $page, string $slug, int $limit): PaginationInterface
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->innerJoin('a.categorie', 'cat')
            ->innerJoin('cat.rubrique', 'rub')
            ->addSelect('cat')
            ->addSelect('rub')
            ->where('cat.slug = :slugin')
            ->andWhere('a.enabled = :status')
            ->setParameters(['slugin' => $slug, 'status' => Annonce::STATUS['enabled']]);
        $annonces = $this->paginateItems($qb->getQuery(), $page, $limit);
        $this->hydratePicture($annonces);
        return $annonces;
    }

    /**
     * Retourne les annonces activées par utilisateur
     * @param User $user
     * @return int|mixed|string
     */
    public function getAnnonceEnabledByUser(User $user)
    {
        $qb = $this->createQueryBuilder('a');
        return
            $qb
                ->innerJoin('a.user', 'user')
                ->addSelect('user')
                ->where('a.user = :infoUser')
                ->leftJoin('user.indicatifPays', 'indicatifPays')
                ->addSelect('indicatifPays')
                ->andWhere('a.enabled = :status')
                ->setParameters(['infoUser' => $user, 'status' => Annonce::STATUS['enabled']])
                ->getQuery()
                ->getResult()
            ;
    }

    /**
     * Retourne les annonces activées par utilisateur avec la pagination
     * @param User $user
     * @param int $page
     * @param int $limit
     * @return PaginationInterface
     */
    public function getAnnonceEnabledByUserPaginate(User $user, int $page, int $limit): PaginationInterface
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->innerJoin('a.user', 'user')
            ->addSelect('user')
            ->where('a.user = :infoUser')
            ->andWhere('a.enabled = :status')
            ->setParameters(['infoUser' => $user, 'status' => Annonce::STATUS['enabled']])
        ;
        $annonces = $this->paginateItems($qb->getQuery(), $page, $limit);
        $this->hydratePicture($annonces);
        return $annonces;
    }

    public function lastListAnnonces()
    {

        $qb = $this->createQueryBuilder('a');
        $annonces = $qb
                        ->leftJoin("a.categorie", 'cat')
                        ->addSelect('cat')
                        ->where('a.enabled = :status')
                        ->orderBy('a.createdAt', 'DESC')
                        ->setMaxResults(10)
                        ->setParameter('status', Annonce::STATUS['enabled'])
                        ->getQuery()
                        ->getResult()
        ;
        $this->hydratePicture($annonces);
        return $annonces;
    }

    public function relatedAnnoncesByCategorie(Annonce $annonce)
    {
        $qb = $this->createQueryBuilder('a');
        $annonces = $qb
            ->leftJoin("a.categorie", 'cat')
            ->addSelect('cat')
            ->where('a.enabled = :status')
            ->andWhere('cat.id = :id')
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults(3)
            ->setParameter('status', Annonce::STATUS['enabled'])
            ->setParameter('id', $annonce->getCategorie()->getId())
            ->getQuery()
            ->getResult()
        ;
        $this->hydratePicture($annonces);
        return $annonces;
    }


    private function hydratePicture($annonces){
        if( ! is_array($annonces) )
        {
            if(method_exists($annonces, 'getItems'))
            {
                $annonces = $annonces->getItems();
            }
        }
        $pictures = $this->getEntityManager()->getRepository(Picture::class)->findForProperties($annonces);
        foreach ($annonces as $annonce){
            /** @var Annonce $annonce */
            if ($pictures->containsKey($annonce->getId())){
                $annonce->setPicture($pictures->get($annonce->getId()));
            }
        }
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
