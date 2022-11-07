<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\AreaUnRaf;
use App\Entity\Pasantia;
use App\Entity\Convenio;
use App\Form\AreaUnRafType;
use Symfony\Component\HttpFoundation\Request;

/**
     * @Route("/user")
     */
class AreaUnRafController extends AbstractController
{
    /**
     * @Route("/registroAreaUnRaf", name="registroAreaUnRaf")
     */
    public function registroAreaUnRaf(Request $request)
    {
        try {
            $areaUnRaf = new AreaUnRaf();
            $dateTime = new \DateTime();
            $username = $this->getUser();
            
            $formulario = $this->createForm(AreaUnRafType::class,$areaUnRaf);
            $formulario -> handleRequest($request);
            if ($formulario->isSubmitted() && $formulario->isValid() && $this->validarArea($areaUnRaf)){
                
                $entManager = $this->getDoctrine()->getManager();
                $areaUnRaf->setFechaModificacion($dateTime);
                $areaUnRaf->setUltimoUsuario("".$username->getUsername());
                $entManager->persist($areaUnRaf);
                $entManager->flush();
                $this -> addFlash('info', '¡El Departamento se ha registrado exitosamente!');
                return $this->redirectToRoute('listarAreaUnRaf');
            }
        } catch (\Throwable $th) {
            $this -> addFlash('error', '¡Error en el registro de departamentos!'.$th);
            return $this->render('areaUnRaf/index.html.twig', [
                'formulario' => $formulario ->createView(),
            ]);
        }
        return $this->render('areaUnRaf/index.html.twig', [
            'formulario' => $formulario ->createView(),
        ]);
    }
    /**
     * @Route("/listarAreaUnRaf", name="listarAreaUnRaf")
     */
    public function listarAreaUnRaf(Request $request)
    {
        try {
            $manager=$this->getDoctrine()->getManager();
            $form = $this->createForm(AreaUnRafType::class,new AreaUnRaf());
            $form->handleRequest($request);
            
            $areaUnRafs= $manager->getRepository(AreaUnRaf::class)->findAll();
        } catch (\Throwable $th) {
            $this -> addFlash('error', '¡Error al listar las areas!'.$th);
            return $this->redirectToRoute("inicio");
        }
        return $this->render('areaUnRaf/listarAreaUnRaf.html.twig',
                ['areaUnRafs' => $areaUnRafs]
            );
    }
    
    /**
     * @Route("/modificarAreaUnRaf/{id}", name="modificarAreaUnRaf")
     */
    
    public function modificarAreaUnRaf(Request $request, $id)
    {
        try {
            $manager=$this->getDoctrine()->getManager();
            $username = $this->getUser();
            $dateTime = new \DateTime();
            $areaUnRaf= $manager->getRepository(AreaUnRaf::class)->find($id	);
            
            $form = $this->createForm(AreaUnRafType::class,$areaUnRaf);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid() && $this->validarEmpresa($areaUnRaf) ){
                $areaUnRaf->setFechaModificacion($dateTime);
                $areaUnRaf->setUltimoUsuario("".$username->getUsername());
                $manager->flush();
                
                $this -> addFlash('info', '¡El Areas Encargadas se ha registrado exitosamente!');
                return $this->redirectToRoute('listarAreaUnRaf');
                
            }
        } catch (\Throwable $th) {
            $this -> addFlash('error', '¡Error al modificar el departamento!'.$th);
            return $this->render('areaUnRaf/modificarAreaUnRaf.html.twig',
            ['formulario' => $form->createView()]
            );
        }
        return $this->render('areaUnRaf/modificarAreaUnRaf.html.twig',
        ['formulario' => $form->createView()]
        );
    }
    /**
     * @Route("/eliminarAreaUnRaf/{id}", name="eliminarAreaUnRaf")
     */
public function eliminarAreaUnRaf(Request $request, $id)
    {
        try {
            $manager=$this->getDoctrine()->getManager();
        
            $form = $this->createForm(AreaUnRafType::class,new AreaUnRaf());
            $form->handleRequest($request);
            
            $areaUnRaf= $manager->getRepository(AreaUnRaf::class)->find($id);
            $areasActuales= array();
            $areasEncargada= array();
            $ultimasArea= array();
            $areasSiguiente= array();
            $areasActuales= $manager->getRepository(Pasantia::class)->findByareaActual($areaUnRaf);
            $areasEncargada=$manager->getRepository(Pasantia::class)->findByareaEncargada($areaUnRaf);
            $ultimasArea=$manager->getRepository(Convenio::class)->findByultimaArea($areaUnRaf);
            $areasSiguiente=$manager->getRepository(Convenio::class)->findByareaSiguiente($areaUnRaf);
            //verifico que no sea parte de una pasantia
            if( $areasActuales!=null && $areasActuales!=0 & $areasEncargada!=null & $areasEncargada!=0 ){
                $this -> addFlash('error', '¡El Departamento es parte de una pasantía!');
                return $this->listarAreaUnRaf($request);
            }
            //verifico que no sea parte de un convenio
            if( $ultimasArea!=null && $ultimasArea!=0 && $areasSiguiente!=null && $areasSiguiente!=0 ){
                $this -> addFlash('error', '¡El Departamento es parte de un Convenio!');
                return $this->listarAreaUnRaf($request);
            }
            $manager->remove($areaUnRaf);
            $manager->flush();
        } catch (\Throwable $th) {
            $this -> addFlash('error', '¡Error al eliminar el area!');
            return $this->listarAreaUnRaf($request);
        }
        $this -> addFlash('info', '¡El AreaUnRaf se ha eliminado exitosamente!');
        return $this->listarAreaUnRaf($request);
        
    }
    /**
     * validar formulario
     */
    public function validarArea($areaUnRaf){
        $nombre=$areaUnRaf->getNombre();
        $re = '/^[A-Za-zñÑäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙ .-]{2,149}$/';
        $manager=$this->getDoctrine()->getManager();
        

        /**Validar Nombre del Area*/
        if(!preg_match($re, $nombre)){
            $this -> addFlash('error', 'Ingrese un Nombre valido');
            return false;
        }
        /**Validar Nombre Encargado */
        if(!preg_match($re, $areaUnRaf->getEncargado())){
            $this -> addFlash('error', 'Ingrese un nombre de encargado valido');
            return false;
        }
        /**Validar Telefono */
        if(!preg_match('/^(?:(?:00)?549?)?0?(?:11|[2368]\d)(?:(?=\d{0,2}15)\d{2})??\d{8}$/', $areaUnRaf->getTelefono())){
            $this -> addFlash('error', 'Ingrese un Telefono valido');
            return false;
        }
        
        
        $areaValidacion= $manager->getRepository(AreaUnRaf::class)->find($areaUnRaf->getNombre());
        if(!$areaValidacion==null ){
            $this -> addFlash('error', 'Error, El area ya esta cargada');
            return false;
        }
        return true;
    }
}
