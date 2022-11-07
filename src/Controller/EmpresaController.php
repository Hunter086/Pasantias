<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Regex;
use Doctrine\Common\Collections\ArrayCollection;

use App\Entity\Empresa;
use App\Entity\Pasantia;
use App\Entity\Contacto;
use App\Form\EmpresaType;
use App\Form\ContactoType;
use App\Form\SeguimientoOrganizacionType;

use App\Entity\Convenio;
use App\Repository\EmpresaRepository;
use App\Repository\UserRepository;

/**
* @Route("/admin")
*/
class EmpresaController extends AbstractController
{
    /**
     * @Route("/registroEmpresa", name="registroEmpresa")
     */
    public function registroEmpresa(Request $request)
    {
        $empresa = new Empresa();
        $contacto = new Contacto();
        $username = $this->getUser();
        $dateTime = new \DateTime();


        $formulariocontacto = $this->createForm(ContactoType::class,$contacto);
        $formulariocontacto -> handleRequest($request);
        
        $formulario = $this->createForm(EmpresaType::class,$empresa);
        $formulario -> handleRequest($request);
        
        
        if ($formulario->isSubmitted() && $formulario->isValid() && $this->validarEmpresa($empresa) && $this->validarContacto($contacto) ){
            try {
                $empresa->setFechaModificacion($dateTime);
                $empresa->setUltimoUsuario("".$username->getUsername());
                $empresa->setEstado('Inactiva');
                $empresa->addContacto($contacto);
                $entManager = $this->getDoctrine()->getManager();
                $entManager->persist($empresa);
               
                $entManager->flush();
                $this -> addFlash('info', '¡La Organización se ha registrado exitosamente!');
                return $this->redirectToRoute('listarEmpresaInactivas');
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡La Organización no se registro correctamente!'.$th);
                return $this->render('empresa/nuevaorganizacion.html.twig', [
                    'formulario' => $formulario ->createView(),'formulariocontacto' => $formulariocontacto ->createView(),
                ]);
            }
        }
        return $this->render('empresa/nuevaorganizacion.html.twig', [
            'formulario' => $formulario ->createView(),'formulariocontacto' => $formulariocontacto ->createView(),
        ]);
    }
    /**
     * @Route("/listarEmpresa", name="listarEmpresa")
     */
    public function listarEmpresa(Request $request)
    {
        try {
            $manager=$this->getDoctrine()->getManager();
   
        
            $empresa= $manager->getRepository(Empresa::class)->findAll();
        } catch (\Throwable $th) {
            $this -> addFlash('error', '¡Error al listar organizaciones!');
            return $this->redirectToRoute("inicio");
        }
        return $this->render('empresa/listarEmpresa.html.twig',
                ['empresa' => $empresa]
            );
    }
     /**
     * @Route("/listarEmpresaActiva", name="listarEmpresaActiva")
     */
    public function listarEmpresaActiva(Request $request)
    {
        try {
            $manager=$this->getDoctrine()->getManager();
        
            $empresas= array();
            $empresas= $manager->getRepository(Empresa::class)->findBy(
                array(
                  'estado' => 'Activa'
                )        
              );
        } catch (\Throwable $th) {
            $this -> addFlash('error', '¡Error al listar organizaciones Activas!');
            return $this->redirectToRoute("inicio");
        }
        
        return $this->render('empresa/listarEmpresa.html.twig',
                ['empresas' => $empresas]
            );
    }
    /**
     * @Route("/listarEmpresaInactivas", name="listarEmpresaInactivas")
     */
    public function listarEmpresaInactivas(Request $request)
    {
        try {
            $manager=$this->getDoctrine()->getManager();
        
        
            $empresaInactivas= $manager->getRepository(Empresa::class)->findAll();
            $empresas= array();
            if($empresaInactivas!=null){
                foreach ($empresaInactivas as $empresaInactiva) {
                    if($empresaInactiva->getEstado()=='Inactiva'){
                        array_push($empresas, $empresaInactiva);
                    }
                }
            }
        } catch (\Throwable $th) {
            $this -> addFlash('error', '¡Error al listar organizaciones Inactivas!');
            return $this->redirectToRoute("inicio");
        }
        
        return $this->render('empresa/listarEmpresaInactivas.html.twig',
                ['empresas' => $empresas]
            );
    }
    
    /**
     * @Route("/modificarEmpresa/{id}", name="modificarEmpresa")
     */
    
