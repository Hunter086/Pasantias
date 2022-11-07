<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use App\Entity\User;
use App\encriptado;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends AbstractController
{
    /**
     * @Route("/backIntranet", name="backIntranet")
     */
    public function backIntranet(){
        $encriptado = new encriptado();
        $user = $this->getUser();
        
        if ($user != null){
            $url= "http://intranet.unraf.edu.ar/Intranet/login/" . $encriptado->encriptar($user->getEmail());    
        }else{
            $url= "http://intranet.unraf.edu.ar/Intranet/login/";    
        }
        
        return $this->redirect($url);
    }

    /**
     * @Route("/login/{email}/{rol}/{estado}", name="login")
     */
    public function login($email,$rol,$estado)
    {
        //Instanciamos encriptado.
        $encriptado = new Encriptado();

        //Instancias necesarias
        $em = $this->getDoctrine()->getManager();
        $users = null;

        //desencriptamos el email
        $email = $encriptado->desencriptar($email);
        
        //Buscamos en  la BD por email al usuario.
        $users= $em->getRepository(User::class)->findBy(['email'=>$email]);
        
        //Si no hay un usuario con ese email, lo creamos de 0.
        if($users == null){
            //Instancia
            $user= new User();
            //Le damos los atributos
            $user->setEmail($email);
            
            //Decodificamos los roles, y lo seteamos.
            $rol=json_decode($rol,TRUE);
            $user->setRoles(['ROLE_USER']);

            //El parámetro estado en este caso no lo vamos a agregar.
            //Pero si lo desea, agregue un atributo en la entidad User y actualice la BD
            //$user->setEstado($estado);
            $em->persist($user);
            $em->flush();

            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));

        }else{
            //Si ya existe, entonces simplemente lo guardamos en la sesión
            foreach ($users as  $user){

                //Lo mismo, decodificamos y lo seteamos.
                //$rol=json_decode($rol,TRUE);
                //$user->setRoles($rol);
                
                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $this->get('security.token_storage')->setToken($token);
                $this->get('session')->set('_security_main', serialize($token));
                $em->flush();
            }
        }
        
        return $this->redirectToRoute('inicio');
    }
    /**
     * @Route("/superadmin/roles", name="roles")
     */
    public function OtorgarRol(){
        $manager= $this->getDoctrine()->getManager();
        $usuarios= $manager->getRepository(User::class)->findAll();

        return $this->render('usuarios/roles.html.twig', ['usuarios' => $usuarios]
    );


    }
    /**
     * @Route("/superadmin/cambioSuperAdmin/{id}", name="cambioSuperAdmin")
     */
    public function cambioSuperAdmin(request $request,$id){
        $manager=$this->getDoctrine()->getManager();
        $usuario= $manager->getRepository(User::class)->find($id);
        $usuario->setRoles(['ROLE_USER','ROLE_ADMIN', 'ROLE_SUPERADMIN']);
        $manager->flush($usuario);
        $this->addFlash('info','Se cambió el permiso a Super Admin');
        return $this->redirectToRoute('roles');
    }
    /**
     * @Route("/superadmin/cambioAdmin/{id}", name="cambioSuperAdmin")
     */
    public function cambioAdmin(request $request,$id){
        $manager=$this->getDoctrine()->getManager();
        $usuario= $manager->getRepository(User::class)->find($id);
        $usuario->setRoles(['ROLE_USER','ROLE_ADMIN']);
        $manager->flush($usuario);
        $this->addFlash('info','Se cambió el permiso a Admin');
        return $this->redirectToRoute('roles');
    }
}
