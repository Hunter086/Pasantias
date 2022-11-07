<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\Convenio;
use App\Entity\Empresa;
use App\Entity\CorreoElectronico;

use App\Repository\VencimientoRepository;

use App\Form\Paso1recepcionConvenioType;
use App\Form\Paso2SolicitarfirmadeRectoradoType;
use App\Form\Paso3CargarDatosExpedienteType;
use App\Form\Paso4CargarDatosdelConvenioType;
use App\Form\Paso5ArchivoConvenioType;
use App\Form\Paso6ImputacionConvenioType;
use App\Form\Paso7ArchivarConvenioaEmpresaType;

use App\Form\FechaRenovacionType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * @Route("/admin")
 * La implementació de los formularios se utilizo un swich con un contador. El mismo se encuentra al final
     * del código.
     * En los twig cada paso esta detallado en los nombres.
*/
class ConvenioController extends AbstractController
{
    /**
     * @Route("/recepcionConvenio", name="recepcionConvenio")
     */
    public function recepcionConvenio(Request $request, SluggerInterface $slugger)
    {
        try {
            $manager=$this->getDoctrine()->getManager();
            $dateTime = new \DateTime();
            $username = $this->getUser();
            $year = $dateTime->format('d-m-Y');
            $convenio = new Convenio();
            $formulario = $this->createForm(Paso1RecepcionConvenioType::class, $convenio);
            $formulario->handleRequest($request);
            $convenio->setPasos(1);
            $manager->flush();
            if ($formulario->isSubmitted() && $formulario->isValid()) {
                $empresa= $manager->getRepository(Empresa::class)->find($formulario->get('empresa')->getData());
                //recorremos el la lista de combenios carcados para verificar que no exita uno activo para esa empresa.
                foreach ($empresa->getConvenio() as $convenios) {
                    if($convenios!= null && $convenios->getEstado()=='Activo' || $convenios->getEstado()=='En Proceso'){
                        $this -> addFlash('error', '¡Error al cargar los datos! Ya existe un convenio para '.$formulario->get('empresa')->getData()->getNombre());
                        return $this->redirectToRoute('recepcionConvenio');
                    }
                }
                    //cargamos los datos
                    $convenio->setFechaModificacion($dateTime);
                    $convenio->setUltimoUsuario("".$username->getUsername());
                    $convenio->getEmpresa()->setEstado('Activa');
                    $convenio->setPasos(2);
                    $convenio->setFechaInicio($dateTime);
                    $convenio->setFechaFin($dateTime);
                    $convenio->setFechaInicioTramite($dateTime);
                    $convenio->setFechaFinTramite($dateTime);
                    #Setear Pasos Siguientes
                    $convenio->setEstado('En Proceso');
                    $manager->persist($convenio);
                    $manager->flush();  
                    $this -> addFlash('info', '¡Datos cargados exitosamente!');
                    return $this->redirectToRoute('solicitarFirmaRectorado',['id'=> $convenio->getId()]);
            }         
        } catch (\Throwable $th) {
            $this -> addFlash('error', '¡Error al cargar los datos!'.$th);
            return $this->redirectToRoute('recepcionConvenio');
        }
        return $this->render('convenio/pasos/Paso1recepcionConvenio.html.twig', [
            'formulario' => $formulario->createView(),
        ]);
    }



