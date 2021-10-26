<?php

namespace App\Manager;


use App\Controller\EnvioController;
use App\Entity\Envio;
use App\Entity\EnvioAduana;
use App\Entity\EnvioAduanaTraza;
use App\Entity\EnvioAnomaliaTraza;
use App\Entity\EnvioManifiesto;
use App\Entity\EnvioTraza;
use App\Entity\Estructura;
use App\Entity\Localizacion;
use App\Entity\TrabajadorCredencial;
use App\Repository\AgenciaRepository;
use App\Repository\FicheroEnvioAduanaRepository;
use App\Repository\EnvioRepository;
use App\Repository\EstructuraTipoRepository;
use App\Repository\LocalizacionRepository;
use App\Repository\NomencladorRepository;
use App\Repository\PaisRepository;
use App\Utils\EnvioAnomalia;
use App\Utils\EnvioDireccion;
use App\Utils\EnvioPreRecepcion;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerBuilder;

class EnvioManager extends _Manager_
{
    /**
     * EnvioManager constructor.
     */
    public function __construct(
        private LocalizacionRepository $localizacionRepository,
        private FicheroEnvioAduanaRepository $envioManifiestoRepository,
        private PaisRepository $paisRepository,
        private NomencladorRepository $nomencladorRepository,
        private AgenciaRepository $agenciaRepository,
        private EnvioRepository $envioRepository,
        private EstructuraTipoRepository $estructuraTipoRepository
    )
    {
    }


    /***
     * @param string $noGuia
     * @param string $codTracking
     * @return ?EnvioPreRecepcion
     */
    public function obtnerEnvioManifestado(string $noGuia, string $codTracking): ?EnvioPreRecepcion
    {
        $envioPreRecepcion = new EnvioPreRecepcion();
        $envioManifestado = $this->envioManifiestoRepository->findByGuiaAndCodigo($noGuia,$codTracking);

        $envioIgualCodTracking = $this->envioRepository->findOneBy(['cod_tracking'=> $codTracking]);
        //Coger la fecha y si esdel mismo a;o entoces hacer el pareo decirlo al cliente y hacer validaciones
        $parearEnvio = (bool)$envioIgualCodTracking;

        if ( $envioManifestado ) {

            $envioPreRecepcion->no_guia = $envioManifestado->getNoGuiaAerea();
            $envioPreRecepcion->cod_tracking = $envioManifestado->getCodigo();
            $envioPreRecepcion->peso = $envioManifestado->getPeso();

            $envioPreRecepcion->pais_origen = $this->paisRepository->findByCodigoAduana($envioManifestado->getPaisOrigen())->getId();

            $envioPreRecepcion->agencia = $this->agenciaRepository->findByCodigoAduana($envioManifestado->getAgenciaOrigen())->getId();

            $envioPreRecepcion->entidad_ctrl_aduana = false;

            $provDest = $envioManifestado->getProvinciaDestinatario();
            $envioPreRecepcion->provincia = $provDest?->getId();

            $munDest = $envioManifestado->getMunicipioDestinatario();
            $envioPreRecepcion->municipio = $munDest?->getId();

            $envioPreRecepcion->requiere_pareo = $parearEnvio;

            $envioPreRecepcion->irregularidades = [];

            $envioPreRecepcion->remitente = $envioManifestado->getRemitente();
            $envioPreRecepcion->destinatario = $envioManifestado->getDestinatario();

            $direccion = new EnvioDireccion();
            $direccion->setCalle($envioManifestado->getCalleDestinatario());
            $direccion->setEntreCalle($envioManifestado->getEntreCalleDestinatario());
            $direccion->setYCalle($envioManifestado->getYCalleDestinatario());
            $direccion->setNumero($envioManifestado->getNoDestinatario());
            $direccion->setPiso($envioManifestado->getPisoDestinatario());
            $direccion->setApto($envioManifestado->getAptoDestinatario());
            $direccion->setProvincia(
                $envioManifestado->getProvinciaDestinatario()
            );
            $direccion->setMunicipio(
                $envioManifestado->getMunicipioDestinatario()
            );

            $envioPreRecepcion->direcciones = [];
            $envioPreRecepcion->direcciones[] = $direccion;

        }else{
            $envioPreRecepcion = null;
        }

        return $envioPreRecepcion;
    }

