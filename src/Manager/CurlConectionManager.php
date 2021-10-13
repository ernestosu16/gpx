<?php


namespace App\Manager;

use App\Entity\EnvioManifiesto;
use App\Entity\Nomenclador;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;


class CurlConectionManager extends _Manager_
{
    const FTP_ENVIO_MANIFIESTO = "APP_ENVIO_MANIFIESTO_FTP_ACCESO";
    const FTP_DIERECTORI_ERROR_NAME = "manifiestoError";
    private array $ftp_config = [];
    private $ch;
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
        // setup connection
        $this->ch = curl_init();
        // check for successful connection
        if (!$this->ch)
            throw new \Exception('Could not initialize cURL.');
        // connection options
        $options = [
            CURLOPT_USERPWD        => $this->ftp_config['user'].':'.$this->ftp_config['pass'],
            CURLOPT_PORT           => $port,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FTP_SSL        => CURLFTPSSL_ALL, // require SSL For both control and data connections
            CURLOPT_FTPSSLAUTH     => CURLFTPAUTH_DEFAULT, // let cURL choose the FTP authentication method (either SSL or TLS)
            CURLOPT_SSL_CIPHER_LIST => 'DEFAULT@SECLEVEL=1'
        ];
        // cURL FTP enables passive mode by default, so disable it by enabling the PORT command and allowing cURL to select the IP address for the data connection
        if (!$passive_mode)
            $options[CURLOPT_FTPPORT] = '-';
        // If implicit mode
        if ($implicit) {
            $options[CURLOPT_SSL_VERIFYPEER] = false;
            $options[CURLOPT_SSL_VERIFYHOST] = false;
        } else { // No implicit mode
            $options[CURLOPT_SSL_VERIFYPEER] = true;
            $options[CURLOPT_SSL_VERIFYHOST] = true;
        }

