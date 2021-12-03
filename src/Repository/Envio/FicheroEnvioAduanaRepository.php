<?php

namespace App\Repository\Envio;

use App\Entity\Envio\FicheroEnvioAduana;
use App\Repository\_Repository_;

/**
 * @method FicheroEnvioAduana|null find($id, $lockMode = null, $lockVersion = null)
 * @method FicheroEnvioAduana|null findOneBy(array $criteria, array $orderBy = null)
 * @method FicheroEnvioAduana[]    findAll()
 * @method FicheroEnvioAduana[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class FicheroEnvioAduanaRepository extends _Repository_
{
    protected static function classEntity(): string
    {
        return FicheroEnvioAduana::class;
    }
}