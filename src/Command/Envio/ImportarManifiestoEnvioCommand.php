<?php


namespace App\Command\Envio;


use App\Command\BaseCommand;
use App\Command\BaseCommandInterface;
use App\Config\Nomenclador\_Nomenclador_;
use App\Entity\Nomenclador;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use phpseclib3\Net\SFTP;
use phpseclib3\Net\SSH2;

final class ImportarManifiestoEnvioCommand extends BaseCommand implements BaseCommandInterface
{

    const FTP_ENVIO_MANIFIESTO = "FTP_ENVIO_MANIFIESTO";
    private array $ftp_config = [];

    static function getCommandName(): string
    {
        return 'app:envio:manifiesto';
    }

    static function getCommandDescription(): string
    {
        return 'Importa los envios de los manifiestos.xml';
    }

    /**
     * @throws ORMException
     */
    private function getFTPConfi(): array
    {
        if(empty($this->ftp_config)){
            $em = $this->getEntityManager();
            /** @var Nomenclador $ftpConfi */
            $ftpConfi = $em->getRepository(Nomenclador::class)->findOneByCodigo(self::FTP_ENVIO_MANIFIESTO);
            if (!$ftpConfi)
                throw new ORMException('No se encontró la configuración del FTP_ENVIO_MANIFIESTO');

            $this->ftp_config['host'] = $ftpConfi->getParametro("host");
            $this->ftp_config['user'] = $ftpConfi->getParametro("user");
            $this->ftp_config['pass'] = $ftpConfi->getParametro("pass");
        }

        return $this->ftp_config;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ftpConfi = $this->getFTPConfi();

        //dump($ftpConfi);exit;

        //$sftp = new SFTP($ftpConfi['host'],21);
        //$sftp->login($ftpConfi['user'], $ftpConfi['pass']);

        //$ssh = new SSH2($ftpConfi['host'],21);
        //dump($ssh->getServerPublicHostKey());exit;

        /*if ($expected != $ssh->getServerPublicHostKey()) {
            throw new \Exception('Host key verification failed');
        }

        if (!$ssh->login($ftpConfi['user'], $ftpConfi['pass'])) {
            throw new \Exception('Login failed');
        }*/

        dump('ok');exit;
    }
}