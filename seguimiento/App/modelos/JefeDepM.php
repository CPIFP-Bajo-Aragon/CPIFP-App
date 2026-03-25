<?php

class JefeDepM {
    

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



public function info_modulo($id_modulo){
    $this->db->query("SELECT * FROM segui_departamento_modulo 
    WHERE id_modulo=:id_modulo;");
    $this->db->bind(':id_modulo', $id_modulo);
    return $this->db->registros();
 }
 



 // trae el horario semanal de un modulo concreto
 public function obtener_horario_semana_modulo($id_modulo){
    $this->db->query("SELECT 
    seg_horario_modulo.id_modulo, 
    seg_dias_semana.id_dia_semana, 
    seg_dias_semana.dia_semana, 
    seg_dias_semana.dia_corto, 
    seg_horario_modulo.horas_dia
FROM 
    seg_dias_semana
LEFT JOIN 
    seg_horario_modulo 
    ON seg_dias_semana.id_dia_semana = seg_horario_modulo.id_dia_semana 
    AND seg_horario_modulo.id_modulo = :id_modulo
WHERE 
    seg_dias_semana.dia_corto NOT IN ('S', 'D') 
    AND seg_dias_semana.id_dia_semana IS NOT NULL;
");
    $this->db->bind(':id_modulo',$id_modulo);
    return $this->db->registros();
 }



 // devuelve los temas con examenes y dual (NO FALTAS, ACTIVIDADES y OTROS )
public function temas_del_modulo($id_modulo){
    $this->db->query("SELECT * FROM seg_temas 
    WHERE id_modulo = :id_modulo AND (descripcion = '' OR descripcion = 'Examenes' OR descripcion = 'Dual') 
    ORDER BY tema;");
    $this->db->bind(':id_modulo',$id_modulo);
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
    
    // DEVUELVE TODAS LAS ASIGNATURAS DE UN DEPARTAMENTO (VIENE DE UNA VISTA CREADA)
public function obtener_asignaturas($id_dep){
    $this->db->query("SELECT * FROM segui_departamento_modulo where id_departamento=:id_dep;");
    $this->db->bind(':id_dep',$id_dep);
    return $this->db->registros();
}


  
public function obtener_ciclo_id($id_ciclo){
    $this->db->query("SELECT * FROM cpifp_ciclos where id_ciclo=:id_ciclo;");
    $this->db->bind(':id_ciclo',$id_ciclo);
    return $this->db->registros();
}


// DEVUELVE TODA LA INFO DEL MODULO Y SU ID_S_EVA  Y FECHAS DE CADA EVALUACION - PARA EL SEGUIMIENTO
public function evaluaciones_modulo_curso_ciclo($id_modulo){
    $this->db->query("SELECT cpifp_ciclos.id_ciclo, ciclo, ciclo_corto, cpifp_ciclos.id_departamento, cpifp_ciclos.id_grado, id_turno,
    cpifp_curso.id_curso, curso, numero,
    cpifp_modulo.id_modulo, modulo, nombre_corto, cuerpo, horas_semanales, horas_totales, turno, 
    seg_evaluaciones.id_s_eva, id_evaluacion, fecha
    FROM cpifp_curso, cpifp_ciclos, cpifp_modulo, seg_evaluaciones WHERE
    cpifp_modulo.id_curso=cpifp_curso.id_curso AND
    cpifp_curso.id_ciclo=cpifp_ciclos.id_ciclo AND
    cpifp_ciclos.id_grado=seg_evaluaciones.id_grado AND
    seg_evaluaciones.id_curso=numero AND
    id_modulo=:id_modulo;");
    $this->db->bind(':id_modulo',$id_modulo);
    return $this->db->registros();
}






// *****************************************************************************************
// *****************************************************************************************
// ************************************* REPARTO HORAS *************************************
// *****************************************************************************************
// *****************************************************************************************



// PARA FOL E INGLES
public function modulos($id_dep){
    $this->db->query("SELECT * FROM `segui_departamento_modulo`
    WHERE departamento_modulo=:id_dep;");
    $this->db->bind(':id_dep',$id_dep);
    return $this->db->registros();
}

public function modulos_ciclo($id_ciclo, $id_dep){
    $this->db->query("SELECT * FROM segui_departamento_modulo WHERE id_ciclo=:id_ciclo
    AND departamento_modulo=:id_departamento;");
    $this->db->bind(':id_ciclo',$id_ciclo);
    $this->db->bind(':id_departamento',$id_dep);
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



// INFO DEL PROFESOR DE UN DEPARTAMENTO DE FORMACION
public function departamentos_formacion($id_profesor){
    $this->db->query("SELECT * FROM cpifp_profesor_departamento, cpifp_departamento
    WHERE cpifp_profesor_departamento.id_departamento=cpifp_departamento.id_departamento
    AND id_profesor=:id AND isFormacion=1;");
    $this->db->bind(':id', $id_profesor);
    return $this->db->registros();
}


// DEVUELVE TODAS LAS ASIGNATURAS DE UN CICLO CONCRETO, CURSO Y DEPARTAMENTO (VIENE DE UNA VISTA CREADA)
public function info_curso_ciclo_concreto($id_dep, $id_ciclo, $id_curso){
    $this->db->query("SELECT * FROM segui_departamento_modulo 
    WHERE departamento_modulo=:id_dep AND id_ciclo=:id_ciclo AND id_curso=:id_curso;");
    $this->db->bind(':id_dep',$id_dep);
    $this->db->bind(':id_ciclo',$id_ciclo);
    $this->db->bind(':id_curso',$id_curso);
    return $this->db->registros();
}



// TODOS LOS PROFESORES X DEPARTAMENTO
// public function obtener_profes($id_dep){
//     $this->db->query("SELECT departamento,cpifp_profesor.id_profesor, nombre_completo 
//     FROM cpifp_profesor, cpifp_departamento, cpifp_profesor_departamento
//     WHERE cpifp_profesor_departamento.id_profesor=cpifp_profesor.id_profesor 
//     AND cpifp_profesor_departamento.id_departamento=cpifp_departamento.id_departamento
//     AND cpifp_departamento.id_departamento=:id_dep;");
//     $this->db->bind(':id_dep',$id_dep);
//     return $this->db->registros();
// }


// HORAS QUE DA UN PROFESOR EN UN MODULO
public function horas_profes_modulo($id_dep){
    $this->db->query("SELECT cpm.* FROM cpifp_profesor_modulo cpm 
    LEFT JOIN cpifp_profesor_departamento cpd 
    ON cpm.id_profesor=cpd.id_profesor 
    WHERE id_departamento=:id;");
    $this->db->bind(':id',$id_dep);
    return $this->db->registros();
 }



// TRAE SI HAY REGISTROS. SE USA PARA INSERTAR O ACTUALIZAR
// public function registros($id_dep,$id_modulo){
//     $this->db->query("SELECT cpm.* FROM cpifp_profesor_modulo cpm 
//     LEFT JOIN cpifp_profesor_departamento cpd 
//     ON cpm.id_profesor=cpd.id_profesor 
//     WHERE id_departamento=:id AND id_modulo=:id_modulo;");
//     $this->db->bind(':id',$id_dep);
//     $this->db->bind(':id_modulo',$id_modulo);
//     return $this->db->registros();
//  }
 public function registros($id_modulo){
    $this->db->query("SELECT cpm.* FROM cpifp_profesor_modulo cpm 
    LEFT JOIN cpifp_profesor_departamento cpd 
    ON cpm.id_profesor=cpd.id_profesor 
    WHERE id_modulo=:id_modulo;");
    $this->db->bind(':id_modulo',$id_modulo);
    return $this->db->registros();
 }




/************************ inserta resparto ****************************/

public function reparto($modulo, $array, $id_lectivo){

    for($i=0;$i<sizeof($array);$i++){   
        $this->db->query("INSERT INTO cpifp_profesor_modulo (id_lectivo, id_profesor, id_modulo, horas_profesor) 
        VALUES (:id_lectivo,:profe, :modulo, :horas);");
        $this->db->bind(':profe',$array[$i]->profe);
        $this->db->bind(':modulo',$modulo);
        $this->db->bind(':horas',$array[$i]->horas);     
        $this->db->bind(':id_lectivo',$id_lectivo);   
        $this->db->execute();
    }
    return true;
}



/************************ MODIFICACION ****************************/

public function actualizar_reparto($modulo, $array, $id_lectivo){

    //borramos todos los datos que pueda haber de ese modulo
    $this->db->query("DELETE FROM cpifp_profesor_modulo WHERE id_modulo=:id_modulo;");
    $this->db->bind(':id_modulo',$modulo);
    $this->db->execute();

    for($i=0;$i<sizeof($array);$i++){   
        $this->db->query("INSERT INTO cpifp_profesor_modulo (id_lectivo,id_profesor, id_modulo, horas_profesor) 
        VALUES (:id_lectivo,:profe, :modulo, :horas);");
        $this->db->bind(':profe',$array[$i]->profe);
        $this->db->bind(':modulo',$modulo);
        $this->db->bind(':horas',$array[$i]->horas);    
        $this->db->bind(':id_lectivo',$id_lectivo);   
        $this->db->execute();
    }
    return true;
}




// *****************************************************************************************
// *****************************************************************************************
// ******************************* PROGRAMACIONES ******************************************
// *****************************************************************************************
// *****************************************************************************************




public function nuevas_por_ciclo($id_dep){
    $this->db->query("SELECT 
    m.id_departamento,
    m.departamento,
    m.id_ciclo,
    m.ciclo,
    COUNT(p.id_modulo) AS suma
FROM 
    segui_departamento_modulo m
JOIN 
    seg_programaciones p ON m.id_modulo = p.id_modulo
WHERE 
    p.nueva = 1 
and m.id_departamento=:id_departamento
GROUP BY 
    m.id_departamento, m.departamento, m.id_ciclo;");
$this->db->bind(':id_departamento',$id_dep);
    return $this->db->registros();
}



// solo las activas de cada modulo
public function programaciones_modulos_activas($id_departamento){
    $this->db->query("SELECT * FROM segui_departamento_modulo, seg_programaciones, cpifp_profesor
    WHERE seg_programaciones.id_modulo=segui_departamento_modulo.id_modulo 
    AND seg_programaciones.id_profesor=cpifp_profesor.id_profesor
    AND segui_departamento_modulo.id_departamento=:id_departamento
    AND seg_programaciones.activa=1;");
    $this->db->bind(':id_departamento',$id_departamento);
    return $this->db->registros();
}

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
    $this->db->query("SELECT 
    p1.*, sdm.*, cpf.*         
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



//*************************************** FOL // LEO ***************************************************/




public function nuevas_fol_leo($id_dep){
    $this->db->query("SELECT 
    SUM(CASE WHEN p1.nueva = 1 THEN 1 ELSE 0 END) AS suma
FROM 
    seg_programaciones p1
JOIN 
    segui_departamento_modulo sdm ON p1.id_modulo = sdm.id_modulo
WHERE 
    sdm.departamento_modulo = :id_departamento;");
$this->db->bind(':id_departamento',$id_dep);
    return $this->db->registros();
}





// solo las activas de cada modulo
public function programaciones_modulos_activas_fol_leo($id_departamento){
    $this->db->query("SELECT * FROM segui_departamento_modulo, seg_programaciones, cpifp_profesor
    WHERE seg_programaciones.id_modulo=segui_departamento_modulo.id_modulo 
    AND seg_programaciones.id_profesor=cpifp_profesor.id_profesor
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
    $this->db->query("SELECT 
    p1.*, sdm.*, cpf.*         
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







// SELECT * FROM `seg_programaciones`, cpifp_modulo, cpifp_ciclos ,cpifp_curso
// where seg_programaciones.id_modulo=cpifp_modulo.id_modulo
// and cpifp_modulo.id_curso=cpifp_curso.id_curso
// and  cpifp_curso.id_ciclo=cpifp_ciclos.id_ciclo
// and seg_programaciones.activa=1


// *****************************************************************************************
// *****************************************************************************************
// ******************************* ACTAS DE SEGUIMIENTO ************************************
// *****************************************************************************************
// *****************************************************************************************


    public function obtener_actas($id_dep){
        $this->db->query("SELECT * FROM `segui_departamento_modulo`, seg_totales , seg_indicadores
        WHERE seg_totales.id_modulo=segui_departamento_modulo.id_modulo 
        AND seg_indicadores.id_indicador=seg_totales.id_indicador
        AND id_departamento=:id_dep;");
        $this->db->bind(':id_dep',$id_dep);
        return $this->db->registros();
    }






    public function obtener_actas_evaluacion($id_dep, $id_evaluacion){
        $this->db->query("SELECT * FROM segui_departamento_modulo, seg_totales, seg_indicadores, seg_seguimiento_modulo, seg_evaluaciones, cpifp_evaluaciones
        WHERE segui_departamento_modulo.id_modulo = seg_totales.id_modulo
        AND seg_indicadores.id_indicador=seg_totales.id_indicador
        AND seg_seguimiento_modulo.id_seguimiento=seg_totales.id_seguimiento
        AND seg_evaluaciones.id_seg_evaluacion=seg_seguimiento_modulo.id_seg_evaluacion
        AND cpifp_evaluaciones.id_evaluacion=seg_evaluaciones.id_evaluacion
        AND id_departamento=:id_dep
        AND cpifp_evaluaciones.id_evaluacion=:id_evaluacion;");
        $this->db->bind(':id_dep',$id_dep);
        $this->db->bind(':id_evaluacion',$id_evaluacion);
        return $this->db->registros();
    }



    // ACTAS DE TODOS LOS CICLOS X EVALUACION - buena
public function info_actas_evaluacion($id_evaluacion, $id_dep){
    $this->db->query("SELECT * FROM seg_totales, cpifp_profesor_modulo, cpifp_profesor , cpifp_modulo, cpifp_departamento, 
    seg_indicadores, seg_seguimiento_modulo, cpifp_evaluaciones,seg_evaluaciones, cpifp_ciclos, cpifp_curso, cpifp_grados, seg_numero
    where seg_totales.id_modulo=cpifp_profesor_modulo.id_modulo and cpifp_profesor.id_profesor=cpifp_profesor_modulo.id_profesor 
    and cpifp_profesor_modulo.id_modulo=cpifp_modulo.id_modulo and cpifp_departamento.id_departamento=cpifp_modulo.id_departamento 
    and seg_indicadores.id_indicador=seg_totales.id_indicador and seg_seguimiento_modulo.id_seguimiento=seg_totales.id_seguimiento 
    and seg_seguimiento_modulo.id_seg_evaluacion=seg_evaluaciones.id_seg_evaluacion and seg_evaluaciones.id_evaluacion=cpifp_evaluaciones.id_evaluacion
    and cpifp_ciclos.id_ciclo=cpifp_curso.id_ciclo
    and cpifp_ciclos.id_grado=cpifp_grados.id_grado
    and cpifp_curso.id_numero=seg_numero.id_numero
    and cpifp_curso.id_curso=cpifp_modulo.id_curso
    and cpifp_evaluaciones.id_evaluacion=:id_evaluacion
    and cpifp_departamento.id_departamento=:id_dep;");
    $this->db->bind(':id_evaluacion',$id_evaluacion);
    $this->db->bind(':id_dep',$id_dep);
    return $this->db->registros();
}




// ACTAS DE TODOS LOS CICLOS - buena
public function info_actas($id_dep){
    $this->db->query("SELECT * FROM seg_totales, cpifp_profesor_modulo, cpifp_profesor , cpifp_modulo, cpifp_departamento, 
    seg_indicadores, seg_seguimiento_modulo, cpifp_evaluaciones,seg_evaluaciones, cpifp_ciclos, cpifp_curso, cpifp_grados, seg_numero
    where seg_totales.id_modulo=cpifp_profesor_modulo.id_modulo and cpifp_profesor.id_profesor=cpifp_profesor_modulo.id_profesor 
    and cpifp_profesor_modulo.id_modulo=cpifp_modulo.id_modulo and cpifp_departamento.id_departamento=cpifp_modulo.id_departamento 
    and seg_indicadores.id_indicador=seg_totales.id_indicador and seg_seguimiento_modulo.id_seguimiento=seg_totales.id_seguimiento 
    and seg_seguimiento_modulo.id_seg_evaluacion=seg_evaluaciones.id_seg_evaluacion and seg_evaluaciones.id_evaluacion=cpifp_evaluaciones.id_evaluacion
    and cpifp_ciclos.id_ciclo=cpifp_curso.id_ciclo
    and cpifp_ciclos.id_grado=cpifp_grados.id_grado
    and cpifp_curso.id_numero=seg_numero.id_numero
    and cpifp_curso.id_curso=cpifp_modulo.id_curso
    and cpifp_departamento.id_departamento=:id_dep;");
    $this->db->bind(':id_dep',$id_dep);
    return $this->db->registros();
}






// ACTAS DE UN CICLO COCNCRETO
public function actas_ciclo($ciclo){
    $this->db->query("SELECT * FROM seg_totales, cpifp_profesor_modulo, cpifp_profesor , cpifp_modulo, cpifp_departamento, 
    seg_indicadores, seg_seguimiento_modulo, cpifp_evaluaciones,seg_evaluaciones, cpifp_ciclos, cpifp_curso
    where seg_totales.id_modulo=cpifp_profesor_modulo.id_modulo and cpifp_profesor.id_profesor=cpifp_profesor_modulo.id_profesor 
    and cpifp_profesor_modulo.id_modulo=cpifp_modulo.id_modulo and cpifp_departamento.id_departamento=cpifp_modulo.id_departamento 
    and seg_indicadores.id_indicador=seg_totales.id_indicador and seg_seguimiento_modulo.id_seguimiento=seg_totales.id_seguimiento 
    and seg_seguimiento_modulo.id_seg_evaluacion=seg_evaluaciones.id_seg_evaluacion and seg_evaluaciones.id_evaluacion=cpifp_evaluaciones.id_evaluacion
    and cpifp_ciclos.id_ciclo=cpifp_curso.id_ciclo
    and cpifp_curso.id_curso=cpifp_modulo.id_curso 
    AND cpifp_ciclos.ciclo=:ciclo;");
    $this->db->bind(':ciclo',$ciclo);
    return $this->db->registros();
}



public function preguntas_ep1(){
    $this->db->query("SELECT * FROM `seg_preguntas`, seg_indicadores
    where seg_preguntas.id_indicador=seg_indicadores.id_indicador
    and seg_indicadores.id_indicador=6;");
    return $this->db->registros();
}


// public function info_actas_ep1($id_pregunta){
//     $this->db->query("SELECT * FROM seg_seguimiento_preguntas, seg_seguimiento_modulo, cpifp_modulo, cpifp_profesor_modulo, cpifp_profesor , seg_indicadores, seg_preguntas
// , cpifp_departamento, cpifp_ciclos, cpifp_curso
//   where seg_seguimiento_preguntas.id_seguimiento=seg_seguimiento_modulo.id_seguimiento
//   and seg_seguimiento_modulo.id_modulo=cpifp_profesor_modulo.id_modulo
// and cpifp_profesor_modulo.id_modulo=cpifp_modulo.id_modulo
// and cpifp_profesor.id_profesor=cpifp_profesor_modulo.id_profesor 
// and seg_indicadores.id_indicador=seg_preguntas.id_indicador
// and seg_preguntas.id_pregunta=seg_seguimiento_preguntas.id_pregunta
// and cpifp_departamento.id_departamento=cpifp_modulo.id_departamento 
// and cpifp_ciclos.id_ciclo=cpifp_curso.id_ciclo
// and cpifp_curso.id_curso=cpifp_modulo.id_curso
// and seg_seguimiento_preguntas.id_pregunta=:id_pregunta;");
//     $this->db->bind(':id_pregunta',$id_pregunta);
//     return $this->db->registros();
// }

public function info_actas_ep1($id_dep){
    $this->db->query("SELECT * FROM seg_seguimiento_preguntas, seg_seguimiento_modulo, cpifp_modulo, cpifp_profesor_modulo, cpifp_profesor ,
     seg_indicadores, seg_preguntas, cpifp_departamento, cpifp_ciclos, cpifp_curso, cpifp_grados, seg_numero
  where seg_seguimiento_preguntas.id_seguimiento=seg_seguimiento_modulo.id_seguimiento
  and seg_seguimiento_modulo.id_modulo=cpifp_profesor_modulo.id_modulo
and cpifp_profesor_modulo.id_modulo=cpifp_modulo.id_modulo
and cpifp_profesor.id_profesor=cpifp_profesor_modulo.id_profesor 
and seg_indicadores.id_indicador=seg_preguntas.id_indicador
and seg_preguntas.id_pregunta=seg_seguimiento_preguntas.id_pregunta
and cpifp_departamento.id_departamento=cpifp_modulo.id_departamento 
and cpifp_ciclos.id_ciclo=cpifp_curso.id_ciclo
and cpifp_ciclos.id_grado=cpifp_grados.id_grado
and cpifp_curso.id_numero=seg_numero.id_numero
and cpifp_curso.id_curso=cpifp_modulo.id_curso
and seg_indicadores.indicador_corto='EP1'
and cpifp_departamento.id_departamento=:id_dep;");
$this->db->bind(':id_dep',$id_dep);
return $this->db->registros();
}









    

}