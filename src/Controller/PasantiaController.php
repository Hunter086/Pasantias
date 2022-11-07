<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//entities
use App\Entity\Pasantia;
use App\Entity\Pasante;
use App\Entity\Pago;
use App\Entity\Vencimiento;

//forms
use App\Form\ActaCompromisoPaso9Type;
//repo
use App\Repository\VencimientoRepository;


use Symfony\Component\HttpFoundation\Request;

use App\Form\Paso1EnviarInformacionPasantiaType;
use App\Form\Paso2RecibiryVerificarDocumentacionType;
use App\Form\Paso3SolicitarAprobaciondepasantiaType;
use App\Form\Paso4AbrirExpedientePasantiaType;
use App\Form\Paso5ContactoOrganizacionType;
use App\Form\Paso6CargarConvenioType;
use App\Form\Paso7InicioConvocatoriaType;
use App\Form\Paso8EnviodeNominasyDocumentacionType;
use App\Form\Paso9ActaCompromisoType;
use App\Form\Paso10CargarPasante;
use App\Form\BuscarPasanteType;
use App\Form\CargarPasanteType;
use App\Form\FechaRenovacionType;
use App\Form\RenovarPasantiaType;
use App\Form\SeguimientoOrganizacionType;

/**
     * @Route("/admin")
     * La implementació de los formularios se utilizo un swich con un contador. El mismo se encuentra al final
     * del código.
     * En los twig cada paso esta detallado en los nombres.
     */
class PasantiaController extends AbstractController
{
    /**
        * @Route("/nuevaPasantia", name="nuevaPasantia")
    */
    public function nuevaPasantia(Request $request)
    {
        /**if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this -> addFlash('error', '¡No tiene acceso a esta página!');
            return $this->redirect('https://intranet.unraf.edu.ar/');
        }*/
        $manager= $this->getDoctrine()->getManager();
        $dateTime = new \DateTime();
        $fechaActual = $dateTime->format('d-m-Y');
        $username = $this->getUser();
        $pasantia = new Pasantia();
        $formulario = $this->createForm(Paso1EnviarInformacionPasantiaType::class, $pasantia);
        $formulario->handleRequest($request);
        if ($formulario->isSubmitted() && $formulario->isValid()) {
            try {
                //cargamos la pasantia
                $pasantia->setFechaModificacion($dateTime);
                $pasantia->setUltimoUsuario("".$username->getUsername());
                $pasantia->setPasos(2);
                $pasantia->setFechaInicio($dateTime);
                $pasantia->setFechaFin($dateTime);
                $pasantia->setFechaInicioTramite($dateTime);
                $pasantia->setFechaFinTramite($dateTime);
                $pasantia->setfechaUltimaModificacion($dateTime);
                #Setear Pasos Siguientes 
                $pasantia->setEstado('En Proceso');
                $manager->persist($pasantia);
                $manager->flush();
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al cargar los datos!'.$th);
                return $this->redirectToRoute('nuevaPasantia');
            }                     
            

            $this -> addFlash('info', '¡Documentación recibida correctamente!');
            return $this->redirectToRoute('recibiryVerificarDocumentacion',['id'=> $pasantia->getId()]);
        }

        return $this->render('pasantia/pasos/Paso1enviarInformaciondePasanti.html.twig', [
            'formulario' => $formulario->createView(),
        ]);
    }
    /**
     * @Route("/recibiryVerificarDocumentacion/{id}", name="recibiryVerificarDocumentacion")
     */
    public function recibiryVerificarDocumentacion(Request $request, $id)
    {
            $manager=$this->getDoctrine()->getManager();
            $dateTime = new \DateTime();
            $username = $this->getUser();
            $pasantia= $manager->getRepository(Pasantia::class)->find($id);
            $formulario = $this->createForm(Paso2RecibiryVerificarDocumentacionType::class,$pasantia);
            $formulario->handleRequest($request);
            $pasantia->setPasos(2);
            $manager->flush();

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            try {
                $pasantia->setFechaModificacion($dateTime);
                $pasantia->setUltimoUsuario("".$username->getUsername());
                $pasantia->setPasos(3);
                $pasantia->setfechaUltimaModificacion($dateTime);
                
                $manager->persist($pasantia);
                $manager->flush();
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al cargar los datos!'.$th);
                return $this->render('pasantia/pasos/Paso2recibiryVerificarDocumentacion.html.twig', [
                    'formulario' => $formulario->createView(),'pasantia' => $pasantia,
                ]);
            }                     
            

            $this -> addFlash('info', '¡Datos cargados exitosamente!');
            return $this->redirectToRoute('solicitarAprobaciondepasantia',['id'=> $pasantia->getId()]);
        }

