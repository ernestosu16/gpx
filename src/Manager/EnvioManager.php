<?php

namespace App\Manager;

use App\Entity\Envio\Envio;
use App\Entity\Envio\EnvioManifiesto;
use App\Entity\Localizacion;
use App\Entity\Nomenclador;
use App\Entity\Pais;
use App\Entity\TrabajadorCredencial;
use App\Utils\EnvioDireccion;
use App\Utils\EnvioPreRecepcion;
use App\Utils\ModoRecepcion;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class EnvioManager extends _Manager_
{

    private EntityManagerInterface $entityManager;
    /**
     * EnvioManager constructor.
     */

    /**
     * @throws ORMException
     * @throws \Exception
     */
    public function __construct(EntityManagerInterface $doctrineEntityManager
    )
    {
        $this->entityManager = $doctrineEntityManager;
    }

    private function getEnvioRepository(): EnvioRepository
    {
        return $this->entityManager->getRepository(Envio::class);
    }

    private function getNomencladorRepository(): NomencladorRepository
    {
        return $this->entityManager->getRepository(Nomenclador::class);
    }


    /***
     * @param string $noGuia
     * @param string $codTracking
     * @return ?EnvioPreRecepcion
     */
    public function obtnerEnvioManifestado(string $noGuia, string $codTracking): ?EnvioPreRecepcion
    {
        $envioPreRecepcion = new EnvioPreRecepcion();

        /** @var $envioManifestado EnvioManifiesto * */
        $envioManifestado = $this->entityManager->getRepository(EnvioManifiesto::class)->findByGuiaAndCodigo($noGuia, $codTracking);

        $envioIgualCodTracking = $this->entityManager->getRepository(Envio::class)->findByEnvioToCodTrackingCalendarYear($codTracking);

        $parearEnvio = (bool)$envioIgualCodTracking;

        if ($envioManifestado) {

            $envioPreRecepcion->id = $envioManifestado->getId();
            $envioPreRecepcion->no_guia = $envioManifestado->getNoGuiaAerea();
            $envioPreRecepcion->cod_tracking = $envioManifestado->getCodigo();
            $envioPreRecepcion->peso = $envioManifestado->getPeso();

            $envioPreRecepcion->pais_origen = $this->entityManager->getRepository(Pais::class)->findOneByCodigoAduana($envioManifestado->getPaisOrigen())?->getId();

            $envioPreRecepcion->agencia = $this->entityManager->getRepository(Agencia::class)->findByCodigoAduana($envioManifestado->getAgenciaOrigen())->getId();

            $envioPreRecepcion->entidad_ctrl_aduana = false;

            $provDest = $envioManifestado->getProvinciaDestinatario();
            $envioPreRecepcion->provincia = $provDest?->getId();

            $munDest = $envioManifestado->getMunicipioDestinatario();
            $envioPreRecepcion->municipio = $munDest?->getId();

            $envioPreRecepcion->requiere_pareo = $parearEnvio;

            $envioPreRecepcion->irregularidades = [];

            $envioPreRecepcion->remitente = $envioManifestado->getRemitente();
            $envioPreRecepcion->destinatario = $envioManifestado->getDestinatario();

            $envioPreRecepcion->modo_recepcion = ModoRecepcion::$MANIFESTADO;

        } else {
            $envioPreRecepcion = null;
        }

        return $envioPreRecepcion;
    }

    public function recepcionarEnvios($envios, TrabajadorCredencial $user, $clientIP): bool
    {
        $recepcionados = true;

        $deserializer = SerializerBuilder::create()->build();
        try {
            foreach ($envios as $envio) {

                /** @var EnvioPreRecepcion $envioPreRecepcion */
                $envioPreRecepcion = $deserializer->deserialize(json_encode($envio), EnvioPreRecepcion::class, 'json');

                /** @var $envioManifestado EnvioManifiesto * */

                $fechaActual = new \DateTime();

                /**
                 * Envio
                 */
                $envio = new Envio();
                $envio->setFechaRecepcion($fechaActual);
                $envio->setCodTracking($envioPreRecepcion->cod_tracking);
                $envio->setPareo($envioPreRecepcion->pareo);
                $envio->setPeso($envioPreRecepcion->peso);

                $envio->setEstructuraOrigen($user->getEstructura());
                $envio->setEstructuraDestino($user->getEstructura());

                $envio->setAgencia($this->entityManager->getRepository(Agencia::class)->find($envioPreRecepcion->agencia));

                $estadoRecepcionado = $this->entityManager->getRepository(Nomenclador::class)->findOneByCodigo('APP_ENVIO_ESTADO_RECEPCIONADO');
                $envio->setEstado($estadoRecepcionado);

                $envio->setPaisOrigen($this->entityManager->getRepository(Pais::class)->find($envioPreRecepcion->pais_origen));

                $envio->setPaisDestino($this->entityManager->getRepository(Pais::class)->getPaisCuba());

                $empresa = $user->getEstructura()->searchParentsByTipo(
                    $this->entityManager->getRepository(EstructuraTipo::class)->findOneByCodigo(EstructuraTipo::EMPRESA)
                );

                if (!$empresa)
                    throw new ORMException('La empresa no existe');

                $envio->setEmpresa($empresa);

                $canalVerde = $this->entityManager->getRepository(Nomenclador::class)->findOneByCodigo('APP_ENVIO_CANAL_VERDE');
                $envio->setCanal($canalVerde);

                /** @var Localizacion $provincia */
                $provincia = $this->entityManager->getRepository(Localizacion::class)->find($envioPreRecepcion->provincia);
                $envio->setProvincia($provincia);

                /** @var Localizacion $municipio */
                $municipio = $this->entityManager->getRepository(Localizacion::class)->find($envioPreRecepcion->municipio);
                $envio->setMunicipio($municipio);

                /**
                 * Para envios manifestados
                 */
                if ($envioPreRecepcion->modo_recepcion == ModoRecepcion::$MANIFESTADO) {

                    $envioManifestado = $this->entityManager->getRepository(EnvioManifiesto::class)->find($envioPreRecepcion->id);
                    $envioManifestado->setRecepcionado(true);
                    $this->entityManager->persist($envioManifestado);

                    $envio->setDestinatario($envioManifestado->getDestinatario());
                    $envio->setRemitente($envioManifestado->getRemitente());

                    $direccion = new EnvioDireccion();
                    $direccion->setCalle($envioManifestado->getCalleDestinatario());
                    $direccion->setEntreCalle($envioManifestado->getEntreCalleDestinatario());
                    $direccion->setYCalle($envioManifestado->getYCalleDestinatario());
                    $direccion->setNumero($envioManifestado->getNoDestinatario());
                    $direccion->setPiso($envioManifestado->getPisoDestinatario());
                    $direccion->setApto($envioManifestado->getAptoDestinatario());
                    $direccion->setProvincia(
                        $envioManifestado->getProvinciaDestinatario()?->getId()
                    );
                    $direccion->setMunicipio(
                        $envioManifestado->getMunicipioDestinatario()?->getId()
                    );

                    $normalizers = [new ObjectNormalizer()];
                    $serializer = new Serializer($normalizers, []);

                    $dereccionesSerializadas = $serializer->normalize($direccion);

                    $direcciones = [];
                    $direcciones[] = $dereccionesSerializadas;

                    $envio->setDirecciones($direcciones);

                } else {
                    $envioManifestado = null;
                }

                $this->entityManager->persist($envio);

                /**
                 * Envio trazas
                 */

                $envioTraza = new EnvioTraza();
                $envioTraza->setFecha($fechaActual);
                $envioTraza->setPeso($envioPreRecepcion->peso);
                $envioTraza->setEnvio($envio);
                $envioTraza->setEstado($estadoRecepcionado);
                $envioTraza->setTrabajador($user->getTrabajador());
                $envioTraza->setEstructuraOrigen($user->getEstructura());
                $envioTraza->setIp($clientIP);
                $envioTraza->setCanal($canalVerde);

                $this->entityManager->persist($envioTraza);

                /**
                 * Anomalias del envio
                 */

                foreach ($envioPreRecepcion->irregularidades as $anomalia) {

                    $envioAnomaliaTraza = new EnvioAnomaliaTraza();
                    $envioAnomaliaTraza->setAnomalia($this->entityManager->getRepository(Nomenclador::class)->find($anomalia->getId()));
                    $envioAnomaliaTraza->setDescripcion($anomalia->getDescripcion());
                    $envioAnomaliaTraza->setEnvioTraza($envioTraza);

                    $this->entityManager->persist($envioAnomaliaTraza);
                }

                /**
                 * Envio aduana
                 */

                $envioAduana = new EnvioAduana();
                $envioAduana->setEnvio($envio);
                $envioAduana->setCodTracking($envioPreRecepcion->cod_tracking);
                $envioAduana->setProvinciaAduana($provincia->getCodigoAduana());
                $envioAduana->setMunicipioAduana($municipio->getCodigoAduana());
                $envioAduana->setEstado($estadoRecepcionado);
                $envioAduana->setArancel( $envioManifestado ? $envioManifestado->isArancel() : false );
                $envioAduana->setDatosDespacho(null);
                $this->entityManager->persist($envioAduana);

                /**
                 * Trazas aduana
                 */

                $envioAduanaTraza = new EnvioAduanaTraza();
                $envioAduanaTraza->setEnvio($envio);
                $envioAduanaTraza->setFecha($fechaActual);
                $envioAduanaTraza->setEstado($estadoRecepcionado);

                $this->entityManager->persist($envioAduanaTraza);

            }

            $this->entityManager->flush();
        } catch (\Exception $e) {
            return false;
        }

        return $recepcionados;
    }

    public function saveEnvioAnomalias(string $id, array $anomalias, TrabajadorCredencial $user)
    {
        $envio = $this->entityManager->getRepository(Envio::class)->find($id);

        $envioTraza = new EnvioTraza();
        $envioTraza->setFecha(new \DateTime());
        $envioTraza->setPeso($envio->getPeso());
        $envioTraza->setEnvio($envio);
        $envioTraza->setEstado($envio->getEstado());
        $envioTraza->setTrabajador($user->getTrabajador());
        $envioTraza->setEstructuraOrigen($user->getEstructura());
        $ip = array_key_exists('REMOTE_ADDR', $_SERVER) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        $envioTraza->setIp($ip);
        $envioTraza->setCanal($envio->getCanal());

        $this->entityManager->persist($envioTraza);

        foreach ($anomalias as $key => $value) {

            $envioAnomaliaTraza = new EnvioAnomaliaTraza();
            $envioAnomaliaTraza->setAnomalia($this->entityManager->getRepository(Nomenclador::class)->findOneBy(['codigo' => $key]));
            $envioAnomaliaTraza->setDescripcion($value);
            $envioAnomaliaTraza->setEnvioTraza($envioTraza);

            $this->entityManager->persist($envioAnomaliaTraza);
            $this->entityManager->flush();
        }
    }

    public function cambiarEstado($id, TrabajadorCredencial $user)
    {
        $envio = $this->getEnvioRepository()->find($id);
        $estado = $this->getNomencladorRepository()->findOneByCodigo('APP_ENVIO_ESTADO_RECEPCIONADO');

        $envio->setEstado($estado);
        $this->entityManager->persist($envio);

        $traza = new EnvioTraza();
        $traza->setEstado($estado);
        $traza->setCanal($envio->getCanal());
        $traza->setEnvio($envio);
        $traza->setEstructuraDestino($envio->getEstructuraDestino());
        $traza->setEstructuraOrigen($envio->getEstructuraOrigen());
        $traza->setFecha(new \DateTime());
        $ip = array_key_exists('REMOTE_ADDR', $_SERVER) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        $traza->setIp($ip);
        $traza->setPeso($envio->getPeso());
        $traza->setTrabajador($user->getTrabajador());
        $this->entityManager->persist($envio);

        $this->entityManager->flush();

    }

    public function addDespachoAduanaEnvio($url, $env_aduana_id, $cod_envio, $empresa){

        //$em = $this->getDoctrine()->getManager();

        $soapClient = new \nusoap_client($url);
        $soapClient->soap_defencoding = 'UTF-8';
        $soapClient->decode_utf8 = false;

        /** @var EnvioManifiesto $manifiesto */
        $manifiesto = $this->entityManager->getRepository(EnvioManifiesto::class)->findOneBy(['codigo'=>$cod_envio]);

        /** @var EnvioAduana $envio_aduana */
        $envio_aduana = $this->entityManager->getRepository(EnvioAduana::class)->find($env_aduana_id);


        $valor = json_encode($empresa->getParametros());
        $cod_aduana = json_decode($valor);

        $result = $soapClient->call('GABLDespachado',
            [
                'usuario'=>'aerov',
                'clave'=>'eh7443fx',
                'manifiesto'=>'590/2021',
                'blga'=>'703-10541510 5089198',
                'codigoaduana' => '0202'
            ]);

        /*if ($manifiesto->getCodigo() != null && $manifiesto->getNoGuiaAerea() != null){
            $result = $soapClient->call('GABLDespachado',
                [
                    'usuario'=>'aerov',
                    'clave'=>'eh7443fx',
                    'manifiesto'=>$manifiesto->getCodigo(),
                    'blga'=>$manifiesto->getNoGuiaAerea(),
                    'codigoaduana' => '0202'
                ]);

        }else{
            return false;
        }*/

        /*$res = json_decode($result);

        if($res->success == true){
            $res = json_decode($result, true);
            $envio_aduana->setDatosDespacho($res);
            $this->entityManager->persist($envio_aduana);
            $this->entityManager->flush();
            $respuesta = true;
        }else{
            $respuesta = false;
        }*/


        $res = json_decode($result, true);
        $envio_aduana->setDatosDespacho($res);
        //dump($envio_aduana);exit();
        $this->entityManager->persist($envio_aduana);
        $this->entityManager->flush();
        $respuesta = true;

        return $respuesta;
    }

    public function verificarConectAduana($url){

        set_time_limit(120);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_exec($ch);

        $info = curl_getinfo($ch);
        curl_close($ch);

        if($info["connect_time"]==0)
        {
            return 0;
        }
        else
        {
            return 1;

        }

    }

}