        // set connection options, use foreach so useful errors can be caught instead of a generic "cannot set options" error with curl_setopt_array()
        foreach ($options as $option_name => $option_value) {
            if (!curl_setopt($this->ch, $option_name, $option_value))
                throw new \Exception(sprintf('Could not set cURL option: %s', $option_name));
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
        $curl = $this->ch;
        curl_setopt($curl, CURLOPT_URL, $strURL);
        curl_setopt($curl, CURLOPT_FTPLISTONLY, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $rsData = curl_exec($curl);
        if (!$rsData)
            throw new \Exception(sprintf('Making request failed: [%s] - %s', curl_errno($curl), curl_error($curl)));
        curl_close($curl);

        $content = preg_replace('#\n+#', ' ', $rsData);
        $content = trim($content);
        $result_directories = explode(' ', $content);
        return $result_directories;
    }

    /**
     * @throws \Exception
     */
    public function getFilesFromDirectories(string $url = null): array
    {
        $urlBase = $this->urlBase . $url . '/';
        $curl = $this->ch;
        curl_setopt($curl, CURLOPT_URL, $urlBase);
        curl_setopt($curl, CURLOPT_FTPLISTONLY, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $rsData = curl_exec($curl);
        if (!$rsData)
            throw new \Exception(sprintf('Making request failed: [%s] - %s', curl_errno($curl), curl_error($curl)));
        curl_close($curl);

        $content = preg_replace('#\n+#', ' ', $rsData);
        $content = trim($content);
        $result_directories = explode(' ', $content);
        return $result_directories;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->urlBase;
    }

    public function upload($remote_file, $local_file)
    {
        $curl = $this->ch;
        curl_setopt($curl, CURLOPT_UPLOAD, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
        // set file name
        if (!curl_setopt($curl, CURLOPT_URL, $this->url.$remote_file))
            throw new \Exception ("Could not set cURL file name: $remote_file");
        // open memory stream for writing
        $stream = fopen('php://temp', 'w+');
        // check for valid stream handle
        if (!$stream)
            throw new \Exception('Could not open php://temp for writing.');
        // write file into the temporary stream
        fwrite($stream, file_get_contents($local_file));
        // rewind the stream pointer
        rewind($stream);
        // set the file to be uploaded
        if (!curl_setopt($curl, CURLOPT_INFILE, $stream))
            throw new \Exception("Could not load file $remote_file");
        // upload file
        if (!curl_exec($curl))
            throw new \Exception(sprintf('Could not upload file. cURL Error: [%s] - %s', curl_errno($curl), curl_error($curl)));
        // close the stream handle
        fclose($stream);
    }

    public function download(string $remote_file,string $local_file, string $file_name)
    {
//        if(!file_exists ($local_file)){
//            mkdir($local_file,0777,true);
//        }
//
//        $file_handle = fopen($local_file . '/' . $file_name, "w+");
//
//        $curl = $this->ch;
//        curl_setopt($curl, CURLOPT_URL, $remote_file);
//        curl_setopt($curl, CURLOPT_UPLOAD, false);
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//        //Pass our file handle to cURL.
//        curl_setopt($curl, CURLOPT_FILE, $file_handle);
//
//        dump($curl);
//        curl_exec($curl);
//        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//
//        curl_close($curl);
//
//        fclose($file_handle);
//
//        if($statusCode == 200){
//            echo 'Downloaded!';
//        } else{
//            echo "Status Code: " . $statusCode;
//        }
//
//        exit;


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

        //Create a cURL handle.
        $curl = curl_init($remote_file);

        $options = [
            CURLOPT_USERPWD        => $this->ftp_config['user'].':'.$this->ftp_config['pass'],
            CURLOPT_PORT           => 21,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FTP_SSL        => CURLFTPSSL_ALL, // require SSL For both control and data connections
            CURLOPT_FTPSSLAUTH     => CURLFTPAUTH_DEFAULT, // let cURL choose the FTP authentication method (either SSL or TLS)
            CURLOPT_SSL_CIPHER_LIST => 'DEFAULT@SECLEVEL=1'
        ];

        $options[CURLOPT_SSL_VERIFYPEER] = false;
        $options[CURLOPT_SSL_VERIFYHOST] = false;

        foreach ($options as $option_name => $option_value) {
            if (!curl_setopt($curl, $option_name, $option_value))
                throw new \Exception(sprintf('Could not set cURL option: %s', $option_name));
        }

        //Pass our file handle to cURL.
        curl_setopt($curl, CURLOPT_FILE, $fp);

        //Timeout if the file doesn't download after 20 seconds.
        curl_setopt($curl, CURLOPT_TIMEOUT, 20);

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

        if($statusCode == 200){
            echo 'Downloaded!';
        } else{
            echo "Status Code: " . $statusCode;
        }
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
//        dump($strURL);
//        dump($filename);
//
//        $curl = $this->ch;
//        // connection options
//        $options = array(
//            //CURLOPT_HEADER => 0,
//            CURLOPT_RETURNTRANSFER => true, // Return data inplace of echoing on screen
//            CURLOPT_URL => $strURL
//            //CURLOPT_QUOTE => array('DELE /' . $filename)
//        );
//
//        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
//        foreach ($options as $option_name => $option_value) {
//            if (!curl_setopt($curl, $option_name, $option_value))
//                throw new \Exception(sprintf('Could not set cURL option: %s', $option_name));
//        }

        //$url = $strURL;

        $ch = curl_init();

        $options = [
            CURLOPT_USERPWD        => $this->ftp_config['user'].':'.$this->ftp_config['pass'],
            CURLOPT_PORT           => 21,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FTP_SSL        => CURLFTPSSL_ALL, // require SSL For both control and data connections
            CURLOPT_FTPSSLAUTH     => CURLFTPAUTH_DEFAULT, // let cURL choose the FTP authentication method (either SSL or TLS)
            CURLOPT_SSL_CIPHER_LIST => 'DEFAULT@SECLEVEL=1',
            CURLOPT_QUOTE => array( $deletePath),
            //CURLOPT_QUOTE => array('DELE /EMCI/DHL/' . $filename),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_RETURNTRANSFER =>true,
            CURLOPT_URL => $strURL
        ];

//        $options[CURLOPT_SSL_VERIFYPEER] = false;
//        $options[CURLOPT_SSL_VERIFYHOST] = false;

        //curl_setopt_array();//para pasar las opciones completas

        foreach ($options as $option_name => $option_value) {
            if (!curl_setopt($ch, $option_name, $option_value))
                throw new \Exception(sprintf('Could not set cURL option: %s', $option_name));
        }

        //curl_setopt($ch, CURLOPT_URL, $strURL);
        //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


//        $result = curl_exec($ch);
//        $result = json_decode($result);

        if (!curl_exec($ch))
            throw new \Exception(sprintf('Could not delete file. cURL Error: [%s] - %s', curl_errno($ch), curl_error($ch)));

        curl_close($ch);
    }
}