        return $this->render('pasantia/pasos/Paso2recibiryVerificarDocumentacion.html.twig', [
            'formulario' => $formulario->createView(),
        ]);
    }
    /**
     * @Route("/solicitarAprobaciondepasantia/{id}", name="solicitarAprobaciondepasantia")
     */
    public function solicitarAprobaciondepasantia(Request $request, $id)
    {
            $manager=$this->getDoctrine()->getManager();
            $dateTime = new \DateTime();
            $username = $this->getUser();
            $pasantia= $manager->getRepository(Pasantia::class)->find($id);
            $formulario = $this->createForm(Paso3SolicitarAprobaciondepasantiaType ::class,$pasantia);
            $formulario->handleRequest($request);
            $pasantia->setPasos(3);
            $manager->flush();

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            try {
                if($pasantia->getEstado()=='Rechazada'){
                    
                    return $this->redirectToRoute('pasantiaRechazada',['id'=> $pasantia->getId()]);
                }
                if (isset($_POST['solicitudrechazada'])) {

                    $pasantia->setEstado('Rechazada');
                    $pasantia->setPasos(4);
                    $manager->persist($pasantia);
                    $manager->flush();
                    return $this->redirectToRoute('pasantiaRechazada',['id'=> $pasantia->getId()]);
                } else {
                    $pasantia->setFechaModificacion($dateTime);
                    $pasantia->setUltimoUsuario("".$username->getUsername());
                    $pasantia->setfechaUltimaModificacion($dateTime);
                    $pasantia->setPasos(4);
                    
                    $manager->persist($pasantia);
                    $manager->flush();
                }
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al cargar los datos!'.$th);
                return $this->render('pasantia/pasos/Paso3solicitarAprobaciondepasantia.html.twig', [
                    'formulario' => $formulario->createView(),'pasantia' => $pasantia,
                ]);
            }                     
            
            
            $this -> addFlash('info', '¡Datos cargados exitosamente!');
            return $this->redirectToRoute('abrirExpedientePasantia',['id'=> $pasantia->getId()]);
        }

        return $this->render('pasantia/pasos/Paso3solicitarAprobaciondepasantia.html.twig', [
            'formulario' => $formulario->createView(),'pasantia' => $pasantia,
        ]);
    }
    /**
     * @Route("/pasantiaRechazada/{id}", name="pasantiaRechazada")
     */
    public function pasantiaRechazada(Request $request, $id)
    {
        $manager=$this->getDoctrine()->getManager();

        $pasantia= $manager->getRepository(Pasantia::class)->find($id);
        return $this->render('pasantia/verDatosPasantiaRechazada.html.twig', ['pasantia' => $pasantia]
            );
    }
    /**
     * @Route("/abrirExpedientePasantia/{id}", name="abrirExpedientePasantia")
     */
    public function abrirExpedientePasantia(Request $request, $id)
    {
            $manager=$this->getDoctrine()->getManager();
            $dateTime = new \DateTime();
            $username = $this->getUser();
            $pasantia= $manager->getRepository(Pasantia::class)->find($id);
            $formulario = $this->createForm(Paso4AbrirExpedientePasantiaType::class,$pasantia);
            $formulario->handleRequest($request);
            $pasantia->setPasos(4);
            $manager->flush();

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            try {
                $pasantia->setFechaModificacion($dateTime);
                $pasantia->setUltimoUsuario("".$username->getUsername());
                $pasantia->setfechaUltimaModificacion($dateTime);
                $pasantia->setPasos(5);
                
                $manager->persist($pasantia);
                $manager->flush();
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al cargar los datos!'.$th);
                return $this->render('pasantia/pasos/Paso4abrirExpedientePasantia.html.twig', [
                    'formulario' => $formulario->createView(),'pasantia' => $pasantia,
                ]);
            }                     
            

            $this -> addFlash('info', '¡Datos cargados exitosamente!');
            return $this->redirectToRoute('contactoOrganizacion',['id'=> $pasantia->getId()]);
        }

        return $this->render('pasantia/pasos/Paso4abrirExpedientePasantia.html.twig', [
            'formulario' => $formulario->createView(),'pasantia' => $pasantia,
        ]);
    }
    /**
     * @Route("/contactoOrganizacion/{id}", name="contactoOrganizacion")
     */
    public function contactoOrganizacion(Request $request, $id)
    {
            $manager=$this->getDoctrine()->getManager();
            $dateTime = new \DateTime();
            $username = $this->getUser();
            $pasantia= $manager->getRepository(Pasantia::class)->find($id);
            $formulario = $this->createForm(Paso5ContactoOrganizacionType::class,$pasantia);
            $formulario->handleRequest($request);
            $pasantia->setPasos(5);
            $manager->flush();

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            try {
                $pasantia->setFechaModificacion($dateTime);
                $pasantia->setUltimoUsuario("".$username->getUsername());
                $pasantia->setfechaUltimaModificacion($dateTime);
                $pasantia->setPasos(6);
                
                
                $manager->persist($pasantia);
                $manager->flush();
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al cargar los datos!'.$th);
                return $this->render('pasantia/pasos/Paso5contactoOrganizacion.html.twig', [
                    'formulario' => $formulario->createView(),'pasantia' => $pasantia,
                ]);
            }                     
            

            $this -> addFlash('info', '¡Datos cargados exitosamente!');
            return $this->redirectToRoute('cargarConvenio',['id'=> $pasantia->getId()]);
        }

        return $this->render('pasantia/pasos/Paso5contactoOrganizacion.html.twig', [
            'formulario' => $formulario->createView(),'pasantia' => $pasantia,
        ]);
    }
     /**
     * @Route("/cargarConvenio/{id}", name="cargarConvenio")
     */
    public function cargarConvenio(Request $request, $id)
    {
            $manager=$this->getDoctrine()->getManager();
            $dateTime = new \DateTime();
            $username = $this->getUser();
            $pasantia= $manager->getRepository(Pasantia::class)->find($id);
            $formulario = $this->createForm(Paso6CargarConvenioType::class,$pasantia);
            $formulario->handleRequest($request);
            $pasantia->setPasos(6);
            $manager->flush();

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            try {
                $pasantia->setFechaModificacion($dateTime);
                $pasantia->setUltimoUsuario("".$username->getUsername());
                $pasantia->setfechaUltimaModificacion($dateTime);
                $pasantia->setPasos(7);
                
                $manager->persist($pasantia);
                $manager->flush();
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al cargar los datos!'.$th);
                return $this->render('pasantia/pasos/Paso6cargarConvenio.html.twig', [
                    'formulario' => $formulario->createView(),'pasantia' => $pasantia,
                ]);
            }                     
            

            $this -> addFlash('info', '¡Datos cargados exitosamente!');
            return $this->redirectToRoute('iniciarConvocatoria',['id'=> $pasantia->getId()]);
        }

        return $this->render('pasantia/pasos/Paso6cargarConvenio.html.twig', [
            'formulario' => $formulario->createView(),'pasantia' => $pasantia,
        ]);
    }
    /**
     * @Route("/iniciarConvocatoria/{id}", name="iniciarConvocatoria")
     */
    public function iniciarConvocatoria(Request $request, $id)
    {
            $manager=$this->getDoctrine()->getManager();
            $dateTime = new \DateTime();
            $username = $this->getUser();
            $pasantia= $manager->getRepository(Pasantia::class)->find($id);
            
            $pasantia->setPasos(7);
            $manager->flush();

            $formulario = $this->createForm(Paso7InicioConvocatoriaType::class,$pasantia);
            $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            try {
                
                $pasantia->setFechaModificacion($dateTime);
                $pasantia->setUltimoUsuario("".$username->getUsername());
                $pasantia->setfechaUltimaModificacion($dateTime);
                $pasantia->setPasos(8);
           
                $manager->persist($pasantia);
                $manager->flush();
                
            $this -> addFlash('info', '¡Datos cargados exitosamente!');
            return $this->redirectToRoute('enviodeNominasyDocumentacion',['id'=> $pasantia->getId()]);
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al cargar los datos!'.$th);
                return $this->render('pasantia/pasos/Paso7iniciarConvocatoria.html.twig', [
                    'formulario' => $formulario->createView(),'pasantia'=> $pasantia]);
            }                     
            

        }

        return $this->render('pasantia/pasos/Paso7iniciarConvocatoria.html.twig', [
            'formulario' => $formulario->createView(),'pasantia'=> $pasantia]);
    }

    /**
     * @Route("/enviodeNominasyDocumentacion/{id}", name="enviodeNominasyDocumentacion")
     */
    public function enviodeNominasyDocumentacion(Request $request, $id)
    {
            $manager=$this->getDoctrine()->getManager();
            $dateTime = new \DateTime();
            $username = $this->getUser();
            $pasantia= $manager->getRepository(Pasantia::class)->find($id);
            
            $pasantia->setPasos(8);
            $manager->flush();

            $formulario = $this->createForm(Paso8EnviodeNominasyDocumentacionType::class,$pasantia);
            $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            try {
                
                $pasantia->setFechaModificacion($dateTime);
                $pasantia->setUltimoUsuario("".$username->getUsername());
                $pasantia->setfechaUltimaModificacion($dateTime);
                $pasantia->setPasos(9);
            
                $manager->persist($pasantia);
                $manager->flush();
                
            $this -> addFlash('info', '¡Datos cargados exitosamente!');
            return $this->redirectToRoute('actaCompromiso',['id'=> $pasantia->getId()]);
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al cargar los datos!'.$th);
                return $this->render('pasantia/pasos/Paso8EnviodeNominasyDocumentacion.html.twig', [
                    'formulario' => $formulario->createView(),'pasantia'=> $pasantia]);
            }                     
            

        }

        return $this->render('pasantia/pasos/Paso8EnviodeNominasyDocumentacion.html.twig', [
        'formulario' => $formulario->createView(),'pasantia'=> $pasantia]);
    }
    /**
     * @Route("/actaCompromiso/{id}", name="actaCompromiso")
     */
    public function actaCompromiso(Request $request, $id)
    {
            $manager=$this->getDoctrine()->getManager();
            $dateTime = new \DateTime();
            $username = $this->getUser();
            $pasantia= $manager->getRepository(Pasantia::class)->find($id);
            
            $pasantia->setPasos(9);
            $manager->flush();

            $formulario = $this->createForm(Paso9ActaCompromisoType::class,$pasantia);
            $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            
            try {
                    $pasantia->setPasos(10);     
                    $pasantia->setFechaModificacion($dateTime);
                    $pasantia->setUltimoUsuario("".$username->getUsername());
                    $pasantia->setfechaUltimaModificacion($dateTime);
                    
                    $manager->persist($pasantia);
                    $manager->flush();
                    
                    $this -> addFlash('info', '¡Datos cargados exitosamente!');
                    return $this->redirectToRoute('cargarPasante',['id'=> $pasantia->getId()]);
                
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al cargar los datos!'.$th);
                return $this->render('pasantia/pasos/Paso9actaCompromiso.html.twig', [
                    'formulario' => $formulario->createView(),'pasantia'=> $pasantia]);
            }                     
            

        }

        return $this->render('pasantia/pasos/Paso9actaCompromiso.html.twig', [
        'formulario' => $formulario->createView(),'pasantia'=> $pasantia]);
    }
    
    /**
     * @Route("/cargarPasante/{id}", name="cargarPasante")
     */
    public function cargarPasante(Request $request, $id)
    {
            $manager=$this->getDoctrine()->getManager();
            $dateTime = new \DateTime();
            $username = $this->getUser();
            $pasantia= $manager->getRepository(Pasantia::class)->find($id);
            /**Va a buscar los pasates cuya pasantia se Inactiva o Activa y sea de la misma pasantia */
            $pasantes= $this->findByPasantePasantia($pasantia); 

            $formulario = $this->createForm(Paso10CargarPasante::class,$pasantia);
            $formulario->handleRequest($request);
            $pasantia->setPasos(10);
            $manager->flush();

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            try {
                if(!$this->findByPasanteCantidad($pasantia)){
                    $this -> addFlash('error', '¡No ingreso datos a la tabla!');
                    return $this->render('pasantia/pasos/Paso10cargarPasante.html.twig', [
                    'formulario' => $formulario->createView(),'pasantia'=> $pasantia,'pasantes'=>  $pasantes]);
                }
                $pasantia->setFechaModificacion($dateTime);
                $pasantia->setUltimoUsuario("".$username->getUsername());
                $pasantia->setfechaUltimaModificacion($dateTime);
                $pasantia->setPasos(11);
                $manager->persist($pasantia);
                $manager->flush();
                //cargar venciomientos
                
            $this -> addFlash('info', '¡Datos cargados exitosamente!');
            return $this->redirectToRoute('finalizarCarga',['id'=> $pasantia->getId()]);
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al cargar los datos!'.$th);
                return $this->render('pasantia/pasos/Paso10cargarPasante.html.twig', [
                    'formulario' => $formulario->createView(),'pasantia'=> $pasantia,'pasantes'=>  $pasantes]);
            }                     
            

        }

        return $this->render('pasantia/pasos/Paso10cargarPasante.html.twig', [
            'formulario' => $formulario->createView(),'pasantia'=> $pasantia,'pasantes'=>  $pasantes]);
    }
    /**sacar los pasantes que esten inactivos y asignados a una pasantia determinada */
    public function findByPasantePasantia($pasantia){
            $manager=$this->getDoctrine()->getManager();
            $pasantesInactivos= $manager->getRepository(Pasante::class)->findby(array(
                'estadoPasante' => 'Inactivo'
              )       // $where          
            );
            $pasantesPasantia= $pasantia->getPasante()->toArray();
            $pasantes= array_merge($pasantesPasantia, $pasantesInactivos);
            return $pasantes;
    }
    /**sacar los pasantes que esten inactivos y que no esten en la pasantia */
    public function findByPasantiaToPasanteInactivo($pasantia){
        $manager=$this->getDoctrine()->getManager();
        $pasantesInactivos= $manager->getRepository(Pasante::class)->findby(array(
            'estadoPasante' => 'Inactivo'
          )       // $where          
        );
        $pasantesPasantia= $pasantia->getPasante()->toArray();
        foreach ($pasantesPasantia as $key => $value) {
            if($value->getEstadoPasante()=="Activo"){
                unset($pasantesPasantia[$key]);
            }
        }
        $pasantes= array_merge($pasantesPasantia, $pasantesInactivos);
        return $pasantes;
}
    /**Sacar si exiten pasantes cargados */
    public function findByPasanteCantidad($pasantia){
        $manager=$this->getDoctrine()->getManager();
        $pasantesPasantia= $pasantia->getPasante()->toArray();
        $cont= 0;
        foreach ($pasantesPasantia as $pasante) {
            if($pasante->getEstadoPasante()=="Activo"){
                $cont++;
            }
        }
        if($cont==0 || $pasantesPasantia== null ){
            return false;
        }
        return true;
}
    /**
     * buscador de palabras
     */
    /*public function buscar($formularioBuscador, $manager, $formulario, $pasantia)
    {
        try {
            $palabra= $formularioBuscador->get('search')->getData();
            $pasante = $manager->getRepository(Pasante::class)->createQueryBuilder('o')
            ->where('o.estadoPasante LIKE :estado and o.nombre LIKE :pasante or o.apellido LIKE :pasante or o.dni LIKE :pasante
            or o.legajo LIKE :pasante or o.cuil LIKE :pasante')
            ->setParameter('estado', 'Inactivo')
            ->setParameter('pasante', '%'.$palabra.'%')
            ->getQuery()
            ->getResult();
        } catch (\Throwable $th) {
            $this -> addFlash('error', '¡Error al buscar los datos!'.$th);
            return $this->render('pasantia/pasos/Paso10cargarPasante.html.twig', [
        'formulario' => $formulario->createView(),'pasantia'=> $pasantia,'pasante'=>  $pasante]);
        }
        return $this->render('pasantia/pasos/Paso10cargarPasante.html.twig', [
        'formulario' => $formulario->createView(),'pasantia'=> $pasantia,'pasante'=>  $pasante]);

    }*/


    /**
     * @Route("/agregarPasantedePasantia/{id}/{idPasante}", name="agregarPasantedePasantia")
     */
    public function agregarPasantedePasantia(Request $request, $id, $idPasante)
    {
            $manager=$this->getDoctrine()->getManager();
            $dateTime = new \DateTime();
            $username = $this->getUser();
            $pasantia= $manager->getRepository(Pasantia::class)->find($id);
            $pasante= $manager->getRepository(Pasante::class)->find($idPasante);
            $formulario = $this->createForm(CargarPasanteType::class,$pasantia);
            $formulario->handleRequest($request);
            

            try {
                $pasantia->setFechaModificacion($dateTime);
                $pasantia->setUltimoUsuario("".$username->getUsername());
                $pasante->setFechaSeguimiento($dateTime);
                $pasante->setIsSeguimientodelMes(false);
                $pasante->setIsInformeSeguimiento('No Realizado');
                $pasante->setEstadoPasante('Activo');
                $pasantia->addPasante($pasante);
                
                $manager->persist($pasantia);
                $manager->flush();
                $this -> addFlash('info', '¡Pasante Agregado exitosamente!');
                return $this->redirectToRoute('cargarPasante',['id'=> $pasantia->getId()]);
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al cargar los datos!'.$th);
                return $this->redirectToRoute('cargarPasante',['id'=> $pasantia->getId()]);
            }                     
        
    }
    /**
     * @Route("/eliminarPasanteCargado/{id}/{idPasante}", name="eliminarPasanteCargado")
     * quitamos de la lista los usuarios cargados
     */
    public function eliminarPasanteCargado(Request $request, $id, $idPasante)
    {
        $manager=$this->getDoctrine()->getManager();
        
        
        $pasante= $manager->getRepository(Pasante::class)->find($idPasante);
        $pasantia= $manager->getRepository(Pasantia::class)->find($id);
        
       try {

            $pasante->setEstadoPasante('Inactivo');
            $pasante->removePasantia($pasantia);
            $pasantia->removePasante($pasante);
            $manager->flush();
            $this -> addFlash('info', '¡Pasante Eliminado exitosamente!');
                return $this->redirectToRoute('cargarPasante',['id'=> $pasantia->getId()]);
       } catch (\Throwable $th) {
        $this -> addFlash('error', '¡Error al cargar los datos!'.$th);
        return $this->redirectToRoute('cargarPasante',['id'=> $pasantia->getId()]);
       }
       

       $this -> addFlash('info', '¡Pasante  Eliminado exitosamente!');
       return $this->redirectToRoute('cargarPasante',['id'=> $pasantia->getId()]);
    }
    /**
     * @Route("/eliminarPasantedePasantia/{id}/{idPasante}", name="eliminarPasantedePasantia")
     * damos de baja los pasantes si  se cumple la condicion de que tenga el ultimo seguimiento hecho, no tenga pagos sin completar y el informe este realizado
     */
    public function eliminarPasantedePasantia(Request $request, $id, $idPasante)
    {
        $manager=$this->getDoctrine()->getManager();
        
        
        $pasante= $manager->getRepository(Pasante::class)->find($idPasante);
        $pasantia= $manager->getRepository(Pasantia::class)->find($id);
        $empresa= $pasantia->getConvenio()->getEmpresa();
       try {
            if($pasante->getEstadoPasante()=="Inactivo"){
                $this -> addFlash('info', '¡El pasante ya se encuentra inactivo!');
                return $this->verDatosPasantiaCargada($request,$id);
            }
            if($pasante->getIsSeguimientodelMes()==false){
            $this -> addFlash('error', '¡Error aún quedan seguimientos de pasantes sin realizar!');
            return $this->verDatosPasantiaCargada($request,$id);
            }
            if($empresa->getIsSeguimientodelMes()==false){
                $this -> addFlash('error', '¡Error aún quedan seguimientos de organizaciones sin realizar!');
                return $this->verDatosPasantiaCargada($request,$id);
                }
            if($pasante->getIsInformeSeguimiento()=='No Realizado'){
            $this -> addFlash('error', '¡Error aún no ha realizado el Informe de Seguimiento!');
            return $this->verDatosPasantiaCargada($request,$id);
            }
            foreach ($pasante->getPago() as $pago) {
               if($pago->getEstado()=='No Abonado' || $pago->getEstado()=='Pagado' ){
                $this -> addFlash('error', '¡Error aún quedan pagos sin facturar!');
                return $this->verDatosPasantiaCargada($request,$id);
                
               }
            }
            $pasante->setIsSeguimientodelMes(false);
            $empresa->setIsSeguimientodelMes(false);
            $pasante->setEstadoPasante('Inactivo');
            $manager->flush();
            $this -> addFlash('info', '¡El pasante se ha eliminado correctamente!');
            return $this->verDatosPasantiaCargada($request,$id);
       } catch (\Throwable $th) {
        $this -> addFlash('error', '¡Error al eliminar el pasante!'.$th);
        return $this->verDatosPasantiaCargada($request,$id);
       }
       return $this->verDatosPasantiaCargada($request,$id);
    }
    /**
     * @Route("/finalizarCarga/{id}", name="finalizarCarga")
     */
    public function finalizarCarga(Request $request, $id)
    {
            $manager=$this->getDoctrine()->getManager();
            $dateTime = new \DateTime();
            $pasantia= $manager->getRepository(Pasantia::class)->find($id);
            

            try {
                $pasantia->setfechaUltimaModificacion($dateTime);
                $pasantia->setPasos(11);
                $pasantia->setEstado('Activa');
                
                $manager->persist($pasantia);
                $manager->flush();
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al cargar los datos!'.$th);
                return $this->redirectToRoute('finalizarCarga',['id'=> $pasantia->getId()]);
            }                     
        

        return $this->render('pasantia/pasos/Paso11finalizarCarga.html.twig',['id'=> $pasantia->getId(),'pasantia'=> $pasantia]);
    }
     /**
     * @Route("/verDatosPasantiaCargada/{id}", name="verDatosPasantiaCargada")
     */
    public function verDatosPasantiaCargada(Request $request, $id){
        
        $manager=$this->getDoctrine()->getManager();
        //$actaCompromiso= $manager->getRepository(ActaCompromiso::class)->find($id);
        $pasantia= $manager->getRepository(Pasantia::class)->find($id);
        return $this->render('pasantia/verDatosPasantiaCargada.html.twig', ['pasantia' => $pasantia]
            );
     }
     /**
     * @Route("/verDatosPasantiaInactiva/{id}", name="verDatosPasantiaInactiva")
     */
    public function verDatosPasantiaInactiva(Request $request, $id){
        
        $manager=$this->getDoctrine()->getManager();

        $pasantia= $manager->getRepository(Pasantia::class)->find($id);
        return $this->render('pasantia/verDatosPasantiaInactiva.html.twig', ['pasantia' => $pasantia]
            );
     }
     

    /**
     * @Route("/listarPasantiaActivas", name="listarPasantiaActivas")
     */
    public function listarPasantiaActivas(Request $request)
    {
        $manager=$this->getDoctrine()->getManager();
        $pasantia= $manager->getRepository(Pasantia::class)->findByestado('Activa');
        
        return $this->render('pasantia/listarPasantiaActiva.html.twig',
                ['pasantia' => $pasantia]
            );
    }
    /**
     * @Route("/listarPasantiaEnProceso", name="listarPasantiaEnProceso")
     */
    public function listarPasantiaEnProceso(Request $request)
    {

        $manager=$this->getDoctrine()->getManager();
   
        
        $pasantia= $manager->getRepository(Pasantia::class)->findByestado('En Proceso');
        
        return $this->render('pasantia/listarPasantiaEnProceso.html.twig',
                ['pasantia' => $pasantia]
            );
    }
    /**
     * @Route("/listarPasantiaRechazada", name="listarPasantiaRechazada")
     */
    public function listarPasantiaRechazada(Request $request)
    {

        $manager=$this->getDoctrine()->getManager();
   
        
        $pasantia= $manager->getRepository(Pasantia::class)->findByestado('Rechazada');
        
        return $this->render('pasantia/listarPasantiaRechazadas.html.twig',
                ['pasantia' => $pasantia]
            );
    }
    /**
     * @Route("/listarPasantiaInactiva", name="listarPasantiaInactiva")
     */
    public function listarPasantiaInactiva(Request $request)
    {

        $manager=$this->getDoctrine()->getManager();
   
        
        $pasantias= $manager->getRepository(Pasantia::class)->findByestado('Inactiva');
        
        return $this->render('pasantia/listarPasantiaInactiva.html.twig',
                ['pasantias' => $pasantias]
            );
    }
    
    /**
     * @Route("/listarPasantiarRenovar", name="listarPasantiarRenovar")
     */
    public function listarPasantiarRenovar(Request $request)
    {

        $manager=$this->getDoctrine()->getManager();
   
        
        $pasantias= $manager->getRepository(Pasantia::class)->findall();
        $fecha_hoy = new \DateTime();

        $anio= $fecha_hoy->format("Y");
        $pasantiasRenovar=array();

                foreach ($pasantias as $pasantia) {
                    if($pasantia->getEstado()=='Activa') {
            
                        $fecha_vencimiento_string= $pasantia->getFechaFin();
                        $fecha_vencimiento_string =  $fecha_vencimiento_string->format("d-m-Y");
                        $fecha_vencimiento= $pasantia->getFechaFin();
                        $anio_vencimiento=   $fecha_vencimiento->format("Y");
            
                        $fecha_vencimiento -> modify('-15 days');
                    
                        
                        if($fecha_vencimiento < $fecha_hoy &&  $anio_vencimiento==$anio){
                            array_push($pasantiasRenovar, $pasantia );
                        }
                    }
            }
            return $this->render('pasantia/listarPasantiaRenovar.html.twig',
            ['pasantias' => $pasantiasRenovar]
        );
        
    }


        
       
    
    
    /**
     * @Route("/modificarPasantia/{id}", name="modificarPasantia")
     */
    
    public function modificarPasantia(Request $request, $id)
    {
        try {
            $manager=$this->getDoctrine()->getManager();
        
            $pasantia= $manager->getRepository(Pasantia::class)->find($id);
            
            $form = $this->createForm(RenovarPasantiaType::class,$pasantia);
            $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){
            $manager->flush();
            $this -> addFlash('info', '¡Se actualizo la fechas!');
            return $this->listarPasantiarRenovar($request);
        }
        return $this->render('pasantia/modificarRenovarPasantia.html.twig',
                ['formulario' => $form->createView(),'pasantia'=> $pasantia]
            );
        } catch (\Throwable $th) {
            $this -> addFlash('error', '¡Error al cargar los datos!');
            return $this->render('pasantia/modificarRenovarPasantia.html.twig',
                ['formulario' => $form->createView(),'pasantia'=> $pasantia]
            );
        }
    }
    /**
     * @Route("/eliminarPasantia/{id}", name="eliminarPasantia")
     */
    public function eliminarPasantia(Request $request, $id)
    {
        $manager=$this->getDoctrine()->getManager();
        $pasantia= $manager->getRepository(Pasantia::class)->find($id);
        foreach ($pasantia->getPasante() as $pasante) {
            if($pasante->getEstadoPasante()=='Activo'){
                foreach ($pasantia->getPago() as $pago) {
                    if($pago->getEstado()!='Facturado'){
                        $this -> addFlash('error', '¡Error aún quedan pagos sin Facturar!');
                        return $this->listarPasantiaActivas($request);
                    }
                }
                $this -> addFlash('error', '¡Error aún quedan pasantes activos!');
                return $this->listarPasantiaActivas($request);
            }
        }
        $pasantia->setEstado('Inactiva');
        $manager->persist($pasantia);
        $manager->flush();
        $this -> addFlash('info', 'La Pasantia se ha  Inhabilitado correctamente');
        return $this->listarPasantiaInactiva($request);
        
    }
    /**
     * @Route("/eliminarPasantiaEnProceso/{id}", name="eliminarPasantiaEnProceso")
     */
    public function eliminarPasantiaEnProceso(Request $request, $id)
    {

        
        $manager=$this->getDoctrine()->getManager();
        $pasantia= $manager->getRepository(Pasantia::class)->find($id);
       try {
            if($pasantia->getEstado()=='En Proceso'){
            $manager->remove($pasantia);
            $manager->flush();
            $this -> addFlash('info', 'La Pasantia se ha eliminado correctamente');
            return $this->listarPasantiaActivas($request);
        }
       } catch (\Throwable $th) {
        $this -> addFlash('error', 'Error en el sistema'.$th);
        return $this->listarPasantiaInactiva($request);
       }
           
        $this -> addFlash('error', 'La Pasantia no se puede eliminar porque contiene datos importantes');
        return $this->listarPasantiaInactiva($request);
        
    }
    /**
     * @Route("/cargarNuevoPasante/{id}", name="cargarNuevoPasante")
     * Esta funcionalidad es para cargar pasantes depues de dar de alta una pasantia.
     */
    public function cargarNuevoPasante(Request $request, $id)
    {
            $manager=$this->getDoctrine()->getManager();
            $dateTime = new \DateTime();
            $username = $this->getUser();
            $pasantia= $manager->getRepository(Pasantia::class)->find($id);
            /**Va a buscar los pasates cuya pasantia se Inactiva o Activa y sea de la misma pasantia */
            $pasantes= $this->findByPasantePasantia($pasantia); 

            $formulario = $this->createForm(Paso10CargarPasante::class,$pasantia);
            $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            try {
                if(!$this->findByPasanteCantidad($pasantia)){
                    $this -> addFlash('error', '¡No ingreso datos a la tabla!');
                    return $this->render('pasantia/cargarNuevoPasante.html.twig', [
                    'formulario' => $formulario->createView(),'pasantia'=> $pasantia,'pasantes'=>  $pasantes]);
                }
                $pasantia->setFechaModificacion($dateTime);
                $pasantia->setUltimoUsuario("".$username->getUsername());
                $pasantia->setfechaUltimaModificacion($dateTime);
                $manager->persist($pasantia);
                $manager->flush();
                //cargar venciomientos
                
            $this -> addFlash('info', '¡Datos cargados exitosamente!');
            return $this->redirectToRoute('verDatosPasantiaCargada',['id'=> $pasantia->getId()]);
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al cargar los datos!'.$th);
                return $this->render('pasantia/cargarNuevoPasante.html.twig', [
                    'formulario' => $formulario->createView(),'pasantia'=> $pasantia,'pasantes'=>  $pasantes]);
            }                     
            

        }

        return $this->render('pasantia/cargarNuevoPasante.html.twig', [
            'formulario' => $formulario->createView(),'pasantia'=> $pasantia,'pasantes'=>  $pasantes]);
    }
    /**
     * @Route("/agregarNuevoPasantedePasantia/{id}/{idPasante}", name="agregarNuevoPasantedePasantia")
     */
    public function agregarNuevoPasantedePasantia(Request $request, $id, $idPasante)
    {
            $manager=$this->getDoctrine()->getManager();
            $dateTime = new \DateTime();
            $username = $this->getUser();
            $pasantia= $manager->getRepository(Pasantia::class)->find($id);
            $pasante= $manager->getRepository(Pasante::class)->find($idPasante);
            $formulario = $this->createForm(CargarPasanteType::class,$pasantia);
            $formulario->handleRequest($request);
            

            try {
                $pasantia->setFechaModificacion($dateTime);
                $pasantia->setUltimoUsuario("".$username->getUsername());
                $pasante->setFechaSeguimiento($dateTime);
                $pasante->setIsSeguimientodelMes(false);
                $pasante->setIsInformeSeguimiento('No Realizado');
                $pasante->setEstadoPasante('Activo');
                $pasantia->addPasante($pasante);
                
                $manager->persist($pasantia);
                $manager->flush();
                $this -> addFlash('info', '¡Pasante Agregado exitosamente!');
                return $this->redirectToRoute('cargarNuevoPasante',['id'=> $pasantia->getId()]);
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al cargar los datos!'.$th);
                return $this->redirectToRoute('cargarNuevoPasante',['id'=> $pasantia->getId()]);
            }                     
        
    }
    /**
     * @Route("/quitarNuevoPasanteCargado/{id}/{idPasante}", name="quitarNuevoPasanteCargado")
     * Esta funcion solo es para que si ya tenemos la pasantia cargada agreguemos o quitemos nuevos pasantes a la lista
     */
    public function quitarNuevoPasanteCargado(Request $request, $id, $idPasante)
    {
        $manager=$this->getDoctrine()->getManager();
        
        
        $pasante= $manager->getRepository(Pasante::class)->find($idPasante);
        $pasantia= $manager->getRepository(Pasantia::class)->find($id);
        
       try {

            $pasante->setEstadoPasante('Inactivo');
            $pasante->removePasantia($pasantia);
            $pasantia->removePasante($pasante);
            $manager->flush();
            $this -> addFlash('info', '¡Pasante Eliminado exitosamente!');
                return $this->redirectToRoute('cargarNuevoPasante',['id'=> $pasantia->getId()]);
       } catch (\Throwable $th) {
        $this -> addFlash('error', '¡Error al cargar los datos!'.$th);
        return $this->redirectToRoute('cargarNuevoPasante',['id'=> $pasantia->getId()]);
       }
       

       $this -> addFlash('info', '¡Pasante  Eliminado exitosamente!');
       return $this->redirectToRoute('cargarNuevoPasante',['id'=> $pasantia->getId()]);
    }

    /**
     * @Route("verPasantia/{id}", name="verPasantia")
     */
    
    public function verPasantia(Request $request, $id){

        $manager=$this->getDoctrine()->getManager();
        
        $pasantia= $manager->getRepository(Pasantia::class)->find($id);

        if($pasantia->getEstado()=='Rechazada'){
            return $this->redirectToRoute('pasantiaRechazada',['id'=> $pasantia->getId()]);
        }
        switch ($pasantia->getPasos()){
            case 2:
                
                return $this->redirectToRoute('recibiryVerificarDocumentacion',['id'=> $pasantia->getId()]);
                break;
            case 3:
               
                return $this->redirectToRoute('solicitarAprobaciondepasantia',['id'=> $pasantia->getId()]);
                break;
            case 4:
                
                return $this->redirectToRoute('abrirExpedientePasantia',['id'=> $pasantia->getId()]);
                break;
            case 5:
                
                return $this->redirectToRoute('contactoOrganizacion',['id'=> $pasantia->getId()]);
                break;
            case 6:
                
                return $this->redirectToRoute('cargarConvenio',['id'=> $pasantia->getId()]);
                break;
            case 7:
                
                return $this->redirectToRoute('iniciarConvocatoria',['id'=> $pasantia->getId()]);
                break;
            case 8:
                
                return $this->redirectToRoute('enviodeNominasyDocumentacion',['id'=> $pasantia->getId()]);
                break;
            case 9:
            
                return $this->redirectToRoute('actaCompromiso',['id'=> $pasantia->getId()]);
                break;
            case 10:
                
                return $this->redirectToRoute('cargarPasante',['id'=> $pasantia->getId()]);
                break;
            case 11:
            
                return $this->redirectToRoute('verDatosPasantiaCargada',['id'=> $pasantia->getId()]);
                break;
        }
        $this -> addFlash('error', '¡Error en el sistema, paso fuera de rango!');
        return $this->redirectToRoute('listarpasantia',['id'=> $pasantia->getId()]);
    }
}
