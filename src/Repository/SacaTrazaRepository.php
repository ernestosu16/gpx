<?php

namespace App\Repository;

use App\Entity\SacaTraza;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SacaTraza|null find($id, $lockMode = null, $lockVersion = null)
 * @method SacaTraza|null findOneBy(array $criteria, array $orderBy = null)
 * @method SacaTraza[]    findAll()
 * @method SacaTraza[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SacaTrazaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SacaTraza::class);
    }

    // /**
    //  * @return SacaTraza[] Returns an array of SacaTraza objects
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
    public function findOneBySomeField($value): ?SacaTraza
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
