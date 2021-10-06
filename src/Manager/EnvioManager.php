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
     * @return ?EnvioManifiesto
     */
    public function obtnerEnvioManifestado(string $noGuia, string $codTracking): ?EnvioPreRecepcion
    {
        $envioPreRecepcion = new EnvioPreRecepcion();
        $envioManifestado = $this->envioManifiestoRepository->findByGuiaAndCodigo($noGuia,$codTracking);

        dump('envio manifestado');
        dump($envioManifestado);

        if ( $envioManifestado ) {

            $envioPreRecepcion->setNoGuia($envioManifestado->getNoGuiaAerea());
            $envioPreRecepcion->setCodTracking($envioManifestado->getCodigo());
            $envioPreRecepcion->setPeso($envioManifestado->getPeso());

            dump('prueba 123');

            //Comprobar que sea asi por codigo de aduana
            $envioPreRecepcion->setPaisOrigen($envioManifestado->getPaisOrigen());
            //Hace el metodo para que busque la agencia a partir del codigo dado por manifiesto
            $envioPreRecepcion->setAgencia(


            );
            //Comprobar q sea asi realmente
            $envioPreRecepcion->setEntidadCtrlAduana($envioManifestado->isInteresAduana());

            //$municipioTemp = //$this->localizacionRepository->findOneMunicipioPOrCodAduanaYCodAduanaProv($envioManifestado->getProvinciaDestinatario(),$envioManifestado->getMunicipioDestinatario())

            //Obtener municipio a partir del codigo dado por manifiesto
            $envioPreRecepcion->setMunicipio(
                //$municipioTemp->getCodigo();
                null
            );
            $envioPreRecepcion->setProvincia(
                //$municipioTemp->getParent()->getCodigo();
                $envioPreRecepcion->getMunicipio()
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
            $direccion->setMunicipio(
            //$this->localizacionRepository->findOneMunicipioPOrCodAduanaYCodAduanaProv($envioManifestado->getProvinciaDestinatario(),$envioManifestado->getMunicipioDestinatario())
                null
            );
            $direcciones = [];
            $direcciones[] = $direccion;

            $envioPreRecepcion->setDirecciones($direcciones);

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