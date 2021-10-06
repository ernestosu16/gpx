<?php


namespace App\Command\Envio;


use App\Command\BaseCommand;
use App\Command\BaseCommandInterface;
use App\Config\Nomenclador\_Nomenclador_;
use App\Entity\EnvioManifiesto;
use App\Entity\Estructura;
use App\Entity\Localizacion;
use App\Entity\Nomenclador;
use App\Entity\Persona;
use Doctrine\ORM\ORMException;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Config\Util\XmlUtils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use phpseclib3\Net\SFTP;
use phpseclib3\Net\SSH2;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


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
                //dump("Transitaria - ".$directoriesNameWithExtension);
                $em = $this->getEntityManager();
                /** @var Estructura $transitaria */
                $transitaria = $em->getRepository(Estructura::class)->findOneBy(array('codigo' => $directoriesNameWithExtension));
                if($transitaria == null){
                    dump('No existe esta Transitaria');
                    continue;// salta esta iteracion
                }

                $finderAgencia = new Finder();
                $finderAgencia->depth(0);
                $agencias = $finderAgencia->directories()->in($absoluteDirectoriesPath);

                foreach ($agencias as $agencia){
                    $agenciaAbsoluteDirectoriesPath = $agencia->getRealPath();
                    //dump("Agencia - ".$agenciaAbsoluteDirectoriesPath);
                    $agenciaDirectoriesNameWithExtension = $agencia->getRelativePathname();
                    $finderManifiesto = new Finder();
                    $finderManifiesto->depth(0);
                    $files = $finderManifiesto->files()->in($agenciaAbsoluteDirectoriesPath)->name(['*.xml','*.XML']);
                    if($files->hasResults()){
                        foreach ($files as $file) {
                            $absoluteFilePath = $file->getRealPath();
                            $fileNameWithExtension = $file->getRelativePathname();
                            $this->readManifiestoXML($absoluteFilePath, $fileNameWithExtension, $transitaria);
                        }
                    }
                }

            }
        }
        return Command::SUCCESS;
    }

    protected function readManifiestoXML(string $absoluteFilePath, string $fileNameWithExtension, Estructura $transitaria){
        //libxml_use_internal_errors(true);
        $xml = simplexml_load_file($absoluteFilePath);
        if ($xml === false) {
            echo "Error cargando XML".$fileNameWithExtension."\n";
            //throw new \Exception('Error cargando el fichero ');
            /*foreach(libxml_get_errors() as $error) {
                echo "\t", $error->message;
            }*/
        }else{
            $guia = (string)$xml->noGA;
            $agencia = (string)$xml->agenciaOrigen;
            $no_vuelo = (string)$xml->noVuelo;
            $envios = $xml->envios->envio;

            $this->getEntityManager()->beginTransaction();
            try{
                foreach ($envios as $env){
                    $newenvio = $this->crearEnvioManifiesto($guia, $agencia, $no_vuelo, $transitaria, $env );
                    if($newenvio->getId() == null){
                        throw new \Exception('Manifiesto '.$newenvio->getCodigo().' no es valido.');
                    }
                }
                $this->getEntityManager()->commit();
            }catch (\Exception $e){
                $this->getEntityManager()->rollBack();
                dump($e->getMessage());
            }

        }
    }

    protected function crearEnvioManifiesto(string $guia, string $agencia, string $no_vuelo, Estructura $transitaria, \SimpleXMLElement $env): ?EnvioManifiesto
    {
        /**@var $emPersona \App\Manager\PersonaManager **/
        $emPersona = $this->getContainer()->get('app.manager.persona');

        $serializer = SerializerBuilder::create()->build();
        $remitente = $serializer->deserialize($env->remitente->persona->asXML(), Persona::class, 'xml');
        $destinatario = $serializer->deserialize($env->destinatario->persona->asXML(), Persona::class, 'xml');

        $remitente->setNumeroPasaporte(null);
        $destinatario->setNumeroPasaporte(null);
        $remitente->setNumeroIdentidad(null);

        $existRemitente = $this->getEntityManager()->getRepository(Persona::class)->findOneBy(["hash"=>$emPersona->generarHash($remitente)]);
        if($existRemitente != null){
            $remitente = $existRemitente;
        }

        $existDestinatario = $this->getEntityManager()->getRepository(Persona::class)->findOneBy(["hash"=>$emPersona->generarHash($destinatario)]);
        if($existDestinatario != null){
            $destinatario = $existDestinatario;
        }

        $localizacion = $this->getEntityManager()->getRepository(Localizacion::class)->findMunicipioAndProvinciaByCodigo(
            $env->destinatario->contacto->contactosDomicilios->domicilio->provincia->codigoMunicipio,
            $env->destinatario->contacto->contactosDomicilios->domicilio->provincia->codigoProvincia);

        $newEnvioManifiesto = new EnvioManifiesto($env->noEnvio,(float)$env->peso,$env->fechaImposicion,$agencia,$guia,$no_vuelo,$env->{"paisOrigen-Destino"},
            $env->descripcion, $env->destinatario->persona->nacionalidad, $env->destinatario->persona->fechaNacimiento,$env->destinatario->contacto->contactosTelefonos->telefono->noTelefono,
            $env->destinatario->contacto->contactosDomicilios->domicilio->calle, $env->destinatario->contacto->contactosDomicilios->domicilio->entreCalle,
            $env->destinatario->contacto->contactosDomicilios->domicilio->yCalle, $env->destinatario->contacto->contactosDomicilios->domicilio->no,
            $env->destinatario->contacto->contactosDomicilios->domicilio->piso, $env->destinatario->contacto->contactosDomicilios->domicilio->apto,
            $localizacion[1], $localizacion[0],$env->remitente->persona->nacionalidad,$env->remitente->persona->fechaNacimiento);

        $newEnvioManifiesto->setRemitente($remitente);
        $newEnvioManifiesto->setDestinatario($destinatario);
        $newEnvioManifiesto->setEmpresa($transitaria);

        /**@var $emManifiesto \App\Manager\EnvioManifiestoManager **/
        $emManifiesto = $this->getContainer()->get('app.manager.envio_manifiesto');

        if($emManifiesto->validarEnvioManifiesto($newEnvioManifiesto)){
            dump('Crea');
            return $emManifiesto->createEnvioManifiesto($newEnvioManifiesto);
        }

        return $newEnvioManifiesto;
    }
}