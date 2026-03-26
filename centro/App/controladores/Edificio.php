<?php

class Edificio extends Controlador {

    private $edificioModelo;


    public function __construct() {
        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);

        // Solo el equipo directivo (rol 50) puede acceder
        $this->datos['rolesPermitidos'] = [50];
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }

        $this->edificioModelo = $this->modelo('EdificioM');
    }



    /**********************************************************************************************/
    /*************************************** EDIFICIOS ********************************************/
    /**********************************************************************************************/

    public function index() {
        $edificios = $this->edificioModelo->obtener_edificios();
        // Añadimos el contador de espacios a cada edificio
        foreach ($edificios as $edificio) {
            $edificio->num_espacios = $this->edificioModelo->contar_espacios($edificio->id_edificio);
        }
        $this->datos['edificios'] = $edificios;
        $this->vista('edificios', $this->datos);
    }


    /************************ NUEVO EDIFICIO ****************************/

    public function nuevo_edificio() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = trim($_POST['nombre']);

            if ($this->edificioModelo->nuevo_edificio($nombre)) {
                echo "<script>
                        alert('Edificio dado de alta correctamente.');
                        window.location.href = '" . RUTA_URL . "/edificio';
                      </script>";
            } else {
                die('Algo ha fallado!!');
            }
        } else {
            redireccionar('/edificio');
        }
    }


    /************************ EDITAR EDIFICIO ****************************/

    public function editar_edificio($id_edificio) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = trim($_POST['nombre']);

            if ($this->edificioModelo->editar_edificio($id_edificio, $nombre)) {
                echo "<script>
                        alert('Edificio actualizado correctamente.');
                        window.location.href = '" . RUTA_URL . "/edificio';
                      </script>";
            } else {
                die('Algo ha fallado!!');
            }
        } else {
            redireccionar('/edificio');
        }
    }


    /************************ BORRAR EDIFICIO ****************************/

    public function borrar_edificio($id_edificio) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->edificioModelo->borrar_edificio($id_edificio)) {
                redireccionar('/edificio');
            } else {
                die('Algo ha fallado!!!');
            }
        } else {
            redireccionar('/edificio');
        }
    }



    /**********************************************************************************************/
    /*************************************** ESPACIOS *********************************************/
    /**********************************************************************************************/

    public function edificio_espacios($id_edificio) {
        $this->datos['edificio']  = $this->edificioModelo->info_edificio($id_edificio);
        $this->datos['espacios']  = $this->edificioModelo->espacios_x_edificio($id_edificio);
        $this->vista('edificio_espacios', $this->datos);
    }


    /************************ NUEVO ESPACIO ****************************/

    public function nuevo_espacio($id_edificio) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = trim($_POST['nombre']);

            if ($this->edificioModelo->nuevo_espacio($nombre, $id_edificio)) {
                echo "<script>
                        alert('Espacio dado de alta correctamente.');
                        window.location.href = '" . RUTA_URL . "/edificio/edificio_espacios/" . $id_edificio . "';
                      </script>";
            } else {
                die('Algo ha fallado!!');
            }
        } else {
            redireccionar('/edificio/edificio_espacios/' . $id_edificio);
        }
    }


    /************************ EDITAR ESPACIO ****************************/

    public function editar_espacio($id_ubicacion) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre      = trim($_POST['nombre']);
            $id_edificio = trim($_POST['id_edificio']);

            if ($this->edificioModelo->editar_espacio($id_ubicacion, $nombre)) {
                echo "<script>
                        alert('Espacio actualizado correctamente.');
                        window.location.href = '" . RUTA_URL . "/edificio/edificio_espacios/" . $id_edificio . "';
                      </script>";
            } else {
                die('Algo ha fallado!!');
            }
        } else {
            redireccionar('/edificio');
        }
    }


    /************************ BORRAR ESPACIO ****************************/

    public function borrar_espacio($id_ubicacion) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_edificio = trim($_POST['id_edificio']);

            if ($this->edificioModelo->borrar_espacio($id_ubicacion)) {
                redireccionar('/edificio/edificio_espacios/' . $id_edificio);
            } else {
                die('Algo ha fallado!!!');
            }
        } else {
            redireccionar('/edificio');
        }
    }

}
