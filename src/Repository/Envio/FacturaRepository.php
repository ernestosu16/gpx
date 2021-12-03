<?php

namespace App\Repository\Envio;

use App\Entity\Envio\Factura;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Factura|null find($id, $lockMode = null, $lockVersion = null)
 * @method Factura|null findOneBy(array $criteria, array $orderBy = null)
 * @method Factura[]    findAll()
 * @method Factura[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class FacturaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Factura::class);
    }

    public function getSacasNoFatura(string $noFactura)
    {
        $factura = $this->findOneBy(['numero_factura' => $noFactura]);

        return $factura->getSacas();
    }

    public function getFacturaByNoFactura(string $noFactura)
    {
        return $this->findOneBy(['numero_factura' => $noFactura]);
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

    public function findEnviosNoFacturaAndEstado($noFactura, $estado = 'APP_FACTURA_ESTADO_CREADA')
    {
        $factura = $this->createQueryBuilder('f')
            ->join('f.estado', 'e', Expr\Join::WITH, "e.codigo='$estado'")
            ->andWhere("f.numero_factura='$noFactura'")
            ->getQuery()
            ->getResult();

        return $factura ? $factura[0]->getEnvios()->toArray() : $factura;
    }
}
