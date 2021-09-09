<?php

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

abstract class _NestedTreeRepository_ extends NestedTreeRepository
{
    abstract static function classEntity(): string;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(static::classEntity()));
    }
}
