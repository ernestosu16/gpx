<?php

namespace App\Repository\Nomenclador;


use App\Entity\Nomenclador\LocalizacionTipo;
use App\Repository\_NestedTreeRepository_;

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
