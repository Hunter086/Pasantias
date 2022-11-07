<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Regex;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use App\Entity\Pasante;
use App\Entity\Pasantia;
use App\Entity\Contacto;
use App\Entity\Archivos;

use App\Form\PasanteType;
use App\Form\ModificarPasanteType;
use App\Form\ContactoType;
use App\Form\PagoType;
use App\Form\CargarArchivosType;
use App\Form\SeguimientoPasanteType;

/**
 * @Route("/admin")
 * La implementació de los formularios se utilizo un swich con un contador. El mismo se encuentra al final
     * del código.
     * En los twig cada paso esta detallado en los nombres.
*/
class PasanteController extends AbstractController
{
    /**
     * @Route("/registroPasante", name="registroPasante")
     */
    public function registroPasante(Request $request)
    {

        $pasante = new Pasante();
        $contacto = new Contacto();
        $username = $this->getUser();
        $dateTime = new \DateTime();

        $formulariocontacto = $this->createForm(ContactoType::class,$contacto);
        $formulariocontacto -> handleRequest($request);
        
        $formulario = $this->createForm(PasanteType::class,$pasante);
        $formulario -> handleRequest($request);
        
        
        if ($formulario->isSubmitted() && $formulario->isValid() && $this->validarPasante($pasante) && $this->validarContacto($contacto) ){
            try {
                $pasante->setFechaModificacion($dateTime);
                $pasante->setUltimoUsuario("".$username->getUsername());
                $pasante->setIsSeguimientodelMes(false);
                $pasante->setIsInformeSeguimiento('No Realizado');
                $pasante->setEstadoPasante('Inactivo');
                $pasante->addContacto($contacto);
                $entManager = $this->getDoctrine()->getManager();
                $entManager->persist($pasante);
               
                $entManager->flush();
                $this -> addFlash('info', '¡El pasante se ha registrado exitosamente!');
                return $this->redirectToRoute('listarPasantesInactivos');
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡El Pasante no se registro correctamente!'.$th);
                return $this->render('pasante/cargarPasante.html.twig', [
                    'formulario' => $formulario ->createView(),'formulariocontacto' => $formulariocontacto ->createView(),
                ]);
            }
        }
        return $this->render('pasante/cargarPasante.html.twig', [
            'formulario' => $formulario ->createView(),'formulariocontacto' => $formulariocontacto ->createView(),
        ]);
    }
    
    /**
     * @Route("/listarPasanteActivos", name="listarPasanteActivos")
     */
    public function listarPasanteActivos(Request $request)
        {

            $manager=$this->getDoctrine()->getManager();
            
            
            $pasanteInactivo= $manager->getRepository(Pasante::class)->findAll();
            $pasante= array();
            foreach ($pasanteInactivo as $pasanteInactivos) {
                if($pasanteInactivos->getEstadopasante()=='Activo'){
                    array_push($pasante, $pasanteInactivos);
                }
            }
            
            return $this->render('pasante/listarPasanteActivos.html.twig',
                    ['pasante' => $pasante]
                );
        }
    /**
     * @Route("/listarPasantesInactivos", name="listarPasantesInactivos")
     */
    public function listarPasantesInactivos(Request $request)
    {
       
        $manager=$this->getDoctrine()->getManager();
        
        
        $pasanteInactivo= $manager->getRepository(Pasante::class)->findAll();
        $pasante= array();
        foreach ($pasanteInactivo as $pasanteInactivos) {
            if($pasanteInactivos->getEstadopasante()=='Inactivo'){
                array_push($pasante, $pasanteInactivos);
            }
        }
        
        return $this->render('pasante/listarPasanteInactivos.html.twig',
                ['pasante' => $pasante]
            );
    }
    
    /**
     * @Route("/modificarPasante/{id}", name="modificarPasante")
     */
    
    public function modificarPasante(Request $request, $id)
    {

        $manager=$this->getDoctrine()->getManager();
        $username = $this->getUser();
        $dateTime = new \DateTime();
        $pasante= $manager->getRepository(Pasante::class)->find($id);
        $contacto = new Contacto();

        $formulariocontacto = $this->createForm(ContactoType::class,$contacto);
        $formulariocontacto -> handleRequest($request);

        $formulario = $this->createForm(ModificarPasanteType::class,$pasante);
        $formulario->handleRequest($request);
        
        if ($formulario->isSubmitted() && $formulario->isValid() && $this->validarModificacionPasante($pasante) && $this->validarContacto($contacto) ){
            try {
                $pasante->setFechaModificacion($dateTime);
                $pasante->setUltimoUsuario("".$username->getUsername());
                
                $pasante->addContacto($contacto);
                $manager->persist($pasante);
                $manager->flush();
            $this -> addFlash('info', '¡El pasante se ha modificado correctamente!');
            return $this->redirectToRoute('listarPasantesInactivos');
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al modificar los datos!');
                return $this->redirectToRoute('modificarPasante',['id'=> $contacto->getpasante()->getId()]);
            }
            
            
        }
        
        return $this->render('pasante/modificarPasante.html.twig',
                ['formulario' => $formulario->createView(),'pasante'=> $pasante,'contacto'=> $pasante->getContacto(), 'formulariocontacto' => $formulariocontacto ->createView(),]
            );
    }
    /**
     * @Route("/seguimientoPasante/{id}/{idPasantia}", name="seguimientoPasante")
     */
    
