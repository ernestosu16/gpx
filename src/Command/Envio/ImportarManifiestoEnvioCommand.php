<?php


namespace App\Command\Envio;


use App\Command\BaseCommand;
use App\Command\BaseCommandInterface;
use App\Config\Nomenclador\_Nomenclador_;
use App\Entity\Agencia;
use App\Entity\EnvioManifiesto;
use App\Entity\Estructura;
use App\Entity\Localizacion;
use App\Entity\Nomenclador;
use App\Entity\Persona;
use App\Manager\CurlConectionManager;
use Doctrine\ORM\ORMException;
use JMS\Serializer\SerializerBuilder;
use SimpleXMLElement;
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
    static function getCommandName(): string
    {
        return 'app:envio:manifiesto';
    }

    static function getCommandDescription(): string
    {
        return 'Importa los envios de los manifiestos.xml';
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /**@var $emCurl CurlConectionManager **/
        $emCurl = $this->getContainer()->get('app.manager.curl_conection');

        $em = $this->getEntityManager();

        //buscando las transitorias
        $transitorias = $emCurl->getContentFromFTP();

        //recooriendo todas las transitorias
        foreach ($transitorias as $transitoria)
        {
            /** @var Estructura $estructuraTransitaria */
            $estructuraTransitaria = $em->getRepository(Estructura::class)->findOneBy(array('codigo' => $transitoria['file']));
            if($estructuraTransitaria == null){
                dump('No existe esta Transitaria');
                continue;// salta esta iteracion
            }
            //buscando las agencias
            $agencias = $emCurl->getContentFromFTP($transitoria['url']);
            //dump($agencias);
            foreach ($agencias as $agencia){
                /** @var Agencia $nomencladorAgencia */
                $nomencladorAgencia = $em->getRepository(Nomenclador::class)->findOneBy(array('codigo' => 'AGENCIA_' . $agencia['file']));
                if($nomencladorAgencia == null){
                    dump('No existe esta Agencia');
                    continue;// salta esta iteracion
                }

                //buscando los xml
                $manifiestos = $emCurl->getContentFromFTP($agencia['url'], true);
                foreach ($manifiestos as $file)
                {
                    $local_directory = $this->get('kernel')->getProjectDir() . '/public/download/envioManifiesto/' . $transitoria['file'] . '/' . $agencia['file'];
                    $emCurl->download($file['url'], $local_directory, $file['file']);
                    //$emCurl->download($agencia['url'], $local_directory, $file['file']);

                    $absoluteFilePath = $local_directory . '/' . $file['file'];
                    $result = $this->readManifiestoXML($absoluteFilePath, $file['file'], $estructuraTransitaria);

                    if($result){//manifiesto leido correctamente
                        $deletePath = 'DELE /' . $transitoria['file'] . '/' . $agencia['file'] . '/' .  $file['file'];
                        $emCurl->deleteFileFromFTP($agencia['url'], $deletePath );
                    }else{//manifiesto con error

                    }
                }
            }
        }

        dump('ok');
        exit;

        $transitoria = 'EMCI';
        $agencia = 'SA';
        $fileName = 'Manifiesto202108080340SA.xml';
        $localFile = $this->get('kernel')->getProjectDir() . '/public/download/envioManifiesto/' . $transitoria . '/' . $agencia; // donde se va a descargar
        $remoteFile = $this->get('kernel')->getProjectDir() . '/public/download/prueba/EMCI/SA/Manifiesto202108080340SA.xml';

        $emCurl->download($remoteFile, $localFile, $fileName);

        //$files = $emCurl->getFilesFromDirectories($directories[0].'/COPA');
        //$dir = __DIR__."/app/public/download/envioManifiesto";
        //dump($this->get('kernel')->getProjectDir());
        exit('ok');



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
                            $result = $this->readManifiestoXML($absoluteFilePath, $fileNameWithExtension, $transitaria);
                            if($result){//manifiesto leido correctamente

                            }else{//manifiesto con error

                            }
                        }
                    }
                }

            }
        }
        return Command::SUCCESS;
    }

    protected function readManifiestoXML(string $absoluteFilePath, string $fileNameWithExtension, Estructura $transitaria): bool
    {
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
                return true;
            }catch (\Exception $e){
                $this->getEntityManager()->rollBack();
                dump($e->getMessage());
                return false;
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