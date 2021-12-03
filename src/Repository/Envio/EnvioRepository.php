<?php

namespace App\Repository\Envio;

use App\Entity\Envio\Envio;
use App\Entity\Envio\EnvioAduana;
use App\Entity\Nomenclador;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Envio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Envio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Envio[]    findAll()
 * @method Envio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class EnvioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Envio::class);
    }

    public function findByEnvioToCodTrackingCalendarYear($codTracking)
    {
        $exp = new Expr();
        $fechaActual = new \DateTime();

        return $this->createQueryBuilder('envio')
            ->andWhere('envio.cod_tracking = :codTracking')
            ->andWhere($exp->andX($exp->gte('envio.fecha_recepcion', ':fecha_init'), $exp->lte('envio.fecha_recepcion', ':fecha_end')))
            ->setParameter('codTracking', $codTracking)
            ->setParameter('fecha_init', $fechaActual->format('Y-01-01 00:00:00'))
            ->setParameter('fecha_end', $fechaActual->format('Y-12-31 23:59:59'))
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function buscarEnvioParaEntregaPorCI($destinatario_id, $userAutenticado)
    {
        $em = $this->getEntityManager();
        $estadoRecepcionado = $em->getRepository(Nomenclador::class)->findOneByCodigo('APP_ENVIO_ESTADO_RECEPCIONADO');

        return $this->createQueryBuilder('envio')
            ->addSelect('envio_aduana.datos_despacho')
            ->andWhere('envio.destinatario = :destinatario')
            ->andWhere('envio.estructura_destino = :estructura_destino')
            ->andWhere('envio.estado = :estado')
            ->innerJoin(EnvioAduana::class, 'envio_aduana', Expr\Join::WITH, 'envio.id = envio_aduana.envio')
            ->andWhere('envio_aduana.datos_despacho IS NULL')
            ->setParameter('destinatario', $destinatario_id)
            ->setParameter('estructura_destino', $userAutenticado->getEstructura())
            ->setParameter('estado', $estadoRecepcionado->getId())
            ->getQuery()
            ->getArrayResult();
    }


}
