<?php

namespace App\Repository;

use App\Entity\EnvioAduana;
use App\Entity\Estructura;
use App\Entity\Nomenclador;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
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
//    public function findByEstado(Nomenclador $estado){
//
//    }

    /**
      * @return EnvioAduana[] Returns an array of EnvioAduana objects
      */
    public function findEnvioAduanaByEstructuraAndEstado(Estructura $structura, Nomenclador $estado)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.envio','env')
            ->andWhere('e.estado = :val')
            ->andWhere('env.empresa = :val1')
            ->setParameter('val', $estado)
            ->setParameter('val1', $structura)
            ->getQuery()
            ->getResult()
        ;
    }
}
