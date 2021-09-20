<?php

namespace App\Repository;

use App\Entity\LocalizacionTipo;

class LocalizacionTipoRepository extends _NestedTreeRepository_
{
    protected static function classEntity(): string
    {
        return LocalizacionTipo::class;
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
