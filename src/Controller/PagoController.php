<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use App\Entity\Pasante;
use App\Entity\Pasantia;
use App\Entity\Pago;
use Symfony\Component\HttpFoundation\Request;
use App\Form\PagoType;
use App\Form\ComprobantePagoType;
use App\Form\FacturaPagoType;
use App\Form\NotaCreditoPagoType;
use Symfony\Component\String\Slugger\SluggerInterface;

use Spipu\Html2Pdf\Html2Pdf;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Stream;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\HttpException;


/**
     * @Route("/admin")
     */
class PagoController extends AbstractController
{
     /**
     * @Route("/pago", name="pago")
     */
    public function index(): Response
    {
        return $this->render('pago/index.html.twig', [
            'controller_name' => 'PagoController',
        ]);
    }
    /**
     * @Route("/cargarPagoPasante/{id}/{idPasantia}", name="cargarPagoPasante")
     */
    public function cargarPagoPasante(Request $request, $id, $idPasantia)
    {
        /*if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this -> addFlash('error', '¡No tiene acceso a esta página!');
            return $this->redirect('https://intranet.unraf.edu.ar/');
        }*/
        
        

        $manager=$this->getDoctrine()->getManager();
        $dateTime = new \DateTime();
        $username = $this->getUser();
        $pasante= $manager->getRepository(Pasante::class)->find($id);
        $pasantia= $manager->getRepository(Pasantia::class)->find($idPasantia);
        $pago= new Pago();
        $formulario = $this->createForm(PagoType::class,$pago);
        $formulario->handleRequest($request);
        
        if ($formulario->isSubmitted() && $formulario->isValid()){
            try {
                $pago->setFechaModificacion($dateTime);
                $pago->setUltimoUsuario("".$username->getUsername());
                $pago->setEstado('No Abonado');
                $manager->persist($pago);
                $pago->setPasantia($pasantia);
                $pasante->addPago($pago);
                $manager->flush();
                $this -> addFlash('info', '¡El pago se cargo correctamente!');
                return $this->redirectToRoute('listarPagoporPasantiaPasante',['id'=> $id,'idPasantia'=>$idPasantia]);
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡El pago no se cargo correctamente!'.$th);
                return $this->redirectToRoute('cargarPagoPasante',['id'=> $id,'idPasantia'=>$idPasantia]);
            }
            
            
        }
        
        return $this->render('pago/cargarPago.html.twig',
                ['formulario' => $formulario->createView(),'pasante'=>$pasante,'pasantia'=>$pasantia]
            );
    }
    /**
     * @Route("/cargandoComprobantedePago/{id}/{idPasantia}/{idPasante}", name="cargandoComprobantedePago")
     */
    
    public function cargandoComprobantedePago(Request $request,SluggerInterface $slugger, $id,$idPasantia,$idPasante)
    {
        $manager=$this->getDoctrine()->getManager();
        $dateTime = new \DateTime();
        $year = $dateTime->format('d-m-Y');
        $pasantia= $manager->getRepository(Pasantia::class)->find($idPasantia);
        $pasante= $manager->getRepository(Pasante::class)->find($idPasante);
        $pago= $manager->getRepository(Pago::class)->find($id);
        if($pago->getEstado()=='Facturado'){
        $this -> addFlash('error', '¡Error el pago ya fue facturado!');
        return $this->redirectToRoute('listarPagoporPasantiaPasante',['id'=> $idPasante,'idPasantia'=>$idPasantia]);
        }

        $formulario = $this->createForm(ComprobantePagoType::class,$pago);
        $formulario->handleRequest($request);
        if ($formulario->isSubmitted() && $formulario->isValid()){
            $this->eliminarComprtobantePago($pago, $request, $idPasante, $idPasantia);
             /** @var UploadedFile $brochureFile */
                $brochureFile = $formulario->get('comprobantePago')->getData();
                // this condition is needed because the 'brochure' field is not required
                // so the PDF file must be processed only when a file is uploaded
                if ($brochureFile) {
                    $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                            //$brochureFile->getClientOriginalName()
                            // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
                }else {
                    $this -> addFlash('error', '¡Error al cargar el archivo!');
                    return $this->listarPagoporPasantiaPasante($request, $idPasante, $idPasantia);
                 }
                try {
                    $pago->setFechaCargadePago($dateTime);
                    $pago->setEstado('Pagado');
                    $pago->setComprobantePago($newFilename);
                    $brochureFile->move(
                        $this->getParameter('ComprobantedePago'),
                        $newFilename
                    );
                    $manager->flush();
                    $this -> addFlash('info', '¡El comprobante fue cargado correctamente!');
                    return $this->listarPagoporPasantiaPasante($request, $idPasante, $idPasantia);
                } catch (\Throwable $th) {
                    $this -> addFlash('error', '¡Error al cargar los datos!'.$th);
                    return $this->render('pago/cargarComprobantedePago.html.twig',
                ['formulario' => $formulario->createView(), 'pago'=>$pago, 'pasantia'=>$pasantia]
            );
                }
        }
        
        
            return $this->render('pago/cargarComprobantedePago.html.twig',
                ['formulario' => $formulario->createView(), 'pago'=>$pago, 'pasantia'=>$pasantia]
            );
        
        
    }
    /**
     * @Route("/cargandoFactura/{id}/{idPasantia}/{idPasante}", name="cargandoFactura")
     */
    
