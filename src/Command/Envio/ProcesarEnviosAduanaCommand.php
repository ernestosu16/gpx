<?php


namespace App\Command\Envio;


use App\Command\BaseCommand;
use App\Command\BaseCommandInterface;
use App\Entity\Pais;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ProcesarEnviosAduanaCommand extends BaseCommand implements BaseCommandInterface
{

    static function getCommandName(): string
    {
        return 'app:envio:recepcionado-aduana';
    }

    static function getCommandDescription(): string
    {
        return 'Procesar los envios recepcionados y enviiarlos al FTP Aduana';
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->paisRepository = $this->getRepository(Pais::class);
    }
}