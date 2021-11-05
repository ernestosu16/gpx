<?php

namespace App\Controller;

use App\Entity\Envio;
use App\Entity\EnvioAduana;
use App\Entity\EnvioManifiesto;
use App\Entity\Estructura;
use App\Entity\EstructuraTipo;
use App\Entity\Nomenclador;
use App\Entity\Saca;
use App\Entity\SacaConsecutivo;
use App\Entity\SacaTraza;
use App\Entity\Trabajador;
use App\Manager\EnvioManager;
use App\Repository\NomencladorRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use SoapClient;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\throwException;
use function Sodium\add;

#[Route('/saca')]
class SacaController extends AbstractController
{
    public function __construct(
        private EnvioManager $envioManager
    )
    {
    }

    #[Route('/imprimir/{id}/', name: 'imprimir_saca')]
    public function Imprimir($id): Response
    {

        $em = $this->getDoctrine()->getManager();
        /** @var Saca $saca */
        $saca = $em->getRepository(Saca::class)->find($id);
        $envios = $em->getRepository(Envio::class)->findBy(['saca' => $id]);
        $fecha = $saca->getFechaCreacion()->format('d-m-Y');
        $numero = -1;
        return $this->render('saca/imprimir_saca.html.twig', [
            'envios' => $envios,
            'saca' => $saca,
            'fecha' => $fecha,
            'numero' => $numero
        ]);
    }

    #[Route('/crear', name: 'crear_saca')]
    public function CrearSaca(): Response
    {
        $empresas = new ArrayCollection();
        $em = $this->getDoctrine()->getManager();
        /** @var Trabajador $user */
        $user = $this->getUser();

        $empresa = $user->getEstructura()->searchParentsByTipo(
            $em->getRepository(EstructuraTipo::class)->findOneByCodigo(EstructuraTipo::EMPRESA)
        );

        /** @var Estructura $item */
        foreach ($empresa->getChildren()->toArray() as $item){
            /** @var EstructuraTipo $i */
            foreach ($item->getTipos()->getValues() as $i){
                if ($i->getCodigo() == 'OFICINA_CCP'){
                    $empresas->add($empresa);
                }
            }

            //if ($item->getTipos()->getValues() == )
        }

        //$oficina = $em->getRepository(Estructura::class)->findAll();
        return $this->render('saca/crear_saca.html.twig', [
            'findAll' => $empresas
        ]);
    }

