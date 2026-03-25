<?php

class AccionM
{
    private $db;

    public function __construct(){
        $this->db = new Base;
    }


// CURSO ACTUAL
public function obtener_lectivo(){
    $this->db->query("SELECT id_lectivo, lectivo, cerrado, 
    date_format(fecha_inicio,'%d-%m-%Y') AS fecha_inicio, 
    date_format(fecha_fin,'%d-%m-%Y') AS fecha_fin
    FROM seg_lectivos where cerrado=0;");
    return $this->db->registros();
}


// TIPOS DE ACCIONE
public function obtener_tipos(){
    $this->db->query("SELECT * FROM seg_acciones;");
    return $this->db->registros();
}


// TODAS LAS CAUSAS Y SOLUCIONES Y A QUE TIPO PERTENECEN
public function obtener_causas_soluciones(){
    $this->db->query("SELECT * FROM seg_soluciones, seg_acciones 
    WHERE seg_soluciones.id_accion=seg_acciones.id_accion;");
    return $this->db->registros();
}




/************************ NUEVA CAUSA/SOLUCION ****************************/


public function nueva_causa_solucion($nuevo){
    $this->db->query("INSERT INTO seg_soluciones (solucion, id_accion) 
    VALUES (:solucion, :id_accion);");
    $this->db->bind(':id_accion', $nuevo->accion);
    $this->db->bind(':solucion', $nuevo->descripcion);            
    if ($this->db->execute()){
        return true;
    }else{
        return false;
    }
}


/************************ BAJA CAUSA/SOLUCION ****************************/

public function borrar_causa_solucion($id_solucion){
    $this->db->query("DELETE FROM seg_soluciones 
    WHERE id_solucion=:id_solucion;");
    $this->db->bind(':id_solucion',$id_solucion);
    if ($this->db->execute()){
        return true;
    }else{
        return false;
    }
}


/************************ MODIFICACION CAUSA/SOLUCION ****************************/

public function editar_causa_solucion($editar, $id_solucion){
    $this->db->query("UPDATE seg_soluciones SET solucion=:solucion, id_accion=:id_accion
    WHERE id_solucion=:id_solucion;"); 
    $this->db->bind(':solucion', $editar['descripcion']);
    $this->db->bind(':id_accion', $editar['accion']);
    $this->db->bind(':id_solucion',$id_solucion);
    if($this->db->execute()){
        return true;
    }else{
        return false;
    }
}



}