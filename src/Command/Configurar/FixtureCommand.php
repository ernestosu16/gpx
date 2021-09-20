<?php

namespace App\Command\Configurar;

use App\Command\BaseCommand;
use App\Command\BaseCommandInterface;
use App\Config\Data\Nomenclador\GrupoData;
use App\Entity\Grupo;
use App\Entity\Localizacion;
use App\Entity\LocalizacionTipo;
use App\Repository\LocalizacionRepository;
use App\Repository\LocalizacionTipoRepository;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

final class FixtureCommand extends BaseCommand implements BaseCommandInterface
{
    static function getCommandName(): string
    {
        return 'app:configurar:fixture';
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->configurarGrupos();

        $this->configurarLocalizacionTipos();

        $this->configurarLocalizaciones();

        return Command::SUCCESS;
    }

    private function configurarGrupos()
    {
        $em = $this->getEntityManager();

        $root = $em->getRepository(Grupo::class)->findOneByCodigo(GrupoData::code());

        /** @var array $localizacionTipo */
        $grupo = Yaml::parseFile($this->getKernel()->getProjectDir() . '/src/Config/Fixtures/grupo.yaml');

        foreach ($grupo['grupos'] as $grupo) {
            if ($em->getRepository(Grupo::class)->findOneByCodigo($grupo['codigo']))
                continue;

            $grupoEntity = new Grupo();
            $grupoEntity->setCodigo($grupo['codigo']);
            $grupoEntity->setNombre($grupo['nombre']);
            $grupoEntity->setDescripcion($grupo['descripcion']);

            $root->addChild($grupoEntity);
            $em->persist($root);
        }

        $em->flush();
    }

    private function configurarLocalizacionTipos()
    {
        /** @var array $localizacionTipo */
        $tipos = Yaml::parseFile($this->getKernel()->getProjectDir() . '/src/Config/Fixtures/localizacion_tipo.yaml');


        $em = $this->getEntityManager();
        foreach ($tipos['tipos'] as $tipo) {
            if ($em->getRepository(LocalizacionTipo::class)->findOneByCodigo($tipo['codigo']))
                continue;

            $tipoEntity = new LocalizacionTipo();

            if ($parent = $em->getRepository(LocalizacionTipo::class)->findOneByCodigo($tipo['parent']))
                $tipoEntity->setParent($parent);

            $tipoEntity->setNombre($tipo['nombre']);
            $tipoEntity->setDescripcion($tipo['descripcion']);
            $tipoEntity->setCodigo($tipo['codigo']);

            $em->persist($tipoEntity);
            $em->flush();

        }
    }

    private function configurarLocalizaciones()
    {
        $localizacion = Yaml::parseFile($this->getKernel()->getProjectDir() . '/src/Config/Fixtures/localizacion.yaml');

        $em = $this->getEntityManager();

        $em->beginTransaction();

        /** @var LocalizacionRepository $localizacionRepository */
        $localizacionRepository = $em->getRepository(Localizacion::class);

        /** @var LocalizacionTipoRepository $tipoRepository */
        $tipoRepository = $em->getRepository(LocalizacionTipo::class);
        foreach ($localizacion['provincias'] as $provincia) {
            if ($localizacionRepository->findOneByCodigo($provincia['codigo']))
                continue;

            $entity = new Localizacion();
            $entity->setNombre($provincia['nombre']);
            $entity->setDescripcion($provincia['descripcion']);
            $entity->setCodigo($provincia['codigo']);
            $entity->setTipo($tipoRepository->getTipoProvincia());

            $em->persist($entity);
            $em->flush();

            foreach ($localizacion['municipios'][$provincia['codigo']] as $municipio) {
                $provinciaEntity = $localizacionRepository->findOneByCodigo($provincia['codigo']);

                if (!$provinciaEntity)
                    throw new ORMException('No se encontró el código "' . $provincia['codigo'] . '"');

                if ($localizacionRepository->findOneByCodigo($municipio['codigo']))
                    continue;

                $entity = new Localizacion();
                $entity->setParent($provinciaEntity);
                $entity->setNombre($municipio['nombre']);
                $entity->setCodigo($municipio['codigo']);
                $entity->setTipo($tipoRepository->getTipoMunicipio());

                $em->persist($entity);
            }
            $em->flush();
        }

        $em->commit();
    }
}
