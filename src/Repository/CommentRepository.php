<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findCommentsListing(int $first = 0, bool $onlyParents = true, int $limit = 5): QueryBuilder
    {
        $query = $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC')
            ->setFirstResult($first)
        ;

            if (isset($limit) && 0 !== $limit) {
                $query->setMaxResults($limit);
            }

        if (true === $onlyParents) {
            $query->where('c.parent is null');
            }

        return $query;
    }

    public function trickFilter(QueryBuilder $query, int $trickId, int $parentId = null): QueryBuilder
    {
        $query->join('c.trick', 't')
            ->andWhere('t.id = :trickId')
            ->setParameter('trickId', $trickId)
        ;

        if (isset($parentId)) {
            $query->join('c.parent', 'p')
                ->andWhere('p.id = :parentId')
                ->setParameter('parentId', $parentId)
            ;

        }

        return $query;
    }

    public function userFilter(QueryBuilder $query, int $userId): QueryBuilder
    {
        $query->join('c.user', 'u')
            ->andWhere('u.id = '. $userId)
        ;

        return $query;
    }

    public function paginate(QueryBuilder $query): Paginator
    {
        return new Paginator($query, true);
    }

    // /**
    //  * @return Comment[] Returns an array of Comment objects
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
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
