<?php


namespace App\Manager;

use App\Entity\Envio\EnvioManifiesto;;
use App\Entity\Nomenclador;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;


class CurlConectionManager extends _Manager_
{
    const FTP_ENVIO_MANIFIESTO = "APP_ENVIO_MANIFIESTO_FTP_ACCESO";
    const FTP_DIERECTORI_ERROR_NAME = "manifiestoError";
    private array $ftp_config = [];
    private array $curl_options;
    //private $ch;
    private string $urlBase;
    private EntityManagerInterface $entityManager;

    /**
     * @throws ORMException
     * @throws \Exception
     */
    public function __construct(EntityManagerInterface $doctrineEntityManager) {
        $protocol = 'ftp'; // El protocolo de comunicación
        $implicit = true; // Usamos FTP implícito sobre TSL
        $port = 21; // Puerto de datos del servidor remoto
        $remote_path = ''; // Ruta del servidor donde leeremos o escribiremos
        $passive_mode = true; // Un servidor pasivo
        $this->entityManager = $doctrineEntityManager;
        $this->getFTPConfi();
        // set host/initial path
        $this->urlBase = $protocol.'://'.$this->ftp_config['host'].'/'.$remote_path;

        $this->curl_options = [
            CURLOPT_USERPWD        => $this->ftp_config['user'].':'.$this->ftp_config['pass'],
            CURLOPT_PORT           => $port,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_FTP_SSL        => CURLFTPSSL_ALL, // require SSL For both control and data connections
            CURLOPT_FTPSSLAUTH     => CURLFTPAUTH_DEFAULT, // let cURL choose the FTP authentication method (either SSL or TLS)
            CURLOPT_SSL_CIPHER_LIST => 'DEFAULT@SECLEVEL=1'
        ];
        // cURL FTP enables passive mode by default, so disable it by enabling the PORT command and allowing cURL to select the IP address for the data connection
        if (!$passive_mode)
            $this->curl_options[CURLOPT_FTPPORT] = '-';
        // If implicit mode
        if ($implicit) {
            $this->curl_options[CURLOPT_SSL_VERIFYPEER] = false;
            $this->curl_options[CURLOPT_SSL_VERIFYHOST] = false;
        } else { // No implicit mode
            $this->curl_options[CURLOPT_SSL_VERIFYPEER] = true;
            $this->curl_options[CURLOPT_SSL_VERIFYHOST] = true;
        }
    }

    /**
     * @throws ORMException
     * @throws \Exception
     */
    private function getFTPConfi()
    {
        if(empty($this->ftp_config)){
            /** @var Nomenclador $ftpConfi */
            $ftpConfi = $this->entityManager->getRepository(Nomenclador::class)->findOneByCodigo(self::FTP_ENVIO_MANIFIESTO);
            if (!$ftpConfi)
                throw new \Exception('No se encontró la configuración del FTP_ENVIO_MANIFIESTO');

            $this->ftp_config['host'] = $ftpConfi->getParametro("servidor");
            $this->ftp_config['user'] = $ftpConfi->getParametro("usuario");
            $this->ftp_config['pass'] = $ftpConfi->getParametro("contrasena");
        }

        return $this->ftp_config;
    }

    /**
     * @throws \Exception
     */
    public function getDirectories(string $strURL = null): array
    {
        $options = [
            CURLOPT_URL => $strURL,
            CURLOPT_FTPLISTONLY => true,
            CURLOPT_RETURNTRANSFER => true
        ];

        $curl = $this->curlInit($options);
        $rsData = curl_exec($curl);
        if (!$rsData)
            throw new \Exception(sprintf('Making request failed: [%s] - %s', curl_errno($curl), curl_error($curl)));
        curl_close($curl);

        $content = preg_replace('#\n+#', ' ', $rsData);
        $content = trim($content);
        $result_directories = explode(' ', $content);

        curl_close($curl);

        return $result_directories;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->urlBase;
    }

    public function upload($remote_file_name, $local_file , $remote_file_path)
    {

        // open memory stream for writing
        $stream = fopen('php://temp', 'w+');
        // check for valid stream handle
        if (!$stream)
            throw new \Exception('Could not open php://temp for writing.');
        // write file into the temporary stream
        fwrite($stream, file_get_contents($local_file));
        // rewind the stream pointer
        rewind($stream);

        $options = [
            CURLOPT_UPLOAD => true,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_FTP_CREATE_MISSING_DIRS => true,
            CURLOPT_URL => $this->getUrl(). $remote_file_path . self::FTP_DIERECTORI_ERROR_NAME . '/' . $remote_file_name,
            CURLOPT_INFILE => $stream
        ];

        $curl = $this->curlInit($options);
        // upload file
        if (!curl_exec($curl))
            throw new \Exception(sprintf('Could not upload file. cURL Error: [%s] - %s', curl_errno($curl), curl_error($curl)));
        // close the stream handle
        fclose($stream);

        unlink($local_file);
    }

    public function download(string $remote_file,string $local_file, string $file_name)
    {
        //The path & filename to save to.
        $saveTo = $local_file . '/' . $file_name;

        if(!file_exists ($local_file)){
            mkdir($local_file,0777,true);
        }

        //Open file handler.
        $fp = fopen($saveTo, 'w+');

        //If $fp is FALSE, something went wrong.
        if($fp === false){
            throw new \Exception('Could not open: ' . $saveTo);
        }

        $options = [
            CURLOPT_FILE => $fp
        ];

        $curl = $this->curlInit($options,$remote_file);

        //Execute the request.
        curl_exec($curl);

        //If there was an error, throw an Exception
        if(curl_errno($curl)){
            throw new \Exception(curl_error($curl));
        }

        //Get the HTTP status code.
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        //Close the cURL handler.
        curl_close($curl);

        //Close the file handler.
        fclose($fp);
    }

    public function getContentFromFTP(string $url = null, bool $file = false): array
    {
        if(!$url){
            $url = $this->urlBase;
        }
        $directories = $this->getDirectories($url);
        $fullPathDirectories = array();
        $fullPathFile = array();
        foreach ($directories as $directory){
            if(strcmp($directory, self::FTP_DIERECTORI_ERROR_NAME) !== 0 ){
                //dump(substr($directory, -4));
                $strUperCase = strtoupper($directory);

                if( !str_contains($strUperCase, '.XML') && $file == false)
                {
                    //dump('Es un directorio');
                    $fullPathDirectories[] = ['url' => $url . $directory . '/', 'file' =>$directory];
                } else{
                    $fullPathFile[] = ['url' => $url . $directory, 'file' =>$directory];
                }
            }

        }

        if($file){
            return $fullPathFile;
        }else{
            return $fullPathDirectories;
        }
    }

    function deleteFileFromFTP(string $strURL,string $deletePath)
    {
        $options = [
            CURLOPT_QUOTE => array( $deletePath),
            CURLOPT_RETURNTRANSFER =>true,
            CURLOPT_URL => $strURL
        ];

        $ch = $this->curlInit($options);

        if (!curl_exec($ch))
            throw new \Exception(sprintf('Could not delete file. cURL Error: [%s] - %s', curl_errno($ch), curl_error($ch)));

        curl_close($ch);
    }

    public function curlInit(array $options, $remote_file = null)
    {
        $opt =  array_replace($this->curl_options,$options);

        if($remote_file)
        {
            $ch = curl_init($remote_file);
        }else{
            $ch = curl_init();
        }
        if (!curl_setopt_array($ch, $opt))
                throw new \Exception('Could not set cURL option correctly');

        return $ch;
    }
}