    /**
     * @Route("solicitarFirmaRectorado/{id}", name="solicitarFirmaRectorado")
     * Paso 2
     */
    public function solicitarFirmaRectorado(Request $request, $id){
                $dateTime = new \DateTime();
                $username = $this->getUser();
                $year = $dateTime->format('d-m-Y');
                $manager=$this->getDoctrine()->getManager();

                    $convenio= $manager->getRepository(Convenio::class)->find($id);
                    $formulario = $this->createForm(Paso2SolicitarfirmadeRectoradoType::class,$convenio);
                    $formulario->handleRequest($request);
                    $convenio->setPasos(2);
                    $manager->flush();

                    if ($formulario->isSubmitted() && $formulario->isValid()) {
                        
                        try {
                            $convenio->setFechaModificacion($dateTime);
                            $convenio->setUltimoUsuario("".$username->getUsername());
                            $convenio->setPasos(3);
                            
                            $manager->persist($convenio);
                            $manager->flush();
                        } catch (\Throwable $th) {
                            $this -> addFlash('error', '¡Error al cargar los datos. El documento ya existe o la empresa ya tiene un convenio !'.$th);
                        return $this->render('convenio/pasos/Paso2solicitarFirmaRectorado.html.twig',
                        array('formulario' => $formulario->createView(), 'convenio' => $convenio)
                            );
                        }                     
                        
                        $this -> addFlash('info', '¡Datos cargados exitosamente!');
                        return $this->redirectToRoute('cargarDatosExpediente',['id'=> $convenio->getId()]);
                        #return $this->render('Convenio/verConvenioPaso2.html.twig', array('formulario' => $formulario->createView(), 'convenio' => $convenio));
                        
                    }
                    return $this->render('convenio/pasos/Paso2solicitarFirmaRectorado.html.twig',
                        array('formulario' => $formulario->createView(), 'convenio' => $convenio)
                    );

                
                
    }
    /**
     * @Route("cargarDatosExpediente/{id}", name="cargarDatosExpediente")
     * Paso 3
     */
    public function cargarDatosExpediente(Request $request, $id){
                $dateTime = new \DateTime();
                $username = $this->getUser();
                $year = $dateTime->format('d-m-Y');
                $manager=$this->getDoctrine()->getManager();

                $convenio= $manager->getRepository(Convenio::class)->find($id);
                
                $formulario = $this->createForm(Paso3CargarDatosExpedienteType::class,$convenio);
                $formulario->handleRequest($request);
                $convenio->setPasos(3);
                $manager->flush();


                if ($formulario->isSubmitted() && $formulario->isValid() && $this->validarExpediente($convenio)) {
                
                    try {
                        $convenio->setFechaModificacion($dateTime);
                        $convenio->setUltimoUsuario("".$username->getUsername());
                        $convenio->setPasos(4);
                        
                        $manager->persist($convenio);
                        $manager->flush();
                    } catch (\Throwable $th) {
                        $this -> addFlash('error', '¡Error al cargar los datos. El documento ya existe o la empresa ya tiene un convenio !'.$th);
                    return $this->render('convenio/pasos/Paso3cargarDatosExpediente.html.twig',
                    array('formulario' => $formulario->createView(), 'convenio' => $convenio)
                        );
                    }                     
                    
                    $this -> addFlash('info', '¡Datos cargados exitosamente!');
                    return $this->redirectToRoute('cargarDatosdelConvenio',['id'=> $convenio->getId()]);
                }
                return $this->render('convenio/pasos/Paso3cargarDatosExpediente.html.twig', array('formulario' => $formulario->createView(), 'convenio' => $convenio)
                );
    }

