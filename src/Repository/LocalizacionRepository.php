<?php

namespace App\Repository;

use App\Entity\Localizacion;
use App\Entity\Nomenclador\LocalizacionTipo;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\QueryBuilder;

class LocalizacionRepository extends _NestedTreeRepository_
{
    protected static function classEntity(): string
    {
        return Localizacion::class;
    }

    public function findAllProvincia(): array
    {
        return $this->findByTipoCodigo(LocalizacionTipo::PROVINCIA);
    }

    public function findByTipoCodigo(string $tipo): array
    {
        $em = $this->getEntityManager();
        $tipo = $em->getRepository(LocalizacionTipo::class)->findOneByCodigo($tipo);
        return $this->findBy(['tipo' => $tipo]);
    }

    public function findByTipo(LocalizacionTipo $tipo): array
    {
        $tipo = $this->getEntityManager()->getRepository(LocalizacionTipo::class)->find($tipo);
        return $this->findBy(['tipo' => $tipo]);
    }

    public function findByTipoAndParent(LocalizacionTipo $tipo, ?Localizacion $parent): array
    {
        return $this->findBy(['parent' => $parent, 'tipo' => $tipo]);
    }

    /**
     * @throws ORMException
     */
    public function createQueryBuilderMunicipio(string $alias = 'l'): QueryBuilder
    {
        $tipo = $this->getEntityManager()
            ->getRepository(LocalizacionTipo::class)
            ->findOneByCodigoHabilitado(LocalizacionTipo::MUNICIPIO);

        if (!$tipo)
            throw new ORMException('Error no se encuentro el tipo "' . LocalizacionTipo::MUNICIPIO . '"');

        return $this->createQueryBuilder($alias)
            ->where($alias . '.tipo = :tipo')
            ->setParameter('tipo', $tipo);
    }

    /**
     * @param string $municipio
     * @param string $provincia
     * @return array
     */
    public function findMunicipioAndProvinciaByCodigo(string $municipio, string $provincia): array
    {
        $em = $this->getEntityManager();
        $tipo = $em->getRepository(LocalizacionTipo::class)->findOneByCodigo(LocalizacionTipo::PROVINCIA);
        $parent = $this->findOneBy(['tipo' => $tipo, 'codigo_aduana' => $provincia]);
        $children = null;
        if($parent){
            $children = $this->findOneBy(['parent' => $parent, 'codigo_aduana' => $municipio]);
        }
        return [$children,$parent];
    }

}
