<?php

namespace App\Repository;

use App\Entity\AnomaliaFactura;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AnomaliaFactura|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnomaliaFactura|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnomaliaFactura[]    findAll()
 * @method AnomaliaFactura[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnomaliaFacturaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnomaliaFactura::class);
    }

    // /**
    //  * @return AnomaliaFactura[] Returns an array of AnomaliaFactura objects
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
    public function findOneBySomeField($value): ?AnomaliaFactura
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
