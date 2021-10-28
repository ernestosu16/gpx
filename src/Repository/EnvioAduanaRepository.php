<?php

namespace App\Repository;

use App\Entity\EnvioAduana;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EnvioAduana|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnvioAduana|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnvioAduana[]    findAll()
 * @method EnvioAduana[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnvioAduanaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnvioAduana::class);
    }

    // /**
    //  * @return EnvioAduana[] Returns an array of EnvioAduana objects
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
    public function findOneBySomeField($value): ?EnvioAduana
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
