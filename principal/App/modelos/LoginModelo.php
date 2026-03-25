<?php

class LoginModelo {

    private $db;

    public function __construct(){
        $this->db = new Base;
    }


    // public function loginEmail($login, $passw){
    //     // $this->db->query("SELECT cpifp_profesor.id_profesor,login, password, nombre_completo, id_rol from cpifp_profesor, cpifp_profesor_departamento  
    //     // WHERE cpifp_profesor_departamento.id_profesor=cpifp_profesor.id_profesor and login = :email AND password = sha2(:passw,256)");
    //     $this->db->query("SELECT * FROM cpifp_profesor WHERE (email = :login OR login = :login) AND password = :passw");
    //                                                 // AND password = sha2(:passw,256)"); COMENTADO SILVIA
    //     $this->db->bind(':login', $login);
    //     $this->db->bind(':passw', $passw);
    //     return $this->db->registro();
    // }




public function loginEmail($login, $passw) {

    $this->db->query("SELECT * FROM cpifp_profesor   
                      WHERE email = :login OR login = :login");
    $this->db->bind(':login', $login);
    $usuario = $this->db->registro();

    if ($usuario && password_verify($passw, $usuario->password)) {
        return $usuario;
    }

    return null;
}




/************************ BUSCAR POR EMAIL ****************************/

public function buscar_por_email($email) {
    $this->db->query("SELECT * FROM cpifp_profesor WHERE email = :email");
    $this->db->bind(':email', $email);
    return $this->db->registro();
}



/************************ RECUPERAR PASS ****************************/

// public function recuperar_password($id_usuario, $password) {
    
//     $this->db->query("UPDATE cpifp_profesor SET password = :password WHERE id_profesor = :id");
//     $this->db->bind(':password', $password);
//     $this->db->bind(':id', $id_usuario);
//     if ($this->db->execute()) {
//         return true;
//     } else {
//         return false;
//     }
// }

public function recuperar_password($id_usuario, $password) {
    
    $password_cifrada = password_hash($password, PASSWORD_DEFAULT);

    $this->db->query("UPDATE cpifp_profesor SET password = :password WHERE id_profesor = :id");
    $this->db->bind(':password', $password_cifrada);
    $this->db->bind(':id', $id_usuario);
    if ($this->db->execute()) {
        return true;
    } else {
        return false;
    }
}



    public function getRolesProfesor($id_profesor){
        $this->db->query("SELECT * FROM cpifp_profesor_departamento
        NATURAL JOIN cpifp_rol
        NATURAL JOIN cpifp_departamento
        WHERE id_profesor=:id_profesor");
        $this->db->bind(':id_profesor',$id_profesor);
        return $this->db->registros();
    }







    
    // public function regenerarPass($password,$email){
    //     $this->db->query("UPDATE cpifp_profesor SET password=sha2(:password,256) where email=:email");
    //     $this->db->bind(':password', $password);
    //     $this->db->bind(':email', $email);
    //     if ($this->db->rowCount()) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }


    // public function recuperar($socio){
    //     $this->db->query("SELECT email FROM USUARIO WHERE id_usuario=:socio");
    //     $this->db->bind(':socio', $socio);
    //     return $this->db->registro();
    // }


    // public function cambiarPass($password,$id){
    
    //     $this->db->query("UPDATE usuario SET passw=MD5(:passw) where id_usuario=:id");
    //     $this->db->bind(':passw', $password);
    //     $this->db->bind(':id', $id);
    //     if ($this->db->execute()) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }



    /*public function registroSesion($id_usuario)
    {
        $this->db->query("INSERT INTO sesiones (id_sesion, id_usuario, fecha_inicio) 
                                        VALUES (:id_sesion, :id_usuario, NOW())");

        $this->db->bind(':id_sesion', session_id());
        $this->db->bind(':id_usuario', $id_usuario);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }


    public function registroFinSesion($id_usuario)
    {
        $this->db->query("UPDATE sesiones SET fecha_fin = NOW()  
                                    WHERE id_usuario = :id_usuario AND id_sesion = :id_sesion");

        $this->db->bind(':id_sesion', session_id());
        $this->db->bind(':id_usuario', $id_usuario);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }*/
    
}
