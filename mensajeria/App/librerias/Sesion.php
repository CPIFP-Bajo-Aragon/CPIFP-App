<?php
    
    class Sesion{

        private static  $sessionTime = TMP_SESION;

        public static function crearSesion($usuarioSesion) {
            session_set_cookie_params(self::$sessionTime);                // No es tiempo de vida de la sesion, si no de la cookie local
            // ini_set("session.cookie_lifetime",$sessionTime);     // Es lo mismo que la linea anterior
            // ini_set('session.gc_maxlifetime', $sessionTime);     // No funciona siempre, depende de configuracion de servidor
            session_start();
            session_regenerate_id();                            // Para crear un id de sesion distinto al antiguo
            $_SESSION["usuarioSesion"] = $usuarioSesion;
        }


        public static function iniciarSesion(&$datos = []) {
            session_start();
            if (isset($_SESSION["usuarioSesion"])){
                setcookie(session_name(), session_id(), time() + self::$sessionTime, "/");      // Necesario para actualizar el tiempo de sesion al haber actividad
                $datos['usuarioSesion'] = $_SESSION["usuarioSesion"];
                $datos['persistencia'] = isset($_SESSION["persistencia"]) ? $_SESSION["persistencia"] : '';
                                // pasamos por referencia los datos de la sesion
            } else {
                session_destroy();
                header('location: ' . RUTA_LOGOUT);
                exit();
            }
        }


        public static function sesionCreada(&$datos = []) {         // si no necesitamos datos de respuesta, le damos un valor por defecto
            session_start();
            if (isset($_SESSION["usuarioSesion"])){
                $datos['usuarioSesion'] = $_SESSION["usuarioSesion"];       // pasamos por referencia los datos de la sesion
                $datos['persistencia'] = isset($_SESSION["persistencia"]) ? $_SESSION["persistencia"] : '';
                return true;
            } else {
                return false;
            }
        }


        public static function cerrarSesion() {        
            session_start();                    // no seria necesaria esta linea, pero por asegurarnos en futuros usos
            setcookie(session_name(), '', time() -3600, "/");
            session_unset();
            session_destroy();
            $_SESSION = array();
        }


        public static function addPersistencia(&$datos,$identificadorDato,$nuevoDato) {        
            $_SESSION["persistencia"][$identificadorDato] = $nuevoDato;
            $datos['persistencia'] = $_SESSION["persistencia"];
        }
        
    }