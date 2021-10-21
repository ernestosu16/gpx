<?php

namespace App\Repository;

use App\Entity\EnvioAnomaliaTraza;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EnvioAnomaliaTraza|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnvioAnomaliaTraza|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnvioAnomaliaTraza[]    findAll()
 * @method EnvioAnomaliaTraza[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnvioAnomaliaTrazaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnvioAnomaliaTraza::class);
    }

    // /**
    //  * @return EnvioAnomaliaTraza[] Returns an array of EnvioAnomaliaTraza objects
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
    public function findOneBySomeField($value): ?EnvioAnomaliaTraza
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
