<?php

namespace App\Repository\Envio;

use App\Entity\Envio\FacturaConsecutivo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FacturaConsecutivo|null find($id, $lockMode = null, $lockVersion = null)
 * @method FacturaConsecutivo|null findOneBy(array $criteria, array $orderBy = null)
 * @method FacturaConsecutivo[]    findAll()
 * @method FacturaConsecutivo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class FacturaConsecutivoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FacturaConsecutivo::class);
    }
}