    public function seguimientoPasante(Request $request, $id, $idPasantia)
    {
        $manager=$this->getDoctrine()->getManager();
        $username = $this->getUser();
        $dateTime = new \DateTime();
        $pasante= $manager->getRepository(Pasante::class)->find($id);
        $pasantia= $manager->getRepository(Pasantia::class)->find($idPasantia);
        $formulario = $this->createForm(SeguimientoPasanteType::class,$pasante);
        $formulario->handleRequest($request);
        
        if ($formulario->isSubmitted() && $formulario->isValid()){
            try {
                $pasante->setFechaModificacion($dateTime);
                $pasante->setUltimoUsuario("".$username->getUsername());
                $pasante->setFechaSeguimiento($dateTime);
                $manager->persist($pasante);
                $manager->flush();
            $this -> addFlash('info', '¡El seguimiento se ha cargado exitosamente!');
            return $this->redirectToRoute('verDatosPasantiaCargada',['id'=> $idPasantia]);
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al cargar los datos del seguimento!');
                return $this->redirectToRoute('seguimientoPasante',['id'=>$id, 'idPasantia'=>$idPasantia ]);
            }
            
            
        }
        
        return $this->render('pasante/seguimientoPasante.html.twig',
                ['formulario' => $formulario->createView(),'pasante'=> $pasante, 'pasantia'=>$pasantia]
            );
    }
    /**
     * @Route("/eliminarPasante/{id}", name="eliminarPasante")
     */
public function eliminarPasante(Request $request, $id)
    {

        $manager=$this->getDoctrine()->getManager();
        
        $form = $this->createForm(PasanteType::class,new pasante());
        $form->handleRequest($request);
        
        $pasante= $manager->getRepository(Pasante::class)->find($id);
        
       try {
            foreach ($pasante->getPago() as $pago) {
               if($pago->getEstadoPago()=='No Abonado' || $pago->getEstadoPago()=='Pagado' ){
                $this -> addFlash('error', '¡Error aún quedan pagos sin facturar!');
                return $this->listarPasantesInactivos($request);
               }
            }
            $pasante->setEstadoPasante('Inactivo');
            $manager->flush();
            $this -> addFlash('info', '¡El pasante se ha eliminado correctamente!');
            return $this->listarPasantesInactivos($request);
       } catch (\Throwable $th) {
        $this -> addFlash('error', '¡Error al eliminar el pasante!'.$th);
        return $this->listarPasantesInactivos($request);
       }
       return $this->listarPasantesInactivos($request);
        
        
    }
   

