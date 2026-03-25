<?php

class Sesion{

    public static function crearSesion($usuarioSesion){
        $sessionTime = 365 * 24 * 60 * 60;                  // 1 año de duración
        session_set_cookie_params($sessionTime);
        session_start();
        session_regenerate_id();                            // Para crear un id de sesion distinto al antiguo
        $_SESSION["usuarioSesion"] = $usuarioSesion;
        // Guardar el tiempo de login
        $_SESSION["login_time"] = time();
    }


    // public static function iniciarSesion(&$datos = []){
    //     session_start();
    //     if (isset($_SESSION["usuarioSesion"])) {
    //         $datos['usuarioSesion'] = $_SESSION["usuarioSesion"];       // pasamos por referencia los datos de la sesion
    //     } else {
    //         session_destroy();
    //         redireccionar('/login'.'/');
    //     }
    // }

    
    public static function iniciarSesion(&$datos = []){
        session_start();
        
        // Tiempo máximo de sesión en segundos
        $tiempo_maximo = 2 * 60 * 60 ; // 2 horas
        
        if (isset($_SESSION["usuarioSesion"])) {

            // Verificar tiempo de login
            if (isset($_SESSION["login_time"]) && (time() - $_SESSION["login_time"]) > $tiempo_maximo) {
                self::cerrarSesion();
                redireccionar('/login/');
            }

            // Pasar los datos de sesión
            $datos['usuarioSesion'] = $_SESSION["usuarioSesion"];
            
            // (Opcional) Renovar el tiempo de sesión al interactuar
            $_SESSION["login_time"] = time();

        } else {
            session_destroy();
            redireccionar('/login/');
        }
    }


    public static function sesionCreada(&$datos = []){         // si no necesitamos datos de respuesta, le damos un valor por defecto
        session_start();
        if (isset($_SESSION["usuarioSesion"])) {
            $datos['usuarioSesion'] = $_SESSION["usuarioSesion"];       // pasamos por referencia los datos de la sesion
            return true;
        } else {
            return false;
        }
    }


    public static function cerrarSesion(){
        // session_start();                    // no seria necesaria esta linea, pero por asegurarnos en futuros usos
        setcookie(session_name(), '', time() - 3600, "/");
        session_unset();
        session_destroy();
        $_SESSION = array();
    }
    
}
