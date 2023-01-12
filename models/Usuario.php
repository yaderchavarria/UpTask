<?php

namespace Model;

class Usuario extends ActiveRecord {
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];

    public function __construct($args = []) {
        
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->password_conf = $args['password_conf'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }

    //validar el login de usuarios

    public function validarLogin() {
        if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';

        }
        if (!filter_var($this->email,FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no valido';
        }
        if (!$this->password) {
            self::$alertas['error'][] = 'El password es obligatorio';

        }
        return self::$alertas;
    }

    //Validación para cuentas nuevas

    public function validarNuevaCuenta() {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre es obligatorio';

        }
        if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';

        }
        if (!$this->password) {
            self::$alertas['error'][] = 'El password es obligatorio';

        }
        if (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';

        }
        if ($this->password !== $this->password2) {
            self::$alertas['error'][] = 'Los passwords deben ser iguales';

        }
        return self::$alertas;
    }

    //valida un email

    public function validarEmail() {
        if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        if (!filter_var($this->email,FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no valido';
        }

        return self::$alertas;  
    }

    //valida el password

    public function validarPassword() {
        if (!$this->password) {
            self::$alertas['error'][] = 'El password es obligatorio';

        }
        if (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';

        }
        if ($this->password !== $this->password2) {
            self::$alertas['error'][] = 'Los passwords deben ser iguales';

        }

        return self::$alertas;
        
    }

    public function validar_perfil() {
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El nombre es obligatorio';

        }
        if(!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
            
        }
        return self::$alertas;
    }

    public function nuevo_password() {
        if(!$this->password_actual) {
            self::$alertas['error'][] = 'El password actual no puede ir vacio';
        }
        if(!$this->password_nuevo) {
            self::$alertas['error'][] = 'El password nuevo no puede ir vacio';
        }
        if(strlen($this->password_nuevo) < 6) {
            self::$alertas['error'][] = 'El password nuevo debe contener al menos 6 caracteres';
        }
        if($this->password_nuevo !== $this->password_conf) {
            self::$alertas['error'][] = 'Los passwords deben ser iguales';
        }

        return self::$alertas;
    }

    //comprobar el password
    public function comprobar_password() {
        return password_verify($this->password_actual,$this->password);
    }

    public function hashPassword() {
        $this->password = password_hash($this->password,PASSWORD_BCRYPT);
        $this->password2 = password_hash($this->password2,PASSWORD_BCRYPT);
    }

    //generar un token

    public function crearToken() {
        $this->token = uniqid();
    }
}