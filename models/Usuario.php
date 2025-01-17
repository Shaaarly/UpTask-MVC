<?php

namespace Model;

use EmptyIterator;

class Usuario extends ActiveRecord {
    // Atributos
    public $id;
    public $nombre;
    public $email;
    public $password;
    public $password2;
    public $passwordActual;
    public $passwordNuevo;
    public $token;
    public $confirmado;

    protected static $tabla = 'usuarios';
    protected static $columnasDB= ['id', 'nombre', 'email', 'password', 'token', 'confirmado']; 

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->passwordActual = $args['passwordActual'] ?? '';
        $this->passwordNuevo = $args['passwordNuevo'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? '0';
    }

    // Validar el Login del Usuario
    public function validarLogin() {
        if(!$this->email) {
            self::$alertas['error'][] = 'El Email del Usuario es Obligatorio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no válido';
        }
        if(!$this->password) {
            self::$alertas['error'][] = 'El Password del Usuario es Obligatorio';
        }

        return self::$alertas;
    }

    // Validacion para cuentas nuevas
    public function validarNuevaCuenta() {
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre del Usuario es Obligatorio';
        }
        if(!$this->email) {
            self::$alertas['error'][] = 'El Email del Usuario es Obligatorio';
        }
        if(!$this->password) {
            self::$alertas['error'][] = 'El Password del Usuario es Obligatorio';
        }
        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El Password debe contener al menos 6 carácteres';
        }
        if($this->password != $this->password2) {
            self::$alertas['error'][] = 'Los Passwords no coinciden';
        }
        if(!$this->password2) {
            self::$alertas['error'][] = 'La confirmación del Password es necesario';
        }

        return self::$alertas;
    }

    // Validar Password
    public function validarPassword() {
        if(!$this->email) {
            self::$alertas['error'][] = 'El Password es Obligatorio';
        }
        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El Password debe contener al menos 6 carácteres';
        }
        return self::$alertas;
    }

    // Validar el perfil
    public function validarPerfil() {
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre es Obligatorio';
        }
        if(!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }
        return self::$alertas;
    }

    public function nuevoPassword() : array{
        if(!$this->passwordActual) {
            self::$alertas['error'][] = 'El Password Actual no puede ir vacio';
        }
        if(!$this->passwordNuevo) {
            self::$alertas['error'][] = 'El Password Nuevo no puede ir vacio';
        }
        if(strlen($this->passwordNuevo) < 6) {
            self::$alertas['error'][] = 'El Password Nuevo debe contener al menos 6 carácteres';
        }
        return self::$alertas;
    }

    // Comprobar el password
    public function comprobarPassword() : bool {
        return password_verify($this->passwordActual, $this->password);
    }

    // Hashea el password
    public function hashPassword() : void {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT );
    }

    // Generar un token
    public function crearToken() : void {
        $this->token = md5(uniqid());
    }

    public function validarEmail() {
        if(!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no válido';
        }
        return self::$alertas;
    }
} 