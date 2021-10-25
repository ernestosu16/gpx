<?php


namespace App\Controller;

use App\Entity\Envio;
use App\Entity\Estructura;
use App\Entity\Factura;
use App\Entity\FacturaConsecutivo;
use App\Entity\Grupo;
use App\Entity\Nomenclador;
use App\Entity\Persona;
use App\Entity\Saca;
use App\Entity\Trabajador;
use App\Repository\FacturaConsecutivoRepository;
use App\Repository\NomencladorRepository;
use App\Repository\SacaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\throwException;

#[Route('/factura')]
class FacturaController extends AbstractController
{
    public function __construct(
        private SacaRepository $sacaRepository,
        private NomencladorRepository $nomencladorRepository
    )
    {
    }

    #[Route('/procesar', name: 'procesar_factura', methods: ['GET'])]
    public function procesarFactura()
    {
        $anomalias = $this->nomencladorRepository->findByChildren('APP_SACA_ANOMALIA');
        return $this->render('factura/procesarFactura.html.twig',[
            "anomalias" => $anomalias,
        ]);
    }

    #[Route('/factura/find-sacas-factura', name: 'find_sacas_factura', options: ["expose" => true] ,methods: ['POST'])]
    public function findSacasFactura(Request $request)
    {
        $noFactura = $request->get('noFactura');
        $sacas = $this->sacaRepository->findSacasNoFactura($noFactura);
        $anomalias = $this->nomencladorRepository->findByChildren('APP_SACA_ANOMALIA');
/*
        $serializer = SerializerBuilder::create()->build();
        $miRespuestaJson = $serializer->serialize($sacas,"json");

        return JsonResponse::fromJsonString($miRespuestaJson);*/

        $html = $this->renderView('factura/sacas.html.twig', ['sacas'=>$sacas, 'anomalias'=>$anomalias->toArray()]);

        return new Response($html);
    }

    #[Route('/crear_factura', name: 'crear_factura')]
    public function CrearFactura(): Response
    {
        $choferes = new ArrayCollection();
        $em = $this->getDoctrine()->getManager();
        $oficinas = $em->getRepository(Estructura::class)->findAll();
        /** @var Nomenclador $nom */
        $nom = $em->getRepository(Nomenclador::class)->findOneByCodigo('APP_TIPO_VEHICULO');
        $vehiculos = $nom->getChildren()->getValues();

        /** @var Trabajador $trabajador */
        $trabajador = $em->getRepository(Trabajador::class)->findAll();
        //$choferes = $trabajador->getPersona();

        foreach ($trabajador as $item){
            /** @var Trabajador $item */
            foreach ($item->getGrupos()->getValues() as $g){
                /** @var Grupo $g */
                if ($g->getCodigo() == 'GRUPO_ADMINISTRADOR'){
                    $id = $item->getPersona()->getId();
                    /** @var Persona $c */
                    $c = $em->getRepository(Persona::class)->find($id);
                    $choferes->add($c);
                }
            }

        }

        return $this->render('factura/crear_factura.html.twig', [
            'findAll' => $oficinas,
            'vehiculos' => $vehiculos,
            'choferes' => $choferes

        ]);
    }

