<?php

namespace App\Repository;

use App\Entity\AnomaliaEnvio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AnomaliaEnvio|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnomaliaEnvio|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnomaliaEnvio[]    findAll()
 * @method AnomaliaEnvio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnomaliaEnvioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnomaliaEnvio::class);
    }

    // /**
    //  * @return AnomaliaEnvio[] Returns an array of AnomaliaEnvio objects
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
    public function findOneBySomeField($value): ?AnomaliaEnvio
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
