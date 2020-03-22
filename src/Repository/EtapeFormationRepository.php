<?php

namespace App\Repository;

use App\Entity\EtapeFormation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method EtapeFormation|null find($id, $lockMode = null, $lockVersion = null)
 * @method EtapeFormation|null findOneBy(array $criteria, array $orderBy = null)
 * @method EtapeFormation[]    findAll()
 * @method EtapeFormation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtapeFormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EtapeFormation::class);
    }

    // /**
    //  * @return EtapeFormation[] Returns an array of EtapeFormation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EtapeFormation
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
