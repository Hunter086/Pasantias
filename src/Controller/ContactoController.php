<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
/**
     * @Route("/user")
     */
class ContactoController extends AbstractController
{
    /**
     * @Route("/registroContacto", name="registroContacto")
     */
    public function registroContacto(Request $request)
    {
        try {
            $contacto = new Contacto();
        
            $formulario = $this->createForm(ContactoType::class,$contacto);
            $formulario -> handleRequest($request);
            
            if ($formulario->isSubmitted() && $formulario->isValid() && $this->validarContacto($contacto) ){
                try {
                    $entManager = $this->getDoctrine()->getManager();
                    $entManager->persist($contacto);
                    $entManager->flush();
                    $this -> addFlash('info', '¡La Contacto se ha registrado exitosamente!');
                    return $this->redirectToRoute('listarContacto');
                } catch (\Throwable $th) {
                    $this -> addFlash('error', '¡La Contacto no se registro correctamente!');
                    return $this->render('Contacto/index.html.twig', [
                        'formulario' => $formulario ->createView(),
                    ]);
                }
            }
        } catch (\Throwable $th) {
            $this -> addFlash('error', '¡Error al registrar el contancto!');
            return $this->render('Contacto/index.html.twig', [
                'formulario' => $formulario ->createView(),
            ]);
        }
        return $this->render('Contacto/index.html.twig', [
            'formulario' => $formulario ->createView(),
        ]);
    }

    /**
     * @Route("/listarContacto", name="listarContacto")
     */
    public function listarContacto(Request $request)
    {
        try {
            $manager=$this->getDoctrine()->getManager();
            $Contacto= $manager->getRepository(Contacto::class)->findAll();
        } catch (\Throwable $th) {
            $this -> addFlash('error', '¡Error al listar contacto!');
            return $this->redirectToRoute("inicio");
        }
        return $this->render('Contacto/listarContacto.html.twig',
                ['Contacto' => $Contacto]
            );
    }
    
    /**
     * @Route("/modificarContacto/{id}", name="modificarContacto")
     */
    
    public function modificarContacto(Request $request, $id)
    {
        try {
            $manager=$this->getDoctrine()->getManager();
        
            $Contacto= $manager->getRepository(Contacto::class)->find($id	);
            
            $form = $this->createForm(ContactoType::class,$Contacto);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid() && $this->validarContacto($Contacto) ){
                
                $manager->flush();
                $this -> addFlash('info', '¡La Contacto se ha registrado exitosamente!');
                return $this->redirectToRoute('listarContacto');
            
            }
        } catch (\Throwable $th) {
            $this -> addFlash('info', '¡Error al cargar contacto!');
            return $this->render('Contacto/modificarContacto.html.twig',
                ['formulario' => $form->createView()]
            );
        }
        
        return $this->render('Contacto/modificarContacto.html.twig',
                ['formulario' => $form->createView()]
            );
    }
    /**
     * @Route("/eliminarContacto/{id}", name="eliminarContacto")
     */
public function eliminarContacto(Request $request, $id)
    {
        try {
            $manager=$this->getDoctrine()->getManager();
            
            $form = $this->createForm(ContactoType::class,new Contacto());
            $form->handleRequest($request);
            
            $Contacto= $manager->getRepository(Contacto::class)->find($id);
            $manager->remove($Contacto);
            $manager->flush();
            $this -> addFlash('info', '¡El Contacto se ha eliminado exitosamente!');
       } catch (\Throwable $th) {
        $this -> addFlash('error', '¡El Contacto es parte de un Convenio Marco!');
        return $this->listarContacto($request);
       }
       return $this->listarContacto($request);
    }
    /**
     * validar formulario
     */
    public function validarContacto($contacto){
        $nombre=$contacto->getNombre();
        $cuit= $contacto->getCuit();
        $re = '/^[A-Za-zäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙÑñ  .]{2,60}$/';
        

        /**Validar Nombre */
        if(!preg_match($re, $nombre)){
            $this -> addFlash('error', 'Ingrese un Nombre valido');
            return false;
        }
        /**Validar Apellido */
        if(!preg_match('/^[A-Za-zäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙÑñ  .]{2,60}$/', $contacto->getApellido())){
            $this -> addFlash('error', 'Ingrese un Apellido valido');
            return false;
        }
        /**Validar Telefono */
        if(!preg_match('/^(?:(?:00)?549?)?0?(?:11|[2368]\d)(?:(?=\d{0,2}15)\d{2})??\d{8}$/', $contacto->getTelefono())){
            $this -> addFlash('error', 'Ingrese un Telefono valido');
            return false;
        }
        return true;
    }
    /**
     * @Route("verDatosContacto/{id}", name="verDatosContacto")
     */
    public function verDatosContacto(Request $request, $id){
        try {
            $manager=$this->getDoctrine()->getManager();

            $Contacto= $manager->getRepository(Contacto::class)->find($id);
        } catch (\Throwable $th) {
            $this -> addFlash('error', '¡Error al cargar los datos del contacto!');
            return $this->redirectToRoute("inicio");
        }
        return $this->render('Contacto/datosContacto.html.twig', ['Contacto' => $Contacto]
            );
     }
}
