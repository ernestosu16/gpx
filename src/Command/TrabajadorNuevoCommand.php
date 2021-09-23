<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class TrabajadorNuevoCommand extends Command
{
    private SymfonyStyle $io;

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    private static function options(): array
    {
        return [
            ['name' => 'numero-identidad', 'mode' => InputOption::VALUE_REQUIRED, 'description' => 'Número del Carné de Identidad', 'label' => 'Número de Identidad'],
            ['name' => 'nombre', 'mode' => InputOption::VALUE_REQUIRED, 'description' => 'Nombre del trabajado', 'label' => 'Nombre'],
            ['name' => 'nombre-segundo', 'mode' => InputOption::VALUE_OPTIONAL, 'description' => 'Segundo nombre del trabajador', 'label' => 'Segundo Nombre'],
            ['name' => 'apellido-primer', 'mode' => InputOption::VALUE_REQUIRED, 'description' => 'Segundo apellido del trabajador', 'label' => 'Primer Apellido'],
            ['name' => 'apellido-segundo', 'mode' => InputOption::VALUE_REQUIRED, 'description' => 'Segundo apellido del trabajador', 'label' => 'Segundo Apellido'],
            ['name' => 'cargo', 'mode' => InputOption::VALUE_REQUIRED, 'description' => 'Cargo que ocupa el trabajador', 'label' => 'Cargo'],
            ['name' => 'username', 'mode' => InputOption::VALUE_REQUIRED, 'description' => 'Usuario', 'label' => 'Usuario'],
            ['name' => 'password', 'mode' => InputOption::VALUE_REQUIRED, 'description' => 'Contraseña', 'label' => 'Contraseña', 'question' => ['hidden' => true]],
            ['name' => 'estructura', 'mode' => InputOption::VALUE_REQUIRED, 'description' => 'Lugar donde trabaja', 'label' => 'Estructura'],
            ['name' => 'grupo', 'mode' => InputOption::VALUE_REQUIRED, 'description' => 'Grupo principal del trabajador', 'label' => 'Grupo'],
        ];
    }

    protected function configure()
    {
        $this->setName('app:trabajador:crear')
            ->setDescription('Crear un trabajador del sistema')
            ->setHelp('');

        foreach (self::options() as $option):
            $this->addOption($option['name'], $option['shortcut'] ?? null, $option['mode'], $option['description'] ?? '', $option['default'] ?? null);
        endforeach;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        foreach (self::options() as $option) {
            if (isset($option['question']) && $option['question'] === false)
                continue;

            if (!isset($option['label']))
                throw new InvalidOptionException(sprintf('%s: No tiene definido "label"', $option['name']));
            $question = new Question($option['label'] . ':');

            if (isset($option['question']['hidden']) && $option['question']['hidden'] === true)
                $question->setHidden(true);
            $answer = $this->getHelper('question')->ask($input, $output, $question);

            $input->setOption($option['name'], $answer);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return Command::SUCCESS;
    }
}
