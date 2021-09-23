<?php

namespace App\Command\Trabajador;

use App\Entity\Estructura;
use App\Entity\EstructuraTipo;
use App\Repository\EstructuraRepository;
use App\Repository\EstructuraTipoRepository;
use App\Utils\Validator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class NuevoCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private Validator                $validator,
        private EstructuraTipoRepository $estructuraTipo,
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('app:trabajador:nuevo')
            ->setDescription('Crear un trabajador del sistema')
            ->setHelp('');

        foreach (self::options() as $option):
            $this->addOption($option['name'], $option['shortcut'] ?? null, $option['mode'], $option['description'] ?? '', $option['default'] ?? null);
        endforeach;
    }

    private static function options(): array
    {
        return [
            ['name' => 'numero-identidad', 'mode' => InputOption::VALUE_REQUIRED, 'description' => 'Número del Carné de Identidad', 'label' => 'Número de Identidad', 'validator' => 'validateNumeroIdentidad'],
            ['name' => 'nombre', 'mode' => InputOption::VALUE_REQUIRED, 'description' => 'Nombre del trabajado', 'label' => 'Nombre', 'validator' => 'validatePalabra'],
            ['name' => 'apellido-primero', 'mode' => InputOption::VALUE_REQUIRED, 'description' => 'Segundo apellido del trabajador', 'label' => 'Primer Apellido', 'validator' => 'validatePalabra'],
            ['name' => 'apellido-segundo', 'mode' => InputOption::VALUE_REQUIRED, 'description' => 'Segundo apellido del trabajador', 'label' => 'Segundo Apellido', 'validator' => 'validatePalabra'],
            ['name' => 'cargo', 'mode' => InputOption::VALUE_REQUIRED, 'description' => 'Cargo que ocupa el trabajador', 'label' => 'Cargo'],
            ['name' => 'usuario', 'mode' => InputOption::VALUE_REQUIRED, 'description' => 'Usuario', 'label' => 'Usuario', 'validator' => 'validateUsername'],
            ['name' => 'password', 'mode' => InputOption::VALUE_REQUIRED, 'description' => 'Contraseña', 'label' => 'Contraseña', 'question' => ['hidden' => true], 'validator' => 'validatePassword'],
            ['name' => 'estructura', 'mode' => InputOption::VALUE_REQUIRED, 'description' => 'Lugar donde trabaja', 'label' => 'Estructura'],
            ['name' => 'grupo', 'mode' => InputOption::VALUE_REQUIRED, 'description' => 'Grupo principal del trabajador', 'label' => 'Grupo'],
        ];
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->io->title('Asistente interactivo para agregar un trabajador');

        $tipos = $this->estructuraTipo->findAll();
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion('Por favor seleccione el tipo de estructura', $tipos, 0);
        $question->setErrorMessage('El Tipo de Estructura "%s" es invalido.');
        /** @var EstructuraTipo $tipo */
        $tipo = $helper->ask($input, $output, $question);
        $this->io->text('<info>' . $tipo . '</info>');

        # Selección de la estructura
        $estructuras = $tipo->getEstructuras()->toArray();
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion('Por favor seleccione una estructura', $estructuras, 0);
        $question->setErrorMessage('La Estructura "%s" es invalida.');
        /** @var Estructura $estructura */
        $estructura = $helper->ask($input, $output, $question);
        $this->io->text('<info>' . $estructura . '</info>');

        foreach (self::options() as $option) {
            if (isset($option['question']) && $option['question'] === false)
                continue;

            if (!isset($option['label']))
                throw new InvalidOptionException(sprintf('%s: No tiene definido "label"', $option['name']));

            $value = $input->getOption($option['name']);
            if (null !== $value)
                $this->io->text('# <info>' . $option['label'] . '</info>: ' . $value);
            else {
                $value = (isset($option['validator'])) ?
                    $this->io->ask($option['label'], null, [$this->validator, $option['validator']]) :
                    $this->io->ask($option['label']);
            }
            $input->setOption($option['name'], $value);
        }
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return Command::SUCCESS;
    }
}
