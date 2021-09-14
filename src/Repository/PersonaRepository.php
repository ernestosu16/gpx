<?php

namespace App\Repository;

use App\Entity\Persona;

/**
 * @method Persona|null find($id, $lockMode = null, $lockVersion = null)
 * @method Persona|null findOneBy(array $criteria, array $orderBy = null)
 * @method Persona[]    findAll()
 * @method Persona[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PersonaRepository extends _Repository_
{
    protected static function classEntity(): string
    {
        return Persona::class;
    }

    public function findOneByNumeroIdentidad(string $numeroIdentidad): ?Persona
    {
        return $this->findOneBy(['numero_identidad' => $numeroIdentidad]);
    }
}
