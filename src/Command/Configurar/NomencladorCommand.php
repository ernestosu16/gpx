<?php

namespace App\Command\Configurar;

use App\Command\BaseCommand;
use App\Command\BaseCommandInterface;
use App\Config\Data\_Data_;
use App\Entity\Nomenclador;
use App\Repository\NomencladorRepository;
use App\Utils\ClassFinderUtil;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use function Symfony\Component\String\u;

final class NomencladorCommand extends BaseCommand implements BaseCommandInterface
{
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

        $finder = new Finder();
        $finder->files()->in('src/Config/Data/Nomenclador');

        foreach ($finder as $file) {
            $content = $file->getContents();
            $tokens = token_get_all($content);
            $namespace = '';
            for ($index = 0; isset($tokens[$index]); $index++) {
                if (!isset($tokens[$index][0])) {
                    continue;
                }
                if (T_NAMESPACE === $tokens[$index][0]) {
                    $index += 2; // Skip namespace keyword and whitespace
                    while (isset($tokens[$index]) && is_array($tokens[$index])) {
                        $namespace .= $tokens[$index++][1];
                    }
                }
                if (T_CLASS === $tokens[$index][0] && T_WHITESPACE === $tokens[$index + 1][0] && T_STRING === $tokens[$index + 2][0]) {
                    $index += 2; // Skip class keyword and whitespace
                    $fqcns[] = u($namespace)->toString() /*. '\\' . $tokens[$index][1]*/
                    ;
                    break;
                }
            }
        }
        $fqcns = array_unique($fqcns);

        foreach ($fqcns as $namespace) {
//            $namespace = u($namespace)->replace('\\', '\\\\')->toString();
            $MyClassesNamespace = ClassFinderUtil::getClassesInNamespace($namespace);
            foreach ($MyClassesNamespace as $item) {
                $classReflection = new ReflectionClass($item);
                if ($classReflection->isAbstract()) continue;
                /** @var _Data_ $className */
                $className = $classReflection->getName();
                $nomenclador[] = $className::newInstance();
            }
        }

        return $nomenclador;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ReflectionException
     * @throws \Doctrine\ORM\ORMException|\Doctrine\ORM\OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var ConsoleSectionOutput $section */
        $section = $output->section();
        $section->writeln('Creando la lista de nomencladores por defecto');

        /** @var _Data_ $nomenclador */
        foreach (self::nomencladores() as $nomenclador) {
            $entity = $this->generarNomencladorEntity($nomenclador, $section);
            if ($entity) {
                $this->getEntityManager()->persist($entity);
                $this->getEntityManager()->flush();
            }
        }
        $section->writeln('OK');
        return Command::SUCCESS;
    }

    private function generarNomencladorEntity(_Data_ $nomenclador, ConsoleSectionOutput $section): ?Nomenclador
    {
        /** @var NomencladorRepository $repository */
        $repository = $this->getEntityManager()->getRepository($nomenclador->getDiscriminator());

        if ($nomenclador->getParent() && !$repository->findOneByCodigo($nomenclador->getParent()->getCodeComplete())) {
            $entity = $this->generarNomencladorEntity($nomenclador->getParent(), $section);

            $section->writeln(sprintf(
                '* Creando nomenclador - ["codigo" => "%s", "nombre" => "%s"]',
                $nomenclador->getCodeComplete(), $nomenclador->getName()
            ));

            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush();
        }

        if ($repository->findOneByCodigo($nomenclador->getCodeComplete()))
            return null;

        $parent = null;
        if ($nomenclador->getParent()) {
            $parent = $repository->findOneByCodigo($nomenclador->getParent()->getCodeComplete());
        }

        if (!$nomenclador->getParent()) {
            $section->writeln(sprintf(
                '* Creando nomenclador - ["codigo" => "%s", "nombre" => "%s"]',
                $nomenclador->getCodeComplete(), $nomenclador->getName()
            ));
        }

        return $this->newEntityNomenclador($nomenclador, $parent);
    }

    private function newEntityNomenclador(_Data_ $nomenclador, $parent): Nomenclador
    {
        $class = $nomenclador->getDiscriminator();
        $entity = new $class();

        if (!$entity instanceof Nomenclador)
            throw new CommandNotFoundException('El objeto instanciado no es de clase "Nomenclador"');

        $entity->setParent($parent);
        $entity->setCodigo($nomenclador->getCodeComplete());
        $entity->setNombre($nomenclador->getName());
        $entity->setDescripcion($nomenclador->getDescription());

        return $entity;
    }
}
