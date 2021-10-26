<?php

namespace App\Repository;

use App\Entity\EnvioManifiesto;

/**
 * @method EnvioManifiesto|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnvioManifiesto|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnvioManifiesto[]    findAll()
 * @method EnvioManifiesto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class EnvioManifiestoRepository extends _Repository_
{
    protected static function classEntity(): string
    {
        return EnvioManifiesto::class;
    }

    public function findByGuiaAndCodigo(string $numeroGuia, string $codigo): ?EnvioManifiesto
    {
        return $this->findOneBy(['no_guia_aerea' => $numeroGuia, 'codigo' => $codigo, 'recepcionado'=>false]);
    }
}
