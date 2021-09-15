<?php

namespace App\Command\Configurar;

use App\Command\BaseCommand;
use App\Command\BaseCommandInterface;
use App\Config\Nomenclador\_Nomenclador_;
use App\Entity\Nomenclador;
use App\Repository\NomencladorRepository;
use App\Util\ClassFinderUtil;
use Doctrine\ORM\ORMException;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;

final class NomencladorCommand extends BaseCommand implements BaseCommandInterface
{
    private array $codigo;

    static function getCommandName(): string
    {
        return 'app:configurar:nomenclador';
    }

    /**
     * @return self[]
     * @throws ReflectionException
     */
    private static function nomencladores(): array
    {
        $nomenclador = [];

        $MyClassesNamespace = ClassFinderUtil::getClassesInNamespace('App\\Config\\Nomenclador');
        foreach ($MyClassesNamespace as $item) {
            $classReflection = new ReflectionClass($item);
            if ($classReflection->isAbstract()) continue;
            /** @var _Nomenclador_ $className */
            $className = $classReflection->getName();
            $nomenclador[] = $className::newInstance();
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
        /** @var ConsoleSectionOutput $section */
        $section = $output->section();
        $section->writeln('Creando la lista de nomencladores por defecto');

        /** @var _Nomenclador_ $nomenclador */
        foreach (self::nomencladores() as $nomenclador) {
            $nomencladorEntity = $this->generarNomencladorEntity($nomenclador);
            if ($nomencladorEntity) {
                $section->writeln(sprintf(
                    '* Creando nomenclador - ["codigo" => "%s", "nombre" => "%s"]',
                    $nomencladorEntity->getCodigo(), $nomencladorEntity->getNombre()
                ));
                $this->getEntityManager()->persist($nomencladorEntity);
                $this->getEntityManager()->flush();
            }
        }
        $section->writeln('OK');
        return Command::SUCCESS;
    }


    private function generarNomencladorEntity(_Nomenclador_ $nomenclador): ?Nomenclador
    {
        $this->codigo = [];
        $codigo_parent = [];
        $this->generarNomenclador($nomenclador);

        # Quitando el ultimo registro para que quede el codigo padre
        $lastKeyCodigo = array_key_last($this->codigo);
        if ($lastKeyCodigo != 0) {
            $codigoParent = $this->codigo;
            unset($codigoParent[$lastKeyCodigo]);
            $codigo_parent = $codigoParent;
        }

        $codigo = implode('_', $this->codigo);
        /** @var NomencladorRepository $repository */
        $repository = $this->getEntityManager()->getRepository($nomenclador->getDiscriminator());

        $nomencladorEntity = $repository->findOneByCodigo($codigo);
        if ($nomencladorEntity)
            return null;

        $parent = null;
        if (count($codigo_parent))
            $parent = $repository->findOneByCodigo(implode('_', $codigo_parent));

        return $this->newEntityNomenclador($codigo, $nomenclador, $parent);
    }

    private function generarNomenclador(_Nomenclador_ $nomenclador)
    {
        if ($nomenclador->getParent())
            $this->generarNomenclador($nomenclador->getParent());
        $this->codigo[] = $nomenclador->getCode();
    }

    private function newEntityNomenclador($codigo, _Nomenclador_ $nomenclador, $parent): Nomenclador
    {
        $class = $nomenclador->getDiscriminator();
        $entity = new $class();

        if (!$entity instanceof Nomenclador)
            throw new CommandNotFoundException('El objeto instanciado no es de clase "Nomenclador"');

        if ($parent)
            $entity->setParent($parent);

        $entity->setCodigo($codigo);
        $entity->setNombre($nomenclador->getName());
        $entity->setDescripcion($nomenclador->getDescription());

        return $entity;
    }
}
