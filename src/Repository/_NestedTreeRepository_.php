<?php

namespace App\Repository;

use App\Entity\Nomenclador;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

abstract class _NestedTreeRepository_ extends NestedTreeRepository
{
    protected abstract static function classEntity(): string;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(static::classEntity()));
    }

    public function findOneByCodigo(string $code): ?Nomenclador
    {
        return $this->findOneBy(['codigo' => $code]);
    }
}