    #[Route('/AnnadirSaca', options: ["expose" => true], name: 'AnnadirSaca', methods: ['POST'])]
    public function Annadir(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $sello = $request->request->get('codTracking');
            $oficina_dest = $request->request->get('oficina_dest');

            /** @var Saca $saca */
            $saca = $em->getRepository(Saca::class)->findOneBy(['sello'=>$sello]);

            //dump($saca->getEstado()).exit();

            if ($saca == null){
                /** @var Envio $saca */
                $saca = $em->getRepository(Envio::class)->findOneBy(['cod_tracking'=>$sello]);

                if ($saca != null){
                    if ($saca->getEstado()->getCodigo() != 'APP_ENVIO_ESTADO_CLASIFICADO' && $saca->getEstado()->getCodigo() != 'APP_ENVIO_ESTADO_FACTURADO'){
                        if ($saca->getEstructuraDestino()->getId() == $oficina_dest){
                            $id = $saca->getId();
                            $cod = $saca->getCodTracking();
                            $peso = $saca->getPeso();

                            $serializer = SerializerBuilder::create()->build();
                            $miRespuestaJson = $serializer->serialize(['id'=>$id, 'cod'=>$cod, 'peso'=>$peso],"json");

                            return JsonResponse::fromJsonString($miRespuestaJson);
                        }else{
                            $respuesta = 'El envío esta mal reeencaminado';
                        }
                    }else{
                        $respuesta = 'El envío ya esta clasificado o facturado';
                    }
                }else{
                    $respuesta = 'El envío no se encuentra en el sistema';
                }
            }else{
                if ($saca != null){
                    if ($saca->getEstado()->getCodigo() == 'APP_ENVIO_SACA_ESTADO_CREADA'){
                        if ($saca->getDestino()->getId() == $oficina_dest){
                            $id = $saca->getId();
                            $cod = $saca->getSello();
                            $peso = $saca->getPeso();

                            $serializer = SerializerBuilder::create()->build();
                            $miRespuestaJson = $serializer->serialize(['id'=>$id, 'cod'=>$cod, 'peso'=>$peso],"json");

                            return JsonResponse::fromJsonString($miRespuestaJson);
                        }else{
                            $respuesta = 'La saca esta mal reeencaminado';
                        }
                    }else{
                        $respuesta = 'La saca ya está facturada';
                    }
                }else{
                    $respuesta = 'La saca no se encuentra en el sistema';
                }
            }



            return new JsonResponse(['respuesta'=>true ,'mensaje'=>$respuesta]);

        } else {
            throwException('Hacker');
        }
    }

    #[Route('/GuardarFactura', options: ["expose" => true], name: 'GuardarFactura', methods: ['POST'])]
    public function Guardar(Request $request)
    {
        $factura = new Factura();
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            /** @var NomencladorRepository $nomencladorRepository */
            $nomencladorRepository = $em->getRepository(Nomenclador::class);

            /** @var Estructura $estructura_destino */
            $estructura_destino = $em->getRepository(Estructura::class)->find($request->request->get('oficina'));

            /** @var Trabajador $chofer */
            $chofer = $em->getRepository(Trabajador::class)->findOneBy(['persona' => $request->request->get('chofer')]);

            /** @var Nomenclador $estado */
            $estado = $em->getRepository(Nomenclador::class)->findOneBy(['codigo'=>'APP_ENVIO_FACTURA_ESTADO_CREADA']);

            /** @var Nomenclador $tipo_vehiculo */
            $tipo_vehiculo = $em->getRepository(Nomenclador::class)->find($request->request->get('tipo_vehiculo'));

            /** @var Trabajador $user */
            $user = $this->getUser();

            /** @var Trabajador $usuario */
            $usuario = $em->getRepository(Trabajador::class)->findOneBy(['persona' => $user->getPersona()->getId()]);

            $estructura_origen = $user->getEstructura();

            $anno = new \DateTime();

            /** @var FacturaConsecutivo $consecutiva */
            $consecutiva = $em->getRepository(FacturaConsecutivo::class)->findOneBy(['oficina_origen'=>$estructura_origen, 'oficina_destino'=>$estructura_destino]);
            if ($consecutiva == null){
                $facturaConsecutiva = new FacturaConsecutivo();
                $facturaConsecutiva->setOficinaOrigen($estructura_origen);
                $facturaConsecutiva->setOficinaDestino($estructura_destino);
                $facturaConsecutiva->setAnno($anno);
                $facturaConsecutiva->setNumero(1);
                $em->persist($facturaConsecutiva);
                $numFactura = 1;
            }else{
                $numFactura = $consecutiva->getNumero() + 1;
                $consecutiva->setNumero($numFactura);
            }
            $em->persist($consecutiva);

            $estEnvioFacturada = $em->getRepository(Nomenclador::class)->findOneBy(['codigo'=>'APP_ENVIO_ESTADO_FACTURADO']);
            $estSacaFacturada = $em->getRepository(Nomenclador::class)->findOneBy(['codigo'=>'APP_SACA_ESTADO_FACTURADA']);

            $codFactura = $estructura_origen->getCodigoPostal() . $estructura_destino->getCodigoPostal() . $numFactura . $anno->format('Y');
            //dump($codFactura).exit();
            $listado = $request->request->get('list');
            //dump($listado).exit();
            foreach ($listado as $item) {
                /** @var Saca $saca */
                $saca = $em->getRepository(Saca::class)->find($item);

                if ($saca == null){
                    /** @var Envio $envio */
                    $envio = $em->getRepository(Envio::class)->find($item);
                    $envio->setFactura($factura);
                    $envio->setEstado($estEnvioFacturada);
                    $em->persist($envio);
                }else{
                    $saca->setFactura($factura);
                    $saca->setEstado($estSacaFacturada);
                    $em->persist($saca);
                }
            }

            $date = new \DateTime();
            $factura->setFecha($date);
            $factura->setNumeroFactura($numFactura);
            $factura->setCodigoFactura($codFactura);
            $factura->setChofer($chofer);
            $factura->setChapaVehiculo($request->request->get('chapa'));
            $factura->setEstado($estado);
            $factura->setTipoVehiculo($tipo_vehiculo);
            $factura->setTrabajador($usuario);
            $factura->setOrigen($estructura_origen);
            $factura->setDestino($estructura_destino);

            $em->persist($factura);
            $em->flush();

            $id_factura = $factura->getId();

            return new JsonResponse($id_factura);
        } else {
            throwException('Hacker');
        }
    }

    #[Route('/imprimir/{id}', name: 'imprimir_factura')]
    public function Imprimir($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Factura $factura */
        $factura = $em->getRepository(Factura::class)->find($id);
        $fecha = $factura->getFecha()->format('d-m-Y');
        $sacas = $em->getRepository(Saca::class)->findBy(['factura'=>$id]);
        $envios = $em->getRepository(Envio::class)->findBy(['factura'=>$id]);

        return $this->render('factura/imprimir_factura.html.twig', [
            'factura' => $factura,
            'fecha' => $fecha,
            'sacas' => $sacas,
            'envios' => $envios
        ]);
    }
}