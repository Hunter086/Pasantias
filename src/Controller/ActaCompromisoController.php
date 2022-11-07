<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Spipu\Html2Pdf\Html2Pdf;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Stream;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
//entities
use App\Entity\Pasantia;
use App\Entity\Pasante;
use App\Entity\ActaCompromiso;

use App\Form\ActaCompromisoType;
/**
     * @Route("/admin")
     */
class ActaCompromisoController extends AbstractController
{
    /**
     * @Route("/agregar_actacompromiso/{idpasante}/{idpasantia}", name="agregar_actacompromiso")
     * 
     */
    public function agregar_actacompromiso(Request $request,SluggerInterface $slugger, $idpasante, $idpasantia)
    {
            try {
                $manager=$this->getDoctrine()->getManager();
                $dateTime = new \DateTime();
                $year = $dateTime->format('d-m-Y');
                $actaCompromiso = new ActaCompromiso();
                $pasantia = $manager->getRepository(Pasantia::class)->find($idpasantia);
                $pasante = $manager->getRepository(Pasante::class)->find($idpasante);
                $formulario = $this->createForm(ActaCompromisoType::class, $actaCompromiso);
                $formulario->handleRequest($request);

                //unlink("DocumentoActaCompromiso/".$|->getDocumentoConvenio());
                if ($formulario->isSubmitted() && $formulario->isValid()) {
                    /** @var UploadedFile $brochureFile */
    
                        $brochureFile = $formulario->get('path')->getData();
                        // this condition is needed because the 'brochure' field is not required
                        // so the PDF file must be processed only when a file is uploaded
                        if ($brochureFile) {
                            $originalFilename = pathinfo('DocumentoActaCompromiso'.$year, PATHINFO_FILENAME);
                            //$brochureFile->getClientOriginalName()
                            // this is needed to safely include the file name as part of the URL
                            $safeFilename = $slugger->slug($originalFilename);
                            $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
                            //si existe el acta compromiso
                            $actaCompromiso=$manager->getRepository(ActaCompromiso::class)->findOneBy(array(
                                'pasantia' => $idpasantia,
                                'pasante' => $idpasante
                              )       // $where          
                            );
                            // ya Existe acta?
                            if($actaCompromiso!= null && $actaCompromiso!=""){
                                $actaCompromiso->setPath($newFilename);
                                $actaCompromiso->setFecha($dateTime);
                                $actaCompromiso->setEstado('Cargada');
                                $manager->persist($actaCompromiso);
                                $manager->flush();
                                $brochureFile->move(
                                    $this->getParameter('DocumentoActaCompromiso'),
                                    $newFilename
                                );    
                            }else {
                                $actaCompromiso = new ActaCompromiso();
                                $actaCompromiso->setPath($newFilename);
                                $actaCompromiso->setFecha($dateTime);
                                $actaCompromiso->setPasantia($pasantia);
                                $actaCompromiso->setPasante($pasante);
                                $actaCompromiso->setEstado('Cargada');
                                //agregar a pasantia
                                $pasantia->addActaCompromiso($actaCompromiso);
                                $manager->persist($pasantia);
                                $manager->persist($actaCompromiso);
                                $manager->flush();
                                $brochureFile->move(
                                    $this->getParameter('DocumentoActaCompromiso'),
                                    $newFilename
                                );              
                            }
                        }        
                    $this -> addFlash('info', '¡Datos cargados exitosamente!');
                    return $this->redirectToRoute('verDatosPasantiaCargada',['id'=> $idpasantia]);
                    
                    
                }

            } catch (\Throwable $th) {
                throw $th;
                
            }


            return $this->render('acta_compromiso/agregarActaCompromiso.html.twig',[
                'formulario' => $formulario->createView(),
            ]);
    }
     /**
     * @Route("/eliminar_actacompromiso/{idpasante}/{idpasantia}", name="eliminar_actacompromiso")
     */
    public function eliminar_actacompromiso(Request $request, $idpasante, $idpasantia)
    {
            try {
                $manager=$this->getDoctrine()->getManager();
                $actaCompromiso=$manager->getRepository(ActaCompromiso::class)->findOneBy(array(
                    'pasantia' => $idpasantia,
                    'pasante' => $idpasante
                  )       // $where          
                );
                if ($actaCompromiso!=null && $actaCompromiso!='') {
                    unlink("DocumentoActaCompromiso/".$actaCompromiso->getPath());
                    $actaCompromiso->setEstado("Eliminada");
                    $actaCompromiso->setPath("");
                    $manager->persist($actaCompromiso);
                    $manager->flush();
                    $this -> addFlash('info', '¡El documento ha sido eliminado!');
                    return $this->redirectToRoute('verDatosPasantiaCargada',['id'=> $idpasantia]);
                }
            } catch (\Throwable $th) {
                $this -> addFlash('error', '¡Error en la cargar los datos!'.$th);
                return $this->redirectToRoute('verDatosPasantiaCargada',['id'=> $idpasantia]);
            }

            $this -> addFlash('error', '¡Error en la cargar los datos!');
            return $this->redirectToRoute('verDatosPasantiaCargada',['id'=> $idpasantia]);
    }
    /**
     * @Route("/ver_actacompromiso", name="ver_actacompromiso")
     */
    public function ver_actacompromiso(Request $request){
        try {
            $manager= $this->getDoctrine()->getManager();
            $actaCompromiso = new ActaCompromiso();
            $idpasantia = $request->get('idpasantia');
            $idpasante = $request->get('idpasante');
            $actaCompromiso =$manager->getRepository(ActaCompromiso::class)->findOneBy(array(
                'pasantia' => $idpasantia,
                'pasante' => $idpasante
              )       // $where          
            );
            //si no existe se envia al usuario a que cargue el acta
            if($actaCompromiso!=null & $actaCompromiso!=""){
                $path= "DocumentoActaCompromiso/".$actaCompromiso->getPath();
                //existe el la ruta del archivo
                if (file_exists($path)) {
                    header("Content-type: application/pdf");
                    header("Content-Disposition: inline; filename=documento.pdf");
                    //mostrar archivo
                    readfile($path);
                } else {
                    $this -> addFlash('error', '¡El archivo no exite!');
                    return $this->redirectToRoute('verDatosPasantiaCargada',['id'=> $idpasantia->getId()]);
                }
            }else {
                $this -> addFlash('error', '¡El archivo no exite!');
                return $this->redirectToRoute('agregar_actacompromiso',['idpasantia'=> $idpasantia, 'idpasante' => $idpasante]);
            }
        } catch (\Throwable $th) {
            $this -> addFlash('error', '¡Error al ejecutar la función!');
            return $this->redirectToRoute('verDatosPasantiaCargada',['id'=> $idpasantia->getId()]);
        }
    }
}