    #[Route('/Annadir', options: ["expose" => true], name: 'Annadir', methods: ['POST'])]
    public function AnndirEnvio(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $codTracking = $request->request->get('codTracking');
            $oficina_dest = $request->request->get('oficina_dest');

            /** @var Trabajador $user */
            $user = $this->getUser();

            /** @var Estructura $estructura */
            $empresa = $em->getRepository(Estructura::class)->find($user->getEstructura()->getId());

            /** @var Estructura $ofic_dest */
            $ofic_dest = $em->getRepository(Estructura::class)->find($oficina_dest);

            /** @var Envio $envio */
            $envio = $em->getRepository(Envio::class)->findOneBy(['cod_tracking' => $codTracking]);
            //dump($ofic_dest);exit();
            $respuesta = '';

            if ($envio != null) {
                if ($envio->getEstado()->getCodigo() != 'APP_ENVIO_ESTADO_CLASIFICADO' && $envio->getEstado()->getCodigo() != 'APP_ENVIO_ESTADO_FACTURADO') {
                    if ($envio->getMunicipio()->getId() == $ofic_dest->getMunicipio()->getId()) {

                        $id = $envio->getId();
                        $cod = $envio->getCodTracking();
                        $peso = $envio->getPeso();

                        $serializer = SerializerBuilder::create()->build();
                        $miRespuestaJson = $serializer->serialize(['id' => $id, 'cod' => $cod, 'peso' => $peso], "json");

                        /** @var EnvioAduana $envio_aduana */
                        $envio_aduana = $em->getRepository(EnvioAduana::class)->findOneBy(['envio'=>$id]);
                        //dump($envio_aduana);exit();
                        if ($envio_aduana != null){
                            if ($envio_aduana->getDatosDespacho() == null){
                                $url= "https://sua.aduana.cu/GINASUA/serviciosExternos?wsdl";

                                if ($this->envioManager->verificarConectAduana($url) == 1){
                                    if ($this->envioManager->addDespachoAduanaEnvio($url, $envio_aduana->getId(), $cod, $empresa)){
                                        return JsonResponse::fromJsonString($miRespuestaJson);
                                    }else{
                                        $respuesta = 'El servicio del despacho de la aduana no está funcionando, por favor intentelo mas tarde.';
                                    }
                                }else{
                                    $respuesta = 'La conexión con el servicio de aduana esta tardando mucho, por favor intentelo mas tarde.';
                                }
                            }else{
                                return JsonResponse::fromJsonString($miRespuestaJson);
                            }
                        }else{
                            $respuesta = 'El envío aduana no exite';
                        }

                    } else {
                        $respuesta = 'El envío esta mal reeencaminado';
                    }
                } else {
                    $respuesta = 'El envío ya esta clasificado o facturado';
                }
            } else {
                $respuesta = 'El envío no se encuentra en el sistema';
            }

            return new JsonResponse(['respuesta' => true, 'mensaje' => $respuesta]);

        } else {
            throwException('Hacker');
        }
    }

    #[Route('/Guardar', name: 'Guardar', options: ["expose" => true], methods: ['POST'])]
    public function Guardar(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $saca = new Saca();

            /** @var NomencladorRepository $nomencladorRepository */
            $nomencladorRepository = $em->getRepository(Nomenclador::class);

            /** @var Estructura $estructura */
            $estructura_destino = $em->getRepository(Estructura::class)->find($request->request->get('oficina'));

            /** @var Trabajador $user */
            $user = $this->getUser();

            /** @var Trabajador $trabajador */
            $trabajador = $em->getRepository(Trabajador::class)->findOneBy(['persona' => $user->getPersona()->getId()]);

            $estructura_origen = $user->getEstructura();

            /** @var Nomenclador $estado */
            $estado = $em->getRepository(Nomenclador::class)->findOneBy(['codigo' => 'APP_SACA_ESTADO_CREADA']);

            if (!$estado)
                return new JsonResponse(['error' => 'Error el estado de la saca "CREADO" no existe'], 500);

            /** @var Nomenclador $tipo */
            $tipo = $nomencladorRepository->findOneByCodigo('APP_SACA_TIPO_EMBALAJE_SACA');

            if (!$tipo)
                return new JsonResponse(['error' => 'Error el en tipo de embalaje "SACA" no existe'], 500);

            $listado = $request->request->get('list');

            $anno = new DateTime();

            /** @var SacaConsecutivo $consecutiva */
            $consecutiva = $em->getRepository(SacaConsecutivo::class)->findOneBy(['oficina_origen' => $estructura_origen, 'oficina_destino' => $estructura_destino]);

            if ($consecutiva === null) {
                $consecutiva = new SacaConsecutivo();
                $consecutiva->setOficinaOrigen($estructura_origen);
                $consecutiva->setOficinaDestino($estructura_destino);
                $consecutiva->setAnno($anno);
                $consecutiva->setNumero(1);

            } else {
                $consecutiva->setNumero($consecutiva->getNumero() + 1);
            }

            $em->persist($consecutiva);

            $codSaca = $estructura_origen->getCodigoPostal() . $estructura_destino->getCodigoPostal() . $anno->format('Y') . $consecutiva->getNumero();

            /** @var Nomenclador $estClasificada */
            $estClasificada = $em->getRepository(Nomenclador::class)->findOneBy(['codigo' => 'APP_ENVIO_ESTADO_CLASIFICADO']);

            if (!$estClasificada)
                return new JsonResponse(['error' => 'Error el estado del envío "CLASIFICADO" no existe'], 500);

            foreach ($listado as $item) {
                /** @var Envio $envio */
                $envio = $em->getRepository(Envio::class)->find($item);
                $envio->setSaca($saca);
                $envio->setEstado($estClasificada);
                $em->persist($envio);
            }

            $date = new DateTime();

            /**
             * Saca
             */
            $saca->setFechaCreacion($date);
            $saca->setCodigo($codSaca);
            $saca->setOrigen($estructura_origen);
            $saca->setDestino($estructura_destino);
            $saca->setEstado($estado);
            $saca->setTipoEmbalaje($tipo);
            $saca->setSello($request->request->get('sello'));
            $saca->setPeso($request->request->get('peso'));
            $em->persist($saca);

            /**
             * Saca trazas
             */

            /** @var SacaTraza $sacaTraza */
            $sacaTraza = new SacaTraza();
            $sacaTraza->setFecha($date);
            $sacaTraza->setPeso($request->request->get('peso'));
            $sacaTraza->setEstado($estado);
            $sacaTraza->setSaca($saca);
            $sacaTraza->setEstructura($estructura_destino);
            $sacaTraza->setTrabajador($trabajador);
            $sacaTraza->setIp('');
            $em->persist($sacaTraza);

            $em->flush();

            $id = $saca->getId();

            return new JsonResponse($id);
        } else {
            throwException('Hacker');
        }
    }


}
