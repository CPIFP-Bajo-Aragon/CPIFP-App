<?php

class jdRepartoM {
    

    private $db;


    public function __construct(){
        $this->db = new Base;
    }



// curso lectivo
public function obtener_lectivo(){
    $this->db->query("SELECT id_lectivo, lectivo, cerrado, date_format(fecha_inicio,'%d-%m-%Y') AS fecha_inicio, 
    date_format(fecha_fin,'%d-%m-%Y') AS fecha_fin
    FROM seg_lectivos WHERE cerrado=0;");
    return $this->db->registros();
}


// INFO DEL PROFESOR DE UN DEPARTAMENTO DE FORMACION
public function departamentos_formacion($id_profesor){
    $this->db->query("SELECT * FROM cpifp_profesor_departamento, cpifp_departamento
    WHERE cpifp_profesor_departamento.id_departamento=cpifp_departamento.id_departamento
    AND id_profesor=:id 
    AND isFormacion=1;");
    $this->db->bind(':id', $id_profesor);
    return $this->db->registros();
}



//***********************************************************/
//***********************************************************/
//************************ FOL / INGLES **********************/
//***********************************************************/
//***********************************************************/


// PARA FOL E INGLES
public function modulos($id_dep){
    $this->db->query("SELECT * FROM `segui_departamento_modulo`
    WHERE departamento_modulo=:id_dep 
    ORDER BY id_departamento;");
    $this->db->bind(':id_dep',$id_dep);
    return $this->db->registros();
}



// programaciones nuevas (para el badge)
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


// TODOS LOS PROFESORES X DEPARTAMENTO
public function obtener_profes($id_dep){
    $this->db->query("SELECT departamento,cpifp_profesor.id_profesor, nombre_completo 
    FROM cpifp_profesor, cpifp_departamento, cpifp_profesor_departamento
    WHERE cpifp_profesor_departamento.id_profesor=cpifp_profesor.id_profesor 
    AND cpifp_profesor_departamento.id_departamento=cpifp_departamento.id_departamento
    AND cpifp_departamento.id_departamento=:id_dep
    AND activo=1;");
    $this->db->bind(':id_dep',$id_dep);
    return $this->db->registros();
}


// HORAS QUE DA UN PROFESOR EN UN MODULO
public function horas_profes_modulo($id_dep, $id_lectivo){
    $this->db->query("SELECT cpm.* FROM cpifp_profesor_modulo cpm 
    LEFT JOIN cpifp_profesor_departamento cpd 
    ON cpm.id_profesor=cpd.id_profesor 
    WHERE id_departamento=:id AND id_lectivo=:id_lectivo;");
    $this->db->bind(':id',$id_dep);
    $this->db->bind(':id_lectivo',$id_lectivo);  
    return $this->db->registros();
 }



 
//***********************************************************/
//***********************************************************/
//************************ RESTO DEPARTS. ********************/
//***********************************************************/
//***********************************************************/



// programaciones nuevas (para el badge)
 public function nuevas_por_ciclo($id_dep, $id_lectivo){
    $this->db->query("SELECT m.id_departamento,m.departamento,m.id_ciclo,m.ciclo,COUNT(p.id_modulo) AS suma
    FROM segui_departamento_modulo m
    JOIN seg_programaciones p ON m.id_modulo = p.id_modulo
    WHERE p.nueva = 1 
    AND verificada_jefe_dep = 0
    AND m.departamento_modulo=:id_departamento
    AND p.id_lectivo=:id_lectivo
    GROUP BY m.id_departamento, m.departamento, m.id_ciclo;");
    $this->db->bind(':id_departamento',$id_dep);
    $this->db->bind(':id_lectivo',$id_lectivo);  
    return $this->db->registros();
}




// TODOS LOS CICLOS DE UN DEPARTAMENTO (RESTO DEPARTAMENTOS)
public function obtener_ciclos($id_dep){
    $this->db->query("SELECT cpifp_ciclos.id_ciclo, ciclo, ciclo_corto, cpifp_departamento.id_departamento, 
    departamento, cpifp_grados.id_grado, nombre, cpifp_turnos.id_turno, turno
    FROM cpifp_ciclos, cpifp_departamento, cpifp_grados, cpifp_turnos
    WHERE cpifp_departamento.id_departamento=cpifp_ciclos.id_departamento
    AND cpifp_grados.id_grado=cpifp_ciclos.id_grado
    AND cpifp_turnos.id_turno=cpifp_ciclos.id_turno
    AND cpifp_ciclos.id_departamento=:id_departamento;");
    $this->db->bind(':id_departamento',$id_dep);
    return $this->db->registros();
}



// TODOS LOS CURSOS Y CICLOS DE UN DEPARTAMENTO (RESTO DEPARTAMENTOS)
public function obtener_ciclos_cursos($id_dep){
    $this->db->query("SELECT cpifp_ciclos.id_ciclo, ciclo, cpifp_grados.id_grado, nombre, 
    cpifp_curso.id_curso, cpifp_curso.curso, cpifp_curso.id_numero,
    seg_numero.numero, seg_numero.nombre_curso
    FROM cpifp_ciclos,cpifp_grados, cpifp_curso, seg_numero
    WHERE cpifp_grados.id_grado=cpifp_ciclos.id_grado 
    AND cpifp_curso.id_ciclo=cpifp_ciclos.id_ciclo
    AND seg_numero.id_numero=cpifp_curso.id_numero
    AND cpifp_ciclos.id_departamento=:id_departamento;");
    $this->db->bind(':id_departamento',$id_dep);
    return $this->db->registros();
}




/****************************************************************************/



// informacion de un modulo concreto
public function info_modulo($id_modulo){
    $this->db->query("SELECT * FROM segui_departamento_modulo 
    WHERE id_modulo=:id_modulo;");
    $this->db->bind(':id_modulo', $id_modulo);
    return $this->db->registros();
 }
 


 // horario semanal de un modulo concreto
 public function obtener_horario_semana_modulo($id_modulo){
    $this->db->query("SELECT seg_horario_modulo.id_modulo, seg_dias_semana.id_dia_semana, seg_dias_semana.dia_semana, 
    seg_dias_semana.dia_corto, seg_horario_modulo.horas_dia
    FROM seg_dias_semana
    LEFT JOIN seg_horario_modulo ON seg_dias_semana.id_dia_semana = seg_horario_modulo.id_dia_semana AND seg_horario_modulo.id_modulo = :id_modulo
    WHERE seg_dias_semana.dia_corto NOT IN ('S', 'D') AND seg_dias_semana.id_dia_semana IS NOT NULL;");
    $this->db->bind(':id_modulo',$id_modulo);
    return $this->db->registros();
 }



//modulos de un ciclo por departamento
public function modulos_ciclo($id_ciclo, $id_dep){
    $this->db->query("SELECT * FROM segui_departamento_modulo 
    WHERE id_ciclo=:id_ciclo
    AND departamento_modulo=:id_departamento;");
    $this->db->bind(':id_ciclo',$id_ciclo);
    $this->db->bind(':id_departamento',$id_dep);
    return $this->db->registros();
}


// VERIFICA SI HAY FECHAS DE EVALUACIONES PARA ESE GRADO, TURNO Y NUMERO-CURSO
public function hay_fechas_evaluacion($id_grado, $id_turno, $id_numero){
    $this->db->query("SELECT * FROM `seg_evaluaciones` 
    WHERE id_grado=:id_grado 
    AND id_turno=:id_turno 
    AND id_numero=:id_numero;");
    $this->db->bind(':id_grado', $id_grado);
    $this->db->bind(':id_turno', $id_turno);
    $this->db->bind(':id_numero', $id_numero);
    return $this->db->registros();
}




// si hay registros o no
public function registros($id_modulo, $id_lectivo){
    $this->db->query("SELECT cpm.* FROM cpifp_profesor_modulo cpm 
    LEFT JOIN cpifp_profesor_departamento cpd 
    ON cpm.id_profesor=cpd.id_profesor 
    WHERE id_modulo=:id_modulo 
    AND id_lectivo=:id_lectivo;");
    $this->db->bind(':id_modulo',$id_modulo);
    $this->db->bind(':id_lectivo',$id_lectivo);  
    return $this->db->registros();
}





//***********************************************************************************************************/
//********************** REPARTO DE PROFESORES EN MODULO E IDS_SEGUIMIENTO **********************************/
//***********************************************************************************************************/

public function reparto($id_modulo, $array, $id_lectivo){

        // recogemos los datos de ese modulo 
        $this->db->query("SELECT * FROM segui_departamento_modulo 
        WHERE id_modulo=:id_modulo;");
        $this->db->bind(':id_modulo', $id_modulo);
        $info_modulo = $this->db->registros();
        $id_grado = $info_modulo[0]->id_grado;
        $id_turno = $info_modulo[0]->id_turno;
        $id_numero = $info_modulo[0]->id_numero;
    
        // verificamos que hay fechas de evaluaciones para ese turno, grado...
        $this->db->query("SELECT * FROM `seg_evaluaciones` 
        WHERE id_grado=:id_grado 
        AND id_turno=:id_turno 
        AND id_numero=:id_numero;");
        $this->db->bind(':id_grado', $id_grado);
        $this->db->bind(':id_turno', $id_turno);
        $this->db->bind(':id_numero', $id_numero);
        $info_evaluaciones = $this->db->registros();


        // si hay, asignamos id_seguimiento. Insertamos en seg_seguimiento_modulo y damos valor a las preguntas 38,39,40
        if(!empty($info_evaluaciones)){

            // verificamos si hay id_seguimiento ya asignados
            $this->db->query("SELECT * FROM `seg_seguimiento_modulo` 
            WHERE id_modulo=:id_modulo;");
            $this->db->bind(':id_modulo', $id_modulo);
            $this->db->execute();
            $info_seguimiento = $this->db->registros();

            // si no hay ids_seguimiento asignados
            if(empty($info_seguimiento)){

                for ($i=0;$i<sizeof($info_evaluaciones);$i++) { 

                    $this->db->query("INSERT INTO seg_seguimiento_modulo (id_seg_evaluacion, id_modulo)
                    VALUES (:id_seg_evaluacion, :id_modulo)");
                    $this->db->bind(':id_seg_evaluacion', $info_evaluaciones[$i]->id_seg_evaluacion);
                    $this->db->bind(':id_modulo', $id_modulo);
                    $this->db->execute();
                    $ultimo_indice = $this->db->ultimoIndice();

                    for ($preg = 38; $preg <= 40; $preg++) {
                        $this->db->query("INSERT INTO seg_seguimiento_preguntas (id_seguimiento, id_pregunta, respuesta)
                        VALUES (:id_seguimiento, :id_pregunta, 0)");
                        $this->db->bind(':id_seguimiento', $ultimo_indice);
                        $this->db->bind(':id_pregunta', $preg);
                        $this->db->execute();
                    }
                }
            }
        }

    
        // Finalmente insertamos en cpif_profesor_modulo
        for($i=0;$i<sizeof($array);$i++){   
            $this->db->query("INSERT INTO cpifp_profesor_modulo (id_lectivo, id_profesor, id_modulo, horas_profesor, cambia_programacion) 
            VALUES (:id_lectivo,:profe, :modulo, :horas, -1);");
            $this->db->bind(':profe',$array[$i]->profe);
            $this->db->bind(':modulo',$id_modulo);
            $this->db->bind(':horas',$array[$i]->horas);     
            $this->db->bind(':id_lectivo',$id_lectivo);   
            $this->db->execute();
        }

        
    return true;

}


//***********************************************************************************************************/
//********************** BORRA ASIGNACION SI EL ARRAY DEL REPARTO LLEGA VACIO *******************************/
//***********************************************************************************************************/

public function borrar_asignacion($id_modulo,$id_lectivo){
    $this->db->query("DELETE FROM cpifp_profesor_modulo 
    WHERE id_modulo=:id_modulo
    AND id_lectivo=:id_lectivo;");
    $this->db->bind(':id_modulo',$id_modulo);
    $this->db->bind(':id_lectivo',$id_lectivo);   
    if ($this->db->execute()){
        return true;
     }else{
        return false;
     }
}




//***********************************************************************************************************/
//********************** ACTUALIZAR REPARTO DE PROFESORES EN MODULO *****************************************/
//***********************************************************************************************************/

public function actualizar_reparto($modulo, $array, $id_lectivo){

        // borramos todos los datos que pueda haber de ese modulo y año
        $this->db->query("DELETE FROM cpifp_profesor_modulo 
        WHERE id_modulo=:id_modulo AND id_lectivo=:id_lectivo;");
        $this->db->bind(':id_modulo',$modulo);
        $this->db->bind(':id_lectivo',$id_lectivo);  
        $this->db->execute();

        // insertamos de nuevo
        for($i=0;$i<sizeof($array);$i++){   
            $this->db->query("INSERT INTO cpifp_profesor_modulo (id_lectivo,id_profesor, id_modulo, horas_profesor, cambia_programacion) 
            VALUES (:id_lectivo,:profe, :modulo, :horas, -1);");
            $this->db->bind(':profe',$array[$i]->profe);
            $this->db->bind(':modulo',$modulo);
            $this->db->bind(':horas',$array[$i]->horas);    
            $this->db->bind(':id_lectivo',$id_lectivo);   
            $this->db->execute();
        }
        return true;

}







//********************************************/
//********************************************/
//**** INSERTAR HIS_TOTAL_CURSO **************/
//********************************************/
//********************************************/


 public function resumen_modulos($id_dep, $lectivo){
    $this->db->query("SELECT * FROM his_total_modulo
    WHERE id_lectivo=:id_lectivo AND id_departamento_modulo=:id;");
    $this->db->bind(':id_lectivo', $lectivo[0]->id_lectivo);
    $this->db->bind(':id', $id_dep);
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





}