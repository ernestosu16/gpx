<?php

namespace App\Repository;

use App\Entity\Factura;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method Factura|null find($id, $lockMode = null, $lockVersion = null)
 * @method Factura|null findOneBy(array $criteria, array $orderBy = null)
 * @method Factura[]    findAll()
 * @method Factura[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FacturaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Factura::class);
    }


    public function getSacasNoFatura(string $noFactura)
    {
        $factura = $this->findOneBy(['numero_factura'=>$noFactura]);

        return $factura->getSacas();
    }

    public function getFacturaByNoFactura(string $noFactura)
    {
        return $this->findOneBy(['numero_factura'=>$noFactura]);
    }

    public function findSacasNoFacturaAndEstado($noFactura, $estado = 'APP_FACTURA_ESTADO_CREADA')
    {
         $factura = $this->createQueryBuilder('f')
            ->join('f.estado', 'e', Expr\Join::WITH, "e.codigo='$estado'")
            ->andWhere("f.numero_factura='$noFactura'")
            ->getQuery()
            ->getResult();

        return $factura ? $factura[0]->getSacas()->toArray() : $factura;
    }
    // /**
    //  * @return Factura[] Returns an array of Factura objects
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
    public function findOneBySomeField($value): ?Factura
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
