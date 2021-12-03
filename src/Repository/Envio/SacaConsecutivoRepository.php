<?php

namespace App\Repository\Envio;

use App\Entity\Envio\SacaConsecutivo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SacaConsecutivo|null find($id, $lockMode = null, $lockVersion = null)
 * @method SacaConsecutivo|null findOneBy(array $criteria, array $orderBy = null)
 * @method SacaConsecutivo[]    findAll()
 * @method SacaConsecutivo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class SacaConsecutivoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SacaConsecutivo::class);
    }
}
