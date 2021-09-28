<?php

namespace App\Command\Configurar;

use App\Command\BaseCommand;
use App\Command\BaseCommandInterface;
use App\Config\Data\Nomenclador\EstructuraTipoData;
use App\Config\Data\Nomenclador\GrupoData;
use App\Config\Data\Nomenclador\MenuData;
use App\Entity\Estructura;
use App\Entity\EstructuraTipo;
use App\Entity\Grupo;
use App\Entity\Localizacion;
use App\Entity\LocalizacionTipo;
use App\Entity\Menu;
use App\Repository\LocalizacionRepository;
use App\Repository\LocalizacionTipoRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

final class FixturesCommand extends BaseCommand implements BaseCommandInterface
{
    static function getCommandName(): string
    {
        return 'app:configurar:fixtures';
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var ConsoleSectionOutput $section */
        $section = $output->section();

        $section->writeln('* Iniciando el comando de creación de datos por defecto');

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

            $grupoEntity = new Grupo();
            $grupoEntity->setCodigo($grupo['codigo']);
            $grupoEntity->setNombre($grupo['nombre']);
            $grupoEntity->setDescripcion($grupo['descripcion']);

            if (isset($grupo['roles']) && is_array($grupo['roles']))
                $grupoEntity->setRoles($grupo['roles']);

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
                $menuChildEntity = new Menu();
                $menuChildEntity->setRoot($root);
                $menuChildEntity->setCodigo($child['codigo']);
                $menuChildEntity->setNombre($child['nombre']);
                $menuChildEntity->setRoute($child['route']);
                $menuChildEntity->setIcon($child['icon']);
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
}
