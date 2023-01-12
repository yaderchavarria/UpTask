<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    
    public static function login(Router $router) {

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin();

            if (empty($alertas)) {
                //verificar que el usuario exista

                $usuario = Usuario::where('email',$auth->email);

                if (!$usuario || !$usuario->confirmado) {
                    Usuario::setAlerta('error','El usuario no existe o no está confirmado');
                } else {
                    //el usuario existe
                    if (password_verify($_POST['password'],$usuario->password)) {
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //redireccionar
                        header('Location: /dashboard');

                        
                    } else {
                        Usuario::setAlerta('error','Password incorrecto');
                    }
                }
            }
        }

        $alertas = Usuario::getAlertas();
        //render a la vista
        $router->render('auth/login',[
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }

    public static function logout() {
        
        session_start();
        $_SESSION = [];

        header('Location: /');
    }

    public static function crear(Router $router) {

        $alertas = [];

        $usuario = new Usuario;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validarNuevaCuenta();

            

            if (empty($alertas)) {

                $existeUsuario = Usuario::where('email',$usuario->email);

                if ($existeUsuario) {
                    Usuario::setAlerta('error','El usuario ya está registrado');
                    $alertas = Usuario::getAlertas();
                } else {
                    //hashear el password
                    $usuario->hashPassword();

                    //eliminar password2
                    unset($usuario->password2);

                    //generar el token
                    $usuario->crearToken();
                    //crear un nuevo usuario

                    $resultado = $usuario->guardar();

                    //Enviar email

                    $email = new Email($usuario->email,$usuario->nombre,$usuario->token);
                    $email->enviarConfirmacion();

                    if ($resultado) {
                        header('Location: /mensaje');
    
                    }
                }
            }
        
        }

        //render a la vista

        $router->render('auth/crear', [
            'titulo' => 'Crea tu Cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router) {

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if (empty($alertas)) {
                //buscar el usuario
                $usuario = Usuario::where('email', $usuario->email);

                if ($usuario && $usuario->confirmado === '1') {
                    //generar un nuevo token

                    $usuario->crearToken();
                    unset($usuario->password2);


                    //actualizar el usuario

                    $usuario->guardar();

                    //enviar el email

                    $email = new Email($usuario->email,$usuario->nombre,$usuario->token);
                    $email->enviarInstrucciones();

                    //imprimir la alerta

                    Usuario::setAlerta('exito','Se ha enviado las instrucciones a tu email');


                } else {
                    Usuario::setAlerta('error','El usuario no existe o no está confirmado');
                    
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide',[
            'titulo' => 'olvidaste tu password',
            'alertas' => $alertas
        ]);
    }

    public static function restablecer(Router $router) {

        $token = s($_GET['token']);
        $mostrar = true;

        if (!$token) header('Location: /');

        //identificar el usuario con este token

        $usuario = Usuario::where('token',$token);

        if (empty($usuario)) {
            Usuario::setAlerta('error','Token no válido');
            $mostrar = false;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //añadir el nuevo password

            $usuario->sincronizar($_POST);
            //validar password
            $alertas = $usuario->validarPassword();

            if (empty($alertas)) {
                //hashear el nuevo password

                $usuario->hashPassword();

             

                //eliminar el token

                $usuario->token = '';

                //guardar el usuario en la base de datos
               $resultado = $usuario->guardar();
                //redireccionar

                if ($resultado) {
                    header('Location: /');
                }
            }

            
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/restablecer',[
            'titulo' => 'Restablecer password',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);

    }

    public static function mensaje(Router $router) {

        $router->render('auth/mensaje',[
            'titulo' => 'Cuenta creada exitosamente'

        ]);
       
    }

    public static function confirmar(Router $router) {

        $token = s($_GET['token']);

        if (!$token) header('Location: /');

        //encontrar al usuario con este token

        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            //no se encontró un usuario con ese token
            Usuario::setAlerta('error','token no valido');
        } else {
            //confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->token = '';
            unset($usuario->password2);
            //guardar en la base de datos
            $usuario->guardar();

            Usuario::setAlerta('exito','Cuenta confirmada correctamente');
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar',[
            'titulo' => 'Confirma tu cuenta',
            'alertas' => $alertas
        ]);
       
    }
}