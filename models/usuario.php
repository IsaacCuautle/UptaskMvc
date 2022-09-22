<?php 
namespace Model;

class Usuario extends ActiveRecord{
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id','nombre','email','token','password','confirmado'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre=$args['nombre'] ?? '';
        $this->email=$args['email'] ?? '';
        $this->token=$args['token'] ?? '';
        $this->password=$args['password'] ?? '';
        $this->password2=$args['password2'] ?? '';
        $this->password_actual=$args['password_actual'] ?? '';
        $this->password_nuevo=$args['password_nuevo'] ?? '';
        $this->confirmado=$args['confirmado'] ?? 0;
    }
    //Validar Login
    public function validarLogin(){
        if(!$this->email){
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        if(!filter_var($this->email,FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'Email no valido';
            return self::$alertas;
        }
        if(!$this->password){
            self::$alertas['error'][] = 'El password es obligatorio';
        }
        return self::$alertas;
    }

    //validacion para cuentas nuevas
    public function validacionNuevaCuenta(){
        if(!$this->nombre){
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }
        if(!$this->email){
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'El password es obligatorio';
        }
        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'El password debe contener almenos 6 caracteres';
        }
        if($this->password !== $this->password2){
            self::$alertas['error'][] = 'El password no coincide';
        }
        return self::$alertas;
    }

    public function nuevoPassword(){
        if(!$this->password_actual){
            self::$alertas['error'][] = 'El password actual no puede estar vacio';
        }
        if(!$this->password_nuevo){
            self::$alertas['error'][] = 'El password nuevo no puede estar vacio';
        }
        if(strlen($this->password_nuevo) < 6){
            self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';
        }

        return self::$alertas;
    }

    //Valida un email
    public function validarEmail()
    {
        if(!$this->email){
            self::$alertas['error'][] = 'El email es obligatorio';
            return self::$alertas;
        }

        if(!filter_var($this->email,FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'Email no valido';
            return self::$alertas;
        }
    }

    public function validarPerfil()
    {
        if(!$this->nombre){
            self::$alertas['error'][] = 'El nombre es obligatorio';
            
        }
        if(!$this->email){
            self::$alertas['error'][] = 'El email es obligatorio';

        }

        return self::$alertas;
    }

    //Valida el password
    public function validarPassword()
    {
        if(!$this->password){
            self::$alertas['error'][] = 'El password es obligatorio';
        }
        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'El password debe contener almenos 6 caracteres';
        }
        return self::$alertas;
    }

    //Comprobar password
    public function comprobarPassword() : bool{
        return password_verify($this->password_actual,$this->password);
    }

    //Hashea el password
    public function hashPassword(){
        $this->password = password_hash($this->password,PASSWORD_BCRYPT);
    }

    //Generar un token
    public function crearToken(){
        $this->token = uniqid();
    }

}

?>