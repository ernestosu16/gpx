<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

abstract class _Repository_ extends ServiceEntityRepository
{
    abstract protected static function classEntity(): string;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, static::classEntity());
    }
}
