<?php

namespace App\Repository;

use App\Entity\FacturaConsecutivo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FacturaConsecutivo|null find($id, $lockMode = null, $lockVersion = null)
 * @method FacturaConsecutivo|null findOneBy(array $criteria, array $orderBy = null)
 * @method FacturaConsecutivo[]    findAll()
 * @method FacturaConsecutivo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FacturaConsecutivoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FacturaConsecutivo::class);
    }

    // /**
    //  * @return FacturaConsecutivo[] Returns an array of FacturaConsecutivo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FacturaConsecutivo
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
