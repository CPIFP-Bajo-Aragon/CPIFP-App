<?php

class EdificioM {

    private $db;

    public function __construct() {
        $this->db = new Base;
    }


    /**********************************************************************************************/
    /*************************************** EDIFICIOS ********************************************/
    /**********************************************************************************************/

    // TODOS LOS EDIFICIOS
    public function obtener_edificios() {
        $this->db->query("SELECT * FROM man_edificio ORDER BY edificio;");
        return $this->db->registros();
    }

    // INFO UN EDIFICIO CONCRETO
    public function info_edificio($id_edificio) {
        $this->db->query("SELECT * FROM man_edificio WHERE id_edificio = :id_edificio;");
        $this->db->bind(':id_edificio', $id_edificio);
        return $this->db->registro();
    }

    // NÚMERO DE ESPACIOS DE UN EDIFICIO
    public function contar_espacios($id_edificio) {
        $this->db->query("SELECT COUNT(*) as total FROM man_ubicacion WHERE id_edificio = :id_edificio;");
        $this->db->bind(':id_edificio', $id_edificio);
        $resultado = $this->db->registro();
        return $resultado->total;
    }


    /************************ NUEVO EDIFICIO ****************************/

    public function nuevo_edificio($nombre) {
        $this->db->query("INSERT INTO man_edificio (edificio) VALUES (:edificio);");
        $this->db->bind(':edificio', $nombre);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }


    /************************ EDITAR EDIFICIO ****************************/

    public function editar_edificio($id_edificio, $nombre) {
        $this->db->query("UPDATE man_edificio SET edificio = :edificio WHERE id_edificio = :id_edificio;");
        $this->db->bind(':edificio', $nombre);
        $this->db->bind(':id_edificio', $id_edificio);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }


    /************************ BORRAR EDIFICIO ****************************/

    public function borrar_edificio($id_edificio) {
        $this->db->query("DELETE FROM man_edificio WHERE id_edificio = :id_edificio;");
        $this->db->bind(':id_edificio', $id_edificio);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }


    /**********************************************************************************************/
    /*************************************** ESPACIOS *********************************************/
    /**********************************************************************************************/

    // TODOS LOS ESPACIOS DE UN EDIFICIO
    public function espacios_x_edificio($id_edificio) {
        $this->db->query("SELECT man_ubicacion.*, man_edificio.edificio
                          FROM man_ubicacion
                          JOIN man_edificio ON man_ubicacion.id_edificio = man_edificio.id_edificio
                          WHERE man_ubicacion.id_edificio = :id_edificio
                          ORDER BY man_ubicacion.ubicacion;");
        $this->db->bind(':id_edificio', $id_edificio);
        return $this->db->registros();
    }

    // INFO UN ESPACIO CONCRETO
    public function info_espacio($id_ubicacion) {
        $this->db->query("SELECT * FROM man_ubicacion WHERE id_ubicacion = :id_ubicacion;");
        $this->db->bind(':id_ubicacion', $id_ubicacion);
        return $this->db->registro();
    }


    /************************ NUEVO ESPACIO ****************************/

    public function nuevo_espacio($nombre, $id_edificio) {
        $this->db->query("INSERT INTO man_ubicacion (ubicacion, id_edificio) VALUES (:ubicacion, :id_edificio);");
        $this->db->bind(':ubicacion', $nombre);
        $this->db->bind(':id_edificio', $id_edificio);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }


    /************************ EDITAR ESPACIO ****************************/

    public function editar_espacio($id_ubicacion, $nombre) {
        $this->db->query("UPDATE man_ubicacion SET ubicacion = :ubicacion WHERE id_ubicacion = :id_ubicacion;");
        $this->db->bind(':ubicacion', $nombre);
        $this->db->bind(':id_ubicacion', $id_ubicacion);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }


    /************************ BORRAR ESPACIO ****************************/

    public function borrar_espacio($id_ubicacion) {
        $this->db->query("DELETE FROM man_ubicacion WHERE id_ubicacion = :id_ubicacion;");
        $this->db->bind(':id_ubicacion', $id_ubicacion);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

}