    public function cargandoFactura(Request $request,SluggerInterface $slugger, $id,$idPasantia,$idPasante)
    {

        $manager=$this->getDoctrine()->getManager();
        $dateTime = new \DateTime();
        $year = $dateTime->format('d-m-Y');
        $pasantia= $manager->getRepository(Pasantia::class)->find($idPasantia);
        $pasante= $manager->getRepository(Pasante::class)->find($idPasante);
        $pago= $manager->getRepository(Pago::class)->find($id);
        if($pago->getEstado()!='Pagado' && $pago->getEstado()!='Facturado'){
        $this -> addFlash('error', '¡Error no se cargo el comprobante de pago!');
        return $this->listarPagoporPasantiaPasante($request, $idPasante, $idPasantia);
        }
        $formulario = $this->createForm(FacturaPagoType::class,$pago);
        $formulario->handleRequest($request);
        
        if ($formulario->isSubmitted() && $formulario->isValid()){
                $this->eliminarFactura($pago, $request, $idPasante, $idPasantia);
                /** @var UploadedFile $brochureFile */
                $brochureFile = $formulario->get('factura')->getData();
                // this condition is needed because the 'brochure' field is not required
                // so the PDF file must be processed only when a file is uploaded
                if ($brochureFile) {
                    $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                            //$brochureFile->getClientOriginalName()
                            // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
                }else {
                    $this -> addFlash('error', '¡Error al cargar el archivo!');
                    return $this->listarPagoporPasantiaPasante($request, $idPasante, $idPasantia);
                 }
                try {
                    $pago->setFechaCargadePago($dateTime);
                    $pago->setEstado('Facturado');
                    $pago->setFactura($newFilename);
                    $brochureFile->move(
                        $this->getParameter('Factura'),
                        $newFilename
                    );
                    $manager->flush();
                    $this -> addFlash('info', '¡El comprobante fue cargado correctamente!');
                    return $this->listarPagoporPasantiaPasante($request, $idPasante, $idPasantia);
            
                } catch (\Throwable $th) {
                    $this -> addFlash('error', '¡Error al cargar los datos!'.$th);
                    return $this->render('pago/cargarFacturaPago.html.twig',
                ['formulario' => $formulario->createView(), 'pago'=>$pago, 'pasantia'=>$pasantia]
            );
               
            }
        
        }
        
        return $this->render('pago/cargarFacturaPago.html.twig',
                ['formulario' => $formulario->createView(), 'pago'=>$pago, 'pasantia'=>$pasantia]
            );
        
    }
    /**
     * @Route("/cargandoNotadeCredito/{id}/{idPasantia}/{idPasante}", name="cargandoNotadeCredito")
     */
    
