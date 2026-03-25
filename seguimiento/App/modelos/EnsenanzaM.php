<?php

class EnsenanzaM{
    private $db;


    public function __construct(){
        $this->db = new Base;
    }


// TRAE EL CURSO LECTIVO
public function obtener_lectivo(){
   $this->db->query("SELECT id_lectivo, lectivo, cerrado, 
   date_format(fecha_inicio,'%d-%m-%Y') AS fecha_inicio, date_format(fecha_fin,'%d-%m-%Y') AS fecha_fin
   FROM seg_lectivos WHERE cerrado=0;");
   return $this->db->registros();
}

//**************************/
// INFO MODULO
//*************************/


// TRAE TODA LA INFO DE UN MODULO CONCRETO - CURSO ACTUAL
public function info_modulo($id_profe, $id_modulo, $id_lectivo) {
   $this->db->query("SELECT 
   cpifp_profesor.id_profesor, cpifp_profesor.nombre_completo, 
   cpifp_modulo.id_modulo, cpifp_modulo.modulo, cpifp_modulo.nombre_corto, cpifp_modulo.id_curso, cpifp_modulo.codigo_programacion,
   cpifp_modulo.horas_totales, cpifp_modulo.cuerpo, cpifp_modulo.horas_semanales, cpifp_modulo.id_departamento AS departamento_modulo,
   dep_mod.departamento AS nombre_departamento_modulo,
   cpifp_profesor_modulo.horas_profesor, cpifp_profesor_modulo.id_lectivo, cpifp_profesor_modulo.cambia_programacion,
   cpifp_curso.curso, cpifp_curso.id_ciclo, cpifp_curso.id_numero,
   cpifp_ciclos.ciclo, cpifp_ciclos.ciclo_corto, cpifp_ciclos.id_departamento, cpifp_ciclos.id_grado, cpifp_ciclos.id_turno,
   dep_ciclo.departamento AS nombre_departamento_ciclo, dep_ciclo.departamento_corto,
   cpifp_grados.nombre AS grado, 
   cpifp_turnos.turno,
   seg_numero.numero, seg_numero.nombre_curso
   FROM cpifp_profesor_modulo 
   JOIN cpifp_modulo ON cpifp_modulo.id_modulo = cpifp_profesor_modulo.id_modulo
   JOIN cpifp_profesor ON cpifp_profesor.id_profesor = cpifp_profesor_modulo.id_profesor
   JOIN cpifp_curso ON cpifp_curso.id_curso = cpifp_modulo.id_curso
   JOIN seg_numero ON cpifp_curso.id_numero = seg_numero.id_numero
   JOIN cpifp_ciclos ON cpifp_curso.id_ciclo = cpifp_ciclos.id_ciclo
   JOIN cpifp_grados ON cpifp_ciclos.id_grado = cpifp_grados.id_grado
   JOIN cpifp_turnos ON cpifp_ciclos.id_turno = cpifp_turnos.id_turno
   LEFT JOIN cpifp_departamento AS dep_mod ON dep_mod.id_departamento = cpifp_modulo.id_departamento
   LEFT JOIN cpifp_departamento AS dep_ciclo ON dep_ciclo.id_departamento = cpifp_ciclos.id_departamento
   WHERE cpifp_profesor_modulo.id_profesor = :id_profe 
   AND cpifp_profesor_modulo.id_lectivo=:id_lectivo
   AND cpifp_modulo.id_modulo = :id_modulo;");
   $this->db->bind(':id_profe', $id_profe);
   $this->db->bind(':id_modulo', $id_modulo);
   $this->db->bind(':id_lectivo', $id_lectivo);
   return $this->db->registros();
}











//**************************/
// TRAE LOS IDS_SEGUIMIENTO
//**************************/

public function obtener_id_seguimientos_evaluacion($id_modulo){
   $this->db->query("SELECT id_seguimiento, id_modulo, cpifp_evaluaciones.id_evaluacion, evaluacion, 
   seg_evaluaciones.id_calendario, date_format(fecha,'%d-%m-%Y') as fecha, 
   seg_seguimiento_modulo.id_seg_evaluacion, id_grado, id_numero, seg_seguimiento_modulo.id_seg_evaluacion
   FROM seg_seguimiento_modulo, seg_evaluaciones, cpifp_evaluaciones, seg_calendario
   WHERE seg_seguimiento_modulo.id_seg_evaluacion=seg_evaluaciones.id_seg_evaluacion 
   AND cpifp_evaluaciones.id_evaluacion=seg_evaluaciones.id_evaluacion 
   AND seg_calendario.id_calendario=seg_evaluaciones.id_calendario
   AND id_modulo=:id_modulo;");
   $this->db->bind(':id_modulo',$id_modulo);
   return $this->db->registros();
}




//****************************************************************************************************************/
//****************************************************************************************************************/
//*****************************************  INDICADOR HI ********************************************************/
//***************************************************************************************************************/
//**************************************************************************************************************/


// PREGUNTA 38
public function pregunta_38 ($id_modulo, $evaluaciones){

   // trae calendario del modulo entero con sus dias de clase, horas, evaluaciones.....todo!
   $this->db->query("SELECT cm.id_modulo, c.id_calendario, c.id_lectivo, DATE_FORMAT(c.fecha, '%d-%m-%Y') AS fecha, c.dia_semana, 
   CASE 
      WHEN f.id_calendario IS NOT NULL OR (c.dia_semana = 'D' OR c.dia_semana = 'S') THEN 'Sí' 
      ELSE 'No' 
   END AS es_festivo,
   f.id_festivo,
   CASE 
      WHEN ds.dia_corto = c.dia_semana AND hm.horas_dia > 0 THEN 'Sí' 
      ELSE 'No' 
   END AS tiene_clase,
   hm.horas_dia, cev.evaluacion, ev.id_evaluacion, ev.id_seg_evaluacion, ev.id_grado, ev.id_numero
   FROM seg_calendario c
   LEFT JOIN seg_festivos f ON c.id_calendario = f.id_calendario
   LEFT JOIN seg_dias_semana ds ON c.dia_semana = ds.dia_corto
   LEFT JOIN seg_horario_modulo hm ON ds.id_dia_semana = hm.id_dia_semana
   LEFT JOIN seg_festivos fe ON c.id_calendario = fe.id_calendario
   INNER JOIN cpifp_modulo cm ON cm.id_modulo = hm.id_modulo
   INNER JOIN cpifp_curso cp ON cp.id_curso = cm.id_curso
   INNER JOIN cpifp_ciclos ci ON ci.id_ciclo = cp.id_ciclo
   INNER JOIN cpifp_grados cg ON cg.id_grado = ci.id_grado
   LEFT JOIN seg_evaluaciones ev ON c.id_calendario = ev.id_calendario 
      AND ev.id_grado = cg.id_grado 
      AND ev.id_numero = cp.id_numero
      AND ev.id_turno = ci.id_turno  
   LEFT JOIN cpifp_evaluaciones cev ON cev.id_evaluacion = ev.id_evaluacion
   WHERE cm.id_modulo = :id_modulo ORDER BY c.fecha;");
   $this->db->bind(':id_modulo',$id_modulo);
   $calendarios = $this->db->registros();

   $horas_acumuladas = 0;  // Variable para acumular las horas
   $resultado_evaluaciones = [];  // Array para almacenar los objetos con evaluación y horas

   foreach ($calendarios as $dia) {
      // Si encontramos una evaluación
      if ($dia->evaluacion) {
         // Crear un objeto con la evaluación y las horas acumuladas
         $evaluacion_obj = new stdClass();
         $evaluacion_obj->id_modulo = $dia->id_modulo;
         $evaluacion_obj->evaluacion = $dia->evaluacion;
         $evaluacion_obj->id_seg_evaluacion = $dia->id_seg_evaluacion;
         $evaluacion_obj->horas = $horas_acumuladas;
         
         $resultado_evaluaciones[] = $evaluacion_obj;
         
         // Reiniciamos la acumulación de horas para la siguiente evaluación
         $horas_acumuladas = 0;
      }

      // Si es un día con clase (y tiene horas)
      if ($dia->horas_dia > 0 && $dia->es_festivo == 'No') {
         $horas_acumuladas += $dia->horas_dia;  
      }
   }

   foreach($evaluaciones as $evas):
      foreach($resultado_evaluaciones as $resul):
         if($evas['evaluacion']==$resul->evaluacion):
            $this->db->query("UPDATE seg_seguimiento_preguntas 
            SET respuesta=:respuesta
            WHERE id_seguimiento=:id_seguimiento AND id_pregunta=38;");
            $this->db->bind(':id_seguimiento',$evas['id_seguimiento']);
            $this->db->bind(':respuesta',$resul->horas);
            $this->db->execute();
         endif; 
      endforeach; 
   endforeach;

}




// ACTUALIZA LAS PREGUNTAS 39 Y 40 QUE TENIAN VALOR 0
public function faltas_otros_x_evaluacion($evaluaciones, $id_modulo){

      foreach($evaluaciones as $evas){

         $id_seguimiento=$evas['id_seguimiento'];
         $fecha_inicio=$evas['fecha_inicio'];
         $fecha_fin=$evas['fecha_fin'];

         $this->db->query("SELECT *,
         SUM(CASE WHEN descripcion = 'Faltas' THEN horas_dia ELSE 0 END) AS horas_faltas,
         SUM(CASE WHEN descripcion = 'Otros' THEN horas_dia ELSE 0 END) AS horas_otros
         FROM seg_seguimiento_temas, seg_temas 
         WHERE seg_seguimiento_temas.id_tema=seg_temas.id_tema and seg_seguimiento_temas.id_modulo=:id_modulo
         AND fecha>=:fecha_inicio AND fecha<=:fecha_fin;");
         $this->db->bind(':id_modulo',$id_modulo);
         $this->db->bind(':fecha_inicio',$fecha_inicio);
         $this->db->bind(':fecha_fin',$fecha_fin);
         $horas_evaluaciones = $this->db->registros();


         if($horas_evaluaciones[0]->horas_faltas!=null){
            $this->db->query("UPDATE seg_seguimiento_preguntas 
            SET respuesta=:respuesta 
            WHERE id_seguimiento=:id_seguimiento 
            AND id_pregunta=39;");
            $this->db->bind(":id_seguimiento",$id_seguimiento);
            $this->db->bind(":respuesta", $horas_evaluaciones[0]->horas_faltas);
            $this->db->execute();
         }else{
            $this->db->query("UPDATE seg_seguimiento_preguntas
            SET respuesta=0
            WHERE id_seguimiento=:id_seguimiento 
            AND id_pregunta=39;");
            $this->db->bind(":id_seguimiento",$id_seguimiento);
            $this->db->execute();
         }


         if($horas_evaluaciones[0]->horas_otros!=null){
            $this->db->query("UPDATE seg_seguimiento_preguntas 
            SET respuesta=:respuesta 
            WHERE id_seguimiento=:id_seguimiento 
            AND id_pregunta=40;");
            $this->db->bind(":id_seguimiento",$id_seguimiento);
            $this->db->bind(":respuesta", $horas_evaluaciones[0]->horas_otros);
            $this->db->execute();
         }else{
            $this->db->query("UPDATE seg_seguimiento_preguntas 
            SET respuesta=0
            WHERE id_seguimiento=:id_seguimiento 
            AND id_pregunta=40;");
            $this->db->bind(":id_seguimiento",$id_seguimiento);
            $this->db->execute();
         }

      
      };

   return true;

}



// RETORNA LAS RESPUESTAS DEL HI POR ID_SEGUIMIENTO
public function respuestas_hi($evaluaciones) {

   $todas_las_respuestas = [];

    foreach ($evaluaciones as $evas) {
      $id_seguimiento = $evas['id_seguimiento'];

      $this->db->query("SELECT * FROM seg_seguimiento_preguntas 
      WHERE id_seguimiento = :id_seguimiento 
      AND id_pregunta IN (38, 39, 40);");
      $this->db->bind(":id_seguimiento", $id_seguimiento);

      $respuestas = $this->db->registros();
      $todas_las_respuestas = array_merge($todas_las_respuestas, $respuestas);

    }

    return $todas_las_respuestas;
}




public function indice_hi(){
   $this->db->query("SELECT * FROM seg_indicadores 
   WHERE indicador_corto='HI';");
   return $this->db->registros();
}




// INSERTA EL INDICE HI EN SEG_TOTALES // HIS_TOTAL_MODULO
public function insertar_total_hi($hi, $total_hi, $id_modulo, $total_curso, $lectivo, $info_modulo){

      $id_hi = $hi[0]->id_indicador;
      $id_grado = $info_modulo[0]->id_grado;

      $this->db->query("SELECT `seg_indicadores`.`id_indicador`,`indicador`,`indicador_corto`, `cpifp_grados`.`id_grado`, 
      `cpifp_grados`.`nombre` AS `nombre_grado`, `porcentaje`, id_lectivo
      FROM `seg_indicadores`, `cpifp_grados`, `seg_indicadores_grados`
      WHERE `seg_indicadores`.`id_indicador`=`seg_indicadores_grados`.`id_indicador` 
      AND `cpifp_grados`.`id_grado`=`seg_indicadores_grados`.`id_grado`
      AND seg_indicadores_grados.id_grado=:id_grado
      AND seg_indicadores.id_indicador=:id_indicador
      AND seg_indicadores_grados.id_lectivo=:id_lectivo;");
      $this->db->bind(':id_grado',$id_grado);
      $this->db->bind(':id_indicador',$id_hi);
      $this->db->bind(':id_lectivo', $lectivo[0]->id_lectivo);
      $info_indicador = $this->db->registros();


      $id_seguimientos_unicos = [];
      foreach ($total_hi as $r) {
         if (!in_array($r['id_seguimiento'], $id_seguimientos_unicos)) {
            $id_seguimientos_unicos[] = $r['id_seguimiento'];
         }
      }


      foreach ($id_seguimientos_unicos as $id_seguimiento) {
         $this->db->query("DELETE FROM seg_totales
         WHERE id_seguimiento = :id_seguimiento 
         AND id_indicador = :id_indicador;");
         $this->db->bind(':id_seguimiento', $id_seguimiento);
         $this->db->bind(':id_indicador', $id_hi);
         $this->db->execute();
      }


      for($i=0;$i<sizeof($total_hi);$i++){
         $this->db->query("INSERT INTO seg_totales (id_seguimiento, id_modulo, id_indicador, total) 
         VALUES (:id_seguimiento,:id_modulo,:id_indicador,:total);");
         $this->db->bind(":id_seguimiento",$total_hi[$i]['id_seguimiento']);
         $this->db->bind(":id_modulo",$total_hi[$i]['id_modulo']);
         $this->db->bind(":total",$total_hi[$i]['total']);
         $this->db->bind(":id_indicador",$id_hi);
         $this->db->execute();
      };


      // borramos e insertamos en his_total_modulo
      $this->db->query("DELETE FROM his_total_modulo
      WHERE id_modulo = :id_modulo 
      AND id_indicador = :id_indicador
      AND id_lectivo=:id_lectivo;");
      $this->db->bind(":id_modulo",$id_modulo);
      $this->db->bind(':id_indicador', $id_hi);
      $this->db->bind(':id_lectivo', $lectivo[0]->id_lectivo);
      $this->db->execute();


      $this->db->query("INSERT INTO his_total_modulo (id_lectivo, lectivo,id_ciclo, ciclo, ciclo_corto,
      id_departamento, departamento, departamento_corto,id_grado, grado,id_turno, turno,
      id_modulo, modulo, nombre_corto,id_departamento_modulo, departamento_modulo,id_curso, curso,
      id_numero, numero, nombre_curso,id_profesor, profesor,id_indicador, indicador, indicador_corto,total, modulo_conforme) 
      VALUES (:id_lectivo, :lectivo,:id_ciclo, :ciclo, :ciclo_corto,:id_departamento, :departamento, :departamento_corto,
      :id_grado, :grado,:id_turno, :turno,:id_modulo, :modulo, :nombre_corto,:id_departamento_modulo, :departamento_modulo,
      :id_curso, :curso,:id_numero, :numero, :nombre_curso,:id_profesor, :profesor,:id_indicador, :indicador, :indicador_corto,:total, :modulo_conforme)");

      $this->db->bind(':id_lectivo', $lectivo[0]->id_lectivo);
      $this->db->bind(':lectivo', $lectivo[0]->lectivo);

      $this->db->bind(':id_ciclo', $info_modulo[0]->id_ciclo);
      $this->db->bind(':ciclo', $info_modulo[0]->ciclo);
      $this->db->bind(':ciclo_corto', $info_modulo[0]->ciclo_corto);

      $this->db->bind(':id_departamento', $info_modulo[0]->id_departamento);
      $this->db->bind(':departamento', $info_modulo[0]->nombre_departamento_ciclo);
      $this->db->bind(':departamento_corto', $info_modulo[0]->departamento_corto);

      $this->db->bind(':id_grado', $info_modulo[0]->id_grado);
      $this->db->bind(':grado', $info_modulo[0]->grado);

      $this->db->bind(':id_turno', $info_modulo[0]->id_turno);
      $this->db->bind(':turno', $info_modulo[0]->turno);

      $this->db->bind(':id_modulo', $info_modulo[0]->id_modulo);
      $this->db->bind(':modulo', $info_modulo[0]->modulo);
      $this->db->bind(':nombre_corto', $info_modulo[0]->nombre_corto);

      $this->db->bind(':id_departamento_modulo', $info_modulo[0]->departamento_modulo);
      $this->db->bind(':departamento_modulo',$info_modulo[0]->nombre_departamento_modulo);

      $this->db->bind(':id_curso', $info_modulo[0]->id_curso);
      $this->db->bind(':curso', $info_modulo[0]->curso);

      $this->db->bind(':id_numero', $info_modulo[0]->id_numero);
      $this->db->bind(':numero', $info_modulo[0]->numero);
      $this->db->bind(':nombre_curso', $info_modulo[0]->nombre_curso);

      $this->db->bind(':id_profesor', $info_modulo[0]->id_profesor);
      $this->db->bind(':profesor', $info_modulo[0]->nombre_completo);

      $this->db->bind(':id_indicador', $info_indicador[0]->id_indicador);
      $this->db->bind(':indicador', $info_indicador[0]->indicador);
      $this->db->bind(':indicador_corto', $info_indicador[0]->indicador_corto);

      $this->db->bind(':total', $total_curso);

      if($total_curso >= $info_indicador[0]->porcentaje){
         $modulo_conforme = 1;
         $this->db->bind(':modulo_conforme', $modulo_conforme);
      }else{
         $modulo_conforme = 0;
         $this->db->bind(':modulo_conforme', $modulo_conforme);
      }


      if ($this->db->execute()){
         return true;
      }else{
         return false;
      }


}




//****************************************************************************************************************/
//****************************************************************************************************************/
//*********************** PREGUNTA 36 - HORAS DE DOCENCIA PREVISTAS X ALUS EFECTIVOS ****************************/
//*****************************************   (INDICADOR AA) **************************************************/
//*************************************************************************************************************/
//**************************************************************************************************************/


// TRAE LAS RESPUESTAS DE AA Y DE HI UNICAMENTE
public function respuestas_aa_hi($modulo){
   $this->db->query("SELECT seg_seguimiento_preguntas.id_seguimiento, seg_seguimiento_modulo.id_seg_evaluacion, cpifp_evaluaciones.id_evaluacion, evaluacion, 
   id_modulo, seg_preguntas.id_pregunta, seg_indicadores.id_indicador, indicador, indicador_corto, pregunta, respuesta
   FROM seg_seguimiento_preguntas
   INNER JOIN seg_seguimiento_modulo ON seg_seguimiento_preguntas.id_seguimiento = seg_seguimiento_modulo.id_seguimiento
   INNER JOIN seg_preguntas ON seg_preguntas.id_pregunta = seg_seguimiento_preguntas.id_pregunta
   INNER JOIN seg_evaluaciones ON seg_seguimiento_modulo.id_seg_evaluacion = seg_evaluaciones.id_seg_evaluacion
   INNER JOIN cpifp_evaluaciones ON cpifp_evaluaciones.id_evaluacion = seg_evaluaciones.id_evaluacion
   INNER JOIN seg_indicadores ON seg_preguntas.id_indicador = seg_indicadores.id_indicador
   WHERE id_modulo = :modulo AND (seg_indicadores.indicador_corto = 'AA' OR seg_indicadores.indicador_corto = 'HI');");
   $this->db->bind(":modulo",$modulo);
   return $this->db->registros();
}



// acrualiza la pregunta 36
public function actualizar_aa_36($datos){
   foreach($datos as $dat):
      if($dat->id_pregunta==36):
         $this->db->query("UPDATE seg_seguimiento_preguntas 
         SET respuesta=:respuesta 
         WHERE id_pregunta=:pregunta 
         AND id_seguimiento=:id_seguimiento;");
         $this->db->bind(":pregunta",$dat->id_pregunta);
         $this->db->bind(":respuesta",$dat->respuesta);
         $this->db->bind(":id_seguimiento",$dat->id_seguimiento);
         $this->db->execute();
      endif;
   endforeach;
   return true;
}


// TRAE TODAS LAS RESPUESTAS DEL PROCESO DE UN INDICADOR Y MODULO CONCRETO
public function obtener_respuestas_indicador($modulo, $indicador_corto) {
   $this->db->query("SELECT seg_seguimiento_preguntas.id_seguimiento, seg_seguimiento_modulo.id_seg_evaluacion, cpifp_evaluaciones.id_evaluacion, evaluacion, 
   id_modulo, seg_preguntas.id_pregunta, seg_indicadores.id_indicador, indicador, indicador_corto, pregunta, respuesta
   FROM seg_seguimiento_preguntas
   JOIN seg_seguimiento_modulo ON seg_seguimiento_preguntas.id_seguimiento = seg_seguimiento_modulo.id_seguimiento
   JOIN seg_preguntas ON seg_preguntas.id_pregunta = seg_seguimiento_preguntas.id_pregunta
   JOIN seg_evaluaciones ON seg_seguimiento_modulo.id_seg_evaluacion = seg_evaluaciones.id_seg_evaluacion
   JOIN cpifp_evaluaciones ON cpifp_evaluaciones.id_evaluacion = seg_evaluaciones.id_evaluacion
   JOIN seg_indicadores ON seg_preguntas.id_indicador = seg_indicadores.id_indicador
   WHERE id_modulo = :modulo 
   AND indicador_corto = :indicador_corto;");
   $this->db->bind(":modulo", $modulo);
   $this->db->bind(":indicador_corto", $indicador_corto);
   return $this->db->registros();
}


// ACTUALIZA EN TOTAL RESULTADOS DEL INDICADOR AA
public function actualizar_total_resultados_aa($totales, $id_modulo, $total_curso, $lectivo){

      for($i=0;$i<sizeof($totales);$i++){
         $this->db->query("UPDATE seg_totales SET total=:total 
         WHERE id_seguimiento=:id_seguimiento 
         AND id_modulo=:id_modulo 
         AND id_indicador=1;");
         $this->db->bind(":id_seguimiento",$totales[$i]->id_seguimiento);
         $this->db->bind(":id_modulo",$id_modulo);
         $this->db->bind(":total",$totales[$i]->resultado);
         $this->db->execute();
      };

      $this->db->query("UPDATE his_total_modulo SET total=:total
      WHERE id_modulo=:id_modulo 
      AND id_indicador=1 
      AND id_lectivo=:id_lectivo;");
      $this->db->bind(":id_modulo",$id_modulo);
      $this->db->bind(":total",$total_curso);
      $this->db->bind(":id_lectivo",$lectivo[0]->id_lectivo);
      if ($this->db->execute()){
         return true;
      }else{
         return false;
      }
}



//*******************************/
// INFO DE UN INDICADOR CONCRETO
//*******************************/

public function indicador_preguntas($indicador_corto){
   $this->db->query("SELECT * FROM seg_preguntas
   JOIN seg_indicadores ON seg_indicadores.id_indicador = seg_preguntas.id_indicador
   WHERE seg_indicadores.indicador_corto = :indicador_corto;");
   $this->db->bind(":indicador_corto",$indicador_corto);
   return $this->db->registros();
}



// TRAE EL ID_SEGUIMIENTO MAYOR PARA EL INDICADOR AP2
public function segui_ap2($id_modulo){
   $this->db->query("SELECT MAX(id_seguimiento) as ap2 
   FROM `seg_seguimiento_modulo` WHERE id_modulo=:id_modulo;");
   $this->db->bind(":id_modulo",$id_modulo);
   return $this->db->registros();
}


// trae el total de casa evaluacion (tabla seg_totales)
public function seg_totales($id_modulo){
   $this->db->query("SELECT * FROM seg_totales 
   WHERE id_modulo=:id_modulo;");
   $this->db->bind(":id_modulo",$id_modulo);
   return $this->db->registros();
}


// trae el total del modulo para cada indicador (tabla historica modulo)
public function obtener_total_curso($id_modulo, $lectivo){
   $this->db->query("SELECT * FROM his_total_modulo 
   WHERE id_modulo=:id_modulo 
   AND id_lectivo=:id_lectivo;");
   $this->db->bind(":id_modulo",$id_modulo);
   $this->db->bind(":id_lectivo",$lectivo[0]->id_lectivo);
   return $this->db->registros();
}



//******************************************************************/
// PARA LIMITAR EL ACCESO SI NO HAY HORAS NI TEMAS NI SEGIMIENTOS
//*****************************************************************/

public function obtener_modulos($id_profe, $id_lectivo) {
   $this->db->query("SELECT cpifp_profesor.id_profesor, cpifp_profesor.nombre_completo,
   cpifp_modulo.id_modulo,cpifp_modulo.modulo,cpifp_modulo.nombre_corto, cpifp_modulo.horas_totales, cpifp_modulo.cuerpo, 
   cpifp_modulo.horas_semanales, cpifp_modulo.id_departamento AS departamento_modulo,
   cpifp_profesor_modulo.horas_profesor, cpifp_profesor_modulo.id_lectivo,
   cpifp_modulo.id_curso, 
   cpifp_curso.curso, cpifp_curso.id_ciclo, 
   cpifp_ciclos.ciclo, cpifp_ciclos.ciclo_corto,cpifp_ciclos.id_departamento, cpifp_ciclos.id_grado, cpifp_ciclos.id_turno,
   cpifp_grados.nombre AS grado, cpifp_turnos.turno,
   cpifp_curso.id_numero, 
   seg_numero.numero, seg_numero.nombre_curso
   FROM cpifp_profesor_modulo 
   JOIN cpifp_modulo ON cpifp_modulo.id_modulo = cpifp_profesor_modulo.id_modulo
   JOIN cpifp_profesor ON cpifp_profesor.id_profesor = cpifp_profesor_modulo.id_profesor
   JOIN cpifp_curso ON cpifp_curso.id_curso = cpifp_modulo.id_curso
   JOIN seg_numero ON cpifp_curso.id_numero = seg_numero.id_numero
   JOIN cpifp_ciclos ON cpifp_curso.id_ciclo = cpifp_ciclos.id_ciclo
   JOIN cpifp_grados ON cpifp_ciclos.id_grado = cpifp_grados.id_grado
   JOIN cpifp_turnos ON cpifp_ciclos.id_turno = cpifp_turnos.id_turno
   WHERE cpifp_profesor_modulo.id_profesor = :id_profe
   AND cpifp_profesor_modulo.id_lectivo = :id_lectivo;");
   $this->db->bind(':id_profe', $id_profe);
   $this->db->bind(":id_lectivo",$id_lectivo);
   return $this->db->registros();
}

public function hay_temas($id_modulo){
   $this->db->query("SELECT COUNT(*) as hay_temas 
   FROM seg_temas 
   WHERE id_modulo = :id_modulo;");
   $this->db->bind(":id_modulo",$id_modulo);
   return $this->db->registros();
}


public function hay_horas($id_modulo){
   $this->db->query("SELECT COUNT(*) as hay_horas 
   FROM seg_horario_modulo 
   WHERE id_modulo = :id_modulo;");
   $this->db->bind(":id_modulo",$id_modulo);
   return $this->db->registros();
}


public function hay_seguimiento($id_modulo){
   $this->db->query("SELECT COUNT(*) as hay_seguimiento 
   FROM seg_seguimiento_modulo
   WHERE id_modulo = :id_modulo;");
   $this->db->bind(":id_modulo",$id_modulo);
   return $this->db->registros();
}



public function suma_temas($id_modulo) {
    $this->db->query("SELECT SUM(total_horas) AS suma 
    FROM seg_temas 
    WHERE id_modulo = :id_modulo;");
    $this->db->bind(":id_modulo", $id_modulo);
    return $this->db->registros();
}





/************************************************************************/
/************************************************************************/
/***************************** INSERTAR AA ******************************/
/************************************************************************/
/************************************************************************/


// TRAE SOLO LA RESPUESTA 38 DEL INDICADOR HI
public function respuestas_38_hi($modulo){
   $this->db->query("SELECT seg_seguimiento_preguntas.id_seguimiento, seg_seguimiento_modulo.id_seg_evaluacion, cpifp_evaluaciones.id_evaluacion, evaluacion, 
   id_modulo, seg_preguntas.id_pregunta, seg_indicadores.id_indicador, indicador, indicador_corto, pregunta, respuesta 
   FROM seg_seguimiento_preguntas
   JOIN seg_seguimiento_modulo ON seg_seguimiento_preguntas.id_seguimiento = seg_seguimiento_modulo.id_seguimiento
   JOIN seg_preguntas ON seg_preguntas.id_pregunta = seg_seguimiento_preguntas.id_pregunta
   JOIN seg_evaluaciones ON seg_seguimiento_modulo.id_seg_evaluacion = seg_evaluaciones.id_seg_evaluacion
   JOIN cpifp_evaluaciones ON cpifp_evaluaciones.id_evaluacion = seg_evaluaciones.id_evaluacion
   JOIN seg_indicadores ON seg_indicadores.id_indicador = seg_preguntas.id_indicador
   WHERE id_modulo = :modulo 
   AND indicador_corto = 'HI'
   AND seg_seguimiento_preguntas.id_pregunta=38;");
   $this->db->bind(":modulo",$modulo);
   return $this->db->registros();
}





public function insertar_aa($respuestas, $totales, $id_modulo, $id_indicador, $total_curso, $lectivo, $info_modulo){

      $id_grado = $info_modulo[0]->id_grado;
      $this->db->query("SELECT `seg_indicadores`.`id_indicador`,`indicador`,`indicador_corto`, `cpifp_grados`.`id_grado`, 
      `cpifp_grados`.`nombre` AS `nombre_grado`, `porcentaje`, id_lectivo
      FROM `seg_indicadores`, `cpifp_grados`, `seg_indicadores_grados`
      WHERE `seg_indicadores`.`id_indicador`=`seg_indicadores_grados`.`id_indicador` 
      AND `cpifp_grados`.`id_grado`=`seg_indicadores_grados`.`id_grado`
      AND seg_indicadores_grados.id_grado=:id_grado
      AND seg_indicadores.id_indicador=:id_indicador
      AND seg_indicadores_grados.id_lectivo=:id_lectivo;");
      $this->db->bind(':id_grado',$id_grado);
      $this->db->bind(':id_indicador',$id_indicador);
      $this->db->bind(':id_lectivo', $lectivo[0]->id_lectivo);
      $info_indicador = $this->db->registros();


     // eliminar respuestas anteriores para preguntas 33-37
      $id_seguimientos_unicos = [];

      foreach ($respuestas as $r) {
         if (!in_array($r->id_seguimiento, $id_seguimientos_unicos)) {
            $id_seguimientos_unicos[] = $r->id_seguimiento;
         }
      }

      foreach ($id_seguimientos_unicos as $id_seguimiento) {
         
         $this->db->query("DELETE FROM seg_seguimiento_preguntas 
         WHERE id_seguimiento = :id_seguimiento 
         AND id_pregunta BETWEEN 33 AND 37;");
         $this->db->bind(':id_seguimiento', $id_seguimiento);
         $this->db->execute();

         $this->db->query("DELETE FROM seg_totales
         WHERE id_seguimiento = :id_seguimiento 
         AND id_indicador = :id_indicador;");
         $this->db->bind(':id_seguimiento', $id_seguimiento);
         $this->db->bind(':id_indicador', $id_indicador);
         $this->db->execute();

      }

      // insertamos en seg_seguimiento_preguntas
      for($i=0;$i<sizeof($respuestas);$i++){
         $this->db->query("INSERT INTO seg_seguimiento_preguntas (id_seguimiento, id_pregunta, respuesta) 
         VALUES (:id_seguimiento, :pregunta, :respuesta);");
         $this->db->bind(":pregunta",$respuestas[$i]->pregunta);
         $valor_respuesta = $respuestas[$i]->respuesta;
            if (empty($valor_respuesta)) {
               $valor_respuesta = 0;
         }
         $this->db->bind(":respuesta",$valor_respuesta);
         $this->db->bind(":id_seguimiento",$respuestas[$i]->id_seguimiento);
         $this->db->execute();
      };

      // insertamos en seg_totales
      for($i=0;$i<sizeof($totales);$i++){
         $this->db->query("INSERT INTO seg_totales (id_seguimiento, id_modulo, id_indicador, total) 
         VALUES (:id_seguimiento, :id_modulo, :id_indicador, :total);");
         $this->db->bind(":id_seguimiento",$totales[$i]->id_seguimiento);
         $this->db->bind(":id_modulo",$id_modulo);
         $this->db->bind(":id_indicador",$id_indicador);
         $this->db->bind(":total",$totales[$i]->resultado);
         $this->db->execute();
      };


      // borramos e insertamos en his_total_modulo
      $this->db->query("DELETE FROM his_total_modulo
      WHERE id_modulo = :id_modulo 
      AND id_indicador = :id_indicador
      AND id_lectivo = :id_lectivo;");
      $this->db->bind(":id_modulo",$id_modulo);
      $this->db->bind(':id_indicador', $id_indicador);
      $this->db->bind(':id_lectivo', $lectivo[0]->id_lectivo);
      $this->db->execute();


      $this->db->query("INSERT INTO his_total_modulo (
      id_lectivo, lectivo, id_ciclo, ciclo, ciclo_corto, id_departamento, departamento, departamento_corto,
      id_grado, grado, id_turno, turno, id_modulo, modulo, nombre_corto,
      id_departamento_modulo, departamento_modulo, id_curso, curso, id_numero, numero, nombre_curso,
      id_profesor, profesor, id_indicador, indicador, indicador_corto, total, modulo_conforme) 
      VALUES (:id_lectivo, :lectivo,:id_ciclo, :ciclo, :ciclo_corto,:id_departamento, :departamento, :departamento_corto,
      :id_grado, :grado,:id_turno, :turno,:id_modulo, :modulo, :nombre_corto,
      :id_departamento_modulo, :departamento_modulo,:id_curso, :curso,:id_numero, :numero, :nombre_curso,
      :id_profesor, :profesor,:id_indicador, :indicador, :indicador_corto,:total, :modulo_conforme)");

      $this->db->bind(':id_lectivo', $lectivo[0]->id_lectivo);
      $this->db->bind(':lectivo', $lectivo[0]->lectivo);

      $this->db->bind(':id_ciclo', $info_modulo[0]->id_ciclo);
      $this->db->bind(':ciclo', $info_modulo[0]->ciclo);
      $this->db->bind(':ciclo_corto', $info_modulo[0]->ciclo_corto);

      $this->db->bind(':id_departamento', $info_modulo[0]->id_departamento);
      $this->db->bind(':departamento', $info_modulo[0]->nombre_departamento_ciclo);
      $this->db->bind(':departamento_corto', $info_modulo[0]->departamento_corto);

      $this->db->bind(':id_grado', $info_modulo[0]->id_grado);
      $this->db->bind(':grado', $info_modulo[0]->grado);

      $this->db->bind(':id_turno', $info_modulo[0]->id_turno);
      $this->db->bind(':turno', $info_modulo[0]->turno);

      $this->db->bind(':id_modulo', $info_modulo[0]->id_modulo);
      $this->db->bind(':modulo', $info_modulo[0]->modulo);
      $this->db->bind(':nombre_corto', $info_modulo[0]->nombre_corto);

      $this->db->bind(':id_departamento_modulo', $info_modulo[0]->departamento_modulo);
      $this->db->bind(':departamento_modulo',$info_modulo[0]->nombre_departamento_modulo);

      $this->db->bind(':id_curso', $info_modulo[0]->id_curso);
      $this->db->bind(':curso', $info_modulo[0]->curso);

      $this->db->bind(':id_numero', $info_modulo[0]->id_numero);
      $this->db->bind(':numero', $info_modulo[0]->numero);
      $this->db->bind(':nombre_curso', $info_modulo[0]->nombre_curso);

      $this->db->bind(':id_profesor', $info_modulo[0]->id_profesor);
      $this->db->bind(':profesor', $info_modulo[0]->nombre_completo);

      $this->db->bind(':id_indicador', $info_indicador[0]->id_indicador);
      $this->db->bind(':indicador', $info_indicador[0]->indicador);
      $this->db->bind(':indicador_corto', $info_indicador[0]->indicador_corto);

      $this->db->bind(':total', $total_curso);

      $modulo_conforme='';
      if($total_curso >= $info_indicador[0]->porcentaje){
         $modulo_conforme = 1;
         $this->db->bind(':modulo_conforme', $modulo_conforme);
      }else{
         $modulo_conforme = 0;
         $this->db->bind(':modulo_conforme', $modulo_conforme);
      }
      
      if ($this->db->execute()){
         return true;
      }else{
         return false;
      }

}




/************************************************************************/
/************************************************************************/
/***************************** INSERTAR AP ******************************/
/************************************************************************/
/************************************************************************/


public function insertar_ap($respuestas, $totales, $id_modulo, $id_indicador, $lectivo, $info_modulo){

      $id_grado = $info_modulo[0]->id_grado;
      $this->db->query("SELECT `seg_indicadores`.`id_indicador`,`indicador`,`indicador_corto`, `cpifp_grados`.`id_grado`, 
      `cpifp_grados`.`nombre` AS `nombre_grado`, `porcentaje`, id_lectivo
      FROM `seg_indicadores`, `cpifp_grados`, `seg_indicadores_grados`
      WHERE `seg_indicadores`.`id_indicador`=`seg_indicadores_grados`.`id_indicador` 
      AND `cpifp_grados`.`id_grado`=`seg_indicadores_grados`.`id_grado`
      AND seg_indicadores_grados.id_grado=:id_grado
      AND seg_indicadores.id_indicador=:id_indicador
      AND seg_indicadores_grados.id_lectivo=:id_lectivo;");
      $this->db->bind(':id_grado',$id_grado);
      $this->db->bind(':id_indicador',$id_indicador);  
      $this->db->bind(':id_lectivo', $lectivo[0]->id_lectivo);
      $info_indicador = $this->db->registros();


     // eliminar respuestas anteriores para preguntas 41 Y 42
      $id_seguimientos_unicos = [];

      foreach ($respuestas as $r) {
         if (!in_array($r->id_seguimiento, $id_seguimientos_unicos)) {
            $id_seguimientos_unicos[] = $r->id_seguimiento;
         }
      }

      foreach ($id_seguimientos_unicos as $id_seguimiento) {
         
         $this->db->query("DELETE FROM seg_seguimiento_preguntas 
         WHERE id_seguimiento = :id_seguimiento 
         AND id_pregunta BETWEEN 41 AND 42;");
         $this->db->bind(':id_seguimiento', $id_seguimiento);
         $this->db->execute();

         $this->db->query("DELETE FROM seg_totales
         WHERE id_seguimiento = :id_seguimiento 
         AND id_indicador = :id_indicador;");
         $this->db->bind(':id_seguimiento', $id_seguimiento);
         $this->db->bind(':id_indicador', $id_indicador);
         $this->db->execute();

      }


      // insertamos en seg_seguimiento_preguntas
      for($i=0;$i<sizeof($respuestas);$i++){
         $this->db->query("INSERT INTO seg_seguimiento_preguntas (id_seguimiento, id_pregunta, respuesta) 
         VALUES (:id_seguimiento, :pregunta, :respuesta);");
         
         $this->db->bind(":pregunta",$respuestas[$i]->pregunta);

         $valor_respuesta = $respuestas[$i]->respuesta;
            if (empty($valor_respuesta)) {
               $valor_respuesta = 0;
         }
         $this->db->bind(":respuesta",$valor_respuesta);
         $this->db->bind(":id_seguimiento",$respuestas[$i]->id_seguimiento);
         $this->db->execute();
      };





      // insertamos en seg_totales
      for($i=0;$i<sizeof($totales);$i++){
         $this->db->query("INSERT INTO seg_totales (id_seguimiento, id_modulo, id_indicador, total) 
         VALUES (:id_seguimiento, :id_modulo, :id_indicador, :total);");
         $this->db->bind(":id_seguimiento",$totales[$i]->id_seguimiento);
         $this->db->bind(":id_modulo",$id_modulo);
         $this->db->bind(":id_indicador",$id_indicador);
         $this->db->bind(":total",$totales[$i]->resultado);
         $this->db->execute();
      };


      // borramos e insertamos en his_total_modulo
      $this->db->query("DELETE FROM his_total_modulo
      WHERE id_modulo = :id_modulo 
      AND id_indicador = :id_indicador
      AND id_lectivo =:id_lectivo;");
      $this->db->bind(":id_modulo",$id_modulo);
      $this->db->bind(':id_indicador', $id_indicador);
      $this->db->bind(':id_lectivo', $lectivo[0]->id_lectivo);
      $this->db->execute();

      $this->db->query ("SELECT * FROM seg_totales
      JOIN seg_seguimiento_modulo ON seg_totales.id_seguimiento = seg_seguimiento_modulo.id_seguimiento
      JOIN seg_evaluaciones ON seg_seguimiento_modulo.id_seg_evaluacion = seg_evaluaciones.id_seg_evaluacion
      JOIN cpifp_evaluaciones ON cpifp_evaluaciones.id_evaluacion = seg_evaluaciones.id_evaluacion
      WHERE seg_totales.id_indicador = :id_indicador
      AND seg_totales.id_modulo = :id_modulo
      AND cpifp_evaluaciones.id_evaluacion = (
         SELECT MAX(cpifp_evaluaciones.id_evaluacion)
         FROM seg_totales
         JOIN seg_seguimiento_modulo ON seg_totales.id_seguimiento = seg_seguimiento_modulo.id_seguimiento
         JOIN seg_evaluaciones ON seg_seguimiento_modulo.id_seg_evaluacion = seg_evaluaciones.id_seg_evaluacion
         JOIN cpifp_evaluaciones ON cpifp_evaluaciones.id_evaluacion = seg_evaluaciones.id_evaluacion
         WHERE seg_totales.id_indicador = :id_indicador
         AND seg_totales.id_modulo = :id_modulo
      )");
      $this->db->bind(":id_modulo",$id_modulo);
      $this->db->bind(':id_indicador', $id_indicador);
      $total_modulo = $this->db->registros();


      $this->db->query("INSERT INTO his_total_modulo (
      id_lectivo, lectivo, id_ciclo, ciclo, ciclo_corto, id_departamento, departamento, departamento_corto,
      id_grado, grado, id_turno, turno, id_modulo, modulo, nombre_corto,
      id_departamento_modulo, departamento_modulo, id_curso, curso, id_numero, numero, nombre_curso,
      id_profesor, profesor, id_indicador, indicador, indicador_corto, total, modulo_conforme) 
      VALUES (:id_lectivo, :lectivo,:id_ciclo, :ciclo, :ciclo_corto,:id_departamento, :departamento, :departamento_corto,
      :id_grado, :grado,:id_turno, :turno,:id_modulo, :modulo, :nombre_corto,
      :id_departamento_modulo, :departamento_modulo,:id_curso, :curso,:id_numero, :numero, :nombre_curso,
      :id_profesor, :profesor,:id_indicador, :indicador, :indicador_corto,:total, :modulo_conforme)");

      $this->db->bind(':id_lectivo', $lectivo[0]->id_lectivo);
      $this->db->bind(':lectivo', $lectivo[0]->lectivo);

      $this->db->bind(':id_ciclo', $info_modulo[0]->id_ciclo);
      $this->db->bind(':ciclo', $info_modulo[0]->ciclo);
      $this->db->bind(':ciclo_corto', $info_modulo[0]->ciclo_corto);

      $this->db->bind(':id_departamento', $info_modulo[0]->id_departamento);
      $this->db->bind(':departamento', $info_modulo[0]->nombre_departamento_ciclo);
      $this->db->bind(':departamento_corto', $info_modulo[0]->departamento_corto);

      $this->db->bind(':id_grado', $info_modulo[0]->id_grado);
      $this->db->bind(':grado', $info_modulo[0]->grado);

      $this->db->bind(':id_turno', $info_modulo[0]->id_turno);
      $this->db->bind(':turno', $info_modulo[0]->turno);

      $this->db->bind(':id_modulo', $info_modulo[0]->id_modulo);
      $this->db->bind(':modulo', $info_modulo[0]->modulo);
      $this->db->bind(':nombre_corto', $info_modulo[0]->nombre_corto);

      $this->db->bind(':id_departamento_modulo', $info_modulo[0]->departamento_modulo);
      $this->db->bind(':departamento_modulo',$info_modulo[0]->nombre_departamento_modulo);

      $this->db->bind(':id_curso', $info_modulo[0]->id_curso);
      $this->db->bind(':curso', $info_modulo[0]->curso);

      $this->db->bind(':id_numero', $info_modulo[0]->id_numero);
      $this->db->bind(':numero', $info_modulo[0]->numero);
      $this->db->bind(':nombre_curso', $info_modulo[0]->nombre_curso);

      $this->db->bind(':id_profesor', $info_modulo[0]->id_profesor);
      $this->db->bind(':profesor', $info_modulo[0]->nombre_completo);

      $this->db->bind(':id_indicador', $info_indicador[0]->id_indicador);
      $this->db->bind(':indicador', $info_indicador[0]->indicador);
      $this->db->bind(':indicador_corto', $info_indicador[0]->indicador_corto);

      $this->db->bind(':total', $total_modulo[0]->total);

      $modulo_conforme='';
      if($total_modulo[0]->total >= $info_indicador[0]->porcentaje){
         $modulo_conforme = 1;
         $this->db->bind(':modulo_conforme', $modulo_conforme);
      }else{
         $modulo_conforme = 0;
         $this->db->bind(':modulo_conforme', $modulo_conforme);
      }
      
      if ($this->db->execute()){
         return true;
      }else{
         return false;
      }


}



/************************************************************************/
/************************************************************************/
/***************************** INSERTAR AT ******************************/
/************************************************************************/
/************************************************************************/

public function insertar_at($respuestas, $totales, $id_modulo, $id_indicador, $lectivo, $info_modulo){

      $id_grado = $info_modulo[0]->id_grado;
      $this->db->query("SELECT `seg_indicadores`.`id_indicador`,`indicador`,`indicador_corto`, `cpifp_grados`.`id_grado`, 
      `cpifp_grados`.`nombre` AS `nombre_grado`, `porcentaje`, id_lectivo
      FROM `seg_indicadores`, `cpifp_grados`, `seg_indicadores_grados`
      WHERE `seg_indicadores`.`id_indicador`=`seg_indicadores_grados`.`id_indicador` 
      AND `cpifp_grados`.`id_grado`=`seg_indicadores_grados`.`id_grado`
      AND seg_indicadores_grados.id_grado=:id_grado
      AND seg_indicadores.id_indicador=:id_indicador
      AND seg_indicadores_grados.id_lectivo=:id_lectivo;");
      $this->db->bind(':id_grado',$id_grado);
      $this->db->bind(':id_indicador',$id_indicador);
      $this->db->bind(':id_lectivo', $lectivo[0]->id_lectivo);
      $info_indicador = $this->db->registros();


     // eliminar respuestas anteriores para preguntas 43-46
      $id_seguimientos_unicos = [];

      foreach ($respuestas as $r) {
         if (!in_array($r->id_seguimiento, $id_seguimientos_unicos)) {
            $id_seguimientos_unicos[] = $r->id_seguimiento;
         }
      }

      foreach ($id_seguimientos_unicos as $id_seguimiento) {
         
         $this->db->query("DELETE FROM seg_seguimiento_preguntas 
         WHERE id_seguimiento = :id_seguimiento 
         AND id_pregunta BETWEEN 43 AND 46;");
         $this->db->bind(':id_seguimiento', $id_seguimiento);
         $this->db->execute();

         $this->db->query("DELETE FROM seg_totales
         WHERE id_seguimiento = :id_seguimiento 
         AND id_indicador = :id_indicador;");
         $this->db->bind(':id_seguimiento', $id_seguimiento);
         $this->db->bind(':id_indicador', $id_indicador);
         $this->db->execute();

      }


      // insertamos en seg_seguimiento_preguntas
      for($i=0;$i<sizeof($respuestas);$i++){
         $this->db->query("INSERT INTO seg_seguimiento_preguntas (id_seguimiento, id_pregunta, respuesta) 
         VALUES (:id_seguimiento, :pregunta, :respuesta);");
         $this->db->bind(":pregunta",$respuestas[$i]->pregunta);
         $valor_respuesta = $respuestas[$i]->respuesta;
            if (empty($valor_respuesta)) {
               $valor_respuesta = 0;
         }
         $this->db->bind(":respuesta",$valor_respuesta);
         $this->db->bind(":id_seguimiento",$respuestas[$i]->id_seguimiento);
         $this->db->execute();
      };


      // insertamos en seg_totales
      for($i=0;$i<sizeof($totales);$i++){
         $this->db->query("INSERT INTO seg_totales (id_seguimiento, id_modulo, id_indicador, total) 
         VALUES (:id_seguimiento, :id_modulo, :id_indicador, :total);");
         $this->db->bind(":id_seguimiento",$totales[$i]->id_seguimiento);
         $this->db->bind(":id_modulo",$id_modulo);
         $this->db->bind(":id_indicador",$id_indicador);
         $this->db->bind(":total",$totales[$i]->resultado);
         $this->db->execute();
      };


      // borramos e insertamos en his_total_modulo
      $this->db->query("DELETE FROM his_total_modulo
      WHERE id_modulo = :id_modulo 
      AND id_indicador = :id_indicador
      AND id_lectivo = :id_lectivo;");
      $this->db->bind(":id_modulo",$id_modulo);
      $this->db->bind(':id_indicador', $id_indicador);
      $this->db->bind(':id_lectivo', $lectivo[0]->id_lectivo);
      $this->db->execute();


      $this->db->query ("SELECT * FROM seg_totales
      JOIN seg_seguimiento_modulo ON seg_totales.id_seguimiento = seg_seguimiento_modulo.id_seguimiento
      JOIN seg_evaluaciones ON seg_seguimiento_modulo.id_seg_evaluacion = seg_evaluaciones.id_seg_evaluacion
      JOIN cpifp_evaluaciones ON cpifp_evaluaciones.id_evaluacion = seg_evaluaciones.id_evaluacion
      WHERE seg_totales.id_indicador = :id_indicador
      AND seg_totales.id_modulo = :id_modulo
      AND cpifp_evaluaciones.id_evaluacion = (
         SELECT MAX(cpifp_evaluaciones.id_evaluacion)
         FROM seg_totales
         JOIN seg_seguimiento_modulo ON seg_totales.id_seguimiento = seg_seguimiento_modulo.id_seguimiento
         JOIN seg_evaluaciones ON seg_seguimiento_modulo.id_seg_evaluacion = seg_evaluaciones.id_seg_evaluacion
         JOIN cpifp_evaluaciones ON cpifp_evaluaciones.id_evaluacion = seg_evaluaciones.id_evaluacion
         WHERE seg_totales.id_indicador = :id_indicador
         AND seg_totales.id_modulo = :id_modulo
      )");
      $this->db->bind(":id_modulo",$id_modulo);
      $this->db->bind(':id_indicador', $id_indicador);
      $total_modulo = $this->db->registros();


      $this->db->query("INSERT INTO his_total_modulo (
      id_lectivo, lectivo, id_ciclo, ciclo, ciclo_corto, id_departamento, departamento, departamento_corto,
      id_grado, grado, id_turno, turno, id_modulo, modulo, nombre_corto,
      id_departamento_modulo, departamento_modulo, id_curso, curso, id_numero, numero, nombre_curso,
      id_profesor, profesor, id_indicador, indicador, indicador_corto, total, modulo_conforme) 
      VALUES (:id_lectivo, :lectivo,:id_ciclo, :ciclo, :ciclo_corto,:id_departamento, :departamento, :departamento_corto,
      :id_grado, :grado,:id_turno, :turno,:id_modulo, :modulo, :nombre_corto,
      :id_departamento_modulo, :departamento_modulo,:id_curso, :curso,:id_numero, :numero, :nombre_curso,
      :id_profesor, :profesor,:id_indicador, :indicador, :indicador_corto,:total, :modulo_conforme)");

      $this->db->bind(':id_lectivo', $lectivo[0]->id_lectivo);
      $this->db->bind(':lectivo', $lectivo[0]->lectivo);

      $this->db->bind(':id_ciclo', $info_modulo[0]->id_ciclo);
      $this->db->bind(':ciclo', $info_modulo[0]->ciclo);
      $this->db->bind(':ciclo_corto', $info_modulo[0]->ciclo_corto);

      $this->db->bind(':id_departamento', $info_modulo[0]->id_departamento);
      $this->db->bind(':departamento', $info_modulo[0]->nombre_departamento_ciclo);
      $this->db->bind(':departamento_corto', $info_modulo[0]->departamento_corto);

      $this->db->bind(':id_grado', $info_modulo[0]->id_grado);
      $this->db->bind(':grado', $info_modulo[0]->grado);

      $this->db->bind(':id_turno', $info_modulo[0]->id_turno);
      $this->db->bind(':turno', $info_modulo[0]->turno);

      $this->db->bind(':id_modulo', $info_modulo[0]->id_modulo);
      $this->db->bind(':modulo', $info_modulo[0]->modulo);
      $this->db->bind(':nombre_corto', $info_modulo[0]->nombre_corto);

      $this->db->bind(':id_departamento_modulo', $info_modulo[0]->departamento_modulo);
      $this->db->bind(':departamento_modulo',$info_modulo[0]->nombre_departamento_modulo);

      $this->db->bind(':id_curso', $info_modulo[0]->id_curso);
      $this->db->bind(':curso', $info_modulo[0]->curso);

      $this->db->bind(':id_numero', $info_modulo[0]->id_numero);
      $this->db->bind(':numero', $info_modulo[0]->numero);
      $this->db->bind(':nombre_curso', $info_modulo[0]->nombre_curso);

      $this->db->bind(':id_profesor', $info_modulo[0]->id_profesor);
      $this->db->bind(':profesor', $info_modulo[0]->nombre_completo);

      $this->db->bind(':id_indicador', $info_indicador[0]->id_indicador);
      $this->db->bind(':indicador', $info_indicador[0]->indicador);
      $this->db->bind(':indicador_corto', $info_indicador[0]->indicador_corto);

      $this->db->bind(':total', $total_modulo[0]->total);

      $modulo_conforme='';
      if($total_modulo[0]->total >= $info_indicador[0]->porcentaje){
         $modulo_conforme = 1;
         $this->db->bind(':modulo_conforme', $modulo_conforme);
      }else{
         $modulo_conforme = 0;
         $this->db->bind(':modulo_conforme', $modulo_conforme);
      }
      
      if ($this->db->execute()){
         return true;
      }else{
         return false;
      }

}




/************************************************************************/
/************************************************************************/
/***************************** INSERTAR AP2 *****************************/
/************************************************************************/
/************************************************************************/


// borra el ap2 en varias tablas si llegan vacios
public function borrar_ap2($id_seguimiento, $id_indicador, $id_modulo, $id_lectivo){

      $this->db->query("DELETE FROM seg_seguimiento_preguntas
      WHERE id_seguimiento = :id_seguimiento AND id_pregunta IN (56, 57);");
      $this->db->bind(":id_seguimiento",$id_seguimiento);
      $this->db->execute();

      $this->db->query("DELETE FROM seg_totales
      WHERE id_seguimiento=:id_seguimiento AND id_indicador=:id_indicador;");
      $this->db->bind(":id_seguimiento",$id_seguimiento);
      $this->db->bind(":id_indicador",$id_indicador);
      $this->db->execute();

      $this->db->query("DELETE FROM his_total_modulo
      WHERE id_modulo = :id_modulo 
      AND id_indicador = :id_indicador 
      AND id_lectivo = :id_lectivo;");
      $this->db->bind(":id_modulo",$id_modulo);
      $this->db->bind(":id_indicador",$id_indicador);
      $this->db->bind(":id_lectivo",$id_lectivo);
      $this->db->execute();

   return true;
}




public function insertar_ap2($respuestas, $totales, $id_modulo, $id_indicador, $total_curso, $lectivo, $info_modulo){


      $id_grado = $info_modulo[0]->id_grado;
      $this->db->query("SELECT `seg_indicadores`.`id_indicador`,`indicador`,`indicador_corto`, `cpifp_grados`.`id_grado`, 
      `cpifp_grados`.`nombre` AS `nombre_grado`, `porcentaje`, id_lectivo
      FROM `seg_indicadores`, `cpifp_grados`, `seg_indicadores_grados`
      WHERE `seg_indicadores`.`id_indicador`=`seg_indicadores_grados`.`id_indicador` 
      AND `cpifp_grados`.`id_grado`=`seg_indicadores_grados`.`id_grado`
      AND seg_indicadores_grados.id_grado=:id_grado
      AND seg_indicadores.id_indicador=:id_indicador
      AND seg_indicadores_grados.id_lectivo=:id_lectivo;");
      $this->db->bind(':id_grado',$id_grado);
      $this->db->bind(':id_indicador',$id_indicador);
      $this->db->bind(':id_lectivo', $lectivo[0]->id_lectivo);
      $info_indicador = $this->db->registros();


      $this->db->query("SELECT id_seguimiento, id_modulo, cpifp_evaluaciones.id_evaluacion, evaluacion, seg_evaluaciones.id_calendario, 
      date_format(fecha,'%d-%m-%Y') as fecha, 
      seg_seguimiento_modulo.id_seg_evaluacion, id_grado, id_numero, seg_seguimiento_modulo.id_seg_evaluacion
      FROM seg_seguimiento_modulo, seg_evaluaciones, cpifp_evaluaciones, seg_calendario
      WHERE seg_seguimiento_modulo.id_seg_evaluacion=seg_evaluaciones.id_seg_evaluacion 
      AND cpifp_evaluaciones.id_evaluacion=seg_evaluaciones.id_evaluacion 
      AND seg_calendario.id_calendario=seg_evaluaciones.id_calendario
      AND id_modulo=:id_modulo;");
      $this->db->bind(':id_modulo',$id_modulo);
      $ids = $this->db->registros();

      foreach ($ids as $id_seguimiento) {
         $this->db->query("DELETE FROM seg_seguimiento_preguntas 
         WHERE id_seguimiento = :id_seguimiento 
         AND id_pregunta BETWEEN 56 AND 57;");
         $this->db->bind(':id_seguimiento', $id_seguimiento->id_seguimiento);
         $this->db->execute();

         $this->db->query("DELETE FROM seg_totales
         WHERE id_seguimiento = :id_seguimiento 
         AND id_indicador = :id_indicador;");
         $this->db->bind(':id_seguimiento', $id_seguimiento->id_seguimiento);
         $this->db->bind(':id_indicador', $id_indicador);
         $this->db->execute();
      }
      
      
      for($i=0;$i<sizeof($respuestas);$i++){
         $this->db->query("INSERT INTO seg_seguimiento_preguntas (id_seguimiento, id_pregunta, respuesta) 
         VALUES (:id_seguimiento, :pregunta, :respuesta);");
         $this->db->bind(":pregunta",$respuestas[$i]->pregunta);
         $valor_respuesta = $respuestas[$i]->respuesta;
            if (empty($valor_respuesta)) {
               $valor_respuesta = 0;
         }
         $this->db->bind(":respuesta",$valor_respuesta);
         $this->db->bind(":id_seguimiento",$respuestas[$i]->id_seguimiento);
         $this->db->execute();
      };


      for($i=0;$i<sizeof($totales);$i++){
         $this->db->query("INSERT INTO seg_totales (id_seguimiento, id_modulo, id_indicador, total) 
         VALUES (:id_seguimiento, :id_modulo, :id_indicador, :total);");
         $this->db->bind(":id_seguimiento",$totales[$i]->id_seguimiento);
         $this->db->bind(":id_modulo",$id_modulo);
         $this->db->bind(":id_indicador",$id_indicador);
         $this->db->bind(":total",$totales[$i]->resultado);
         $this->db->execute();
      };



      // borramos e insertamos en his_total_modulo
      $this->db->query("DELETE FROM his_total_modulo
      WHERE id_modulo = :id_modulo 
      AND id_indicador = :id_indicador
      AND id_lectivo = :id_lectivo;");
      $this->db->bind(":id_modulo",$id_modulo);
      $this->db->bind(':id_indicador', $id_indicador);
      $this->db->bind(':id_lectivo', $lectivo[0]->id_lectivo);
      $this->db->execute();



      $this->db->query ("SELECT * FROM seg_totales
      JOIN seg_seguimiento_modulo ON seg_totales.id_seguimiento = seg_seguimiento_modulo.id_seguimiento
      JOIN seg_evaluaciones ON seg_seguimiento_modulo.id_seg_evaluacion = seg_evaluaciones.id_seg_evaluacion
      JOIN cpifp_evaluaciones ON cpifp_evaluaciones.id_evaluacion = seg_evaluaciones.id_evaluacion
      WHERE seg_totales.id_indicador = :id_indicador
      AND seg_totales.id_modulo = :id_modulo
      AND cpifp_evaluaciones.id_evaluacion = (
         SELECT MAX(cpifp_evaluaciones.id_evaluacion)
         FROM seg_totales
         JOIN seg_seguimiento_modulo ON seg_totales.id_seguimiento = seg_seguimiento_modulo.id_seguimiento
         JOIN seg_evaluaciones ON seg_seguimiento_modulo.id_seg_evaluacion = seg_evaluaciones.id_seg_evaluacion
         JOIN cpifp_evaluaciones ON cpifp_evaluaciones.id_evaluacion = seg_evaluaciones.id_evaluacion
         WHERE seg_totales.id_indicador = :id_indicador
         AND seg_totales.id_modulo = :id_modulo
      )");
      $this->db->bind(":id_modulo",$id_modulo);
      $this->db->bind(':id_indicador', $id_indicador);
      $total_modulo = $this->db->registros();



      $this->db->query("INSERT INTO his_total_modulo (
      id_lectivo, lectivo, id_ciclo, ciclo, ciclo_corto, id_departamento, departamento, departamento_corto,
      id_grado, grado, id_turno, turno, id_modulo, modulo, nombre_corto,
      id_departamento_modulo, departamento_modulo, id_curso, curso, id_numero, numero, nombre_curso,
      id_profesor, profesor, id_indicador, indicador, indicador_corto, total, modulo_conforme) 
      VALUES (:id_lectivo, :lectivo,:id_ciclo, :ciclo, :ciclo_corto,:id_departamento, :departamento, :departamento_corto,
      :id_grado, :grado,:id_turno, :turno,:id_modulo, :modulo, :nombre_corto,
      :id_departamento_modulo, :departamento_modulo,:id_curso, :curso,:id_numero, :numero, :nombre_curso,
      :id_profesor, :profesor,:id_indicador, :indicador, :indicador_corto,:total, :modulo_conforme)");

      // Asociamos los valores
      $this->db->bind(':id_lectivo', $lectivo[0]->id_lectivo);
      $this->db->bind(':lectivo', $lectivo[0]->lectivo);

      $this->db->bind(':id_ciclo', $info_modulo[0]->id_ciclo);
      $this->db->bind(':ciclo', $info_modulo[0]->ciclo);
      $this->db->bind(':ciclo_corto', $info_modulo[0]->ciclo_corto);

      $this->db->bind(':id_departamento', $info_modulo[0]->id_departamento);
      $this->db->bind(':departamento', $info_modulo[0]->nombre_departamento_ciclo);
      $this->db->bind(':departamento_corto', $info_modulo[0]->departamento_corto);

      $this->db->bind(':id_grado', $info_modulo[0]->id_grado);
      $this->db->bind(':grado', $info_modulo[0]->grado);

      $this->db->bind(':id_turno', $info_modulo[0]->id_turno);
      $this->db->bind(':turno', $info_modulo[0]->turno);

      $this->db->bind(':id_modulo', $info_modulo[0]->id_modulo);
      $this->db->bind(':modulo', $info_modulo[0]->modulo);
      $this->db->bind(':nombre_corto', $info_modulo[0]->nombre_corto);

      $this->db->bind(':id_departamento_modulo', $info_modulo[0]->departamento_modulo);
      $this->db->bind(':departamento_modulo',$info_modulo[0]->nombre_departamento_modulo);

      $this->db->bind(':id_curso', $info_modulo[0]->id_curso);
      $this->db->bind(':curso', $info_modulo[0]->curso);

      $this->db->bind(':id_numero', $info_modulo[0]->id_numero);
      $this->db->bind(':numero', $info_modulo[0]->numero);
      $this->db->bind(':nombre_curso', $info_modulo[0]->nombre_curso);

      $this->db->bind(':id_profesor', $info_modulo[0]->id_profesor);
      $this->db->bind(':profesor', $info_modulo[0]->nombre_completo);

      $this->db->bind(':id_indicador', $info_indicador[0]->id_indicador);
      $this->db->bind(':indicador', $info_indicador[0]->indicador);
      $this->db->bind(':indicador_corto', $info_indicador[0]->indicador_corto);

      $this->db->bind(':total', $total_curso);

      $modulo_conforme='';
      if($total_curso >= $info_indicador[0]->porcentaje){
         $modulo_conforme = 1;
         $this->db->bind(':modulo_conforme', $modulo_conforme);
      }else{
         $modulo_conforme = 0;
         $this->db->bind(':modulo_conforme', $modulo_conforme);
      }
      
      if ($this->db->execute()){
         return true;
      }else{
         return false;
      }


}








}





