<?php

namespace App\Repository;

use App\Entity\LocalizacionTipo;

class LocalizacionTipoRepository extends _NestedTreeRepository_
{
    protected static function classEntity(): string
    {
        return LocalizacionTipo::class;
    }

    /**
     * @return LocalizacionTipo[]
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.parent is not null')->getQuery()->getResult();
    }

    public function getTipoProvincia(): ?LocalizacionTipo
    {
        return $this->findOneByCodigo(LocalizacionTipo::PROVINCIA);
    }

    public function getTipoMunicipio(): ?LocalizacionTipo
    {
        return $this->findOneByCodigo(LocalizacionTipo::MUNICIPIO);
    }
}
