<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Convenio;
use App\Entity\Pasantia;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use App\Entity\User;
use App\encriptado;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class InicioController extends AbstractController
{
    /**
     * @Route("/superAdmin/email/{motivo}/{mensaje}", name="sendEmail")
     */
    public function sendEmail(MailerInterface $mailer,$motivo,$mensaje)
    {
        
        $email = (new Email())
            ->from('test@unraf.edu.ar')
            ->to('lucas.bauducco@unraf.edu.ar')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')

            ->priority(Email::PRIORITY_HIGH)
            ->subject($motivo)
            ->text('')
	    ->html("
		<div>
			<div style='border:3 px solid #fc447c;width:500px; padding:15px;'>
		        	<h2 style='color:#0F9FA8;text-align:center;'>Mensaje desde Intranet</h2>
				<hr>
				<h4>Usuario: ". $this->getUser()->getEmail() ."</h4>
				<hr>
				<h4 style='color:#fc447c;'>Motivo de mensaje: ". $motivo ."</h4>
				<hr>
				<h4 style='color:#2B2B2B;'>Mensaje: ". $mensaje ."</h4>
				<hr>
			</div>
		</div>

	    ");

        $mailer->send($email);
	$this->addFlash('info', 'Se ha enviado el mail correctamente. Pronto tendrá su respuesta');
	return $this->redirecttoroute('inicio');
    }
    /**
     * @Route("/", name="redirectInicio")
     */
    public function redirectInicio(Request $request)
    {
        $username = $this->getUser();
        return $this->redirectToRoute("inicio");
    }
    /**
     * @Route("/user/inicio", name="inicio")
     */
    public function index()
    {
        $username = $this->getUser();
        $manager=$this->getDoctrine()->getManager();
   
        
        $convenio= $manager->getRepository(Convenio::class)->findAll();
        $pasantia= $manager->getRepository(Pasantia::class)->findAll();
        $fecha_hoy = new \DateTime();

        $anio= $fecha_hoy->format("Y");
        $this->seguimientoPasante($pasantia,  $fecha_hoy);
        $this->seguimientoEmpresa($pasantia,  $fecha_hoy);
        $this->renovacionConvenios($convenio,  $fecha_hoy, $anio);
        $this->renovacionPasantia($pasantia,  $fecha_hoy, $anio);
        $this->vencimientoDePagos($pasantia,  $fecha_hoy, $anio);
        
        return $this->render('inicio/index.html.twig');
    }
    
    /**
     * Reinicia los valores del seguimento del pasante
    */
    public function seguimientoPasante($pasantia,  $fecha_hoy)
    {
        $diafecha= $fecha_hoy->format("j");
        if($pasantia!=null && $diafecha<= "15"){
            foreach ($pasantia as $pasantias) {
                if($pasantias->getEstado()=='Activa') {
                    $pasantes= $pasantias->getPasante();
                    foreach($pasantes as $pasante) {
                        if($pasante->getIsInformeSeguimiento()=='No Realizado' && $pasante->getIsSeguimientodelMes()==false){
                            $newPageUrl = $this->generateUrl('verDatosPasantiaCargada', ['id' =>$pasantias->getId()]);
                            $img= '../img/svg/bell.svg';
                            $this->addFlash('seguimientoPasanteMes',
                                sprintf('<img src="%s" alt="bell"> ¡Falta el seguimiento de:  %s %s! <a href="%s">Ver</a> ',$img,$pasante->getNombre(),$pasante->getApellido(),$newPageUrl)
                            );

                        }
                    }
                   
                }

             }
        }
    }
    /**
     * Reinicia los valores del seguimento del pasante
    */
    public function seguimientoEmpresa($pasantias,  $fecha_hoy)
    {
        $diafecha= $fecha_hoy->format("j");
        if($pasantias!=null && $diafecha<= "15"){
            foreach ($pasantias as $pasantia) {
                if($pasantia->getEstado()=='Activa') {
                    $empresa= $pasantia->getConvenio()->getEmpresa();
                        if($empresa->getIsSeguimientodelmes()!=null || $empresa->getIsSeguimientodelmes()==false){
                            $newPageUrl = $this->generateUrl('verDatosPasantiaCargada', ['id' =>$pasantia->getId()]);
                            $img= '../img/svg/bell.svg';
                            $this->addFlash('seguimientoEmpresaMes',
                                sprintf('<img src="%s" alt="bell"> ¡Falta el seguimiento de:  %s! <a href="%s">Ver</a>',$img,$empresa->getNombre(),$newPageUrl)
                            );
                        }
                   
                }

             }
        }
    }
    /**
     * 
     */
    public function renovacionPasantia($pasantia,  $fecha_hoy, $anio)
    {
        if($pasantia!=null) {
            foreach ($pasantia as $pasantias) {
                if($pasantias->getEstado()=='Activa') {
           
                    $fecha_vencimiento_string= $pasantias->getFechaFin();
                    $fecha_vencimiento_string =  $fecha_vencimiento_string->format("d-m-Y");
                    $fecha_vencimiento= $pasantias->getFechaFin();
                    $anio_vencimiento=   $fecha_vencimiento->format("Y");
        
                    $fecha_vencimiento -> modify('-15 days');
                
                    
                    if($fecha_vencimiento <= $fecha_hoy &&  $anio_vencimiento=$anio){
                        $fecha_vencimiento = $fecha_vencimiento->format("d-m-Y");
                        $fecha_hoy = $fecha_hoy->format("d-m-Y");
                        $id= $pasantias->getId();
                        $newPageUrl = $this->generateUrl('verDatosPasantiaCargada', ['id' =>$pasantias->getId()]);
                        $img= '../img/svg/bell.svg';
                        $this->addFlash('renovacionPasantia', 
                            sprintf('<img src="%s" alt="bell"> ¡Alerta vencimiento de Pasantía! <strong>%s</strong> <a href="%s">Ver</a>',$img,$pasantias->getConvenio()->getEmpresa()->getNombre(), $newPageUrl)
                        );
                    }
                }

             }
        
        }
    }
    /**
     * 
     */
    public function vencimientoDePagos($pasantia,  $fecha_hoy, $anio)
    {
        if($pasantia!=null) {
            foreach ($pasantia as $pasantias) {
                if($pasantias->getEstado()=='Activa') {
                    foreach ($pasantias->getPago() as $pago) {
                        $meshoy=   $fecha_hoy->format("M");
                        $mespago=  $pago->getFechaPago()->format("M");
                        if($pago->getEstado()!='Facturado' && $meshoy<=$mespago){
                                $img= '../img/svg/bell.svg';
                                $this->addFlash('pagoVencido',
                                    sprintf('<img src="%s" alt="bell"> ¡Alerta Pagos sin abonar!', $img)
                                );
                        }
                    }
                }

            }
        
        }
    }
    /**
     * 
     */
    public function renovacionConvenios($convenio,  $fecha_hoy, $anio)
    {
        if($convenio!=null) {
            foreach ($convenio as $convenios) {
                if($convenios->getEstado()=='Activo') {
           
                    $fecha_vencimiento_string= $convenios->getFechaFin();
                    $fecha_vencimiento_string =  $fecha_vencimiento_string->format("d-m-Y");
                    $fecha_vencimiento= $convenios->getFechaFin();
                    $anio_vencimiento=   $fecha_vencimiento->format("Y");
        
                    $fecha_vencimiento -> modify('-15 days');
                
                    
                    if($fecha_vencimiento < $fecha_hoy &&  $anio_vencimiento=$anio){
                        $fecha_vencimiento = $fecha_vencimiento->format("d-m-Y");
                        $fecha_hoy = $fecha_hoy->format("d-m-Y");
                        $id= $convenios->getId();
                        $newPageUrl = $this->generateUrl('modificarFechaRenovacionConvenio', ['id' => $convenios->getId()]);
                        $img= '../img/svg/bell.svg';
                        $this->addFlash('renovacionconvenio',
                            sprintf('<img src="%s" alt="bell"> ¡Alerta Vencimiento de Convenio! <strong>%s</strong> <a href="%s">Ver</a>',$img,$convenios->getEmpresa()->getNombre(), $newPageUrl)
                        );
                    }
                }

             }
        
        }
    }



    
    


}
