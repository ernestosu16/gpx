<?php

namespace App\Command\Fixtures;

use App\Command\BaseCommand;
use App\Command\BaseCommandInterface;
use App\Config\Data\Nomenclador\AgenciaData;
use App\Config\Data\Nomenclador\EnvioData;
use App\Config\Data\Nomenclador\EstructuraTipoData;
use App\Config\Data\Nomenclador\FacturaData;
use App\Config\Data\Nomenclador\GrupoData;
use App\Config\Data\Nomenclador\MenuData;
use App\Config\Data\Nomenclador\SacaData;
use App\Entity\Estructura;
use App\Entity\Localizacion;
use App\Entity\Nomenclador;
use App\Entity\Nomenclador\Agencia;
use App\Entity\Nomenclador\EstructuraTipo;
use App\Entity\Nomenclador\Grupo;
use App\Entity\Nomenclador\LocalizacionTipo;
use App\Entity\Nomenclador\Menu;
use App\Entity\Pais;
use App\Repository\LocalizacionRepository;
use App\Repository\Nomenclador\LocalizacionTipoRepository;
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

        $section->writeln('  Creando los canales de la aduana');
        $this->configurarCanales();

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
        $estructura = new Estructura();
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

    private function setNomenclador($data, $path)
    {
        /** @var ?Menu $root */
        $root = $this->getRepository(Nomenclador::class)->findOneByCodigo($data);

        /* if ($root->getChildren()->count())
             return;*/

        $file = Yaml::parseFile($this->getKernel()->getProjectDir() . $path);
        foreach ($file[array_key_first($file)] as $nom) {
            if ($this->getRepository(Nomenclador::class)->findOneByCodigo(strtoupper($root->getCodigo() . '_' . $nom['codigo'])))
                continue;

            $nomEntity = new Nomenclador();
            $nomEntity->setRoot($root->getRoot());
            $nomEntity->setCodigo(strtoupper($root->getCodigo() . '_' . $nom['codigo']));
            $nomEntity->setNombre($nom['nombre']);

            foreach ($nom['children'] as $child) {
                if ($this->getRepository(Nomenclador::class)->findOneByCodigo(strtoupper($nomEntity->getCodigo() . '_' . $child['codigo'])))
                    continue;

                $nomChildEntity = new Nomenclador();
                $nomChildEntity->setRoot($root->getRoot());
                $nomChildEntity->setCodigo(strtoupper($nomEntity->getCodigo() . '_' . $child['codigo']));
                $nomChildEntity->setNombre($child['nombre']);
                $nomChildEntity->setDescripcion($child['descripcion']);
                $nomEntity->addChild($nomChildEntity);
            }

            $root->addChild($nomEntity);
        }
        $this->getEntityManager()->persist($root);
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

    private function configurarCanales()
    {
        /** @var ?Nomenclador $root */
        $root = $this->getRepository(Nomenclador::class)->findOneBy(['codigo' => 'APP_ENVIO']);

        /** @var ?Nomenclador $root_canal */
        $canales = Yaml::parseFile($this->getKernel()->getProjectDir() . '/src/Config/Fixtures/nomenclador/canal.yaml')['canales'][0];

        if ($this->getRepository(Nomenclador::class)->findOneBy(['codigo' => $canales['codigo']]))
            return;

        $roort_canal = new Nomenclador();
        $roort_canal->setRoot($root->getRoot());
        $roort_canal->setCodigo($canales['codigo']);
        $roort_canal->setNombre($canales['nombre']);
        $roort_canal->setDescripcion($canales['descripcion']);

        $root->addChild($roort_canal);
        $this->getEntityManager()->persist($root);

        foreach ($canales['children'] as $canal) {
            if ($this->getRepository(Nomenclador::class)->findOneBy(['codigo' => $canal['codigo']]))
                continue;

            $canalEntity = new Nomenclador();
            $canalEntity->setRoot($root->getRoot());
            $canalEntity->setCodigo($canal['codigo']);
            $canalEntity->setNombre($canal['nombre']);
            $canalEntity->setDescripcion($canal['descripcion']);

            $roort_canal->addChild($canalEntity);

            $this->getEntityManager()->persist($canalEntity);
            $this->getEntityManager()->persist($roort_canal);
        }
        $this->getEntityManager()->persist($root);
        $this->getEntityManager()->flush();
    }

    private function configurarNomenclador()
    {
        $this->setNomenclador('APP_' . FacturaData::code(), '/src/Config/Fixtures/nomenclador/factura.yaml');

        $this->setNomenclador('APP_' . SacaData::code(), '/src/Config/Fixtures/nomenclador/saca.yaml');

        $this->setNomenclador('APP_' . EnvioData::code(), '/src/Config/Fixtures/nomenclador/envio.yaml');

    }
}
