<?php

namespace App\Manager;


use App\Entity\Envio;
use App\Entity\EnvioManifiesto;
use App\Entity\Estructura;
use App\Entity\Localizacion;
use App\Repository\AgenciaRepository;
use App\Repository\EnvioManifiestoRepository;
use App\Repository\LocalizacionRepository;
use App\Repository\NomencladorRepository;
use App\Repository\PaisRepository;
use App\Utils\EnvioDireccion;
use App\Utils\EnvioPreRecepcion;

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
        private AgenciaRepository $agenciaRepository
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

        if ( $envioManifestado ) {

            $envioPreRecepcion->setNoGuia($envioManifestado->getNoGuiaAerea());
            $envioPreRecepcion->setCodTracking($envioManifestado->getCodigo());
            $envioPreRecepcion->setPeso($envioManifestado->getPeso());

            $envioPreRecepcion->setPaisOrigen(
                $this->paisRepository->findByCodigoAduana($envioManifestado->getPaisOrigen())->getId()
            );

            $envioPreRecepcion->setAgencia(
                $this->agenciaRepository->findByCodigoAduana($envioManifestado->getAgenciaOrigen())->getId()
            );

            $envioPreRecepcion->setEntidadCtrlAduana($envioManifestado->isInteresAduana());

            $provDest = $envioManifestado->getProvinciaDestinatario();
            $envioPreRecepcion->setProvincia(
                $provDest ? $provDest->getId() : null
            );

            $munDest = $envioManifestado->getMunicipioDestinatario();
            $envioPreRecepcion->setMunicipio(
                $munDest ? $munDest->getId() : null
            );

            $envioPreRecepcion->setPareo("");

            $envioPreRecepcion->setIrregularidades([]);

            $envioPreRecepcion->setRemitente($envioManifestado->getRemitente());
            $envioPreRecepcion->setDestinatario($envioManifestado->getDestinatario());

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

            $envioPreRecepcion->addDireccion($direccion);

        }else{
            $envioPreRecepcion = null;
        }

        return $envioPreRecepcion;
    }

    public function recepcionarEnvios(array $envios): bool{
        $recepcionados = true;

        foreach ($envios as $envio){

            $envioRecepcionar = new Envio();
            $envioRecepcionar->setCodTracking($envio->codTracking);
            //..

            //Coger la del user autenticado
            $estructuraOrigen = new Estructura();

            $direccion = new EnvioDireccion();
            $direccion->setCalle();
            //....


        }




        return $recepcionados;
    }

}