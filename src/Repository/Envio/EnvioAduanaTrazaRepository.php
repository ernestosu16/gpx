<?php

namespace App\Repository\Envio;

use App\Entity\Envio\EnvioAduanaTraza;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EnvioAduanaTraza|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnvioAduanaTraza|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnvioAduanaTraza[]    findAll()
 * @method EnvioAduanaTraza[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class EnvioAduanaTrazaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnvioAduanaTraza::class);
    }
}
