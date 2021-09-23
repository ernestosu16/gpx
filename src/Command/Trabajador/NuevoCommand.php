<?php

namespace App\Command\Trabajador;

use App\Entity\Estructura;
use App\Entity\EstructuraTipo;
use App\Entity\Grupo;
use App\Entity\Trabajador;
use App\Repository\EstructuraTipoRepository;
use App\Repository\GrupoRepository;
use App\Utils\Validator;
use Doctrine\ORM\EntityManagerInterface;
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
        private EntityManagerInterface   $entityManager,
        private Validator                $validator,
        private EstructuraTipoRepository $estructuraTipo,
        private GrupoRepository          $grupo,
    )
    {
        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
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
            ['name' => 'estructura', 'mode' => InputOption::VALUE_REQUIRED, 'description' => 'Lugar donde trabaja', 'label' => 'Estructura', 'invoke' => 'invokeValidateEstructura'],
            ['name' => 'grupo', 'mode' => InputOption::VALUE_REQUIRED, 'description' => 'Grupo principal del trabajador', 'label' => 'Grupo', 'invoke' => 'invokeValidateGrupo'],
        ];
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->io->title('Asistente interactivo para agregar un trabajador');

        foreach (self::options() as $option) {
            if (isset($option['question']) && $option['question'] === false)
                continue;

            if (!isset($option['label']))
                throw new InvalidOptionException(sprintf('%s: No tiene definido "label"', $option['name']));

            $value = $input->getOption($option['name']);
            if (isset($option['invoke']) && is_string($option['invoke'])) {
                $method = $option['invoke'];
                if (method_exists($this, $method))
                    $value = $this->$method($input, $output);
            } else {
                if (null !== $value)
                    $this->io->text('# <info>' . $option['label'] . '</info>: ' . $value);
                else {
                    $value = (isset($option['validator'])) ?
                        $this->io->ask($option['label'], null, [$this->validator, $option['validator']]) :
                        $this->io->ask($option['label']);
                }
            }
            $input->setOption($option['name'], $value);
        }
    }

    private function invokeValidateEstructura(InputInterface $input, OutputInterface $output): Estructura
    {
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

        return $estructura;
    }

    private function invokeValidateGrupo(InputInterface $input, OutputInterface $output): Grupo
    {
        $grupos = $this->grupo->findAll();
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion('Por favor seleccione el grupo', $grupos, 0);
        $question->setErrorMessage('El Grupo "%s" es invalido.');
        /** @var Grupo $grupo */
        $grupo = $helper->ask($input, $output, $question);
        $this->io->text('<info>' . $grupo . '</info>');
        return $grupo;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $numeroIdentidad = $input->getOption('numero-identidad');
        $nombre = $input->getOption('nombre');
        $apellidoPrimero = $input->getOption('apellido-primero');
        $apellidoSegundo = $input->getOption('apellido-segundo');
        $cargo = $input->getOption('cargo');
        $usuario = $input->getOption('usuario');
        $password = $input->getOption('password');
        $estructura = $input->getOption('estructura');
        $grupo = $input->getOption('grupo');

        $trabajador = new Trabajador();
        $trabajador->setDatoPersona($numeroIdentidad, $nombre, '', $apellidoPrimero, $apellidoSegundo);
        $trabajador->setDatoCredencial($usuario, $password);
        $trabajador->setEstructura($estructura);
        $trabajador->addGrupo($grupo);
        $trabajador->setCargo($cargo);

        $this->entityManager->persist($trabajador);
        $this->entityManager->flush();
        return Command::SUCCESS;
    }
}
