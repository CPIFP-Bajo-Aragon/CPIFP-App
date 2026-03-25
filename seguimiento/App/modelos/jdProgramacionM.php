<?php

class jdProgramacionM {
    

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


public function nuevas_por_ciclo($id_dep, $id_lectivo){
    $this->db->query("SELECT m.id_departamento, m.departamento, m.id_ciclo, m.ciclo, COUNT(p.id_modulo) AS suma
    FROM segui_departamento_modulo m
    JOIN seg_programaciones p ON m.id_modulo = p.id_modulo
    WHERE p.nueva = 1 
    AND m.id_departamento=:id_departamento
    AND p.id_lectivo=:id_lectivo
    GROUP BY m.id_departamento, m.departamento, m.id_ciclo;");
    $this->db->bind(':id_departamento',$id_dep);
    $this->db->bind(':id_lectivo',$id_lectivo);
    return $this->db->registros();
}


public function modulos_ciclo($id_ciclo, $id_dep){
    $this->db->query("SELECT * FROM segui_departamento_modulo 
    WHERE id_ciclo=:id_ciclo
    AND departamento_modulo=:id_departamento;");
    $this->db->bind(':id_ciclo',$id_ciclo);
    $this->db->bind(':id_departamento',$id_dep);
    return $this->db->registros();
}

// solo las activas de cada modulo
public function programaciones_modulos_activas($id_departamento){
    $this->db->query("SELECT * FROM segui_departamento_modulo, seg_programaciones
    WHERE seg_programaciones.id_modulo=segui_departamento_modulo.id_modulo 
    AND segui_departamento_modulo.id_departamento=:id_departamento
    AND seg_programaciones.activa=1;");
    $this->db->bind(':id_departamento',$id_departamento);
    return $this->db->registros();
}


// // solo las activas de cada modulo
// public function programaciones_modulos_activas($id_departamento){
//     $this->db->query("SELECT * FROM segui_departamento_modulo, seg_programaciones, cpifp_profesor
//     WHERE seg_programaciones.id_modulo=segui_departamento_modulo.id_modulo 
//     AND seg_programaciones.id_profesor=cpifp_profesor.id_profesor
//     AND segui_departamento_modulo.id_departamento=:id_departamento
//     AND seg_programaciones.activa=1;");
//     $this->db->bind(':id_departamento',$id_departamento);
//     return $this->db->registros();
// }


// todas las programaciones de los modulos
public function programaciones_departamento($id_departamento){
    $this->db->query("SELECT * FROM segui_departamento_modulo, seg_programaciones, cpifp_profesor
    WHERE seg_programaciones.id_modulo=segui_departamento_modulo.id_modulo 
    AND seg_programaciones.id_profesor=cpifp_profesor.id_profesor
    AND segui_departamento_modulo.id_departamento=:id_departamento;");
    $this->db->bind(':id_departamento',$id_departamento);
    return $this->db->registros();
}


public function programaciones_ediciones_anteriores($id_departamento){
    $this->db->query("SELECT p1.*, sdm.*, cpf.*         
    FROM seg_programaciones p1
    JOIN segui_departamento_modulo sdm ON p1.id_modulo = sdm.id_modulo
    LEFT JOIN cpifp_profesor cpf ON p1.id_profesor = cpf.id_profesor 
    WHERE 
    p1.id_programacion = (
        SELECT MAX(p2.id_programacion)
        FROM seg_programaciones p2
        WHERE p2.id_modulo = p1.id_modulo
          AND (p2.nueva = 0 OR (p2.nueva = 1 AND p2.id_programacion = 
              (SELECT MAX(p3.id_programacion)
               FROM seg_programaciones p3
               WHERE p3.id_modulo = p2.id_modulo
                 AND p3.nueva = 0
                 AND p3.id_programacion < p2.id_programacion)))
    )
    AND sdm.id_departamento = :id_departamento;");
    $this->db->bind(':id_departamento',$id_departamento);
    return $this->db->registros();
}



// profesores AÑO ANTERIOR
public function profesor_anterior($id_ciclo, $id_lectivo){
    $this->db->query("SELECT * 
    FROM segui_departamento_modulo
    JOIN cpifp_profesor_modulo ON segui_departamento_modulo.id_modulo = cpifp_profesor_modulo.id_modulo
    JOIN cpifp_profesor ON cpifp_profesor_modulo.id_profesor = cpifp_profesor.id_profesor
    WHERE cpifp_profesor_modulo.id_lectivo = (
            SELECT id_lectivo
            FROM seg_lectivos WHERE id_lectivo < :id_lectivo ORDER BY id_lectivo DESC LIMIT 1)
    AND segui_departamento_modulo.id_ciclo = :id_ciclo;");
    $this->db->bind(':id_ciclo',$id_ciclo);
    $this->db->bind(':id_lectivo',$id_lectivo);
    return $this->db->registros();
}


// profesor año actual
public function profesor_modulo_ciclo($id_ciclo, $id_lectivo){
    $this->db->query("SELECT * FROM segui_departamento_modulo, cpifp_profesor_modulo, cpifp_profesor
    WHERE segui_departamento_modulo.id_modulo=cpifp_profesor_modulo.id_modulo
    AND cpifp_profesor.id_profesor=cpifp_profesor_modulo.id_profesor
    AND segui_departamento_modulo.id_ciclo=:id_ciclo
    AND cpifp_profesor_modulo.id_lectivo=:id_lectivo;");
    $this->db->bind(':id_ciclo',$id_ciclo);
    $this->db->bind(':id_lectivo',$id_lectivo);
    return $this->db->registros();
}




//*************************************** FOL // LEO ***************************************************/


// profesor año actual
public function profesor_modulo_actual($id_lectivo, $id_departamento){
    $this->db->query("SELECT * FROM segui_departamento_modulo, cpifp_profesor_modulo, cpifp_profesor
    WHERE segui_departamento_modulo.id_modulo=cpifp_profesor_modulo.id_modulo
    AND cpifp_profesor.id_profesor=cpifp_profesor_modulo.id_profesor
    AND segui_departamento_modulo.departamento_modulo=:id_departamento
    AND cpifp_profesor_modulo.id_lectivo=:id_lectivo;");
    $this->db->bind(':id_lectivo',$id_lectivo);
    $this->db->bind(':id_departamento',$id_departamento);
    return $this->db->registros();
}


// profesores AÑO ANTERIOR
public function profesor_modulo_anterior($id_lectivo, $id_departamento){
    $this->db->query("SELECT * 
    FROM segui_departamento_modulo
    JOIN cpifp_profesor_modulo ON segui_departamento_modulo.id_modulo = cpifp_profesor_modulo.id_modulo
    JOIN cpifp_profesor ON cpifp_profesor_modulo.id_profesor = cpifp_profesor.id_profesor
    WHERE cpifp_profesor_modulo.id_lectivo = (
            SELECT id_lectivo
            FROM seg_lectivos WHERE id_lectivo < :id_lectivo ORDER BY id_lectivo DESC LIMIT 1)
    AND segui_departamento_modulo.departamento_modulo = :id_departamento;");
    $this->db->bind(':id_departamento',$id_departamento);
    $this->db->bind(':id_lectivo',$id_lectivo);
    return $this->db->registros();
}



// todas las programaciones de cada modulo DEL AÑO ANTERIOR
public function programaciones_modulos_anio_anterior_fol($id_lectivo, $id_departamento){
    $this->db->query("SELECT * FROM segui_departamento_modulo, seg_programaciones
    WHERE seg_programaciones.id_modulo=segui_departamento_modulo.id_modulo 
    AND seg_programaciones.id_lectivo = (
            SELECT id_lectivo
            FROM seg_lectivos WHERE id_lectivo < :id_lectivo ORDER BY id_lectivo DESC LIMIT 1)
    AND segui_departamento_modulo.departamento_modulo = :id_departamento;");
    $this->db->bind(':id_departamento',$id_departamento);
    $this->db->bind(':id_lectivo',$id_lectivo);
    return $this->db->registros();
}


public function nuevas_fol_leo($id_dep, $id_lectivo){
    $this->db->query("SELECT SUM(CASE WHEN p1.nueva = 1 THEN 1 ELSE 0 END) AS suma
    FROM seg_programaciones p1
    JOIN segui_departamento_modulo sdm ON p1.id_modulo = sdm.id_modulo
    WHERE sdm.departamento_modulo = :id_departamento
    AND verificada_jefe_dep = 0
    AND p1.id_lectivo=:id_lectivo;");
    $this->db->bind(':id_departamento',$id_dep);
    $this->db->bind(':id_lectivo',$id_lectivo);
    return $this->db->registros();
}


public function modulos($id_dep){
    $this->db->query("SELECT * FROM `segui_departamento_modulo`
    WHERE departamento_modulo=:id_dep;");
    $this->db->bind(':id_dep',$id_dep);
    return $this->db->registros();
}


// solo las activas de cada modulo
public function programaciones_modulos_activas_fol_leo($id_departamento){
    $this->db->query("SELECT * FROM segui_departamento_modulo, seg_programaciones
    WHERE seg_programaciones.id_modulo=segui_departamento_modulo.id_modulo 
    AND segui_departamento_modulo.departamento_modulo=:id_departamento
    AND seg_programaciones.activa=1;");
    $this->db->bind(':id_departamento',$id_departamento);
    return $this->db->registros();
}


// todas las programaciones de los modulos
public function programaciones_departamento_fol_leo($id_departamento){
    $this->db->query("SELECT * FROM segui_departamento_modulo, seg_programaciones, cpifp_profesor
    WHERE seg_programaciones.id_modulo=segui_departamento_modulo.id_modulo 
    AND seg_programaciones.id_profesor=cpifp_profesor.id_profesor
    AND segui_departamento_modulo.departamento_modulo=:id_departamento;");
    $this->db->bind(':id_departamento',$id_departamento);
    return $this->db->registros();
}


public function programaciones_ediciones_anteriores_fol_leo($id_departamento){
    $this->db->query("SELECT p1.*, sdm.*, cpf.*         
    FROM seg_programaciones p1
    JOIN segui_departamento_modulo sdm ON p1.id_modulo = sdm.id_modulo
    LEFT JOIN cpifp_profesor cpf ON p1.id_profesor = cpf.id_profesor 
    WHERE 
    p1.id_programacion = (
        SELECT MAX(p2.id_programacion)
        FROM seg_programaciones p2
        WHERE p2.id_modulo = p1.id_modulo
          AND (p2.nueva = 0 OR (p2.nueva = 1 AND p2.id_programacion = 
              (SELECT MAX(p3.id_programacion)
               FROM seg_programaciones p3
               WHERE p3.id_modulo = p2.id_modulo
                 AND p3.nueva = 0
                 AND p3.id_programacion < p2.id_programacion)))
    )
    AND sdm.departamento_modulo = :id_departamento;");
    $this->db->bind(':id_departamento',$id_departamento);
    return $this->db->registros();
}







//*********************************** VERIFICAR PROGRAMACION **********************************************/

public function verifica_programacion($id_modulo) {
    $this->db->query("UPDATE seg_programaciones SET verificada_jefe_dep = 1  
    WHERE id_modulo = :id_modulo AND activa = 1");
    $this->db->bind(':id_modulo', $id_modulo);
    if ($this->db->execute()){
        return true;
     }else{
        return false;
     }
}






    

}