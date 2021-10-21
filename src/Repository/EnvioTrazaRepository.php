<?php

namespace App\Repository;

use App\Entity\EnvioTraza;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EnvioTraza|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnvioTraza|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnvioTraza[]    findAll()
 * @method EnvioTraza[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnvioTrazaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnvioTraza::class);
    }

    // /**
    //  * @return EnvioTraza[] Returns an array of EnvioTraza objects
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
    public function findOneBySomeField($value): ?EnvioTraza
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
