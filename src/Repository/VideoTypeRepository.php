<?php

namespace App\Repository;

use App\Entity\VideoType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VideoType|null find($id, $lockMode = null, $lockVersion = null)
 * @method VideoType|null findOneBy(array $criteria, array $orderBy = null)
 * @method VideoType[]    findAll()
 * @method VideoType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VideoType::class);
    }

    // /**
    //  * @return VideoType[] Returns an array of VideoType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VideoType
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
