<?php

namespace App\Command\Configurar;

use App\Command\BaseCommand;
use App\Command\BaseCommandInterface;
use App\Config\nomenclador\NomencladorInterface;
use App\Entity\Nomenclador;
use App\Repository\NomencladorRepository;
use App\Util\ClassFinderUtil;
use App\Utils\ClassFinder;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ObjectRepository;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;

final class NomencladorCommand extends BaseCommand implements BaseCommandInterface
{

    private ObjectRepository|EntityRepository|NomencladorRepository $repository;
    private array $codigo = [];
    private array $codigo_parent = [];

    static function getCommandName(): string
    {
        return 'app:configurar:nomenclador';
    }

    /**
     * @return NomencladorInterface[]
     * @throws ReflectionException
     */
    private static function nomencladores(): array
    {
        $nomenclador = [];

        $MyClassesNamespace = ClassFinderUtil::getClassesInNamespace('App\\Config\\nomenclador');
        foreach ($MyClassesNamespace as $item) {
            $classReflection = new ReflectionClass($item);
            if ($classReflection->isAbstract()) continue;
            /** @var NomencladorInterface $className */
            $className = $classReflection->getName();
            $nomenclador[] = $className::INFO();
        }

        $MyNamespace = [
            'FormaEntrega',
            'FormaPago',
            'TipoJuridico',
            'TipoMoneda',
        ];

        foreach ($MyNamespace as $namespace) {
            $MyClassesNamespace = ClassFinderUtil::getClassesInNamespace('App\\Config\\nomenclador\\' . $namespace);
            foreach ($MyClassesNamespace as $item) {
                $classReflection = new ReflectionClass($item);
                if ($classReflection->isAbstract()) continue;
                /** @var NomencladorInterface $className */
                $className = $classReflection->getName();
                $nomenclador[] = $className::INFO();
            }
        }

        return $nomenclador;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ORMException
     * @throws ReflectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->repository = $this->getRepository(Nomenclador::class);

        /** @var ConsoleSectionOutput $section1 */
        $section1 = $output->section();
        $section1->writeln('Creando la lista de nomencladores por defecto');

        foreach (self::nomencladores() as $nomenclador) {
            $nomencladorEntity = $this->generarNomencladorEntity($nomenclador);
            if ($nomencladorEntity) {
                $section1->writeln(sprintf(
                    '* Creando nomenclador - ["codigo" => "%s", "nombre" => "%s"]',
                    $nomencladorEntity->getCodigo(), $nomencladorEntity->getNombre()
                ));
                $this->getEntityManager()->persist($nomencladorEntity);
                $this->getEntityManager()->flush();
            }
        }
        $section1->writeln('OK');
        return Command::SUCCESS;
    }


    private function generarNomencladorEntity(NomencladorInterface $nomenclador): ?Nomenclador
    {
        $this->codigo = [];
        $this->codigo_parent = [];
        $this->generarNomenclador($nomenclador);

        # Quitando el ultimo registro para que quede el codigo padre
        $lastKeyCodigo = array_key_last($this->codigo);
        if ($lastKeyCodigo != 0) {
            $codigoParent = $this->codigo;
            unset($codigoParent[$lastKeyCodigo]);
            $this->codigo_parent = $codigoParent;
        }

        $codigo = implode('_', $this->codigo);
        $nomencladorEntity = $this->repository->findOneByCodigo($codigo);
        if ($nomencladorEntity)
            return null;

        $parent = null;
        if (count($this->codigo_parent))
            $parent = $this->repository->findOneByCodigo(implode('_', $this->codigo_parent));

        return $this->repository->nuevo($codigo, $nomenclador->getName(), $nomenclador->getDescription(), $parent);
    }

    private function generarNomenclador(NomencladorInterface $nomenclador)
    {
        if ($nomenclador->getParent())
            $this->generarNomenclador($nomenclador->getParent());
        $this->codigo[] = $nomenclador->getCode();
    }
}
