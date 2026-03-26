<?php

class PersonalM {

    private $db;

    public function __construct(){
        $this->db = new Base;
    }



// TRAE TODO EL PERSONAL
public function obtener_profesores(){
    $this->db->query("SELECT * FROM `cpifp_profesor`;");
    return $this->db->registros();
}


// TRAE TODOS LOS DEPARTAMENTOS DE CADA MIEMBRO
public function obtener_prof_dep(){
    $this->db->query("SELECT * FROM cpifp_profesor_departamento, cpifp_departamento 
    WHERE cpifp_profesor_departamento.id_departamento=cpifp_departamento.id_departamento;");
    return $this->db->registros();
}




/************************ NUEVO MIEMBRO ****************************/

public function nuevo_profesor($nuevo){
    $this->db->query("INSERT INTO cpifp_profesor (login, password, nombre_completo, email, activo, isAdmin) 
    VALUES (:login,:password, :nombre, :email, :activo, :es_admin);");

    $pass_cifrada = password_hash($nuevo['password'], PASSWORD_DEFAULT);

    $this->db->bind(':login', $nuevo['login']);  
    $this->db->bind(':password', $pass_cifrada); 
    $this->db->bind(':nombre', $nuevo['nombre']);
    $this->db->bind(':email', $nuevo['email']);
    $this->db->bind(':activo', $nuevo['activo']);
    $this->db->bind(':es_admin', $nuevo['admin']);     
    if ($this->db->execute()){
        return true;
    }else{
        return false;
    }
}


/************************ BUSCAR POR EMAIL ****************************/

public function buscar_por_email($email) {
    $this->db->query("SELECT * FROM cpifp_profesor WHERE email = :email");
    $this->db->bind(':email', $email);
    return $this->db->registro();
}



/************************ CAMBIAR CONTRASEÑA (ADMIN) ****************************/

public function cambiar_password($id_profesor, $nueva_password) {
    $password_cifrada = password_hash($nueva_password, PASSWORD_DEFAULT);
    $this->db->query("UPDATE cpifp_profesor SET password = :password WHERE id_profesor = :id");
    $this->db->bind(':password', $password_cifrada);
    $this->db->bind(':id', $id_profesor);
    if ($this->db->execute()) {
        return true;
    } else {
        return false;
    }
}



/************************ BORRAR MIEMBRO ****************************/

public function borrar_profesor($id_prof){
    $this->db->query("DELETE FROM cpifp_profesor 
    WHERE id_profesor=:id_prof;");
    $this->db->bind(':id_prof',$id_prof);
    if ($this->db->execute()){
        return true;
    }else{
        return false;
    } 
}


/************************ EDITAR MIEMBRO ****************************/

public function editar_profesor($editar,$id_prof){
    $this->db->query("UPDATE cpifp_profesor 
    SET nombre_completo=:nombre, email=:email, activo=:activo, isAdmin=:is_admin
    WHERE id_profesor=:id_prof;"); 
    $this->db->bind(':nombre',$editar['nombre']);
    $this->db->bind(':email',$editar['email']);
    $this->db->bind(':activo',$editar['activo']);
    $this->db->bind(':is_admin',$editar['admin']);
    $this->db->bind(':id_prof',$id_prof);
    if ($this->db->execute()){
        return true;
    }else{
        return false;
    }        
}
    



/**********************************************************************************************/
/*********************************** REFERENTE A UN MIEMBRO  **********************************/
/**********************************************************************************************/



// TRAE TODOS LOS ROLES
public function obtener_roles(){
    $this->db->query("SELECT * FROM cpifp_rol;");
    return $this->db->registros();
}


// TRAE TODOS LOS DEPARTAMENTOS
public function todos_departamentos(){
    $this->db->query("SELECT * FROM cpifp_departamento;");
    return $this->db->registros();
}

//TRAE LA INFO DEL PROFE (GENERAL)
public function info_profe($id_prof){
    $this->db->query("SELECT * FROM cpifp_profesor 
    WHERE id_profesor=:id_profe;");
    $this->db->bind(':id_profe', $id_prof);
    return $this->db->registros();
}


public function todos_roles_profesor($id_prof){
    $this->db->query("SELECT * FROM `cpifp_profesor` , cpifp_profesor_departamento, cpifp_departamento, cpifp_rol 
    WHERE cpifp_profesor_departamento.id_profesor=cpifp_profesor.id_profesor 
    AND cpifp_rol.id_rol=cpifp_profesor_departamento.id_rol
    AND cpifp_departamento.id_departamento=cpifp_profesor_departamento.id_departamento
    AND cpifp_profesor.id_profesor=:id_profe;");
    $this->db->bind(':id_profe', $id_prof);
    return $this->db->registros();
}
    

/************************ ASIGNAR DEPARTAMENTO ****************************/

public function asignar_departamento($id_profe, $id_dep, $id_rol){
    $this->db->query("INSERT INTO cpifp_profesor_departamento (id_profesor, id_departamento, id_rol) 
    VALUES (:id_profe, :id_dep, :id_rol)");
    $this->db->bind(':id_profe', $id_profe);
    $this->db->bind(':id_dep', $id_dep);
    $this->db->bind(':id_rol', $id_rol);
    if ($this->db->execute()){
        return true;
    }else{
        return false;
    }
}


/************************ BORRAR DEPARTAMENTO ****************************/

public function eliminar_de_departamento($id_profesor, $id_departamento, $id_rol){
    $this->db->query("DELETE FROM cpifp_profesor_departamento 
    WHERE id_profesor=:id_profesor 
    AND id_departamento=:id_departamento 
    AND id_rol=:id_rol;");
    $this->db->bind(':id_departamento',$id_departamento);
    $this->db->bind(':id_profesor',$id_profesor);
    $this->db->bind(':id_rol',$id_rol);
    if ($this->db->execute()){
        return true;
    }else{
        return false;
    } 
}




}