    /**
     * @Route("/cargarDatosdelConvenio/{id}", name="cargarDatosdelConvenio")
     * Paso 4
     */
    public function cargarDatosdelConvenio(Request $request, SluggerInterface $slugger, $id){
        
            $manager=$this->getDoctrine()->getManager();
            $dateTime = new \DateTime();
            $username = $this->getUser();
            $year = $dateTime->format('d-m-Y');
            $convenio= $manager->getRepository(Convenio::class)->find($id);
            $formulario = $this->createForm(Paso4CargarDatosdelConvenioType::class,$convenio);
            $formulario->handleRequest($request);
            $convenio->setPasos(4);
            $manager->flush();
            if($convenio->getDocumentoConvenio()!='' || $convenio->getDocumentoConvenio()!=null ){
                unlink("DocumentoConvenio/".$convenio->getDocumentoConvenio());
                $convenio->setDocumentoConvenio('');
                    
                }
            $convenio->setPasos(4);
            $manager->persist( $convenio);
            $manager->flush();
             
            
            if ($formulario->isSubmitted() && $formulario->isValid()) {
                /** @var UploadedFile $brochureFile */

            $brochureFile = $formulario->get('documentoConvenio')->getData();
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
                if ($brochureFile) {
                    $originalFilename = pathinfo('DocumentoConvenio'.$year, PATHINFO_FILENAME);
                    //$brochureFile->getClientOriginalName()
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
                }        
                try {
                    $convenio->setFechaModificacion($dateTime);
                    $convenio->setUltimoUsuario("".$username->getUsername());
                    $convenio->setPasos(5);
                    $convenio->setDocumentoConvenio($newFilename);
                    $manager->persist($convenio);
                    $manager->flush();
                    $brochureFile->move(
                        $this->getParameter('DocumentoConvenio'),
                        $newFilename
                    );  
                    
                } catch (\Throwable $th) {
                    $this -> addFlash('error', '¡Error en la cargar los datos!'.$th);
                return $this->render('convenio/pasos/Paso4cargarDatosdelConvenio.html.twig',
                array('formulario' => $formulario->createView(), 'convenio' => $convenio)
                    );
                }                     
                
                $this -> addFlash('info', '¡Datos cargados exitosamente!');
                return $this->redirectToRoute('archivoConvenio',['id'=> $convenio->getId()]);
                
                
            }
            return $this->render('convenio/pasos/Paso4cargarDatosdelConvenio.html.twig',
                array('formulario' => $formulario->createView(), 'convenio' => $convenio)
            );

    }
    /**
     * @Route("archivoConvenio/{id}", name="archivoConvenio")
     * Paso 5
     */
    public function archivoConvenio(Request $request, SluggerInterface $slugger,$id){
        $dateTime = new \DateTime();
        $username = $this->getUser();
        $year = $dateTime->format('d-m-Y');
        $manager=$this->getDoctrine()->getManager();

        $convenio= $manager->getRepository(Convenio::class)->find($id);

        $formulario = $this->createForm(Paso5ArchivoConvenioType::class,$convenio);
        $formulario->handleRequest($request);
        $convenio->setPasos(5);
        $manager->flush();
        if ($formulario->isSubmitted() && $formulario->isValid()) {
           
            try {
                $convenio->setFechaModificacion($dateTime);
                $convenio->setUltimoUsuario("".$username->getUsername());
                $convenio->setPasos(6);
                $manager->persist($convenio);
                $manager->flush();
               
                $this -> addFlash('info', '¡Datos cargados exitosamente!');
                return $this->redirectToRoute('imputacionConvenio',['id'=> $convenio->getId()]);
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al cargar los datos!'.$th);
            return $this->render('convenio/pasos/Paso5archivoConvenio.html.twig',
            array('formulario' => $formulario->createView(), 'convenio' => $convenio)
                );
            }

        }
        return $this->render('convenio/pasos/Paso5archivoConvenio.html.twig',
            array('formulario' => $formulario->createView(), 'convenio' => $convenio)
                );
        
    }
    /**
     * @Route("imputacionConvenio/{id}", name="imputacionConvenio")
     * Paso 6
     */
    public function imputacionConvenio(Request $request, SluggerInterface $slugger,$id){
        $dateTime = new \DateTime();
        $username = $this->getUser();
        $year = $dateTime->format('d-m-Y');
        $manager=$this->getDoctrine()->getManager();

        $convenio= $manager->getRepository(Convenio::class)->find($id);

        $formulario = $this->createForm(Paso6ImputacionConvenioType::class,$convenio);
        $formulario->handleRequest($request);
        $convenio->setPasos(6);
        $manager->flush();
        if ($formulario->isSubmitted() && $formulario->isValid()) {
           
            try {
                $convenio->setFechaModificacion($dateTime);
                $convenio->setUltimoUsuario("".$username->getUsername());
                $convenio->setPasos(7);
                $manager->persist($convenio);
                $manager->flush();
               
                $this -> addFlash('info', '¡Datos cargados exitosamente!');
                return $this->redirectToRoute('archivarConvenioaEmpresa',['id'=> $convenio->getId()]);
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al cargar los datos!'.$th);
            return $this->render('convenio/pasos/Paso6imputacionConvenio.html.twig',
            array('formulario' => $formulario->createView(), 'convenio' => $convenio)
                );
            }

        }
        return $this->render('convenio/pasos/Paso6imputacionConvenio.html.twig',
            array('formulario' => $formulario->createView(), 'convenio' => $convenio)
                );
        
    }

