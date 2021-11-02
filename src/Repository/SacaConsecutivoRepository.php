<?php

namespace App\Repository;

use App\Entity\SacaConsecutivo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SacaConsecutivo|null find($id, $lockMode = null, $lockVersion = null)
 * @method SacaConsecutivo|null findOneBy(array $criteria, array $orderBy = null)
 * @method SacaConsecutivo[]    findAll()
 * @method SacaConsecutivo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SacaConsecutivoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SacaConsecutivo::class);
    }

    // /**
    //  * @return SacaConsecutivo[] Returns an array of SacaConsecutivo objects
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
    public function findOneBySomeField($value): ?SacaConsecutivo
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
