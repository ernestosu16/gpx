<?php

namespace App\Repository;

use App\Entity\AnomaliaSaca;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AnomaliaSaca|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnomaliaSaca|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnomaliaSaca[]    findAll()
 * @method AnomaliaSaca[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnomaliaSacaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnomaliaSaca::class);
    }

    // /**
    //  * @return AnomaliaSaca[] Returns an array of AnomaliaSaca objects
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
    public function findOneBySomeField($value): ?AnomaliaSaca
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