    /**
     * @Route("/archivarConvenioaEmpresa/{id}", name="archivarConvenioaEmpresa")
     * Paso 7
     */
    public function archivarConvenioaEmpresa(Request $request, $id){
        $username = $this->getUser();
        $manager=$this->getDoctrine()->getManager();
        $convenio= $manager->getRepository(Convenio::class)->find($id);
        $dateTime = new \DateTime();
        $empresa= $manager->getRepository(Empresa::class)->find($convenio->getEmpresa());
        $formulario = $this->createForm(Paso7ArchivarConvenioaEmpresaType::class, $convenio);
        $formulario->handleRequest($request);
        $convenio->setPasos(7);
        $manager->flush();

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            try {
                //guardar datos
                $convenio->setFechaModificacion($dateTime);
                $convenio->setUltimoUsuario("".$username->getUsername());
                $convenio->setFechaFinTramite($dateTime);
                $convenio->setEstado('Activo');
                $empresa->setEstado('Activa');
                $convenio->setPasos(8);
                $manager->persist($convenio);
                $manager->persist($empresa);
                
                $manager->flush();

                $this -> addFlash('info', '¡Datos cargados exitosamente!');
                return $this->redirectToRoute('convenioFinalizado',['id'=> $convenio->getId()]);
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error en la Documentación!');
                return $this->render('convenio/pasos/Paso7archivarConvenioaEmpresa.html.twig',array('formulario' => $formulario->createView(), 'convenio' => $convenio));
            }
            

        }
        return $this->render('convenio/pasos/Paso7archivarConvenioaEmpresa.html.twig',array('formulario' => $formulario->createView(), 'convenio' => $convenio));
    }
    
    /**
     * @Route("convenioFinalizado/{id}", name="convenioFinalizado")
     * Paso 8
     */
    public function convenioFinalizado(Request $request, $id){
        $manager=$this->getDoctrine()->getManager();

        $convenio= $manager->getRepository(Convenio::class)->find($id);
        return $this->render('convenio/pasos/verConvenioFinalizado.html.twig', ['convenio' => $convenio]
            );
    }
     /**
     * @Route("/listarConvenioEnProceso", name="listarConvenioEnProceso")
     */
    public function listarConvenioEnProceso(Request $request)
    {
        $manager=$this->getDoctrine()->getManager();
   
        
        $convenio= $manager->getRepository(Convenio::class)->findByestado('En Proceso');
        
        return $this->render('convenio/listarConvenioEnProceso.html.twig',
                ['convenio' => $convenio]
            );
    }
    /**
     * @Route("/listarConvenioActivo", name="listarConvenioActivo")
     */
    public function listarConvenioActivo(Request $request)
    {
        $manager=$this->getDoctrine()->getManager();
   
        
        $convenio= $manager->getRepository(Convenio::class)->findByestado('Activo');
        
        return $this->render('convenio/listarConvenioActivo.html.twig',
                ['convenio' => $convenio]
            );
    }
    
    /**
     * @Route("/listarConvenioaRenovar", name="listarConvenioaRenovar")
     */
    public function listarConvenioaRenovar(Request $request)
    {
            $manager=$this->getDoctrine()->getManager();
            $fecha_hoy = new \DateTime();
            
            $convenios= $manager->getRepository(Convenio::class)->findall();
            $anio= $fecha_hoy->format("Y");
            $convenioaRenovar=array();
            if($convenios!=null) {
                foreach ($convenios as $convenio) {
                    if($convenio->getFechaFin()!=null && $convenio->getEstado()=='Activo') {
               
                        $fecha_vencimiento_string= $convenio->getFechaFin();
                        $fecha_vencimiento_string =  $fecha_vencimiento_string->format("d-m-Y");
                        $fecha_vencimiento= $convenio->getFechaFin();
                        $anio_vencimiento=   $fecha_vencimiento->format("Y");
            
                        $fecha_vencimiento -> modify('-15 days');
                    
                        
                        if($fecha_vencimiento < $fecha_hoy &&  $anio_vencimiento=$anio){
                            $fecha_vencimiento -> modify('+15 days');
                            array_push($convenioaRenovar, $convenio);
                        }
                    }
    
            }
            
            }

             return $this->render('convenio/listadeConvenioaVencer.html.twig',
                ['convenios' => $convenioaRenovar]
            );

    }
    
    /**
     * @Route("/modificarConvenio/{id}", name="modificarConvenio")
     */
    
    public function modificarConvenio(Request $request, $id)
    {
        $manager=$this->getDoctrine()->getManager();
        
        $convenio= $manager->getRepository(Convenio::class)->find($id	);
        
        $form = $this->createForm(FechaRenovacionType::class,$convenio);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){
            
            $manager->flush();
            $this -> addFlash('info', '¡El Convenio se ha registrado exitosamente!');
            return $this->redirectToRoute('listarConvenioaRenovar');
            
        }
        
        return $this->render('convenio/modificarConvenio.html.twig',
                ['formulario' => $form->createView()]
            );
    }
    /**
     * @Route("/eliminarConvenio/{id}", name="eliminarConvenio")
     */
