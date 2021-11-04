<?php

namespace App\Repository;

use App\Entity\Envio;
use App\Entity\Nomenclador;
use App\Entity\Trabajador;
use App\Entity\TrabajadorCredencial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Envio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Envio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Envio[]    findAll()
 * @method Envio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnvioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Envio::class);
    }

    // /**
    //  * @return Envio[] Returns an array of Envio objects
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
    public function findOneBySomeField($value): ?Envio
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findByEnvioToCodTrackingCalendarYear($codTracking)
    {
        $exp = new Expr();
        $fechaActual = new \DateTime();

        return $this->createQueryBuilder('envio')
            ->andWhere('envio.cod_tracking = :codTracking')
            ->andWhere($exp->andX($exp->gte('envio.fecha_recepcion',':fecha_init'),$exp->lte('envio.fecha_recepcion',':fecha_end')))
            ->setParameter('codTracking', $codTracking)
            ->setParameter('fecha_init', $fechaActual->format('Y-01-01 00:00:00'))
            ->setParameter('fecha_end', $fechaActual->format('Y-12-31 23:59:59'))
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function buscarEnvioParaEntregaPorCI($destinatario_id,Trabajador $userAutenticado)
    {
        $em = $this->getEntityManager();
        $estadoRecepcionado = $em->getRepository(Nomenclador::class)->findOneByCodigo('APP_ENVIO_ESTADO_RECEPCIONADO');

        return $this->createQueryBuilder('envio')
            ->andWhere('envio.destinatario_id = :destinatario')
            ->andWhere('envio.estructura_destino_id = :estructura_destino')
            ->andWhere('envio.estado_id = :estado')

            ->join('envio.envio_aduana', 'envio_aduana', Expr\Join::WITH, 'envio_aduana.id=')
            ->andWhere('envio_aduana.datos_despacho != :datos_despacho')


            ->setParameter('destinatario', $destinatario_id)
            ->setParameter('estructura_destino', $userAutenticado->getEstructura())
            ->setParameter('estado', $estadoRecepcionado->getId() )

            ->setParameter('datos_despacho', [] )



            ->getQuery()
            ->getOneOrNullResult();
    }



}
