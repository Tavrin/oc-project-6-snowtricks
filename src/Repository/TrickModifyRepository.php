<?php

namespace App\Repository;

use App\Entity\TrickModify;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrickModify|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrickModify|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrickModify[]    findAll()
 * @method TrickModify[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickModifyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrickModify::class);
    }

    // /**
    //  * @return TrickModify[] Returns an array of TrickModify objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TrickModify
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