public function eliminarConvenio(Request $request, $id)
    {
        $manager=$this->getDoctrine()->getManager();
        
        
        
        $convenio= $manager->getRepository(Convenio::class)->find($id);

        if($convenio->getPasantia()!=null){
            $pasantia= $convenio->getPasantia();
            foreach ($pasantia as $pasantias) {
                if($pasantias->getEstadoPasantia()=='Activa'){
                    $this -> addFlash('error', '¡El Convenio es parte de una Pasantia Activa!');
                    return $this->redirectToRoute('listarEmpresaActiva');
                }
            }
        }
        $convenio->getEmpresa()->setEstadoEmpresa('Inactiva');
        $convenio->setEstadoConvenio('Inactivo');
        $manager->flush();
        $this -> addFlash('info', '¡El Convenio se ha eliminado exitosamente!');
        return $this->listarConvenioInactivo($request);
        
    }
    /**
     * @Route("/listarConvenioInactivo", name="listarConvenioInactivo")
     */
    public function listarConvenioInactivo(Request $request)
    {
        $manager=$this->getDoctrine()->getManager();
        
        $convenio= $manager->getRepository(Convenio::class)->findByestado('Inactivo');
        
        return $this->render('convenio/listarConvenioInactivo.html.twig',
                ['convenio' => $convenio]
            );
    }
    
    /**
     * @Route("verConvenio/{id}", name="verConvenio")
     */
    
     public function verConvenio(Request $request,SluggerInterface $slugger, $id){
        $manager=$this->getDoctrine()->getManager();
        
        $convenio= $manager->getRepository(Convenio::class)->find($id);

        switch ($convenio->getPasos()){
            case 2:
                return $this->redirectToRoute('solicitarFirmaRectorado',['id'=> $convenio->getId()]);
                
                break;
            case 3:
                return $this->redirectToRoute('cargarDatosExpediente',['id'=> $convenio->getId()]);
                break;
            case 4:
                return $this->redirectToRoute('cargarDatosdelConvenio',['id'=> $convenio->getId()]);
                break;
            case 5:
                return $this->redirectToRoute('archivoConvenio',['id'=> $convenio->getId()]);
                break;
            case 6:
                return $this->redirectToRoute('imputacionConvenio',['id'=> $convenio->getId()]);
                break;
            case 7:
                return $this->redirectToRoute('archivarConvenioaEmpresa',['id'=> $convenio->getId()]);
                break;
            case 8:
                return $this->redirectToRoute('convenioFinalizado',['id'=> $convenio->getId()]);
                break;   
        }
        $this -> addFlash('error', '¡Error en el sistema, paso fuera de rango!');
        return $this->redirectToRoute('listarConvenioEnProceso',['id'=> $convenio->getId()]);
    }
    public function nuevovencimiento(Request $request, $id){
        $manager=$this->getDoctrine()->getManager();

        $convenio= $manager->getRepository(Convenio::class)->find($id);
        $notaEmpresa= $convenio->getNotaEmpresa();
        return $this->redirect("/PasantiasyBecasFuturxsProfesionalesTest/SolicituddeConvenios/".$notaEmpresa);

    }
    /**
     * @Route("verPDFNotaEmpresa/{id}", name="verPDFNotaEmpresa")
     */
    public function verPDFNotaEmpresa(Request $request, $id){
        $manager=$this->getDoctrine()->getManager();

        $convenio= $manager->getRepository(Convenio::class)->find($id);
        $notaEmpresa= $convenio->getNotaEmpresa();
        return $this->redirect("/PasantiasyBecasFuturxsProfesionalesTest/SolicituddeConvenios/".$notaEmpresa);

    }
    /**
     * @Route("verPDFDocumentoConvenio/{id}", name="verPDFDocumentoConvenio")
     */
    public function verPDFDocumentoConvenio(Request $request, $id){
        $manager=$this->getDoctrine()->getManager();

        $convenio= $manager->getRepository(Convenio::class)->find($id);
        $documentoConvenio= $convenio->getDocumentoConvenio();
        return $this->redirect("/PasantiasyBecasFuturxsProfesionalesTest/DocumentoConvenio/".$documentoConvenio);

    }
    /**
     * @Route("verDatosConvenioFinalizado/{id}", name="verDatosConvenioFinalizado")
     */
     public function verDatosConvenioFinalizado(Request $request, $id){
        $manager=$this->getDoctrine()->getManager();

        $convenio= $manager->getRepository(Convenio::class)->find($id);
        return $this->render('convenio/verDatosConvenioFirmado.html.twig', ['convenio' => $convenio]
            );
     }
    /**
     * @Route("/modificarFechaRenovacionConvenio/{id}", name="modificarFechaRenovacionConvenio")
     */
    public function  modificarFechaRenovacionConvenio(Request $request, $id){
        $manager=$this->getDoctrine()->getManager();
        $dateTime = new \DateTime();
        $username = $this->getUser();
        $convenio= $manager->getRepository(Convenio::class)->find($id);
        $formulario = $this->createForm(FechaRenovacionType::class, $convenio);
        $formulario->handleRequest($request);
        if ($formulario->isSubmitted()) {
            try {
                $convenio->setFechaModificacion($dateTime);
                $convenio->setUltimoUsuario("".$username->getUsername());
                $manager->persist($convenio);
                $manager->flush();
                $this -> addFlash('info', '¡Fecha Actualizada!');
                return $this->redirectToRoute('convenioFinalizado',['id'=> $convenio->getId()]);
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al cargar los datos!'.$th);
                return $this->render('convenio/verDatosConvenioFirmado.html.twig',
                array('formulario' => $formulario->createView(), 'convenio' => $convenio)
                );
            }
        }
        return $this->render('convenio/modificarConvenio.html.twig',
            array('formulario' => $formulario->createView(), 'convenio' => $convenio)
                );
     }
    
    /**
     * validar formulario
     */
    public function validarExpediente($convenio){
        $nombre=$convenio->getTituloExpediente();
        $re = '/^[A-Za-zäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙÑñ  .0-9]{2,149}$/';
        

        /**Validar Nombre */
        if(!preg_match($re, $nombre)){
            $this -> addFlash('error', 'Ingrese un Nombre valido');
            return false;
        }
        /**Validar Nª de expediente */
        if(!preg_match('/^[0-9]{2,}+(\/){1}+[0-9]{2,}$/', $convenio->getNumeroExpediente())){
            $this -> addFlash('error', 'Ingrese un Nª de Expediente valido');
            return false;
        }
       
        return true;
    }

    


}
