<?php

namespace App\Repository;

use App\Entity\EnvioAduanaTraza;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EnvioAduanaTraza|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnvioAduanaTraza|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnvioAduanaTraza[]    findAll()
 * @method EnvioAduanaTraza[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnvioAduanaTrazaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnvioAduanaTraza::class);
    }

    // /**
    //  * @return EnvioAduanaTraza[] Returns an array of EnvioAduanaTraza objects
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
    public function findOneBySomeField($value): ?EnvioAduanaTraza
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
