<?php

namespace App\Repository;

use App\Entity\StripeEventLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method StripeEventLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method StripeEventLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method StripeEventLog[]    findAll()
 * @method StripeEventLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StripeEventLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StripeEventLog::class);
    }

    // /**
    //  * @return StripeEventLog[] Returns an array of StripeEventLog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StripeEventLog
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
