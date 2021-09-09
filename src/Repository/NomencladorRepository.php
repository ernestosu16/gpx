<?php

namespace App\Repository;

use App\Entity\Nomenclador;

class NomencladorRepository extends _NestedTreeRepository_
{
    static function classEntity(): string
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

    public function findOneByCodigo(string $code): ?Nomenclador
    {
        return $this->findOneBy(['codigo' => $code]);
    }

    public function nuevo(string $codigo, string $nombre, string $descripcion, ?Nomenclador $parent = null): Nomenclador
    {
        $n = new Nomenclador();
        if ($parent)
            $n->setParent($parent);
        $n->setCodigo($codigo);
        $n->setNombre($nombre);
        $n->setDescripcion($descripcion);
        return $n;
    }


    public function findByChildren(string $codigo)
    {
        $nomenclador = $this->findOneByCodigo($codigo);
        return $nomenclador?->getChildren();
    }
}
