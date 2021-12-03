<?php

namespace App\Repository\Envio;

use App\Entity\Envio\EnvioTraza;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EnvioTraza|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnvioTraza|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnvioTraza[]    findAll()
 * @method EnvioTraza[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class EnvioTrazaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnvioTraza::class);
    }
}
