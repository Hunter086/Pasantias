<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Vencimiento;
use App\Entity\Pasantia;
use App\Entity\Pasante;
use App\Entity\Convenio;
use App\Entity\Pago;
/**
    * @Route("/user")
*/
class VencimientoController extends AbstractController
{
    /**
     * @Route("/listarvencimientosPasantias", name="listarvencimientosPasantias")
     * 
     */
    public function listarvencimientosPasantias()
    {
        try {
            $manager=$this->getDoctrine()->getManager();
            $pasantiasRenovar= $manager->getRepository(Pasantia::class)->findall();
            $fecha_hoy = new \DateTime();
            $anio= $fecha_hoy->format("Y");
            $pasantias=array();

                foreach ($pasantiasRenovar as $pasantiaRenovar) {
                    if($pasantiaRenovar->getEstado()=='Activa') {
            
                        $fecha_vencimiento_string= $pasantiaRenovar->getFechaFin();
                        $fecha_vencimiento_string =  $fecha_vencimiento_string->format("d-m-Y");
                        $fecha_vencimiento= $pasantiaRenovar->getFechaFin();
                        $anio_vencimiento=   $fecha_vencimiento->format("Y");
            
                        $fecha_vencimiento -> modify('-15 days');
                    
                        
                        if($fecha_vencimiento < $fecha_hoy &&  $anio_vencimiento==$anio){
                            $fecha_vencimiento -> modify('+15 days');
                            array_push($pasantias, $pasantiaRenovar );
                        }
                    }
            }
        } catch (\Throwable $th) {
            $this -> addFlash('error', '¡Error al listar los vencimientos!'.$th);
            return $this->redirectToRoute("inicio");
        }
        return $this->
        render('vencimiento/listarvencimientosPasantias.html.twig',
                ['pasantias' => $pasantias]
            );

    }
    /**
     * @Route("/listarvencimientosConvenios", name="listarvencimientosConvenios")
     * 
     */
    public function listarvencimientosConvenios()
    {
        try {
            $manager=$this->getDoctrine()->getManager();
            $conveniosRenovar= $manager->getRepository(Convenio::class)->findall();
            $fecha_hoy = new \DateTime();
            $anio= $fecha_hoy->format("Y");
            $convenios=array();

                foreach ($conveniosRenovar as $convenioRenovar) {
                    if($convenioRenovar->getEstado()=='Activo') {
            
                        $fecha_vencimiento_string= $convenioRenovar->getFechaFin();
                        $fecha_vencimiento_string =  $fecha_vencimiento_string->format("d-m-Y");
                        $fecha_vencimiento= $convenioRenovar->getFechaFin();
                        $anio_vencimiento=   $fecha_vencimiento->format("Y");
            
                        $fecha_vencimiento -> modify('-15 days');
                    
                        
                        if($fecha_vencimiento < $fecha_hoy &&  $anio_vencimiento==$anio){
                            $fecha_vencimiento -> modify('+15 days');
                            array_push($convenios, $convenioRenovar );
                        }
                    }
            }
        } catch (\Throwable $th) {
            $this -> addFlash('error', '¡Error al listar los vencimientos!'.$th);
            return $this->redirectToRoute("inicio");
        }
        return $this->
        render('vencimiento/listarvencimientosConvenios.html.twig',
                ['convenios' => $convenios]
            );

    }
    /**
     * @Route("/listarvencimientosPagos", name="listarvencimientosPagos")
     * 
     */
    public function listarvencimientosPagos()
    {
        try {
            $manager=$this->getDoctrine()->getManager();
            $pagosRenovar= $manager->getRepository(Pago::class)->findall();
            $fecha_hoy = new \DateTime();
            $anio= $fecha_hoy->format("Y");
            $pagos=array();

                foreach ($pagosRenovar as $pagoRenovar) {
                    $meshoy=   $fecha_hoy->format("M");
                    $mespago=  $pagoRenovar->getFechaPago()->format("M");
                    if($pagoRenovar->getEstado()!='Facturado' && $meshoy<=$mespago){
                        array_push($pagos, $pagoRenovar );
                    }
            }
        } catch (\Throwable $th) {
            $this -> addFlash('error', '¡Error al listar los vencimientos!'.$th);
            return $this->redirectToRoute("inicio");
        }
        return $this->
        render('vencimiento/listarvencimientosPagos.html.twig',
                ['pagos' => $pagos]
            );

    }

}
