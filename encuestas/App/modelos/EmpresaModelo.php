<?php

class EmpresaModelo {
    private $db;

    public function __construct(){
        $this->db = new Base;
    }

    public function getEmpresas($soloActivas = false){
        $where = $soloActivas ? "WHERE activa = 1" : "";
        $this->db->query("SELECT * FROM en_empresa $where ORDER BY empresa");
        return $this->db->registros();
    }

    public function getEmpresa($id_empresa){
        $this->db->query("SELECT * FROM en_empresa WHERE id_empresa = :id");
        $this->db->bind(':id', $id_empresa);
        return $this->db->registro();
    }

    public function getEmpresaByToken($token){
        $this->db->query("SELECT * FROM en_empresa WHERE token_acceso = :token AND activa = 1");
        $this->db->bind(':token', $token);
        return $this->db->registro();
    }

    public function addEmpresa($datos){
        $token = generarToken();
        $this->db->query("INSERT INTO en_empresa
                            (empresa, contacto, email, telefono, activa, token_acceso)
                           VALUES (:empresa, :contacto, :email, :telefono, 1, :token)");
        $this->db->bind(':empresa',  trim($datos['empresa']));
        $this->db->bind(':contacto', trim($datos['contacto'] ?? ''));
        $this->db->bind(':email',    trim($datos['email']    ?? ''));
        $this->db->bind(':telefono', trim($datos['telefono'] ?? ''));
        $this->db->bind(':token',    $token);
        return $this->db->executeLastId();
    }

    public function editEmpresa($datos){
        $this->db->query("UPDATE en_empresa
                             SET empresa  = :empresa,
                                 contacto = :contacto,
                                 email    = :email,
                                 telefono = :telefono,
                                 activa   = :activa
                           WHERE id_empresa = :id");
        $this->db->bind(':empresa',  trim($datos['empresa']));
        $this->db->bind(':contacto', trim($datos['contacto'] ?? ''));
        $this->db->bind(':email',    trim($datos['email']    ?? ''));
        $this->db->bind(':telefono', trim($datos['telefono'] ?? ''));
        $this->db->bind(':activa',   $datos['activa']);
        $this->db->bind(':id',       $datos['id_empresa']);
        return $this->db->execute();
    }

    public function delEmpresa($id_empresa){
        $this->db->query("DELETE FROM en_empresa WHERE id_empresa = :id");
        $this->db->bind(':id', $id_empresa);
        return $this->db->execute();
    }

    public function regenerarToken($id_empresa){
        $token = generarToken();
        $this->db->query("UPDATE en_empresa SET token_acceso = :token WHERE id_empresa = :id");
        $this->db->bind(':token', $token);
        $this->db->bind(':id',    $id_empresa);
        if($this->db->execute()) return $token;
        return false;
    }
}
