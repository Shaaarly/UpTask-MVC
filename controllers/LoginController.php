<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Classes\Email;

class LoginController {

    public static function login(Router $router) {

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);

            $alertas = $usuario->validarLogin();

            if(empty($alertas)) {
                // Verificar que el usuario exista
                $usuario = Usuario::where('email', $usuario->email);

                if(!$usuario || !$usuario->confirmado) {
                    Usuario::setAlerta('error', 'El Usuario No Existe o No está confirmado');
                } else {
                    // El usuario existe
                    if( password_verify($_POST['password'], $usuario->password)) {
                        // Iniciar

                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // Redireccionar
                        header('Location: /dashboard');

                    } else {
                        Usuario::setAlerta('error', 'Password Incorrecto');
                    }
                }
            }
        }

        $alertas = Usuario::getAlertas();
        // Render a la vista
        $router->render('auth/login', [
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

        $usuario = new Usuario;
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);

            
            $alertas = $usuario->validarNuevaCuenta();

            if(empty($alertas)) {
                $existeUsuario = Usuario::where('email', $usuario->email);

                if($existeUsuario) {
                    Usuario::setAlerta('error', 'El Usuario ya esta registrado');
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hashear el password
                    $usuario->hashPassword();

                    // Eliminar passowrd2
                    unset($usuario->password2);

                    // Generar el token
                    $usuario->crearToken();

                    // Crear un nuevo usuario
                    $resultado = $usuario->guardar();

                    // Enviar Email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);

                    $email->enviarConfirmacion();

                    if($resultado) {
                        header('Location: /mensaje'); 
                    }
                }
            }
        }

        // Render a la vista
        $router->render('auth/crear', [
            'titulo' => 'Crear Cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router) {
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas)) {
                // Buscar el usuario
                $usuario = Usuario::where('email', $usuario->email);

                if($usuario && $usuario->confirmado) {
                    // Generar un nuevo token
                    $usuario->crearToken();
                    unset($usuario->password2);

                    // Actualizar el Usuario
                    $usuario->guardar();

                    // Enviar el email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarInstrucciones();

                    // Imprimir la alerta
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu Email');


                
                } else {
                    Usuario::setAlerta('error', 'El Usuario no existe o no está confirmado');
                    $alertas = Usuario::getAlertas();
                }
            }
        }

        $router->render('auth/olvide', [
            'titulo' => 'Recuperar Cuenta',
            'alertas' => $alertas
        ]);
    }
    public static function reestablecer(Router $router) {

        $token = s($_GET['token']);
        $mostrar = true;

        if(!$token) header('Location: /');

        // Identificar el usuario
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token no Válido');
            $mostrar = false;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Añadir un nuevo password
            $usuario->sincronizar($_POST);

            // Validar el password
            $alertas = $usuario->validarPassword();

            
            if(empty($alertas)) {
                // Hashear password
                $usuario->hashPassword();

                // Eliminar el token
                $usuario->token = null;

                // Guardar el usuario en la BD
                $resultado = $usuario->guardar();

                // Redirecionar
                if($resultado) {    
                    header('Location: /');
                }
            }

        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/reestablecer', [
            'titulo' => 'Reestablecer Cuenta',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router) {

        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta Creada Exitosamente'
        ]);
    }

    public static function confirmar(Router $router) {

        $token = S($_GET['token']);

        if(!$token) header('Location: /');

        // Encontrar el usuario correspondiente
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            // No se encuentra el usuario con ese token
            Usuario::setAlerta('error', 'Token no Válido');
        } else {
            // Confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->token = "";
            unset($usuario->password2);

            // Guardar en la base de datos
            $usuario->guardar();

            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar', [
            'titulo' => 'Confirmar Cuenta',
            'alertas' => $alertas
        ]);
    }
}