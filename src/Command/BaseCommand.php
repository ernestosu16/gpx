<?php

namespace App\Command;

use App\Kernel;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use Exception;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BaseCommand
 * @package App\Command
 */
abstract class BaseCommand extends Command implements BaseCommandInterface
{
    public static function getCommandDescription(): string
    {
        return '';
    }

    protected function get($id): ?object
    {
        return $this->getContainer()->get($id);
    }

    #[Pure]
    protected function getKernel(): Kernel
    {
        return $this->getApplication()->getKernel();
    }

    protected function getContainer(): ContainerInterface
    {
        return $this->getKernel()->getContainer();
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questions = [];

        foreach ($this->inputs() as $key => $value) {
            if (isset($value['interact']) && $value['interact'] === false)
                continue;

            if (!$input->getArgument($key)) {
                $question = new Question($value['label'] . ': ');

                if ($value['required']) {
                    $question->setValidator(function ($key) use ($value) {
                        if (empty($key)) {
                            throw new Exception($value['label'] . ' no puede estar vacía');
                        }

                        return $key;
                    });
                }

                if ($key === 'password')
                    $question->setHidden(true);

                $questions[$key] = $question;
            }
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }

    /**
     * @return array
     *
     * Ejemplo:
     *  return ['password' => [
     *     'label' => 'Contraseña',
     *     'description' => 'Contraseña de acceso al webmaster',
     *     'required' => true
     *  ]];
     */
    protected function inputs(): array
    {
        return [];
    }

    protected function option(): array
    {
        return [];
    }

    protected function configure()
    {
        $definition = [];
        foreach ($this->inputs() as $key => $value) {
            $definition[] = new InputArgument($key, $value['required'] ? InputArgument::REQUIRED : InputArgument::OPTIONAL, $value['description']);
        }

        foreach ($this->option() as $value) {
            $definition[] = new InputOption($value['name'], $value['shortcut'], $value['mode'], $value['description']);
        }

        $this
            ->setName(static::getCommandName())
            ->setDescription(static::getCommandDescription())
            ->setDefinition($definition);
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager(): object
    {
        return $this->get('doctrine.orm.entity_manager');
    }

    /**
     * @param string $entityName The name of the entity.
     * @return EntityRepository|ObjectRepository
     */
    public function getRepository(string $entityName): EntityRepository|ObjectRepository
    {
        return $this->getEntityManager()->getRepository($entityName);
    }
}
