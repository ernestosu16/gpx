<?php

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

abstract class _NestedTreeRepository_ extends NestedTreeRepository
{
    protected abstract static function classEntity(): string;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(static::classEntity()));
    }

    public function findOneByCodigo(?string $code): ?object
    {
        return $code ? $this->findOneBy(['codigo' => $code]) : null;
    }

    public function findOneByCodigoHabilitado(string $code): ?object
    {
        return $this->findOneBy(['codigo' => $code, 'habilitado' => true]);
    }
}
