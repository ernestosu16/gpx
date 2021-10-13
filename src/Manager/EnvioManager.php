<?php

namespace App\Manager;


use App\Controller\EnvioController;
use App\Entity\Envio;
use App\Entity\EnvioManifiesto;
use App\Entity\Estructura;
use App\Entity\Localizacion;
use App\Entity\TrabajadorCredencial;
use App\Repository\AgenciaRepository;
use App\Repository\EnvioManifiestoRepository;
use App\Repository\EnvioRepository;
use App\Repository\LocalizacionRepository;
use App\Repository\NomencladorRepository;
use App\Repository\PaisRepository;
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
        private EnvioManifiestoRepository $envioManifiestoRepository,
        private PaisRepository $paisRepository,
        private NomencladorRepository $nomencladorRepository,
        private AgenciaRepository $agenciaRepository,
        private EnvioRepository $envioRepository
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

            $envioPreRecepcion->pareo = $parearEnvio ? $envioManifestado->getCodigo() : '';

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

        /** @var $em EntityManagerInterface **/
        $em = $this->get('doctrine.orm.default_entity_manager');

        $deserializer = SerializerBuilder::create()->build();

        foreach ($envios as $envio){

            /** @var EnvioPreRecepcion $envioPreRecepcion */
            $envioPreRecepcion = $deserializer->deserialize(json_encode($envio),EnvioPreRecepcion::class,'json');

            $envio = new Envio();
            $envio->setFechaRecepcion(new \DateTime());
            $envio->setCodTracking($envioPreRecepcion->cod_tracking);
            $envio->setPareo($envioPreRecepcion->pareo);
            $envio->setPeso($envioPreRecepcion->peso);

            //Coger la del user autenticado
            $envio->setEstructuraOrigen($user->getEstructura());

            $envio->setDestinatario($envioPreRecepcion->destinatario);
            $envio->setRemitente($envioPreRecepcion->remitente);

            //Ver lo del metodo para buscar una agencia por el id
            $envio->setAgencia($this->agenciaRepository->find($envioPreRecepcion->agencia));

            //Ver lo del metodo para ponerle el estado
            //$envio->setEstado($this->nomencladorRepository->find());

            //Ver lo del metodo para ponerle el pais origen
            $envio->setPaisOrigen($this->paisRepository->find($envioPreRecepcion->pais_origen));

            $envio->setPaisDestino($this->paisRepository->findByCodigoAduana('CUB'));

            //Ver lo del metodo para ponerle la empresa
            //$envio->setEmpresa( );

            $envio->setProvincia($this->localizacionRepository->find($envioPreRecepcion->provincia));

            $envio->setMunicipio($this->localizacionRepository->find($envioPreRecepcion->municipio));

            //Ver lo del metodo para guardar las anomalias en campo json
            //$envio->setAnomalias($envioPreRecepcion->irregularidades);

            //Ver lo del metodo para generar el campo diecciones en el modelo y guardar en campo json
            //$envio->setDirecciones($envioPreRecepcion->direcciones);

            //A;adir lo de las trazas_envio , trazas_aduana y envio_aduana

            $em->persist($envio);
            /*$em->persist($trazaEnvio);
            $em->persist($envioAduana);
            $em->persist($trazaEnvioAduana);
            */
        }

        $em->flush();

        return $recepcionados;
    }

}