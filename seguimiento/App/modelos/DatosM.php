<?php

class DatosM{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }





// CURSO ACTUAL
public function obtener_lectivo(){
   $this->db->query("SELECT id_lectivo, lectivo, cerrado, 
   date_format(fecha_inicio,'%d-%m-%Y') AS fecha_inicio, date_format(fecha_fin,'%d-%m-%Y') AS fecha_fin
   FROM seg_lectivos where cerrado=0;");
   return $this->db->registros();
}



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





//***********************************************************************************************/
//***********************************************************************************************/
//************************** PARA HORAS DE CLASE AL DIA ****************************************/
//*********************************************************************************************/
//*********************************************************************************************/


// trae los nombres de los dias de la semamana excepto sabados y domingos
public function obtener_dias_semana(){
   $this->db->query("SELECT * FROM seg_dias_semana 
   WHERE dia_corto!='S' AND dia_corto!='D';");
   return $this->db->registros();
}


// trae el horario semanal de un modulo concreto
public function obtener_horario_semana_modulo($id_modulo){
   $this->db->query("SELECT id_modulo, seg_dias_semana.id_dia_semana, dia_semana, dia_corto, horas_dia
   FROM seg_horario_modulo, seg_dias_semana 
   WHERE id_modulo=:id_modulo 
   AND seg_dias_semana.id_dia_semana=seg_horario_modulo.id_dia_semana;");
   $this->db->bind(':id_modulo',$id_modulo);
   return $this->db->registros();
}



// INSERTAR HORAS AL DIA
public function horario_semana($id_horario, $horas, $id_modulo, $accion){

   if($accion=='insert'){
         for ($i = 0; $i < count($id_horario); $i++) {
            $horas_dia = is_numeric($horas[$i]) ? (int)$horas[$i] : 0;
            $this->db->query("INSERT INTO seg_horario_modulo (id_dia_semana, id_modulo, horas_dia)
            VALUES (:id_dia_semana, :id_modulo, :horas);");
            $this->db->bind(':id_dia_semana', $id_horario[$i]);
            $this->db->bind(':id_modulo', $id_modulo);
            $this->db->bind(':horas', $horas_dia);
            $this->db->execute();
        }
         return true;
   }else{
         $this->db->query("DELETE FROM seg_horario_modulo 
         WHERE id_modulo = :id_modulo;");
         $this->db->bind(":id_modulo",$id_modulo);
         $this->db->execute();
        for ($i = 0; $i < count($id_horario); $i++) {
            $horas_dia = is_numeric($horas[$i]) ? (int)$horas[$i] : 0;
            $this->db->query("INSERT INTO seg_horario_modulo (id_dia_semana, id_modulo, horas_dia)
            VALUES (:id_dia_semana, :id_modulo, :horas);");
            $this->db->bind(':id_dia_semana', $id_horario[$i]);
            $this->db->bind(':id_modulo', $id_modulo);
            $this->db->bind(':horas', $horas_dia);
            $this->db->execute();
        }
         return true;
   };
}



//***********************************************************************************************/
//***********************************************************************************************/
//************************** PARA TEMAS DEL MODULO *********************************************/
//*********************************************************************************************/
//*********************************************************************************************/


// devuelve los temas con examenes y dual (NO FALTAS, ACTIVIDADES y OTROS )
public function temas_del_modulo($id_modulo){
    $this->db->query("SELECT * FROM seg_temas 
                      WHERE id_modulo = :id_modulo 
                      AND descripcion NOT IN ('Actividades', 'Otros', 'Faltas')
                      ORDER BY tema ASC");
    $this->db->bind(':id_modulo', $id_modulo);
    return $this->db->registros();
}


// cuenta los temas (NO FALTAS, ACTIVIDADES, OTROS, EXAMENES , DUAL)
public function contar_temas($id_modulo){
   $this->db->query("SELECT COUNT(*) AS total
   FROM seg_temas 
   WHERE id_modulo = :id_modulo 
   AND descripcion<>'Actividades' AND descripcion<>'Otros' AND descripcion<>'Faltas'AND descripcion<>'Examenes' AND descripcion<>'Dual';");
   $this->db->bind(':id_modulo',$id_modulo);
   return $this->db->registros();
}


// suma las horas de los temas (NO FALTAS, ACTIVIDADES y OTROS)
public function total_horas_temas($modulo){
   $this->db->query("SELECT SUM(total_horas) AS suma_temas FROM `seg_temas` WHERE id_modulo=:id_modulo
   AND descripcion<>'Actividades' AND descripcion<>'Otros' AND descripcion<>'Faltas';");
   $this->db->bind(":id_modulo",$modulo);
   return $this->db->registros();
}





//***********************************************************************************************/
//***********************************************************************************************/
//************************** PARA BLOQUEAR ENLACES  ********************************************/
//*********************************************************************************************/
//*********************************************************************************************/


public function obtener_modulos($id_profe, $id_lectivo) {
   $this->db->query("SELECT cpifp_profesor.id_profesor, cpifp_profesor.nombre_completo,
   cpifp_modulo.id_modulo,cpifp_modulo.modulo,cpifp_modulo.nombre_corto, cpifp_modulo.horas_totales, cpifp_modulo.cuerpo, 
   cpifp_modulo.horas_semanales, cpifp_modulo.id_departamento AS departamento_modulo,
   cpifp_profesor_modulo.horas_profesor,cpifp_profesor_modulo.id_lectivo,
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
   AND cpifp_profesor_modulo.id_lectivo=:id_lectivo;");
   $this->db->bind(':id_profe', $id_profe);
   $this->db->bind(':id_lectivo', $id_lectivo);
   return $this->db->registros();
}



// cuenta las horas que hay a la semana
public function hay_horas($id_modulo){
   $this->db->query("SELECT COUNT(*) as hay_horas FROM seg_horario_modulo 
   WHERE id_modulo = :id_modulo;");
   $this->db->bind(":id_modulo",$id_modulo);
   return $this->db->registros();
}


// cuenta los temas que hay
public function hay_temas($id_modulo){
   $this->db->query("SELECT COUNT(*) as hay_temas FROM seg_temas 
   WHERE id_modulo = :id_modulo;");
   $this->db->bind(":id_modulo",$id_modulo);
   return $this->db->registros();
}


// cuenta los seguimientos que hay
public function hay_seguimiento($id_modulo){
   $this->db->query("SELECT COUNT(*) as hay_seguimiento FROM seg_seguimiento_modulo
   WHERE id_modulo = :id_modulo;");
   $this->db->bind(":id_modulo",$id_modulo);
   return $this->db->registros();
}


// suma las horas de los temas
public function suma_temas($id_modulo) {
    $this->db->query("SELECT SUM(total_horas) AS suma FROM seg_temas 
    WHERE id_modulo = :id_modulo;");
    $this->db->bind(":id_modulo", $id_modulo);
    return $this->db->registros();
}




//********************************************************************************/
//********************************************************************************/
/************************ REFERENTE A LOS TEMAS **********************************/
//********************************************************************************/
//********************************************************************************/



/************************ NUEVO TEMA ****************************/

public function nuevo_tema($nuevo) {

   $this->db->query("INSERT INTO seg_temas (tema, id_modulo, descripcion, total_horas, estado)
   VALUES (:tema, :id_modulo, :descripcion, :total_horas, 1)");
   $this->db->bind(':tema', $nuevo['numero_tema']);
   $this->db->bind(':id_modulo', $nuevo['id_modulo']);
   $this->db->bind(':descripcion', $nuevo['descripcion']);
   $this->db->bind(':total_horas', $nuevo['total_horas']);

    if ($this->db->execute()) {

         $this->db->query("SELECT COUNT(*) as hay_temas FROM seg_temas 
         WHERE id_modulo = :id_modulo;");
         $this->db->bind(":id_modulo",$nuevo['id_modulo']);
         $hay_temas = $this->db->registros();

        if ($hay_temas[0]->hay_temas == 1) {

               $temas = [
                  ['tema' => 959595, 'descripcion' => 'Examenes'],
                  ['tema' => 949494, 'descripcion' => 'Dual'],
                  ['tema' => 989898, 'descripcion' => 'Actividades'],
                  ['tema' => 979797, 'descripcion' => 'Faltas'],
                  ['tema' => 969696, 'descripcion' => 'Otros']
               ];

            foreach ($temas as $tema) {
               $this->db->query("INSERT INTO seg_temas (id_modulo, tema, descripcion, total_horas, estado)
               VALUES (:id_modulo, :tema, :descripcion, 0, 1);");
               $this->db->bind(':id_modulo', $nuevo['id_modulo']);
               $this->db->bind(':tema', $tema['tema']);
               $this->db->bind(':descripcion', $tema['descripcion']);
               $this->db->execute();
            }
        }
        return true;

    } else {

        return false;

    }

}


/************************ BORRAR TEMA ****************************/

public function borrar_tema($id_tema){
      $this->db->query("DELETE FROM seg_temas
      WHERE id_tema=:id_tema;");
      $this->db->bind(':id_tema',$id_tema);
      if ($this->db->execute()){
         return true;
      }else{
         return false;
      }
}



/************************ EDITAR TEMA ****************************/

public function editar_tema($editar,$id_tema){
      $this->db->query("UPDATE seg_temas
      SET tema=:tema, descripcion=:descripcion, total_horas=:total_horas
      WHERE id_tema=:id_tema;"); 
      $this->db->bind(':tema', $editar['numero_tema']);
      $this->db->bind(':descripcion', $editar['descripcion']);
      $this->db->bind(':total_horas', $editar['total_horas']);
      $this->db->bind(':id_tema',$id_tema);
      if($this->db->execute()){
         return true;
      }else{
         return false;
      }
}






//********************************************************************************/
//********************************************************************************/
/*********************************** PREGUNTA 38 *********************************/
//********************************************************************************/
//********************************************************************************/



public function obtener_id_seguimientos_evaluacion($id_modulo){
   $this->db->query("SELECT id_seguimiento, id_modulo, cpifp_evaluaciones.id_evaluacion, 
   evaluacion, seg_evaluaciones.id_calendario, date_format(fecha,'%d-%m-%Y') as fecha, 
   seg_seguimiento_modulo.id_seg_evaluacion, id_grado, id_numero, seg_seguimiento_modulo.id_seg_evaluacion
   FROM seg_seguimiento_modulo, seg_evaluaciones, cpifp_evaluaciones, seg_calendario
   WHERE seg_seguimiento_modulo.id_seg_evaluacion=seg_evaluaciones.id_seg_evaluacion 
   AND cpifp_evaluaciones.id_evaluacion=seg_evaluaciones.id_evaluacion 
   AND seg_calendario.id_calendario=seg_evaluaciones.id_calendario
   AND id_modulo=:id_modulo;");
   $this->db->bind(':id_modulo',$id_modulo);
   return $this->db->registros();
}




// actualiza la pregunta 38
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
            $this->db->query("UPDATE seg_seguimiento_preguntas SET respuesta=:respuesta
            WHERE id_seguimiento=:id_seguimiento AND id_pregunta=38;");
            $this->db->bind(':id_seguimiento',$evas['id_seguimiento']);
            $this->db->bind(':respuesta',$resul->horas);
            $this->db->execute();
         endif; 
      endforeach; 
   endforeach;

}





}