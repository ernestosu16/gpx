<?php

namespace App\Repository;

use App\Entity\Pais;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pais|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pais|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pais[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PaisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pais::class);
    }

    public function findAll(): array
    {
        return $this->findBy([], ['nombre' => 'ASC']);
    }

    public function getPaisCuba(): ?Pais
    {
        return $this->findOneByCodigoAduana(Pais::PRINCIPAL);
    }

    public function findOneByIata(string $iata): ?Pais
    {
        return $this->findOneBy(['iata' => $iata]);
    }

    public function findOneByCodigoAduana(string $codigo): ?Pais
    {
        return $this->findOneBy(['codigo_aduana' => $codigo]);
    }
}
