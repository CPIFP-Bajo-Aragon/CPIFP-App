<?php

class PreguntasModelo {
    private $db;

    public function __construct(){
        $this->db = new Base;
    }

    public function getPreguntas(){
        $this->db->query("SELECT * 
                FROM preguntas");
                                 

        try {
            return $this->db->registros();
        } catch (Exception $e) {
            // $this->db->query_error($e);
            echo "Error de Acceso a DDBB";
            exit;
        }
    }

}