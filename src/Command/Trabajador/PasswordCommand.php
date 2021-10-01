<?php

namespace App\Command\Trabajador;

use App\Entity\TrabajadorCredencial;
use App\Repository\TrabajadorCredencialRepository;
use App\Utils\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class PasswordCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private EntityManagerInterface         $entityManager,
        private Validator                      $validator,
        private TrabajadorCredencialRepository $credencial
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
        $this->setName('app:trabajador:password')
            ->setDescription('Cambiar la contraseña del trabajador')
            ->setHelp('');

        foreach (self::options() as $option):
            $this->addOption($option['name'], $option['shortcut'] ?? null, $option['mode'], $option['description'] ?? '', $option['default'] ?? null);
        endforeach;
    }

    private static function options(): array
    {
        return [
            ['name' => 'usuario', 'mode' => InputOption::VALUE_REQUIRED, 'description' => 'Usuario', 'label' => 'Usuario', 'invoke' => 'invokeCheckUsername'],
            ['name' => 'password', 'mode' => InputOption::VALUE_REQUIRED, 'description' => 'Contraseña', 'label' => 'Contraseña', 'invoke' => 'invokeValidatePassword'],
        ];
    }


    private function invokeCheckUsername(InputInterface $input, OutputInterface $output): string
    {
        return $this->io->ask('Usuario', null, function ($usuario) {

            if (!$this->credencial->findOneByUsuario($usuario))
                throw new InvalidOptionException(sprintf('El usuario "%s" no existe en el sistema.', $usuario));
            return $usuario;
        });
    }

    private function invokeValidatePassword(InputInterface $input, OutputInterface $output)
    {
        return $this->io->askHidden('Nueva contraseña', function ($password) {
            $this->validator->validatePassword($password);
            return $password;
        });
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->io->title('Cambio de contrasena de un trabajador');

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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var TrabajadorCredencial $credencial */
        $credencial = $this->credencial->findOneByUsuario($input->getOption('usuario'));
        $credencial->setContrasena($input->getOption('password'));

        $this->entityManager->persist($credencial);
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
