<?php


class DepartamentoM {

    private $db;
    

    public function __construct(){
        $this->db = new Base;
    }



// TODOS LOS DEPARTAMENTOS
public function obtener_departamentos(){
    $this->db->query("SELECT * FROM cpifp_departamento;");
    return $this->db->registros();
}


// INFO UN DEPARTAMENTO CONCRETO
public function info_departamento($id_departamento){
    $this->db->query("SELECT * FROM cpifp_departamento 
    WHERE id_departamento=:id_departamento;");
    $this->db->bind(':id_departamento',$id_departamento);
    return $this->db->registros();
}



// PROFESORES DE UN DEPARTAMENTO
public function profesores_x_departamento($id_dep){
    $this->db->query("SELECT * FROM cpifp_profesor_departamento, cpifp_departamento, cpifp_profesor, cpifp_rol
    WHERE cpifp_profesor_departamento.id_departamento=cpifp_departamento.id_departamento 
    AND cpifp_profesor.id_profesor=cpifp_profesor_departamento.id_profesor 
    AND cpifp_rol.id_rol=cpifp_profesor_departamento.id_rol
    AND cpifp_departamento.id_departamento=:id_dep
    ORDER BY cpifp_profesor.activo=0;");
    $this->db->bind(':id_dep',$id_dep);
    return $this->db->registros();
}


// CICLOS DE UN DEPARTAMENTO CONCRETO
public function ciclos_x_departamento($id_dep){
    $this->db->query("SELECT cpifp_ciclos.id_ciclo, ciclo, ciclo_corto, cpifp_departamento.id_departamento, 
    departamento, cpifp_grados.id_grado, nombre, cpifp_turnos.id_turno, turno
    FROM cpifp_ciclos, cpifp_departamento, cpifp_grados, cpifp_turnos
    WHERE cpifp_departamento.id_departamento=cpifp_ciclos.id_departamento
    AND cpifp_grados.id_grado=cpifp_ciclos.id_grado
    AND cpifp_turnos.id_turno=cpifp_ciclos.id_turno
    AND cpifp_departamento.id_departamento=:id_dep;");
    $this->db->bind(':id_dep',$id_dep);
    return $this->db->registros();
}




/************************ NUEVO DEPARTAMENTO ****************************/

public function nuevo_departamento($nuevo){
    $this->db->query("INSERT INTO cpifp_departamento (departamento, departamento_corto, isFormacion,sin_ciclo) 
    VALUES (:departamento, :departamento_corto, :isFormacion, :sin_ciclo);");
    $this->db->bind(':departamento', $nuevo['departamento']);
    $this->db->bind(':departamento_corto', $nuevo['departamento_corto']);
    $this->db->bind(':isFormacion', $nuevo['isFormacion']);
    $this->db->bind(':sin_ciclo', $nuevo['sin_ciclo']);
    if($this->db->execute()){
        return true;
    }else{
        return false;
    }
}



/************************ BORRAR DEPARTAMENTO ****************************/

public function borrar_departamento($id_dep){
    $this->db->query("DELETE FROM cpifp_departamento 
    WHERE id_departamento=:id_dep;");
    $this->db->bind(':id_dep',$id_dep);
    if ($this->db->execute()){
        return true;
    }else{
        return false;
    }
}



/************************ EDITAR DEPARTAMENTO ****************************/

public function editar_departamento($editar,$id_dep){
    $this->db->query("UPDATE cpifp_departamento 
    SET departamento=:departamento, departamento_corto=:departamento_corto, isFormacion=:isFormacion, sin_ciclo=:sin_ciclo
    WHERE id_departamento=:id_dep;"); 
    $this->db->bind(':departamento', $editar['departamento']);
    $this->db->bind(':departamento_corto', $editar['departamento_corto']);
    $this->db->bind(':isFormacion', $editar['isFormacion']);
    $this->db->bind(':sin_ciclo', $editar['sin_ciclo']);
    $this->db->bind(':id_dep',$id_dep);
    if($this->db->execute()){
        return true;
    }else{
        return false;
    }
}



}