<?php

class LoginModelo {
    private $db;

    public function __construct(){
        $this->db = new Base;
    }

    public function loginUsuario($datos){
        $this->db->query("SELECT * 
                                FROM cpifp_profesor
                                WHERE login = :login 
                                    AND password = sha2(:password,256)");
                                    
        $this->db->bind(':login',$datos['usuario']);
        $this->db->bind(':password',$datos['pass']);

        try {
            return $this->db->registro();
        } catch (Exception $e) {
            // $this->db->query_error($e);
            echo "Error de Acceso a DDBB";
            exit;
        }
    }

    public function destinosUsuario($idUsuario)
        {
            $this->db->query("SELECT * 
                            FROM fact_profesor_destino
                            WHERE id_profesor = :id_profesor");
                            
            $this->db->bind(':id_profesor',$idusuario);
    
            try {
                return $this->db->registros();
            } catch (Exception $e) {
                // $this->db->query_error($e);
                echo "Error de Acceso a DDBB";
                exit;
            }
    
        }



}