    public function modificarEmpresa(Request $request, $id)
    {
        try {
            $manager=$this->getDoctrine()->getManager();
        $username = $this->getUser();
        $dateTime = new \DateTime();
        $empresa= $manager->getRepository(Empresa::class)->find($id);
        $contacto = new Contacto();

        $formulariocontacto = $this->createForm(ContactoType::class,$contacto);
        $formulariocontacto -> handleRequest($request);

        $form = $this->createForm(EmpresaType::class,$empresa);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid() && $this->validarModificacionEmpresa($empresa) ){
            $empresa->setFechaModificacion($dateTime);
            $empresa->setUltimoUsuario("".$username->getUsername());
            $empresa->addContacto($contacto);
            $manager->flush();
            $this -> addFlash('info', '¡La Organización se ha registrado exitosamente!');
            if($empresa->getEstado()=="Inactiva"){
                return $this->redirectToRoute('listarEmpresaInactivas');
            }
            return $this->redirectToRoute('listarEmpresaActiva');
            
        }
        } catch (\Throwable $th) {
            $this -> addFlash('error', '¡Error al cargar la organización!');
            return $this->render('empresa/modificarEmpresa.html.twig',
                ['formulario' => $form->createView(),'contacto'=> $empresa->getContacto(), 'formulariocontacto' => $formulariocontacto ->createView(),]
            );
        }
        
        return $this->render('empresa/modificarEmpresa.html.twig',
                ['formulario' => $form->createView(),'contacto'=> $empresa->getContacto(), 'formulariocontacto' => $formulariocontacto ->createView(),]
            );
    }
    /**
     * @Route("/eliminarEmpresa/{id}", name="eliminarEmpresa")
     */
public function eliminarEmpresa(Request $request, $id)
    {
        $manager=$this->getDoctrine()->getManager();
        
        $form = $this->createForm(EmpresaType::class,new Empresa());
        $form->handleRequest($request);
        
        $empresa= $manager->getRepository(Empresa::class)->find($id);
        
       try {
        if($empresa->getConvenio()!=null){
            $convenio= $empresa->getConvenio();
            foreach ($convenio as $convenios) {
                if($convenios->getEstadoConvenio()=='Activo'){
                    $this -> addFlash('error', '¡La Organización es parte de un Convenio Marco!');
                    return $this->listarEmpresa($request);
                }
            }
        }
            $empresa->setEstado('Inactiva');
            $manager->flush();
            $this -> addFlash('info', '¡La Organización se ha inhabilitado exitosamente!');
            return $this->listarEmpresa($request);
       } catch (\Throwable $th) {
        $this -> addFlash('error', '¡Error en el sistema verificar eliminar La Organización!');
       }
       return $this->listarEmpresa($request);
        
        
    }
    /**
     * @Route("/eliminarEmpresaInactiva/{id}/", name="eliminarEmpresaInactiva")
     */
    public function eliminarEmpresaInactiva(Request $request, $id)
    {
        try {
            $manager=$this->getDoctrine()->getManager();
        
        
            $empresa= $manager->getRepository(Empresa::class)->find($id);
            $empresaenConvenio= $manager->getRepository(Convenio::class)->findByempresa($empresa);

            if($empresaenConvenio==null){
                $manager->remove($empresa);
                $manager->flush();
                $this -> addFlash('info', '¡La Organización fue eliminada exitosamente!');
                return $this->redirectToRoute ('listarEmpresaInactivas');
                
            }else{
                $this -> addFlash('error', '¡La Organización es parte de un Convenio!');
                return $this->redirectToRoute ('listarEmpresaInactivas');
            }
        } catch (\Throwable $th) {
            $this -> addFlash('error', '¡Error al eliminar empresa inactiva!');
            return $this->redirectToRoute ('listarEmpresaInactivas');
        }
        $this -> addFlash('error', '¡Error en el sistema verificar eliminar Empresa!');
        return $this->redirectToRoute ('listarEmpresaInactivas');
        
        
    }
    
    /**
     * @Route("/eliminarContactoEmpresa/{id}", name="eliminarContactoEmpresa")
     */
