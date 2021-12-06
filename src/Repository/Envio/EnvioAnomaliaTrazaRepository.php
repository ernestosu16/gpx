<?php

namespace App\Repository\Envio;

use App\Entity\Envio\EnvioAnomaliaTraza;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EnvioAnomaliaTraza|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnvioAnomaliaTraza|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnvioAnomaliaTraza[]    findAll()
 * @method EnvioAnomaliaTraza[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class EnvioAnomaliaTrazaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnvioAnomaliaTraza::class);
    }
}
