<?php

namespace Controllers;

use Model\Proyecto;
use Model\Usuario;
use MVC\Router;

class DashboardController {

    public static function index(Router $router) {

        session_start();

        isAuth();

        $id = $_SESSION['id'];

        $proyectos = Proyecto::belongsTo('propietarioid',$id);

        $router->render('dashboard/index',[
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }

    public static function crear_proyecto(Router $router) {

        session_start();

        isAuth();

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proyecto = new Proyecto($_POST);

            $alertas = $proyecto->validarProyecto();

            if(empty($alertas)) {
                //generar una url unica
                $hash = md5(uniqid());

                $proyecto->url = $hash;
                //almacenar al creador del proyecto

                $proyecto->propietarioid = $_SESSION['id'];

                //guardar proyecto

                $proyecto->guardar();

                //redireccionar

                header('Location: /proyecto?url=' . $proyecto->url);
            }
        }

        $router->render('dashboard/crear-proyecto',[
            'alertas' => $alertas,
            'titulo' => 'Crear Proyecto'
        ]);
    }

    public static function proyecto(Router $router) {

        session_start();

        isAuth();

        $url = $_GET['url'];

        if(!$url) header('Location: /dashboard');

        //revisar que la persona que visit el proyecto es quien lo creo

        $proyecto = Proyecto::where('url',$url);

        if ($proyecto->propietarioid !== $_SESSION['id']) {
            header('Location: /dashboard');
        }

        $router->render('dashboard/proyecto',[
            'titulo' => $proyecto->proyecto
        ]);
    }
    public static function perfil(Router $router) {

        session_start();

        isAuth();

        $alertas = [];

        $usuario = Usuario::find($_SESSION['id']);

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validar_perfil();
            if(empty($alertas)){

                $existeUsuario = Usuario::where('email',$usuario->email);

                if($existeUsuario && $existeUsuario->id !== $usuario->id) {
                    //mensaje de error
                    Usuario::setAlerta('error','Email no valido');
                    $alertas = $usuario->getAlertas();
                } else {
                    //guardar el usuario
                $usuario->guardar();

                Usuario::setAlerta('exito','Guardado Correctamente');
                $alertas = $usuario->getAlertas();

                //asignar el nombre nuevo a la barra
                $_SESSION['nombre'] = $usuario->nombre;
                }

                
            }
        }

        $router->render('dashboard/perfil',[
            'titulo' => 'Perfil',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function cambiar_password(Router $router) {
        session_start();

        isAuth();

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = Usuario::find($_SESSION['id']);

            //sincronizar con los datos del usuario
            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevo_password();

            if(empty($alertas)){
                $resultado = $usuario->comprobar_password();

                if($resultado) {
                    //asignar el nuevo password

                    $usuario->password = $usuario->password_nuevo;
                    //eliminar propiedades no necesarias
                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo);
                    unset($usuario->password_conf);

                    //hashear el nuevo password

                    $usuario->hashPassword();

                    //actualizar

                    $resultado = $usuario->guardar();

                    if($resultado) {
                        Usuario::setAlerta('exito','Password guardado correctamente');
                        $alertas = $usuario->getAlertas();
                    }

                } else {
                    Usuario::setAlerta('error','Password incorrecto');
                    $alertas = $usuario->getAlertas();
                }
            }
        }

        $router->render('dashboard/cambiar-password',[
            'titulo' => 'Cambiar password',
            'alertas' => $alertas
        ]);
    }
}