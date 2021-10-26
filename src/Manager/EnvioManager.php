<?php

namespace App\Manager;

use App\Utils\ModoRecepcion;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use App\Controller\EnvioController;
use App\Entity\Agencia;
use App\Entity\Envio;
use App\Entity\EnvioAduana;
use App\Entity\EnvioAduanaTraza;
use App\Entity\EnvioAnomaliaTraza;
use App\Entity\EnvioManifiesto;
use App\Entity\EnvioTraza;
use App\Entity\Estructura;
use App\Entity\EstructuraTipo;
use App\Entity\Localizacion;
use App\Entity\Nomenclador;
use App\Entity\Pais;
use App\Entity\TrabajadorCredencial;
use App\Repository\AgenciaRepository;
use App\Repository\EnvioManifiestoRepository;
use App\Repository\EnvioRepository;
use App\Repository\EstructuraTipoRepository;
use App\Repository\LocalizacionRepository;
use App\Repository\NomencladorRepository;
use App\Repository\PaisRepository;
use App\Utils\EnvioAnomalia;
use App\Utils\EnvioDireccion;
use App\Utils\EnvioPreRecepcion;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use JMS\Serializer\SerializerBuilder;
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


    /***
     * @param string $noGuia
     * @param string $codTracking
     * @return ?EnvioPreRecepcion
     */
    public function obtnerEnvioManifestado(string $noGuia, string $codTracking): ?EnvioPreRecepcion
    {
        $envioPreRecepcion = new EnvioPreRecepcion();

        /** @var $envioManifestado EnvioManifiesto * */
        $envioManifestado = $this->entityManager->getRepository(EnvioManifiesto::class)->findByGuiaAndCodigo($noGuia,$codTracking);

        $envioIgualCodTracking = $this->entityManager->getRepository(Envio::class)->findByEnvioToCodTrackingCalendarYear($codTracking);

        $parearEnvio = (bool)$envioIgualCodTracking;

        if (  $envioManifestado ) {

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

        }else{
            $envioPreRecepcion = null;
        }

        return $envioPreRecepcion;
    }

    public function recepcionarEnvios($envios,TrabajadorCredencial $user,$clientIP): bool{
        $recepcionados = true;

        $deserializer = SerializerBuilder::create()->build();
        try{
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

                $envio->setEmpresa($user->getEstructura()->searchParentsByTipo(
                    $this->entityManager->getRepository(EstructuraTipo::class)->findOneByCodigo('EMPRESA')
                ));

                $canalVerde = $this->entityManager->getRepository(Nomenclador::class)->findOneByCodigo('CANAL_VERDE');
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
                if ($envioPreRecepcion->modo_recepcion == ModoRecepcion::$MANIFESTADO){

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

                }else{
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
        }catch (\Exception $e){
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
        $envioTraza->setIp('');

        $this->entityManager->persist($envioTraza);

        foreach ($anomalias as $key=>$value )
        {

            $envioAnomaliaTraza = new EnvioAnomaliaTraza();
            $envioAnomaliaTraza->setAnomalia($this->entityManager->getRepository(Nomenclador::class)->findOneBy(['codigo'=>$key]));
            $envioAnomaliaTraza->setDescripcion($value);
            $envioAnomaliaTraza->setEnvioTraza($envioTraza);

            $this->entityManager->persist($envioAnomaliaTraza);
            $this->entityManager->flush();
        }
    }

}