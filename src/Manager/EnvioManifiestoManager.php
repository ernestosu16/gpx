<?php


namespace App\Manager;

use App\Entity\EnvioManifiesto;
use Doctrine\ORM\EntityManagerInterface;
use App\Utils\Validator;

class EnvioManifiestoManager extends _Manager_
{
    /**
     * @var EntityManagerInterface
     */
    protected $doctrineEntityManager;

    public function createEnvioManifiesto(EnvioManifiesto $envioManifiesto)
    {
        /** @var $em EntityManagerInterface **/
        $em = $this->get('doctrine.orm.default_entity_manager');
        $em->persist($envioManifiesto);
        $em->flush();
        return $envioManifiesto;
    }

    public function validarEnvioManifiesto(EnvioManifiesto $envioManifiesto){
        $valido = true;
        if($envioManifiesto->getAgenciaOrigen() != 'COPA' && $envioManifiesto->getAgenciaOrigen() != 'DHL'){
            if($envioManifiesto->getDescripcion() == ''){
                $valido = false;
                dump('Descripcion vacia '.$envioManifiesto->getDescripcion());
            }

            if($envioManifiesto->getPaisOrigen() == 'CUB'){
                dump('Pais de origen no puede ser '.$envioManifiesto->getPaisOrigen());
                $valido = false;
            }

            if ($envioManifiesto->getDestinatario()->getNacionalidad() == "CUB")
            {
                $valido = Validator::validarCI($envioManifiesto->getDestinatario()->getNumeroIdentidad())["valid"];
                if(!$valido){
                    dump('Carnet de identidad del destinatario no valido'.$envioManifiesto->getDestinatario()->getNumeroIdentidad());
                }
            }

            if($envioManifiesto->getCodigo() == ''){
                $valido = false;
                dump('Codigo vacia '.$envioManifiesto->getCodigo());
            }

            if ($envioManifiesto->getDestinatario()->getNombrePrimero() == "" || Validator::tieneCaracteresEspeciales($envioManifiesto->getDestinatario()->getNombrePrimero()))
            {
                $valido = false;
                dump('NombrePrimero de destinatario no valido '.$envioManifiesto->getDestinatario()->getNombrePrimero());
            }

            if ($envioManifiesto->getDestinatario()->getApellidoPrimero() == "" || Validator::tieneCaracteresEspeciales($envioManifiesto->getDestinatario()->getApellidoPrimero()))
            {
                $valido = false;
                dump('ApellidoPrimero de destinatario no valido '.$envioManifiesto->getDestinatario()->getApellidoPrimero());
            }

            if ($envioManifiesto->getRemitente()->getNombrePrimero() == "" || Validator::tieneCaracteresEspeciales($envioManifiesto->getRemitente()->getNombrePrimero()))
            {
                $valido = false;
                dump('NombrePrimero de remitente no valido '.$envioManifiesto->getRemitente()->getNombrePrimero());
            }

            if ($envioManifiesto->getRemitente()->getApellidoPrimero() == "" || Validator::tieneCaracteresEspeciales($envioManifiesto->getRemitente()->getApellidoPrimero()))
            {
                $valido = false;
                dump('ApellidoPrimero de destinatario no valido '.$envioManifiesto->getRemitente()->getApellidoPrimero());
            }

            if (Validator::validarFecha($envioManifiesto->getRemitente()->getFechaNacimiento()) == false) $valido = false;

            if (Validator::validarFecha($envioManifiesto->getDestinatario()->getFechaNacimiento()) == false) $valido = false;
        }

        return $valido;
    }
}