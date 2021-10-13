<?php

namespace App\Manager;


use App\Entity\Envio;
use App\Entity\EnvioManifiesto;
use App\Entity\Estructura;
use App\Entity\Localizacion;
use App\Repository\AgenciaRepository;
use App\Repository\EnvioManifiestoRepository;
use App\Repository\EnvioRepository;
use App\Repository\LocalizacionRepository;
use App\Repository\NomencladorRepository;
use App\Repository\PaisRepository;
use App\Utils\EnvioDireccion;
use App\Utils\EnvioPreRecepcion;
use Doctrine\ORM\EntityManagerInterface;

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

    public function recepcionarEnvios(array $envios): bool{
        $recepcionados = true;

        /** @var $em EntityManagerInterface **/
        $em = $this->get('doctrine.orm.default_entity_manager');

        foreach ($envios as $envio){
            /** @var $envio EnvioPreRecepcion **/

            $envioRecepcionar = new Envio();
            $envioRecepcionar->setFechaRecepcion(new \DateTime());
            $envioRecepcionar->setCodTracking($envio->getCodTracking());
            $envioRecepcionar->setPareo($envio->getPareo());
            $envioRecepcionar->setPeso($envio->getPeso());
            //Coger la del user autenticado
            $estructuraOrigen = new Estructura();

            $envioRecepcionar->setEstructuraOrigen($estructuraOrigen);

            $envioRecepcionar->setDestinatario($envio->getDestinatario());
            $envioRecepcionar->setRemitente($envio->getRemitente());

            $envioRecepcionar->setAgencia(  $envio->getAgencia());


            $direccion = new EnvioDireccion();
            $direccion->setCalle();
            //....


            $em->persist($envioRecepcionar);
        }

        $em->flush();

        return $recepcionados;
    }

}