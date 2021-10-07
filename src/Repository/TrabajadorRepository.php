<?php

namespace App\Repository;

use App\Entity\Estructura;
use App\Entity\Trabajador;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMInvalidArgumentException;

final class TrabajadorRepository extends _Repository_
{
    protected static function classEntity(): string
    {
        return Trabajador::class;
    }

    /**
     * @param Estructura[] $estructuras
     * @param Trabajador[] $excluir
     */
    public function findByEstructuras(array $estructuras, array $excluir = []): array
    {
        if (count(array_filter($estructuras, function ($entry) {
                return !$entry instanceof Estructura;
            })) > 0) {
            throw new ORMInvalidArgumentException('La variable $estructuras debe contener un array de "' . Estructura::class . '"');
        }

        if (count(array_filter($excluir, function ($entry) {
                return !$entry instanceof Trabajador;
            })) > 0) {
            throw new ORMInvalidArgumentException('La variable $excluir debe contener un array de"' . Trabajador::class . '"');
        }

        $collection = new ArrayCollection($this->findBy(['estructura' => $estructuras]));

        # Excluyendo trabajadores de la lista
        if (!empty($excluir)) {
            $collection = $collection->filter(function (Trabajador $trabajador) use ($excluir) {
                return (!in_array($trabajador, $excluir));
            });
        }

        return $collection->toArray();
    }


    /**
     * @throws NonUniqueResultException
     */
    public function findOneByNumeroIdentidad(?string $numeroIdentidad): ?Trabajador
    {
        return $this->createQueryBuilder('trabajador')
            ->join('trabajador.persona', 'persona')
            ->where('persona.numero_identidad = :numeroIdentidad')
            ->setParameter('numeroIdentidad', $numeroIdentidad)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
