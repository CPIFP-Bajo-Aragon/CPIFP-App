<?php

class Sesion {

    public static function crearSesion($usuarioSesion) {
        $sessionTime = TMP_SESION;
        session_set_cookie_params($sessionTime);
        session_start();
        session_regenerate_id();
        $_SESSION['usuarioSesion'] = $usuarioSesion;
        $_SESSION['login_time']    = time();
    }


    public static function iniciarSesion(&$datos = []) {
        session_start();

        $tiempo_maximo = TMP_SESION;

        if (isset($_SESSION['usuarioSesion'])) {

            // Verificar timeout de inactividad
            if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $tiempo_maximo) {
                self::cerrarSesion();
                redireccionar('/login/');
            }

            $datos['usuarioSesion'] = $_SESSION['usuarioSesion'];
            $datos['persistencia']  = isset($_SESSION['persistencia']) ? $_SESSION['persistencia'] : [];

            // Renovar el tiempo al haber actividad
            $_SESSION['login_time'] = time();
            setcookie(session_name(), session_id(), time() + $tiempo_maximo, '/');

        } else {
            session_destroy();
            redireccionar('/login/');
        }
    }


    public static function sesionCreada(&$datos = []) {
        session_start();
        if (isset($_SESSION['usuarioSesion'])) {
            $datos['usuarioSesion'] = $_SESSION['usuarioSesion'];
            $datos['persistencia']  = isset($_SESSION['persistencia']) ? $_SESSION['persistencia'] : [];
            return true;
        } else {
            return false;
        }
    }


    public static function cerrarSesion() {
        setcookie(session_name(), '', time() - 3600, '/');
        session_unset();
        session_destroy();
        $_SESSION = [];
    }


    // Guarda datos que persisten entre peticiones dentro de la sesión
    public static function addPersistencia(&$datos, $identificadorDato, $nuevoDato) {
        $_SESSION['persistencia'][$identificadorDato] = $nuevoDato;
        $datos['persistencia'] = $_SESSION['persistencia'];
    }

}
