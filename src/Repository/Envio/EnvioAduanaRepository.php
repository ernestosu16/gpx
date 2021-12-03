<?php

namespace App\Repository\Envio;

use App\Entity\Envio\EnvioAduana;
use App\Entity\Estructura;
use App\Entity\Nomenclador;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EnvioAduana|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnvioAduana|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnvioAduana[]    findAll()
 * @method EnvioAduana[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class EnvioAduanaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnvioAduana::class);
    }

    /**
     * @return EnvioAduana[] Returns an array of EnvioAduana objects
     */
    public function findEnvioAduanaByEstructuraAndEstado(Estructura $structura, Nomenclador $estado)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.envio', 'env')
            ->andWhere('e.estado = :val')
            ->andWhere('env.empresa = :val1')
            ->setParameter('val', $estado)
            ->setParameter('val1', $structura)
            ->getQuery()
            ->getResult();
    }
}
