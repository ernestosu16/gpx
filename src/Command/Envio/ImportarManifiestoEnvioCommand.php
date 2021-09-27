<?php


namespace App\Command\Envio;


use App\Command\BaseCommand;
use App\Command\BaseCommandInterface;
use App\Config\Nomenclador\_Nomenclador_;
use App\Entity\Nomenclador;
use Doctrine\ORM\ORMException;
use Symfony\Component\Config\Util\XmlUtils;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use phpseclib3\Net\SFTP;
use phpseclib3\Net\SSH2;
use Symfony\Component\Finder\Finder;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\Translation\Exception\InvalidResourceException;

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

        $dir = "/app/public/download/envioManifiesto";
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


        $finder = new Finder();
        // find all files in the current directory
        //$finder->files()->in($dir);
        $finder->depth(0);

        $directories = $finder->directories()->in($dir);

        // check if there are any search results
        if ($directories->hasResults()) {
            foreach ($directories as $directory) {
                $absoluteDirectoriesPath = $directory->getRealPath();
                $directoriesNameWithExtension = $directory->getRelativePathname();

                //dump($absoluteDirectoriesPath);
                //dump($directoriesNameWithExtension);

                $files = $finder->files()->in($absoluteDirectoriesPath)->name(['*.xml','*.XML']);
                if($files->hasResults()){
                    foreach ($files as $file) {
                        $absoluteFilePath = $file->getRealPath();
                        $fileNameWithExtension = $file->getRelativePathname();

                        $this->readManifiestoXML($absoluteFilePath, $fileNameWithExtension);

                        //dump($fileNameWithExtension);
                    }
                }
            }
        }

        exit;



//        try {
//            $dom = XmlUtils::loadFile($file);
//        } catch (\InvalidArgumentException $e) {
//            throw new InvalidResourceException(sprintf('Unable to load "%s": %s', $file, $e->getMessage()), $e->getCode(), $e);
//        }

        //XmlUtils::loadFile("/app/public/download/Manifiesto202108080340SA.xml");

//        $envios = $dom->getElementsByTagName( "envio");
//        foreach ($envios as $envio) {
//            //echo $envio->nodeValue, PHP_EOL;
//            //dump($item++);
//        }

        exit;
        //dump();exit;
    }

    protected function readManifiestoXML(string $absoluteFilePath, string $fileNameWithExtension){
        //libxml_use_internal_errors(true);
        $xml = simplexml_load_file($absoluteFilePath);
        if ($xml === false) {
            echo "Error cargando XML\n";
            //throw new \Exception('Error cargando el fichero ');
            /*foreach(libxml_get_errors() as $error) {
                echo "\t", $error->message;
            }*/
        }else{
            $guia = $xml->noGA;
            $agencia = $xml->agenciaOrigen;
            $no_vuelo = $xml->noVuelo;
        }
        dump($xml);exit;
    }
}