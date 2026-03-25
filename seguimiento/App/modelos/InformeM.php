<?php

class InformeM {
    

    private $db;


    public function __construct(){
        $this->db = new Base;
    }




// TRAE EL CURSO LECTIVO ACTIVO
public function obtener_lectivo(){
    $this->db->query("SELECT id_lectivo, lectivo, cerrado, date_format(fecha_inicio,'%d-%m-%Y') AS fecha_inicio, 
    date_format(fecha_fin,'%d-%m-%Y') AS fecha_fin
    FROM seg_lectivos WHERE cerrado=0;");
    return $this->db->registros();
}


// INDICADORES, GRADOS Y SU PORCENTAJE PARA CURSO ACTUAL
public function obtener_indicadores_grados($id_lectivo){
    $this->db->query("SELECT `seg_indicadores`.`id_indicador`,`indicador`, `indicador_corto`, `cpifp_grados`.`id_grado`, 
    `cpifp_grados`.`nombre` AS `nombre_grado`, `porcentaje`,
    seg_indicadores_grados.id_lectivo
    FROM `seg_indicadores`, `cpifp_grados`, `seg_indicadores_grados`
    WHERE `seg_indicadores`.`id_indicador`=`seg_indicadores_grados`.`id_indicador` 
    AND `cpifp_grados`.`id_grado`=`seg_indicadores_grados`.`id_grado`
    AND seg_indicadores_grados.id_lectivo=:id_lectivo
    ORDER BY seg_indicadores.id_indicador;");
    $this->db->bind(':id_lectivo', $id_lectivo);
    return $this->db->registros();
}


// SOLO DEPARTAMENTOS FORMACION
public function solo_departamentos_formacion(){
    $this->db->query("SELECT * FROM cpifp_departamento
    WHERE isFormacion = 1;");
    return $this->db->registros();
}



//***************************************************************/
//************** INSERTAR HIS_TOTAL_CURSO ***********************/
//***************************************************************/
// se usan para actualizar his_total_curso para el informe por curso


// no hace calculo para FOL Y LEO
  public function resumen_modulos($id_lectivo){
    $this->db->query("SELECT * FROM his_total_modulo
    WHERE id_lectivo=:id_lectivo
    AND id_departamento = id_departamento_modulo
    ORDER BY id_indicador;");
    $this->db->bind(':id_lectivo', $id_lectivo);
    return $this->db->registros();
 }
 


 // TRAE LOS TOTALES DE CADA CICLO POR CURSO - curso actual
 public function his_total_curso($id_lectivo){
    $this->db->query("SELECT * FROM his_total_curso
    WHERE id_lectivo=:id_lectivo
    ORDER BY id_indicador;");
    $this->db->bind(':id_lectivo', $id_lectivo);
    return $this->db->registros();
 }
 


 public function insertar_his_total_curso($promedios) {

    foreach ($promedios as $promedio) {

        // Primero, verificar si ya existe un registro con esa combinación clave
        $this->db->query("SELECT id_total_curso 
        FROM his_total_curso 
        WHERE id_lectivo = :id_lectivo 
        AND id_ciclo = :id_ciclo 
        AND id_turno = :id_turno 
        AND id_curso = :id_curso 
        AND id_indicador = :id_indicador");
        $this->db->bind(':id_lectivo', $promedio['id_lectivo']);
        $this->db->bind(':id_ciclo', $promedio['id_ciclo']);
        $this->db->bind(':id_turno', $promedio['id_turno']);
        $this->db->bind(':id_curso', $promedio['id_curso']);
        $this->db->bind(':id_indicador', $promedio['id_indicador']);
        $this->db->execute();



        if ($this->db->rowCount() > 0) {
            // Ya existe: hacer UPDATE
            $this->db->query("UPDATE his_total_curso SET total = :total, conforme = :conforme
            WHERE id_lectivo = :id_lectivo 
            AND id_ciclo = :id_ciclo 
            AND id_turno = :id_turno 
            AND id_curso = :id_curso 
            AND id_indicador = :id_indicador");

            $this->db->bind(':total', $promedio['total']);
            $this->db->bind(':id_lectivo', $promedio['id_lectivo']);
            $this->db->bind(':id_ciclo', $promedio['id_ciclo']);
            $this->db->bind(':id_turno', $promedio['id_turno']);
            $this->db->bind(':id_curso', $promedio['id_curso']);
            $this->db->bind(':id_indicador', $promedio['id_indicador']);
            $this->db->bind(':conforme', $promedio['modulo_conforme']);
            $this->db->execute();

        } else {

            // No existe: hacer INSERT
            $this->db->query("INSERT INTO his_total_curso (
                id_lectivo, lectivo, id_ciclo, ciclo, ciclo_corto,
                id_departamento, departamento, departamento_corto,
                id_grado, grado, id_turno, turno,
                id_curso, curso, id_numero, numero, nombre_curso,
                id_indicador, indicador, indicador_corto, total, conforme
            ) VALUES (
                :id_lectivo, :lectivo, :id_ciclo, :ciclo, :ciclo_corto,
                :id_departamento, :departamento, :departamento_corto,
                :id_grado, :grado, :id_turno, :turno,
                :id_curso, :curso, :id_numero, :numero, :nombre_curso,
                :id_indicador, :indicador, :indicador_corto, :total, :conforme)");

                $this->db->bind(':id_lectivo', $promedio['id_lectivo']);
                $this->db->bind(':lectivo', $promedio['lectivo']);
                $this->db->bind(':id_ciclo', $promedio['id_ciclo']);
                $this->db->bind(':ciclo', $promedio['ciclo']);
                $this->db->bind(':ciclo_corto', $promedio['ciclo_corto']);
                $this->db->bind(':id_departamento', $promedio['id_departamento']);
                $this->db->bind(':departamento', $promedio['departamento']);
                $this->db->bind(':departamento_corto', $promedio['departamento_corto']);
                $this->db->bind(':id_grado', $promedio['id_grado']);
                $this->db->bind(':grado', $promedio['grado']);
                $this->db->bind(':id_turno', $promedio['id_turno']);
                $this->db->bind(':turno', $promedio['turno']);
                $this->db->bind(':id_curso', $promedio['id_curso']);
                $this->db->bind(':curso', $promedio['curso']);
                $this->db->bind(':id_numero', $promedio['id_numero']);
                $this->db->bind(':numero', $promedio['numero']);
                $this->db->bind(':nombre_curso', $promedio['nombre_curso']);
                $this->db->bind(':id_indicador', $promedio['id_indicador']);
                $this->db->bind(':indicador', $promedio['indicador']);
                $this->db->bind(':indicador_corto', $promedio['indicador_corto']);
                $this->db->bind(':total', $promedio['total']);
                $this->db->bind(':conforme', $promedio['modulo_conforme']);

            $this->db->execute();
        }
    }

    return true;
}





//***************************************************************/
//************** INSERTA PROMEDIO ANUAL *************************/
//***************************************************************/

// his_anual CURSO ACTUAL
public function ver_promedio_anual($id_lectivo){
    $this->db->query("SELECT * FROM his_anual
    WHERE id_lectivo = :id_lectivo
    ORDER BY id_indicador;");
    $this->db->bind(':id_lectivo', $id_lectivo);
    return $this->db->registros();
}




public function insertar_promedio_anual($resultados, $id_lectivo) {

    if (empty($resultados) || !is_array($resultados)) {
        return false;
    }

    $this->db->query("SELECT * 
    FROM his_total_curso 
    WHERE id_lectivo = :id_lectivo;");
    $this->db->bind(':id_lectivo', $id_lectivo);
    $hay_datos = $this->db->execute();


    if(!empty($hay_datos)){

        $this->db->query("DELETE FROM his_anual
        WHERE id_lectivo=:id_lectivo;");
        $this->db->bind(':id_lectivo',$id_lectivo);  
        $this->db->execute();

        foreach ($resultados as $fila) {
            $this->db->query("INSERT INTO his_anual (id_lectivo, id_indicador, promedio) 
            VALUES (:id_lectivo, :id_indicador, :promedio)");
            $this->db->bind(':id_lectivo', $id_lectivo);
            $this->db->bind(':id_indicador', $fila->id_indicador);
            $this->db->bind(':promedio', $fila->promedio);
            $this->db->execute();
        }

    } else {

        foreach ($resultados as $fila) {
            $this->db->query("INSERT INTO his_anual (id_lectivo, id_indicador, promedio) 
            VALUES (:id_lectivo, :id_indicador, :promedio)");
            $this->db->bind(':id_lectivo', $id_lectivo);
            $this->db->bind(':id_indicador', $fila->id_indicador);
            $this->db->bind(':promedio', $fila->promedio);
            $this->db->execute();
        }

    }

    return true;
}








/**********************************************************************************************/
/**********************************************************************************************/
/*********************************** VISTA POR DEPARTAMENTO ************************************/
/**********************************************************************************************/
/**********************************************************************************************/

public function his_total_modulo_dep($id_lectivo, $id_dep){
    $this->db->query("SELECT * FROM his_total_modulo
    WHERE id_departamento_modulo = :id_dep
    AND id_lectivo = :id_lectivo
    ORDER BY id_indicador;");
    $this->db->bind(':id_lectivo', $id_lectivo);
    $this->db->bind(':id_dep', $id_dep);
    return $this->db->registros();
}




/**********************************************************************************************/
/**********************************************************************************************/
/*********************************** VISTA HISTORICOS  ****************************************/
/**********************************************************************************************/
/**********************************************************************************************/

// ver todos his_anual
public function his_anual(){
    $this->db->query("SELECT * FROM his_anual, seg_lectivos, seg_indicadores
    WHERE seg_lectivos.id_lectivo = his_anual.id_lectivo
    AND his_anual.id_indicador = seg_indicadores.id_indicador
    ORDER BY his_anual.id_indicador;");
    return $this->db->registros();
}

// nombres indicadore
public function indicadores(){
    $this->db->query("SELECT * FROM seg_indicadores;");
    return $this->db->registros();
}



    

}