    public function recepcionarEnvios($envios,TrabajadorCredencial $user): bool{
        $recepcionados = true;


        /** @var $em EntityManagerInterface * */
        $em = $this->get('doctrine.orm.default_entity_manager');

        $deserializer = SerializerBuilder::create()->build();

        foreach ($envios as $envio) {

            /** @var EnvioPreRecepcion $envioPreRecepcion */
            $envioPreRecepcion = $deserializer->deserialize(json_encode($envio), EnvioPreRecepcion::class, 'json');

            $fechaActual = new \DateTime();

            /**
             * Envio
             */
            $envio = new Envio();
            $envio->setFechaRecepcion($fechaActual);
            $envio->setCodTracking($envioPreRecepcion->cod_tracking);
            $envio->setPareo($envioPreRecepcion->pareo);
            $envio->setPeso($envioPreRecepcion->peso);

            //Coger la del user autenticado
            $envio->setEstructuraOrigen($user->getEstructura());

            $envio->setDestinatario($envioPreRecepcion->destinatario);
            $envio->setRemitente($envioPreRecepcion->remitente);

            $envio->setAgencia($this->agenciaRepository->find($envioPreRecepcion->agencia));

            $estadoRecepcionado = $this->nomencladorRepository->findOneByCodigo('APP_ENVIO_ESTADO_RECEPCIONADO');
            $envio->setEstado($estadoRecepcionado);

            $envio->setPaisOrigen($this->paisRepository->find($envioPreRecepcion->pais_origen));

            $envio->setPaisDestino($this->paisRepository->getPaisCuba());

            $envio->setEmpresa($user->getEstructura()->searchParentsByTipo(
                $this->estructuraTipoRepository->findOneByCodigo('EMPRESA')
            ));

            /** @var Localizacion $provincia */
            $provincia = $this->localizacionRepository->find($envioPreRecepcion->provincia);
            $envio->setProvincia($provincia);

            /** @var Localizacion $municipio */
            $municipio = $this->localizacionRepository->find($envioPreRecepcion->municipio);
            $envio->setMunicipio($municipio);

            $envio->setDirecciones($envioPreRecepcion->direcciones);

            $em->persist($envio);

            /**
             * Envio trazas
             */

            /** @var EnvioTraza $envioTraza */
            $envioTraza = new EnvioTraza();
            $envioTraza->setFecha($fechaActual);
            $envioTraza->setPeso($envioPreRecepcion->peso);
            $envioTraza->setEnvio($envio);
            $envioTraza->setEstado($estadoRecepcionado);
            $envioTraza->setTrabajador($user->getTrabajador());
            $envioTraza->setEstructuraOrigen($user->getEstructura());
            $envioTraza->setIp('');

            $em->persist($envioTraza);

            /**
             * Anomalias del envio
             */

            foreach ($envioPreRecepcion->irregularidades as $anomalia) {

                $envioAnomaliaTraza = new EnvioAnomaliaTraza();
                $envioAnomaliaTraza->setAnomalia($this->nomencladorRepository->find($anomalia->getId()));
                $envioAnomaliaTraza->setDescripcion($anomalia->getDescripcion());
                $envioAnomaliaTraza->setEnvioTraza($envioTraza);

                $em->persist($envioAnomaliaTraza);
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

            $em->persist($envioAduana);

            /**
             * Trazas aduana
             */

            $envioAduanaTraza = new EnvioAduanaTraza();
            $envioAduanaTraza->setEnvio($envio);
            $envioAduanaTraza->setFecha($fechaActual);
            $envioAduanaTraza->setEstado($estadoRecepcionado);

            $em->persist($envioAduanaTraza);

            /**
             * Cambiar en la tabla envio manifiesto el campo por recepcionado
             */
        }

        $em->flush();

        return $recepcionados;
    }

}