public function eliminarContactoEmpresa(Request $request, $id)
{
    try {
        $manager=$this->getDoctrine()->getManager();
    
        $form = $this->createForm(EmpresaType::class,new Empresa());
        $form->handleRequest($request);
        $contacto = new Contacto();
        $formulariocontacto = $this->createForm(ContactoType::class,$contacto);
        $formulariocontacto -> handleRequest($request);
        $contacto= $manager->getRepository(Contacto::class)->find($id);
        $empresa= $manager->getRepository(Contacto::class)->find($contacto->getEmpresa());
        
        $manager->remove($contacto);
        $manager->flush();
    } catch (\Throwable $th) {
        $this -> addFlash('error', '¡Error al eliminar el contacto de la empresa!');
        return $this->redirectToRoute("inicio");
    }
    $this -> addFlash('info', '¡El contacto se a eliminado exitosamente!');
    return $this->redirectToRoute ('modificarEmpresa',['id'=> $contacto->getEmpresa()->getId()]);
    
}
/**
     * @Route("/seguimientoEmpresa/{id}/{idPasantia}", name="seguimientoEmpresa")
     */
    
    public function seguimientoEmpresa(Request $request, $id, $idPasantia)
    {
        $manager=$this->getDoctrine()->getManager();
        $username = $this->getUser();
        $dateTime = new \DateTime();
        $empresa= $manager->getRepository(Empresa::class)->find($id);
        $pasantia= $manager->getRepository(Pasantia::class)->find($idPasantia);
        $formulario = $this->createForm(SeguimientoOrganizacionType::class,$empresa);
        $formulario->handleRequest($request);
        
        if ($formulario->isSubmitted() && $formulario->isValid()){
            try {
                $empresa->setFechaModificacion($dateTime);
                $empresa->setUltimoUsuario("".$username->getUsername());
                $empresa->setFechaSeguimiento($dateTime);
                $manager->persist($empresa);
                $manager->flush();
            $this -> addFlash('info', '¡El seguimiento se ha cargado exitosamente!');
            return $this->redirectToRoute('verDatosPasantiaCargada',['id'=> $idPasantia]);
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al cargar los datos del seguimento!');
                return $this->redirectToRoute('seguimientoEmpresa',['id'=>$id, 'idPasantia'=>$idPasantia ]);
            }
            
            
        }

        return $this->render('empresa/seguimientoOrganizacion.html.twig',
                ['formulario' => $formulario->createView(),'empresa'=> $empresa, 'pasantia'=>$pasantia]
            );
    }
    /**
     * validar formulario
     */
    public function validarEmpresa($empresa){
        $nombre=$empresa->getNombre();
        $cuit= $empresa->getCuit();
        $re = '/^[A-Za-zäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙÑñ  .]{2,149}$/';
        

        /**Validar Nombre */
        if(!preg_match($re, $nombre)){
            $this -> addFlash('error', 'Ingrese un Nombre valido');
            return false;
        }
        /**Validar Cuit */
        if(!preg_match('/^[0-9]{2}+[-]{1}+[0-9]{7,8}+[-]+[0-9]{1,2}$/', $empresa->getCuit())){
            $this -> addFlash('error', 'Ingrese un Cuit valido');
            return false;
        }
        if(!preg_match('/^[A-Za-zäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙÑñ  .]{2,100}$/', $empresa->getLocalidad())){
            $this -> addFlash('error', 'Ingrese una localidad valida');
            return false;
        }
        if(!preg_match('/^[A-Za-zäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙÑñ0-9  .]{2,100}$/', $empresa->getDireccion())){
            $this -> addFlash('error', 'Ingrese una Dirección valida');
            return false;
        }
        
        $manager= $this->getDoctrine()->getManager();
        /**Validar Razon Social */
        if(!preg_match('/^[A-Za-zäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙÑñ  .]{2,149}$/', $empresa->getRazonSocial())){
            $this -> addFlash('error', 'Ingrese una Razón Social valida');
            return false;
        }
        $manager= $this->getDoctrine()->getManager();
       
        $empresaValidacion= $manager->getRepository(Empresa::class)->findBy(array('cuit'=> $empresa->getCuit()));
        if($empresaValidacion != null){
            $this -> addFlash('error', 'Error, la empresa ya esta cargada');
            return false;
        }
        return true;
    }
    /**
     * validar formulario
     */
    public function validarModificacionEmpresa($empresa){
        $nombre=$empresa->getNombre();
        $cuit= $empresa->getCuit();
        $re = '/^[A-Za-zäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙÑñ  .]{2,149}$/';
        

        /**Validar Nombre */
        if(!preg_match($re, $nombre)){
            $this -> addFlash('error', 'Ingrese un Nombre valido');
            return false;
        }
        /**Validar Cuit */
        if(!preg_match('/^[0-9]{2}+[-]{1}+[0-9]{7,8}+[-]+[0-9]{1,2}$/', $empresa->getCuit())){
            $this -> addFlash('error', 'Ingrese un Cuit valido');
            return false;
        }
        if(!preg_match('/^[A-Za-zäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙÑñ  .]{2,100}$/', $empresa->getLocalidad())){
            $this -> addFlash('error', 'Ingrese una localidad valida');
            return false;
        }
        if(!preg_match('/^[A-Za-zäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙÑñ0-9  .]{2,100}$/', $empresa->getDireccion())){
            $this -> addFlash('error', 'Ingrese una Dirección valida');
            return false;
        }
        /**Validar Razon Social */
        if(!preg_match('/^[A-Za-zäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙÑñ  .]{2,149}$/', $empresa->getRazonSocial())){
            $this -> addFlash('error', 'Ingrese una Razón Social valida');
            return false;
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
     * @Route("verDatosEmpresa/{id}", name="verDatosEmpresa")
     */
    public function verDatosEmpresa(Request $request, $id){
        try {
            $manager=$this->getDoctrine()->getManager();

            $empresa= $manager->getRepository(Empresa::class)->find($id);
        } catch (\Throwable $th) {
            $this -> addFlash('error', '¡Error al ver los datos de la empresa');
            return $this->redirectToRoute("inicio");
        }
        return $this->render('empresa/datosEmpresa.html.twig', ['empresa' => $empresa]
            );
     }
}