    /**
     * @Route("/eliminarPasanteInactivo/{id}", name="eliminarPasanteInactivo")
     */
public function eliminarPasanteInactivo(Request $request, $id)
{

    $manager=$this->getDoctrine()->getManager();
    
    
    
    $pasante= $manager->getRepository(Pasante::class)->find($id);
    
   try {
            $pasantias= array();
            $pasantias= $pasante->getPasantias();
            
            if($pasantias->count()==0 ){
                $this -> addFlash('info', '¡El pasante fue eliminado correctamente!');
                $manager->remove($pasante);
                
                $manager->flush();
                return $this->listarPasantesInactivos($request);
            }

        
        $this -> addFlash('error', '¡El pasante es parte de una pasantia y no se puede eliminar permanentemente!');
        return $this->listarPasantesInactivos($request);
   } catch (\Throwable $th) {
    $this -> addFlash('error', '¡Error al eliminar el pasante!'.$th);
    return $this->listarPasantesInactivos($request);
   }
   return $this->listarPasantesInactivos($request);
    
    
}
    /**
     * @Route("/eliminarContactoPasante/{id}", name="eliminarContactoPasante")
     */
public function eliminarContactoPasante(Request $request, $id)
{

    $manager=$this->getDoctrine()->getManager();
    
    $form = $this->createForm(pasanteType::class,new pasante());
    $form->handleRequest($request);
    $contacto = new Contacto();
    $formulariocontacto = $this->createForm(ContactoType::class,$contacto);
    $formulariocontacto -> handleRequest($request);
    $contacto= $manager->getRepository(Contacto::class)->find($id);
    $pasante= $manager->getRepository(Contacto::class)->find($contacto->getpasante());
    
    $manager->remove($contacto);
    $manager->flush();
    $this -> addFlash('info', '¡El contacto se a eliminado correctamente!');
    return $this->redirectToRoute ('modificarPasante',['id'=> $contacto->getpasante()->getId()]);
    
}
/**
     * @Route("/verDatosPasante/{id}", name="verDatosPasante")
     */
    public function verDatosPasante(Request $request, $id){

        $manager=$this->getDoctrine()->getManager();

        $pasante= $manager->getRepository(Pasante::class)->find($id);
        return $this->render('pasante/datosPasante.html.twig', ['pasante' => $pasante]
            );
     }
     /**
     * @Route("verPDFPasante/{id}", name="verPDFPasante")
     */
    public function verPDFPasante(Request $request, $id){

        $manager=$this->getDoctrine()->getManager();

        
        $archivo= $manager->getRepository(Archivos::class)->find($id);
        
        return $this->redirect("/PasantiasyBecasFuturxsProfesionalesTest/ArchivosPasantes/".$archivo->getArchivo());

    }
     /**
     * @Route("/cargarArchivosPasante/{id}", name="cargarArchivosPasante")
     */
    
    public function cargarArchivosPasante(Request $request,SluggerInterface $slugger, $id){

        $dateTime = new \DateTime();
        $day = $dateTime->format('d-m-Y');

        $manager=$this->getDoctrine()->getManager();
        $pasante= $manager->getRepository(Pasante::class)->find($id);
        

        

        $formulario = $this->createForm(CargarArchivosType::class);
        $formulario->handleRequest($request);
        
        if ($formulario->isSubmitted() && $formulario->isValid() && $this->validarArchivo($formulario)) {
            $this->eliminarPDFS($pasante);
            /** @var UploadedFile $brochureFile */
            $archivos = $formulario->get('archivos')->getData();
            if ($archivos!=null) {
                foreach ($archivos as $archivo) {
                    $documento= new Archivos();
                    try {
                    
                            $originalFilename = pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME);
                            //$brochureFile->getClientOriginalName()
                            // this is needed to safely include the file name as part of the URL
                            $safeFilename = $slugger->slug($originalFilename);
                            $newFilename = $safeFilename.'-'.uniqid().'.'.$archivo->guessExtension();
                            $documento->setNombreArchivo($originalFilename);
                            $documento->setExtencionArchivo($archivo->guessExtension());
                            $documento->setArchivo($newFilename);
                            $pasante->addArchivo($documento);
                            $archivo->move(
                                $this->getParameter('ArchivosPasantes'),
                                $newFilename
                            );
                            
                    } catch (\Throwable $th) {
                        $this -> addFlash('error', '¡Error al guardar los archivos!'.$th);
                        return $this->render('pasante/cargarArchivos.html.twig', [
                            'formulario' => $formulario->createView(),
                        ]);
                    }
                }
                try {
                    $manager->persist($pasante);
                    $manager->flush();
                    $this -> addFlash('info', '¡Archivos guardados exitosamente!');
                        return $this->render('pasante/cargarArchivos.html.twig', [
                            'formulario' => $formulario->createView(),
                        ]);
                } catch (\Throwable $th) {
                    $this -> addFlash('error', '¡Error al guardar los asrchivos!');
                        return $this->render('pasante/cargarArchivos.html.twig', [
                            'formulario' => $formulario->createView(),
                        ]);
                }
                    


            }else{
                $this -> addFlash('error', '¡El archivo no existe!'.$archivos);
                    return $this->render('pasante/cargarArchivos.html.twig', [
                        'formulario' => $formulario->createView(),
                    ]);
            }


        }

