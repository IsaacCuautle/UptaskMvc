<?php 

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController{
    public static function login(Router $router){
        $alertas =[];
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarLogin();

            if(empty($alertas)){
                //Verificar que el usuario exista
                $usuario = Usuario::where('email',$usuario->email);
                if(!$usuario || !$usuario->confirmado){
                    Usuario::setAlerta('error','El usuario no existe o no esta confirmado');
                }
                else{
                    //El usuario existe
                    if(password_verify($_POST['password'],$usuario->password)){
                        //Iniciar la sesion del usuario
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //Redireccionar
                        header('Location: /dashboard');
                        
                    }
                    else{
                        Usuario::setAlerta('error','El password es incorreto');
                    }
                }
                
            }
        $alertas = Usuario::getAlertas();
        }
        
        //Render a la vista
        $router->render('auth/login',[
            'titulo' => 'Inciar Sesion',
            'alertas' => $alertas
        ]);
    }

    public static function logout(){
        session_start();
        $_SESSION = [];
        header('Location: /');

        
    }

    public static function crear(Router $router){
        $alertas=[];
        $usuario = new Usuario;

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $usuario->sincronizar($_POST);
            // debuguear($usuario);
            $alertas = $usuario->validacionNuevaCuenta();
            // debuguear($alertas);
            if(empty($alertas)){
                $existeUsuario = Usuario::where('email',$usuario->email);

                if($existeUsuario){
                Usuario::setAlerta('error','Este Usuario ya esta registrado');
                $alertas = Usuario::getAlertas();
                }else{
                //Hashear el password
                $usuario->hashPassword();

                //Eliminar password 2
                unset($usuario->password2);

                //generar el token
                $usuario->crearToken();

                //Crear un nuevo usuario
                $resultado = $usuario->guardar();

                //Enviar email
                $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                $email->enviarConfirmacion();
                
                    if($resultado){
                    header('Location: /mensaje');
                    }
                }
            }
        }
        //Render a la vista
        $router->render('auth/crear',[
            'titulo' => 'Crea tu cuenta en UpTssk',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router){
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas)){
                //Buscar el usuario
                $usuario = Usuario::where('email',$usuario->email);

                if($usuario && $usuario->confirmado){
                    //Generar un nuevo token
                    $usuario->crearToken();
                    unset($usuario->password2);

                    //Actualizar el usuario
                    $usuario->guardar();
                    //Enviar email
                    $email = new Email($usuario->email,$usuario->nombre,$usuario->token);
                    $email->enviarInstrucciones();

                    //Imprimir una alerta
                    Usuario::setAlerta('exito','Hemos enviado las instrucciones a tu email');
                    $alertas = Usuario::getAlertas();
                }else{
                    Usuario::setAlerta('error','El usuario no, existe o no esta confrimado');
                    $alertas = Usuario::getAlertas();
                }
            }
        }

        $router->render('auth/olvide',[
            'titulo' => 'Recupera tu Cuenta',
            'alertas' => $alertas
        ]);
    }

    public static function restablecer(Router $router){
        $token = s($_GET['token']);
        $mostrar = true;
        if(!$token){ header('Location: /'); }

        //Identificar el usuario con este token
        $usuario = Usuario::where('token',$token);
        if(empty($usuario)){
            Usuario::setAlerta('error','Token no valido');
            $mostrar = false;
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Añadir el nuevo password
            $usuario->sincronizar($_POST);

            //validar el password
            $alertas = $usuario->validarPassword();

            if(empty($alertas)){
                //Hashear el nuevo password
                $usuario->hashPassword();
                //Eliminar el token
                $usuario->token = null;
                //Guardar el usuario en l base de datos
                $resultado=$usuario->guardar();
                //Redireccionar
                if($resultado){
                    header("Location: /");
                }
            }
        }
        $alertas = Usuario::getAlertas();;
        $router->render('auth/restablecer',[
            'titulo' => 'Restablecer Password',
            'alertas'=>$alertas,
            'mostrar'=>$mostrar
        ]);
    }

    public static function mensaje(Router $router){
        $router->render('auth/mensaje',[
            'titulo' => 'Cuenta Creada Exitosamente'
        ]);

        
    }

    public static function confirmar(Router $router){
        $token = s($_GET['token']);
        if(!$token){header('Location: /');}

        //Encontrar  al usuario con ese token
        $usuario = Usuario::where('token',$token);

        if(empty($usuario)){
            //No se encontro un usuario con ese token
            Usuario::setAlerta('error','Token no Valido');
        }else{
            //Confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->token = null;
            unset($usuario->password2);
            //Guardar en la base de datos
            $usuario->guardar();
            Usuario::setAlerta('exito','Cuenta Confirmada Correctamente');
        }
        $alertas = Usuario::getAlertas();
        
        $router->render('auth/confirmar',[
            'titulo' => 'Confirma tu cuenta UpTask',
            'alertas' => $alertas
        ]);
    }
}
 ?>