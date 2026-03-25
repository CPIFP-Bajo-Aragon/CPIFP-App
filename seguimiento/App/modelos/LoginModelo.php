<?php

class LoginModelo
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }


//     public function loginEmail($email, $passw)
// {
//         $this->db->query("SELECT cpifp_profesor.id_profesor,login, password, nombre_completo, id_rol from cpifp_profesor, cpifp_profesor_departamento  
//         WHERE cpifp_profesor_departamento.id_profesor=cpifp_profesor.id_profesor and login = :email AND password = :passw");
//         $this->db->bind(':email', $email);
//         $this->db->bind(':passw', $passw);

//         return $this->db->registro();
//     }


    public function obtener_roles($id_profe){
        $this->db->query(("SELECT * from cpifp_profesor_departamento where id_profesor=:id_profe;"));
        $this->db->bind(':id_profe', $id_profe);
        return $this->db->registros();

    }




  
}
