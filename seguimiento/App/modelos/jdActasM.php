<?php

class jdActasM {
    

    private $db;


    public function __construct(){
        $this->db = new Base;
    }



    
// TRAE EL CURSO LECTIVO ACTIVO
public function obtener_lectivo(){
    $this->db->query("SELECT id_lectivo, lectivo, cerrado, date_format(fecha_inicio,'%d-%m-%Y') AS fecha_inicio, 
    date_format(fecha_fin,'%d-%m-%Y') AS fecha_fin
    FROM seg_lectivos where cerrado=0;");
    return $this->db->registros();
}


// INFO DEL PROFESOR DE UN DEPARTAMENTO DE FORMACION
public function departamentos_formacion($id_profesor){
    $this->db->query("SELECT * FROM cpifp_profesor_departamento, cpifp_departamento
    WHERE cpifp_profesor_departamento.id_departamento=cpifp_departamento.id_departamento
    AND id_profesor=:id AND isFormacion=1;");
    $this->db->bind(':id', $id_profesor);
    return $this->db->registros();
}


// TRAE SOLO EL NOMBRE DE LAS EVALUACIONES
public function nombre_evaluaciones(){
    $this->db->query("SELECT * FROM cpifp_evaluaciones;");
    return $this->db->registros();
}


// TRAE SOLO EL NOMBRE DE LOS INDICADORES
public function nombre_indicadores(){
    $this->db->query("SELECT * FROM seg_indicadores;");
    return $this->db->registros();
}



// PREGUNTAS EP1
public function preguntas_ep1(){
    $this->db->query("SELECT * FROM `seg_preguntas`, seg_indicadores
    where seg_preguntas.id_indicador=seg_indicadores.id_indicador
    and seg_indicadores.id_indicador=6;");
    return $this->db->registros();
}


// DEVUELVE TODAS LAS ASIGNATURAS DE UN DEPARTAMENTO (VIENE DE UNA VISTA CREADA)
public function obtener_asignaturas($id_dep){
    $this->db->query("SELECT * FROM segui_departamento_modulo where id_departamento=:id_dep;");
    $this->db->bind(':id_dep',$id_dep);
    return $this->db->registros();
}



// ACTAS DE TODOS LOS CICLOS - buena
public function info_actas($id_dep,$id_lectivo){
    $this->db->query("SELECT * FROM seg_totales, cpifp_profesor_modulo, cpifp_profesor , cpifp_modulo, cpifp_departamento, 
    seg_indicadores, seg_seguimiento_modulo, cpifp_evaluaciones,seg_evaluaciones, cpifp_ciclos, cpifp_curso, cpifp_grados, seg_numero, cpifp_turnos
    where seg_totales.id_modulo=cpifp_profesor_modulo.id_modulo 
    and cpifp_profesor.id_profesor=cpifp_profesor_modulo.id_profesor 
    and cpifp_profesor_modulo.id_modulo=cpifp_modulo.id_modulo 
    and cpifp_departamento.id_departamento=cpifp_modulo.id_departamento 
    and seg_indicadores.id_indicador=seg_totales.id_indicador 
    and seg_seguimiento_modulo.id_seguimiento=seg_totales.id_seguimiento 
    and seg_seguimiento_modulo.id_seg_evaluacion=seg_evaluaciones.id_seg_evaluacion 
    and seg_evaluaciones.id_evaluacion=cpifp_evaluaciones.id_evaluacion
    and cpifp_ciclos.id_ciclo=cpifp_curso.id_ciclo
    and cpifp_ciclos.id_grado=cpifp_grados.id_grado
    and cpifp_curso.id_numero=seg_numero.id_numero
    and cpifp_curso.id_curso=cpifp_modulo.id_curso
    and cpifp_turnos.id_turno=cpifp_ciclos.id_turno
    and cpifp_departamento.id_departamento=:id_dep
    and cpifp_profesor_modulo.id_lectivo=:id_lectivo
    ORDER BY cpifp_curso.id_numero;");
    $this->db->bind(':id_dep',$id_dep);
    $this->db->bind(':id_lectivo',$id_lectivo);
    return $this->db->registros();
}


// LA INFO SALE DE SEG_EP1 (buena)
public function info_actas_ep1($id_dep, $id_lectivo){

    $this->db->query("SELECT * FROM seg_ep1, seg_seguimiento_modulo, cpifp_modulo, cpifp_profesor_modulo, cpifp_profesor ,
    seg_indicadores, seg_preguntas, cpifp_departamento, cpifp_ciclos, cpifp_curso, cpifp_grados, seg_numero, cpifp_turnos
    where seg_ep1.id_seguimiento=seg_seguimiento_modulo.id_seguimiento
    and seg_seguimiento_modulo.id_modulo=cpifp_profesor_modulo.id_modulo
    and cpifp_profesor_modulo.id_modulo=cpifp_modulo.id_modulo
    and cpifp_profesor.id_profesor=cpifp_profesor_modulo.id_profesor 
    and seg_indicadores.id_indicador=seg_preguntas.id_indicador
    and seg_preguntas.id_pregunta=seg_ep1.id_pregunta
    and cpifp_departamento.id_departamento=cpifp_modulo.id_departamento 
    and cpifp_ciclos.id_ciclo=cpifp_curso.id_ciclo
    and cpifp_ciclos.id_grado=cpifp_grados.id_grado
    and cpifp_curso.id_numero=seg_numero.id_numero
    and cpifp_curso.id_curso=cpifp_modulo.id_curso
    and cpifp_turnos.id_turno=cpifp_ciclos.id_turno
    and seg_indicadores.indicador_corto='EP1'
    and cpifp_departamento.id_departamento=:id_dep
    and cpifp_profesor_modulo.id_lectivo=:id_lectivo
    ORDER BY cpifp_curso.id_numero;");
    $this->db->bind(':id_dep',$id_dep);
    $this->db->bind(':id_lectivo',$id_lectivo);
    return $this->db->registros();
}


public function his_total_modulo($id_dep, $id_lectivo){
    $this->db->query("SELECT * FROM his_total_modulo
    WHERE his_total_modulo.id_departamento_modulo=:id_dep
    AND id_lectivo=:id_lectivo;");
    $this->db->bind(':id_dep',$id_dep);
    $this->db->bind(':id_lectivo',$id_lectivo);
    return $this->db->registros();
}


public function umbrales_indicadores($id_lectivo){
    $this->db->query("SELECT * FROM seg_indicadores_grados
    WHERE id_lectivo=:id_lectivo;");
    $this->db->bind(':id_lectivo',$id_lectivo);
    return $this->db->registros();
}


// ACTAS DE TODOS LOS CICLOS X EVALUACION - buena
public function info_actas_evaluacion($id_evaluacion, $id_dep, $id_lectivo){
    $this->db->query("SELECT * FROM seg_totales, cpifp_profesor_modulo, cpifp_profesor , cpifp_modulo, cpifp_departamento, 
    seg_indicadores, seg_seguimiento_modulo, cpifp_evaluaciones,seg_evaluaciones, cpifp_ciclos, cpifp_curso, cpifp_grados, seg_numero, cpifp_turnos
    where seg_totales.id_modulo=cpifp_profesor_modulo.id_modulo 
    and cpifp_profesor.id_profesor=cpifp_profesor_modulo.id_profesor 
    and cpifp_profesor_modulo.id_modulo=cpifp_modulo.id_modulo 
    and cpifp_departamento.id_departamento=cpifp_modulo.id_departamento 
    and seg_indicadores.id_indicador=seg_totales.id_indicador 
    and seg_seguimiento_modulo.id_seguimiento=seg_totales.id_seguimiento 
    and seg_seguimiento_modulo.id_seg_evaluacion=seg_evaluaciones.id_seg_evaluacion 
    and seg_evaluaciones.id_evaluacion=cpifp_evaluaciones.id_evaluacion
    and cpifp_ciclos.id_ciclo=cpifp_curso.id_ciclo
    and cpifp_ciclos.id_grado=cpifp_grados.id_grado
    and cpifp_curso.id_numero=seg_numero.id_numero
    and cpifp_curso.id_curso=cpifp_modulo.id_curso
    and cpifp_evaluaciones.id_evaluacion=:id_evaluacion
    and cpifp_turnos.id_turno=cpifp_ciclos.id_turno
    and cpifp_departamento.id_departamento=:id_dep
    and cpifp_profesor_modulo.id_lectivo=:id_lectivo
    ORDER BY cpifp_curso.id_numero;");
    $this->db->bind(':id_evaluacion',$id_evaluacion);
    $this->db->bind(':id_dep',$id_dep);
    $this->db->bind(':id_lectivo',$id_lectivo);
    return $this->db->registros();
}


// INDICADORES, GRADOS Y SU PORCENTAJE
public function obtener_indicadores_grados(){
    $this->db->query("SELECT `seg_indicadores`.`id_indicador`,`indicador`, `indicador_corto`, `cpifp_grados`.`id_grado`, 
    `cpifp_grados`.`nombre` AS `nombre_grado`, `porcentaje`
    FROM `seg_indicadores`, `cpifp_grados`, `seg_indicadores_grados`
    WHERE `seg_indicadores`.`id_indicador`=`seg_indicadores_grados`.`id_indicador` 
    AND `cpifp_grados`.`id_grado`=`seg_indicadores_grados`.`id_grado`;");
    return $this->db->registros();
}


public function his_total_curso($id_dep, $id_lectivo){
    $this->db->query("SELECT * FROM his_total_curso
    WHERE his_total_curso.id_departamento=:id_dep
    AND id_lectivo=:id_lectivo;");
    $this->db->bind(':id_dep',$id_dep);
    $this->db->bind(':id_lectivo',$id_lectivo);
    return $this->db->registros();
}






    

}