        return $this->render('pasante/cargarArchivos.html.twig', [
            'formulario' => $formulario->createView(),
        ]);
    }
    
    /**
     * validar formulario
     */
    public function eliminarPDFS($pasante){

        $manager=$this->getDoctrine()->getManager();
        if($pasante->getArchivos()!=null){
            
            foreach ($pasante->getArchivos() as $archivo) {
                unlink("../PasantiasyBecasFuturxsProfesionalesTest/ArchivosPasantes/".$archivo->getArchivo());
                $manager->remove($archivo);
                $pasante->removeArchivo($archivo);
                
            }
            $manager->persist( $pasante);
            $manager->flush();
         
        }

    }
     /**
     * validar formulario
     */
    public function validarArchivo($formulario){

        $archivos = $formulario->get('archivos')->getData();
        $total= count($archivos);
        if ($total<=5) {
            return true;
            
        }else{
            $this->addFlash('error', 'Error Máximo permitido 5 archivos');
            return false;
        }
        

    
    
    }

     /**
     * validar formulario
     */
    public function validarContactoLista($contactoLista){
        
        foreach ($contactoLista as $contactoListas) {
            $this->validarContacto($contactoListas);
        }
   
    return true;
    
    
    }


    
    
    /**
     * validar formulario
     */
    public function validarContacto($contacto){
        
        /**Validar Nombre */
        if(!preg_match('/^[A-Za-zäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙÑñ  .]{2,60}$/', $contacto->getNombre())){
            $this -> addFlash('error', 'Ingrese un Nombre Contacto valido');
            return false;
        }
        /**Validar Apellido */
        if(!preg_match('/^[A-Za-zäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙÑñ  .]{2,60}$/', $contacto->getApellido())){
            $this -> addFlash('error', 'Ingrese un Apellido Contacto valido');
            return false;
        }

        /**Validar Telefono */
        if(!preg_match('/^(?:(?:00)?549?)?0?(?:11|[2368]\d)(?:(?=\d{0,2}15)\d{2})??\d{8}$/', $contacto->getTelefono())){
            $this -> addFlash('error', 'Ingrese un Telefono Contacto valido');
            return false;
        }
        
   
    return true;
    
    
    }
   
    /**
     * validar formulario Modificacion Pasante
     */
    public function validarModificacionPasante($pasante){
        
        /**Validar Nombre */
        if(!preg_match('/^[A-Za-zäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙÑñ  .]{2,60}$/', $pasante->getNombre())){
            $this -> addFlash('error', 'Ingrese un Nombre Contacto valido');
            return false;
        }
        /**Validar Apellido */
        if(!preg_match('/^[A-Za-zäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙÑñ  .]{2,60}$/', $pasante->getApellido())){
            $this -> addFlash('error', 'Ingrese un Apellido Contacto valido');
            return false;
        }
        if(!preg_match('/^[A-Za-zäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙÑñ  .]{2,100}$/', $pasante->getLocalidad())){
            $this -> addFlash('error', 'Ingrese una localidad valida');
            return false;
        }
        if(!preg_match('/^[A-Za-zäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙÑñ0-9  .]{2,100}$/', $pasante->getDireccion())){
            $this -> addFlash('error', 'Ingrese una Dirección valida');
            return false;
        }
   
    return true;
    
    }

     /**
     * validar formulario
     */
    public function validarPasante($pasante){
        $nombre=$pasante->getNombre();
        $cuit= $pasante->getCuil();
        $re = '/^[A-Za-zäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙÑñ  .]{2,100}$/';
        

        /**Validar Nombre */
        if(!preg_match($re, $nombre)){
            $this -> addFlash('error', 'Ingrese un Nombre valido');
            return false;
        }
        if(!preg_match('/^[A-Za-zäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙÑñ  .]{2,100}$/', $pasante->getApellido())){
            $this -> addFlash('error', 'Ingrese un Apellido valido');
            return false;
        }
        /**Validar Cuit */
        if(!preg_match('/^[0-9]{2}+[-]{1}+[0-9]{7,8}+[-]+[0-9]{1,2}$/', $pasante->getCuil())){
            $this -> addFlash('error', 'Ingrese un Cuil/Cuit valido');
            return false;
        }
        /**Validar dni */
        if(!preg_match('/^[0-9]{7,8}$/', $pasante->getDni())){
            $this -> addFlash('error', 'Ingrese un Dni valida');
            return false;
        }
        /**Validar legajo */
        if(!preg_match('/^[0-9]{7,8}$/', $pasante->getLegajo())){
            $this -> addFlash('error', 'Ingrese un Legajo valido');
            return false;
        }
        if(!preg_match('/^[A-Za-zäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙÑñ  .]{2,100}$/', $pasante->getLocalidad())){
            $this -> addFlash('error', 'Ingrese una localidad valida');
            return false;
        }
        if(!preg_match('/^[A-Za-zäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙÑñ0-9  .]{2,100}$/', $pasante->getDireccion())){
            $this -> addFlash('error', 'Ingrese una Dirección valida');
            return false;
        }
        $manager= $this->getDoctrine()->getManager();
       
        $pasanteValidacion= $manager->getRepository(Pasante::class)->findBy(array('legajo'=> $pasante->getLegajo()));
        if($pasanteValidacion != null){
            $this -> addFlash('error', 'Error, la pasante ya esta cargado');
            return false;
        }
        return true;
    }   
}
