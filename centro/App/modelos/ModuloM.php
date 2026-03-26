<?php

class ModuloM{

    private $db;

    public function __construct(){
        $this->db = new Base;
    }






// TRAE INFO DE UN CURSO CONCRETO
public function curso_ciclo_concreto($id_ciclo, $id_curso){
    $this->db->query("SELECT * FROM `cpifp_curso`, cpifp_ciclos, cpifp_departamento, seg_numero
    where cpifp_curso.id_ciclo=cpifp_ciclos.id_ciclo 
    and seg_numero.id_numero=cpifp_curso.id_numero
    and cpifp_ciclos.id_departamento=cpifp_departamento.id_departamento
    and cpifp_curso.id_ciclo=:id_ciclo and cpifp_curso.id_curso=:id_curso ORDER BY cpifp_curso.id_numero;");
    $this->db->bind(':id_ciclo',$id_ciclo);
    $this->db->bind(':id_curso',$id_curso);
    return $this->db->registros();
}


// TRAE INFO DE TODOS LOS MODULOS DE UN CURSO CONCRETO
public function modulos_un_curso($id_curso){
    $this->db->query("SELECT cpifp_departamento.id_departamento, cpifp_departamento.departamento, cpifp_departamento.departamento_corto, cpifp_departamento.isFormacion,
    cpifp_ciclos.id_ciclo, cpifp_ciclos.ciclo, cpifp_ciclos.ciclo_corto, cpifp_ciclos.id_grado, cpifp_ciclos.id_turno,
    cpifp_modulo.id_modulo, cpifp_modulo.modulo, cpifp_modulo.nombre_corto, cpifp_modulo.horas_totales, cpifp_modulo.cuerpo, cpifp_modulo.id_curso,
    cpifp_modulo.horas_semanales, cpifp_modulo.id_departamento as departamento_modulo, cpifp_modulo.codigo_programacion,
    cpifp_curso.curso, cpifp_curso.id_numero, seg_numero.nombre_curso
    FROM cpifp_departamento, cpifp_ciclos, cpifp_modulo, cpifp_curso, seg_numero
    WHERE cpifp_modulo.id_curso=cpifp_curso.id_curso
    AND cpifp_ciclos.id_ciclo=cpifp_curso.id_ciclo
    AND cpifp_departamento.id_departamento=cpifp_ciclos.id_departamento
    AND seg_numero.id_numero=cpifp_curso.id_numero
    AND cpifp_curso.id_curso=:id_curso;");
    $this->db->bind(':id_curso',$id_curso);
    return $this->db->registros();
}



// TRAE EL DEPARTAMENTO PROPIO, FOL E INGLES
public function departamentos($id_departamento){
    $this->db->query("SELECT * FROM cpifp_departamento
    WHERE cpifp_departamento.id_departamento = :id_departamento 
    OR (isFormacion = 1 AND sin_ciclo = 1);");
    $this->db->bind(':id_departamento',$id_departamento);
    return $this->db->registros();
}







/************************ NUEVO MODULO ****************************/

public function nuevo_modulo($nuevo){
    $this->db->query("INSERT INTO cpifp_modulo (modulo, nombre_corto, horas_totales, id_curso, horas_semanales, id_departamento, codigo_programacion) 
    VALUES (:modulo, :nombre_corto, :horas_totales, :id_curso, :horas_semanales, :id_departamento, :codigo_programacion);");
    $this->db->bind(':modulo', $nuevo['modulo']);
    $this->db->bind(':nombre_corto', $nuevo['nombre_corto']);
    $this->db->bind(':horas_totales', $nuevo['horas_totales']);
    $this->db->bind(':id_curso', $nuevo['id_curso']);
    $this->db->bind(':horas_semanales', $nuevo['horas_semanales']);
    $this->db->bind(':id_departamento', $nuevo['id_departamento']);
    $this->db->bind(':codigo_programacion', $nuevo['codigo_programacion']);
    if ($this->db->execute()){
        return true;
    }else{
        return false;
    }
}   


/************************ BORRAR MODULO ****************************/

public function borrar_modulo($id_modulo){
    $this->db->query("DELETE FROM cpifp_modulo 
    WHERE id_modulo=:id_modulo;");
    $this->db->bind(':id_modulo',$id_modulo);
    if ($this->db->execute()){
        return true;
    }else{
        return false;
    }
}


/************************ EDITAR MODULO ****************************/

public function editar_modulo($editar,$id_modulo){
    $this->db->query("UPDATE cpifp_modulo 
    SET modulo=:modulo, nombre_corto=:modulo_corto, horas_totales=:horas_totales,
    horas_semanales=:horas_semanales, id_departamento=:id_departamento, codigo_programacion=:codigo_programacion 
    WHERE id_modulo = :id_modulo;"); 
    $this->db->bind(':modulo', $editar['modulo']);
    $this->db->bind(':modulo_corto', $editar['nombre_corto']);
    $this->db->bind(':horas_totales', $editar['horas_totales']);
    $this->db->bind(':horas_semanales', $editar['horas_semanales']);
    $this->db->bind(':id_departamento', $editar['id_departamento']);
    $this->db->bind(':codigo_programacion', $editar['codigo_programacion']);
    $this->db->bind(':id_modulo',$id_modulo);
    if($this->db->execute()){
        return true;
    }else{
        return false;
    }
}





}