<?php

namespace App\Repository;

use App\Entity\Nomenclador;
use Doctrine\Instantiator\Exception\InvalidArgumentException;

/**
 * @method Nomenclador|null find($id, $lockMode = null, $lockVersion = null)
 * @method Nomenclador|null findOneBy(array $criteria, array $orderBy = null)
 * @method Nomenclador[]    findAll()
 * @method Nomenclador[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NomencladorRepository extends _NestedTreeRepository_
{
    protected static function classEntity(): string
    {
        return Nomenclador::class;
    }

    public function findOneByCodigoAndCodigoPadre(string $codigo, string $parentCode): ?Nomenclador
    {
        /** @var ?Nomenclador $entity */
        $entity = $this->findOneByCodigo($codigo);

        if (!$entity) return null;

        $parent = $entity->getParent();
        if (!$parent) return null;

        if ($parent->getCodigo() !== $parentCode) return null;

        return $entity;
    }

    public static function newInstance(string $codigo, string $nombre, string $descripcion, ?Nomenclador $parent = null): Nomenclador
    {
        $class = static::classEntity();
        $object = new $class();

        if (!$object instanceof Nomenclador)
            throw new InvalidArgumentException('El objeto instanciado no es de clase "Nomenclador"');

        if ($parent)
            $object->setParent($parent);

        $object->setCodigo($codigo);
        $object->setNombre($nombre);
        $object->setDescripcion($descripcion);
        $object->setDescripcion($descripcion);

        return $object;
    }


    public function findByChildren(string $codigo)
    {
        $nomenclador = $this->findOneByCodigo($codigo);
        return $nomenclador?->getChildren();
    }
}
