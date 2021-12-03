<?php

namespace App\Repository\Envio;

use App\Entity\Envio\SacaTraza;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SacaTraza|null find($id, $lockMode = null, $lockVersion = null)
 * @method SacaTraza|null findOneBy(array $criteria, array $orderBy = null)
 * @method SacaTraza[]    findAll()
 * @method SacaTraza[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class SacaTrazaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SacaTraza::class);
    }
}
