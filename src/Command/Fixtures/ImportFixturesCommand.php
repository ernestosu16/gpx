<?php

namespace App\Command\Fixtures;

use App\Command\BaseCommand;
use App\Command\BaseCommandInterface;
use App\Config\Data\Nomenclador\AgenciaData;
use App\Config\Data\Nomenclador\EnvioData;
use App\Config\Data\Nomenclador\FacturaData;
use App\Config\Data\Nomenclador\SacaData;
use App\Config\Data\Nomenclador\EstructuraTipoData;
use App\Config\Data\Nomenclador\GrupoData;
use App\Config\Data\Nomenclador\MenuData;
use App\Entity\Agencia;
use App\Entity\Estructura;
use App\Entity\EstructuraTipo;
use App\Entity\Grupo;
use App\Entity\Localizacion;
use App\Entity\LocalizacionTipo;
use App\Entity\Menu;
use App\Entity\Nomenclador;
use App\Entity\Pais;
use App\Repository\LocalizacionRepository;
use App\Repository\LocalizacionTipoRepository;
use App\Repository\NomencladorRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use function Symfony\Component\String\u;

final class ImportFixturesCommand extends BaseCommand implements BaseCommandInterface
{
    static function getCommandName(): string
    {
        return 'app:fixtures:import';
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var ConsoleSectionOutput $section */
        $section = $output->section();

        $section->writeln('* Iniciando el comando de creación de datos por defecto');

        $section->writeln('  Creando los países');
        $this->configurarPaises();

        $this->getEntityManager()->beginTransaction();
        $section->writeln('  Creando la lista de grupos');
        $this->configurarGrupos();

        $section->writeln('  Creando la lista de tipos de localizaciones');
        $this->configurarLocalizacionTipos();

        $section->writeln('  Creando la lista de localizaciones');
        $this->configurarLocalizaciones();

        $section->writeln('  Creando la lista de menu por defecto');
        $this->configurarMenu();

        $section->writeln('  Creando los tipos de estructuras');
        $this->configurarEstructuraTipo();


        $section->writeln('  Creando las estructuras');
        $this->configurarEstructura();

        $section->writeln('  Creando lista de nomencladores');
        $this->configurarNomenclador();

        $section->writeln('  Creando las agencias');
        $this->configurarAgencias();

        $this->getEntityManager()->commit();

        return Command::SUCCESS;
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    private function configurarGrupos()
    {
        $root = $this->getRepository(Grupo::class)->findOneByCodigo(GrupoData::code());

        if (!$root)
            throw new InvalidOptionException(sprintf('Error %s: No existe.', GrupoData::code()));

        /** @var array $localizacionTipo */
        $grupo = Yaml::parseFile($this->getKernel()->getProjectDir() . '/src/Config/Fixtures/grupo.yaml');

        foreach ($grupo['grupos'] as $grupo) {
            if ($this->getRepository(Grupo::class)->findOneByCodigo($grupo['codigo']))
                continue;

            /** @var Grupo $grupoEntity */
            $grupoEntity = $this->setter(new Grupo(), $grupo);

            $root->addChild($grupoEntity);
            $this->getEntityManager()->persist($root);
        }

        $this->getEntityManager()->flush();
    }

    private function configurarLocalizacionTipos()
    {
        /** @var array $localizacionTipo */
        $tipos = Yaml::parseFile($this->getKernel()->getProjectDir() . '/src/Config/Fixtures/localizacion_tipo.yaml');

        foreach ($tipos['tipos'] as $tipo) {
            if ($this->getRepository(LocalizacionTipo::class)->findOneByCodigo($tipo['codigo']))
                continue;

            $tipoEntity = new LocalizacionTipo();

            if ($parent = $this->getRepository(LocalizacionTipo::class)->findOneByCodigo($tipo['parent']))
                $tipoEntity->setParent($parent);

            $tipoEntity->setNombre($tipo['nombre']);
            $tipoEntity->setDescripcion($tipo['descripcion']);
            $tipoEntity->setCodigo($tipo['codigo']);

            $this->getEntityManager()->persist($tipoEntity);
            $this->getEntityManager()->flush();

        }
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    private function configurarLocalizaciones()
    {
        $localizacion = Yaml::parseFile($this->getKernel()->getProjectDir() . '/src/Config/Fixtures/localizacion.yaml');

        /** @var LocalizacionRepository $localizacionRepository */
        $localizacionRepository = $this->getRepository(Localizacion::class);

        /** @var LocalizacionTipoRepository $tipoRepository */
        $tipoRepository = $this->getRepository(LocalizacionTipo::class);
        foreach ($localizacion['provincias'] as $provincia) {
            if ($localizacionRepository->findOneByCodigo($provincia['codigo']))
                continue;

            $entity = new Localizacion();
            $entity->setNombre($provincia['nombre']);
            $entity->setDescripcion($provincia['descripcion']);
            $entity->setCodigo($provincia['codigo']);
            $entity->setCodigoAduana($provincia['codigo_aduana']);
            $entity->setTipo($tipoRepository->getTipoProvincia());

            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush();

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
                $entity->setCodigoAduana($municipio['codigo_aduana']);
                $entity->setTipo($tipoRepository->getTipoMunicipio());

                $this->getEntityManager()->persist($entity);
            }
            $this->getEntityManager()->flush();
        }
    }

    private function configurarMenu()
    {
        /** @var ?Menu $root */
        $root = $this->getRepository(Menu::class)->findOneByCodigo(MenuData::code());

        if ($root->getChildren()->count())
            return;

        $menu = Yaml::parseFile($this->getKernel()->getProjectDir() . '/src/Config/Fixtures/menu.yaml');
        foreach ($menu['menu'] as $menu) {
            if ($this->getRepository(Menu::class)->findOneByCodigo($menu['codigo']))
                continue;

            $menuEntity = new Menu();
            $menuEntity->setRoot($root);
            $menuEntity->setCodigo($menu['codigo']);
            $menuEntity->setNombre($menu['nombre']);
            $menuEntity->setRoute($menu['route']);
            $menuEntity->setIcon($menu['icon']);

            foreach ($menu['children'] as $child) {
                if ($this->getRepository(Menu::class)->findOneByCodigo($child['codigo']))
                    continue;

                /** @var Menu $menuChildEntity */
                $menuChildEntity = $this->setter(new Menu(), $child);
                $menuChildEntity->setRoot($root);
                $menuEntity->addChild($menuChildEntity);
            }

            $root->addChild($menuEntity);
        }
        $this->getEntityManager()->persist($root);
        $this->getEntityManager()->flush();
    }

    private function configurarEstructuraTipo()
    {
        /** @var array $localizacionTipo */
        $tipos = Yaml::parseFile($this->getKernel()->getProjectDir() . '/src/Config/Fixtures/estructura_tipo.yaml');

        /** @var ?EstructuraTipo $root */
        $root = $this->getRepository(EstructuraTipo::class)->findOneByCodigo(EstructuraTipoData::code());

        foreach ($tipos['tipos'] as $tipo) {
            if ($this->getRepository(EstructuraTipo::class)->findOneByCodigo($tipo['codigo']))
                continue;

            $root = $this->procesarEstructuraTipo($root, $tipo);
        }
        $this->getEntityManager()->persist($root);
        $this->getEntityManager()->flush();
    }

    private function procesarEstructuraTipo(?EstructuraTipo $estructuraTipo, array $tipo): ?EstructuraTipo
    {
        $tipoEntity = new EstructuraTipo();
        $tipoEntity->setNombre($tipo['nombre']);
        $tipoEntity->setDescripcion($tipo['descripcion']);
        $tipoEntity->setCodigo($tipo['codigo']);

        if (isset($tipo['children'])) {
            foreach ($tipo['children'] as $child) {
                $tipoEntity = $this->procesarEstructuraTipo($tipoEntity, $child);
            }
        } else {
            $tipoEntity->setEnd(true);
        }
        $estructuraTipo->addChild($tipoEntity);

        return $estructuraTipo;
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    private function configurarEstructura()
    {
        /** @var array $localizacionTipo */
        $estructuras = Yaml::parseFile($this->getKernel()->getProjectDir() . '/src/Config/Fixtures/estructura.yaml');

        foreach ($estructuras['estructuras'] as $estructura) {
            if ($this->getRepository(Estructura::class)->findOneByCodigo($estructura['codigo']))
                continue;

            $this->getEntityManager()->persist($this->procesarEstructura($estructura));
        }
        $this->getEntityManager()->flush();
    }

    private function procesarEstructura(array $datos): ?Estructura
    {
        $estructura = $root ?? new Estructura();
        $estructura->setNombre($datos['nombre']);
        $estructura->setCodigoPostal($datos['codigo_postal']);
        $estructura->setDescripcion($datos['descripcion']);
        $estructura->setCodigo($datos['codigo']);

        /** @var EstructuraTipo $tipo */
        $tipo = $this->getRepository(EstructuraTipo::class)->findOneByCodigo($datos['tipo']);
        $estructura->addTipo($tipo);

        /** @var Localizacion $municipio */
        $municipio = $this->getRepository(Localizacion::class)->findOneByCodigo($datos['municipio']);

        $estructura->setMunicipio($municipio);

        if (isset($datos['children'])) {
            foreach ($datos['children'] as $child) {
                $child = $this->procesarEstructura($child);
                $estructura->addChild($child);
            }
        }

        return $estructura;
    }

    private function configurarPaises()
    {
        /** @var array $collection */
        $collection = Yaml::parseFile($this->getKernel()->getProjectDir() . '/src/Config/Fixtures/pais.yaml');

        foreach ($collection['pais'] as $pais) {
            if ($this->getRepository(Pais::class)->findOneBy(['nombre' => $pais['nombre']]))
                continue;

            $entity = $this->setter(new Pais(), $pais);
            $this->getEntityManager()->persist($entity);
        }
    }

    private function setter(object $entity, $row): object
    {
        foreach ($row as $key => $value) {
            $set = u($key)
                ->replace('_', ' ')
                ->title(true)
                ->replace(' ', '')
                ->prepend('set')
                ->toString();

            call_user_func([$entity, $set], $value);
        }

        return $entity;
    }

    private function configurarNomenclador()
    {
        $this->envio();
        $this->factura();
        $this->saca();
    }

    private function envio()
    {
        /** @var array $collection */
        $collection = Yaml::parseFile($this->getKernel()->getProjectDir() . '/src/Config/Fixtures/nomenclador/envio.yaml');

        $instance = EnvioData::newInstance();
        /** @var NomencladorRepository $repository */
        $repository = $this->getRepository(Nomenclador::class);
        $parent = $repository->findOneByCodigo($instance->getCodeComplete());
        foreach ($collection['envio'] as $key => $item) {
            $codigo = $parent->getCodigo() . '_' . $key;

            if ($repository->findOneByCodigo($codigo))
                continue;

            $entity = new Nomenclador();
            $entity->setCodigo($parent->getCodigo() . '_' . $key);
            $entity->setNombre($key);

            foreach ($item as $value) {
                $lv2 = new Nomenclador();
                $lv2->setCodigo($entity->getCodigo() . '_' . $value);
                $lv2->setNombre($value);
                $entity->addChild($lv2);
            }

            $parent->addChild($entity);
            $this->getEntityManager()->persist($parent);
        }
        $this->getEntityManager()->flush();
    }

    private function factura()
    {
        /** @var array $collection */
        $collection = Yaml::parseFile($this->getKernel()->getProjectDir() . '/src/Config/Fixtures/nomenclador/factura.yaml');

        $instance = FacturaData::newInstance();
        /** @var NomencladorRepository $repository */
        $repository = $this->getRepository(Nomenclador::class);
        $parent = $repository->findOneByCodigo($instance->getCodeComplete());
        foreach ($collection['factura'] as $key => $item) {
            $codigo = $parent->getCodigo() . '_' . $key;

            if ($repository->findOneByCodigo($codigo))
                continue;

            $entity = new Nomenclador();
            $entity->setCodigo($parent->getCodigo() . '_' . $key);
            $entity->setNombre($key);
            $entity->setRoot($parent->getRoot());

            foreach ($item as $value) {
                $lv2 = new Nomenclador();
                $lv2->setCodigo($entity->getCodigo() . '_' . $value);
                $lv2->setNombre($value);
                $lv2->setRoot($parent->getRoot());
                $entity->addChild($lv2);
            }

            $parent->addChild($entity);
            $this->getEntityManager()->persist($parent);
        }
        $this->getEntityManager()->flush();
    }

    private function saca()
    {
        /** @var array $collection */
        $collection = Yaml::parseFile($this->getKernel()->getProjectDir() . '/src/Config/Fixtures/nomenclador/saca.yaml');

        $instance = SacaData::newInstance();
        /** @var NomencladorRepository $repository */
        $repository = $this->getRepository(Nomenclador::class);
        $parent = $repository->findOneByCodigo($instance->getCodeComplete());
        foreach ($collection['saca'] as $key => $item) {
            $codigo = $parent->getCodigo() . '_' . $key;

            if ($repository->findOneByCodigo($codigo))
                continue;

            $entity = new Nomenclador();
            $entity->setCodigo($parent->getCodigo() . '_' . $key);
            $entity->setNombre($key);
            $entity->setRoot($parent->getRoot());

            foreach ($item as $value) {
                $lv2 = new Nomenclador();
                $lv2->setCodigo($entity->getCodigo() . '_' . $value);
                $lv2->setNombre($value);
                $lv2->setRoot($parent->getRoot());
                $entity->addChild($lv2);
            }

            $parent->addChild($entity);
            $this->getEntityManager()->persist($parent);
        }
        $this->getEntityManager()->flush();
    }

    private function configurarAgencias()
    {
        /** @var ?Agencia $root */
        $root = $this->getRepository(Agencia::class)->findOneBy(['codigo' => AgenciaData::code()]);;

        if ($root->getChildren()->count())
            return;

        $agencia = Yaml::parseFile($this->getKernel()->getProjectDir() . '/src/Config/Fixtures/agencia.yaml');
        foreach ($agencia['agencias'] as $agencia) {
            if ($this->getRepository(Agencia::class)->findOneBy(['codigo' => $agencia['codigo']]))
                continue;

            $agenciaEntity = new Agencia();
            $agenciaEntity->setRoot($root);
            $agenciaEntity->setCodigo($agencia['codigo']);
            $agenciaEntity->setNombre($agencia['nombre']);
            $agenciaEntity->setDescripcion($agencia['descripcion']);

            $root->addChild($agenciaEntity);
        }
        $this->getEntityManager()->persist($root);
        $this->getEntityManager()->flush();
    }
}
