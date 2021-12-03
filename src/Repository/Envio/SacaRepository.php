<?php

namespace App\Repository\Envio;

use App\Entity\Envio\Saca;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Saca|null find($id, $lockMode = null, $lockVersion = null)
 * @method Saca|null findOneBy(array $criteria, array $orderBy = null)
 * @method Saca[]    findAll()
 * @method Saca[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class SacaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Saca::class);
    }

    public function findSacasNoFactura($noFactura, $estado = 'APP_SACA_ESTADO_CREADA')
    {
        return $this->createQueryBuilder('s')
            ->join('s.factura', 'f', Expr\Join::WITH, 'f.numero_factura='.$noFactura)
            ->join('s.estado', 'e', Expr\Join::WITH, "e.codigo='$estado'")
            ->getQuery()
            ->getResult();
    }

    public function findEnviosNoFacturaAndEstado($codTracking, $estado = 'APP_SACA_ESTADO_CREADA')
    {
        $saca = $this->createQueryBuilder('f')
            ->join('f.estado', 'e', Expr\Join::WITH, "e.codigo='$estado'")
            ->andWhere("f.codigo='$codTracking'")
            ->getQuery()
            ->getResult();

        return $saca ? $saca[0]->getEnvios()->toArray() : $saca;
    }

    public function getSacaByCodigo($codigo)
    {
        return $this->findOneBy(['codigo'=>$codigo]);
    }
}