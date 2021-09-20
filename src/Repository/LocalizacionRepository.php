<?php

namespace App\Repository;

use App\Entity\Localizacion;
use App\Entity\LocalizacionTipo;

class LocalizacionRepository extends _NestedTreeRepository_
{
    protected static function classEntity(): string
    {
        return Localizacion::class;
    }

    public function findAllProvincia(): array
    {
        return $this->findByTipo(LocalizacionTipo::PROVINCIA);
    }

    public function findByTipo(string $tipo): array
    {
        $em = $this->getEntityManager();
        $tipo = $em->getRepository(LocalizacionTipo::class)->findOneByCodigo($tipo);
        return $this->findBy(['tipo' => $tipo]);
    }

    public function findByTipoAndParent(LocalizacionTipo $tipo, ?Localizacion $parent)
    {
        $em = $this->getEntityManager();
        return $this->findBy(['parent' => $parent, 'tipo' => $tipo]);
    }
}