    public function cargandoNotadeCredito(Request $request,SluggerInterface $slugger, $id,$idPasantia, $idPasante)
    {
        $manager=$this->getDoctrine()->getManager();
        $dateTime = new \DateTime();
        $year = $dateTime->format('d-m-Y');
        $pasantia= $manager->getRepository(Pasantia::class)->find($idPasantia);
        $pasante= $manager->getRepository(Pasante::class)->find($idPasante);
        $pago= $manager->getRepository(Pago::class)->find($id);
        
        $formulario = $this->createForm(NotaCreditoPagoType::class,$pago);
        $formulario->handleRequest($request);
        
        if ($formulario->isSubmitted() && $formulario->isValid()){
            $this->eliminarNotadeCredito($pago, $request, $idPasante, $idPasantia);
             /** @var UploadedFile $brochureFile */
             $brochureFile = $formulario->get('notadeCredito')->getData();
             // this condition is needed because the 'brochure' field is not required
             // so the PDF file must be processed only when a file is uploaded
             if ($brochureFile) {
                 
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                            //$brochureFile->getClientOriginalName()
                            // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
             }else {
                $this -> addFlash('error', '¡Error al cargar el archivo!');
                return $this->listarPagoporPasantiaPasante($request, $idPasante, $idPasantia);
             }
             try {
                $pago->setFechaCargadePago($dateTime);
                $pago->setEstado('Facturado');
                $pago->setNotadeCredito($newFilename);
                $brochureFile->move(
                    $this->getParameter('NotadeCredito'),
                    $newFilename
                );
                $manager->flush();
                $this -> addFlash('info', '¡El comprobante fue cargado correctamente!');
                return $this->listarPagoporPasantiaPasante($request, $idPasante, $idPasantia);
             } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al cargar los datos!'.$th);
                
            return $this->render('pago/cargandoNotadeCredito.html.twig',
                ['formulario' => $formulario->createView(), 'pago'=>$pago, 'pasantia'=>$pasantia]
            );
             }
        
        }
        
