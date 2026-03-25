<?php

class IndicadorM {

    private $db;

    public function __construct(){
        $this->db = new Base;
    }



    
// CURSO ACTUAL
public function obtener_lectivo(){
    $this->db->query("SELECT id_lectivo, lectivo, date_format(fecha_inicio,'%d-%m-%Y') AS fecha_inicio, 
    date_format(fecha_fin,'%d-%m-%Y') AS fecha_fin, 
    cerrado FROM seg_lectivos WHERE cerrado = 0;");
    return $this->db->registros();
}



// TODOS LOS INDICADORES
public function obtener_indicadores(){
    $this->db->query("SELECT * FROM `seg_indicadores`;");
    return $this->db->registros();
}


// UN INDICADOR CONCRETO
public function obtener_indicador($id_indicador){
    $this->db->query("SELECT * FROM `seg_indicadores` 
    WHERE id_indicador=:id;");
    $this->db->bind(':id',$id_indicador);
    return $this->db->registros();
}
        

// TODAS LAS PREGUNTAS DE UN INDICADOR CONCRETO
public function obtener_preguntas ($id_indicador){
    $this->db->query("SELECT * FROM `seg_preguntas` 
    WHERE id_indicador=:id_indicador;");
    $this->db->bind(':id_indicador',$id_indicador);
    return $this->db->registros();
}




}