<?php


namespace App\Manager;

use App\Entity\EnvioManifiesto;
use App\Entity\Nomenclador;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;


class CurlConectionManager extends _Manager_
{
    const FTP_ENVIO_MANIFIESTO = "FTP_ENVIO_MANIFIESTO";
    private array $ftp_config = [];
    private $ch = false;
    private string $url = '';
    private EntityManagerInterface $entityManager;

//    public function setDefaultEntityManager(EntityManagerInterface $doctrineEntityManager): void
//    {
//        $this->entityManager = $doctrineEntityManager;
//    }

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

            $this->ftp_config['host'] = $ftpConfi->getParametro("host");
            $this->ftp_config['user'] = $ftpConfi->getParametro("user");
            $this->ftp_config['pass'] = $ftpConfi->getParametro("pass");
        }

        return $this->ftp_config;
    }

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
        $this->url = $protocol.'://'.$this->ftp_config['host'].'/'.$remote_path;
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
     * @throws \Exception
     */
    public function getContentBycURL(string $strURL): bool|string
    {
        $ch = curl_init();

        // connection options
        $options = [
            CURLOPT_USERPWD => $this->ftp_config['user'] . ':' . $this->ftp_config['pass'],
            CURLOPT_PORT => 21,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FTP_SSL => CURLFTPSSL_ALL, // require SSL For both control and data connections
            CURLOPT_FTPSSLAUTH => CURLFTPAUTH_DEFAULT, // let cURL choose the FTP authentication method (either SSL or TLS)
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1, // Return data inplace of echoing on screen
            CURLOPT_URL => $strURL,
            CURLOPT_SSL_VERIFYPEER => 0, // Skip SSL Verification
        ];

        // set connection options, use foreach so useful errors can be caught instead of a generic "cannot set options" error with curl_setopt_array()
        foreach ($options as $option_name => $option_value) {
            if (!curl_setopt($ch, $option_name, $option_value))
                throw new \Exception(sprintf('Could not set cURL option: %s', $option_name));
        }

        $rsData = curl_exec($ch);
        curl_close($ch);
        return $rsData;
    }
}