        return $this->render('pago/cargandoNotadeCredito.html.twig',
                ['formulario' => $formulario->createView(), 'pago'=>$pago, 'pasantia'=>$pasantia]
            );
    }
    /**
     *  eliminarNotadeCredito
     */
    public function eliminarNotadeCredito($pago, $request, $idPasante, $idPasantia){

        $manager=$this->getDoctrine()->getManager();
        
            try {
                
                unlink("../PasantiasyBecasFuturxsProfesionalesTest/NotadeCredito/".$pago->getNotadeCredito()); 
               
                $manager->persist($pago);
                $manager->flush();
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al remover la  Nota de Crédito!'.$th);
                return $this->listarPagoporPasantiaPasante($request, $idPasante, $idPasantia);
            }
        
    }
     /**
     *  eliminarFactura
     */
    public function eliminarFactura($pago, $request, $idPasante, $idPasantia){
        $manager=$this->getDoctrine()->getManager();
        
            try {
                
                unlink("../PasantiasyBecasFuturxsProfesionalesTest/Factura/".$pago->getFactura()); 
               
                $manager->persist($pago);
                $manager->flush();
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al remover la Factura!'.$th);
                return $this->listarPagoporPasantiaPasante($request, $idPasante, $idPasantia);
            }
        
    }
    /**
     *  eliminarFactura
     */
    public function eliminarComprtobantePago($pago, $request, $idPasante, $idPasantia){

        $manager=$this->getDoctrine()->getManager();
        
            try {
                
                unlink("../PasantiasyBecasFuturxsProfesionalesTest/ComprobantedePago/".$pago->getComprobantePago()); 
               
                $manager->persist($pago);
                $manager->flush();
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error al remover el Comprobante de Pago!'.$th);
                return $this->listarPagoporPasantiaPasante($request, $idPasante, $idPasantia);
            }
        
    }

    /**
     * @Route("/eliminarPago/{id}/{idPasantia}", name="eliminarPago")
     */
        public function eliminarPago(Request $request, $id, $idPasantia)
        {
            $manager=$this->getDoctrine()->getManager();
            
            
            $pasantia= $manager->getRepository(Pasantia::class)->find($idPasantia);
            $pago= $manager->getRepository(Pago::class)->find($id);
            if($pago->getEstado()=='Facturado'){
                $this -> addFlash('error', '¡El pago ya fue Facturado!');
                return $this->listarPagoporPasantiaPasante($request,$pago->getPasante()->getId(), $idPasantia);
            }
            $manager->remove($pago);
            $manager->flush();
            $this -> addFlash('info', '¡El pago fue eliminado correctamente!');
            return $this->listarPagoporPasantiaPasante($request,$pago->getPasante()->getId(), $idPasantia);
            
            
        }
    
    
    
    /**
     * @Route("/listarPagoporPasantiaPasante/{id}/{idPasantia}", name="listarPagoporPasantiaPasante")
     */
    public function listarPagoporPasantiaPasante(Request $request,$id, $idPasantia)
    {
        $manager=$this->getDoctrine()->getManager();
   
        $pasante= $manager->getRepository(Pasante::class)->find($id);
        $pasantia= $manager->getRepository(Pasantia::class)->find($idPasantia);
        $pagos= $manager->getRepository(Pago::class)->findBy(array('pasante'=> $pasante,'pasantia'=> $pasantia));
        
        
        return $this->render('pago/listarPagoporPasantiaPasante.html.twig',
                ['pagos' => $pagos,'pasantia' => $pasantia,'pasante'=>$pasante]
            );
    }
    /**
     * @Route("/listarPagoporPasantiaPasanteInactiva/{id}/{idPasantia}", name="listarPagoporPasantiaPasanteInactiva")
     */
    public function listarPagoporPasantiaPasanteInactiva(Request $request, $id, $idPasantia)
    {
        $manager=$this->getDoctrine()->getManager();
   
        $pasante= $manager->getRepository(Pasante::class)->find($id);
        $pasantia= $manager->getRepository(Pasantia::class)->find($idPasantia);
        $pagos= $manager->getRepository(Pago::class)->findBy(array('pasante'=> $pasante->getId(),'pasantia'=> $pasantia->getId() ));
        
        
        return $this->render('pago/listarPagoporPasantiaPasanteInactiva.html.twig',
                ['pagos' => $pagos,'pasantia' => $pasantia,'pasante'=>$pasante]
            );
    }
    /**
     * @Route("/verDatosPago/{id}/{idPasantia}", name="verDatosPago")
     */
    public function verDatosPago(Request $request, $id,$idPasantia){

        $manager=$this->getDoctrine()->getManager();

        $pago= $manager->getRepository(Pago::class)->find($id);
        $pasantia= $manager->getRepository(Pago::class)->find($idPasantia);
        return $this->render('pago/verDatosPago.html.twig', ['pago' => $pago, 'pasantia' => $pasantia]
            );
    }
    /**
     * @Route("/verPDFComprovantedePago/{id}", name="verPDFComprovantedePago")
     */
    public function verPDFComprovantedePago(Request $request, $id){
        try {
            $manager= $this->getDoctrine()->getManager();
            $pago= $manager->getRepository(Pago::class)->find($id);
            $comprobante= $pago->getComprobantePago();
            $nombre_fichero = './ComprobantedePago/'.$comprobante;
            
        if (file_exists($nombre_fichero)) {
            header("Content-type: application/pdf");
            header("Content-Disposition: inline; filename=documento.pdf");
            readfile($nombre_fichero);
        } else {
            var_dump("No se encontro El Archivo");
            exit(0);
        }
        } catch (\Throwable $th) {
            var_dump($th);
            exit(0);
        }
    }
    /**
     * @Route("/verPDFFactura/{id}", name="verPDFFactura")
     */
    public function verPDFFactura(Request $request, $id){
        try {
            $manager= $this->getDoctrine()->getManager();
            $pago= $manager->getRepository(Pago::class)->find($id);
            $comprobante= $pago->getFactura();
            $nombre_fichero = './Factura/'.$comprobante;
            
        if (file_exists($nombre_fichero)) {
            header("Content-type: application/pdf");
            header("Content-Disposition: inline; filename=documento.pdf");
            readfile($nombre_fichero);
        } else {
            var_dump("No se encontro El Archivo");
            exit(0);
        }
        } catch (\Throwable $th) {
            var_dump($th);
            exit(0);
        }
    }
    /**
     * @Route("/verPDFNotadeCredito/{id}", name="verPDFNotadeCredito")
     */
    public function verPDFNotadeCredito(Request $request, $id){
        try {
            $manager= $this->getDoctrine()->getManager();
            $pago= $manager->getRepository(Pago::class)->find($id);
            $comprobante= $pago->getNotadeCredito();
            if ($comprobante==null) {
                var_dump("La nota de Crédito no esta cargada");
                exit(0);
            }
            $nombre_fichero = './NotadeCredito/'.$comprobante;
            if (file_exists($nombre_fichero)) {
                header("Content-type: application/pdf");
                header("Content-Disposition: inline; filename=documento.pdf");
                readfile($nombre_fichero);
            } else{
                var_dump("No se encontro El Archivo");
                exit(0);
            }
        } catch (\Throwable $th) {
            var_dump("No se encontro El Archivo");
            exit(0);
        }
    }
    

    
     /**
     * 
     */
    public function validarPago($pago)
    {
        

        /**Validar Nombre */
        if(!preg_match('/^[+-]?[0-9]+\.[0-9]+$/', $pago->getTotalAbonado())){
            $this -> addFlash('error', 'Ingrese un Total Abonado valido');
            return false;
        }
        /**Validar Nª de expediente */
        if(!preg_match('/^[+-]?[0-9]+\.[0-9]+$/',  $pago->getTotalaCobrar())){
            $this -> addFlash('error', 'Ingrese un TotalaCobrar valido');
            return false;
        }
       
        return true;
    }
    
}
