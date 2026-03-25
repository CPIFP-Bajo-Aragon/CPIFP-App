<?php

class CumplimientoM{
    private $db;


    public function __construct(){
        $this->db = new Base;
    }



// TRAE EL CURSO LECTIVO ACTIVO
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


// devuelve TODOS los IDS de seguimiento y fechas de evaluacion de UN MODULO CONCRETO
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



// TRAE TODA LA INFO DEL INDICADOR EP2 (preguntas, categorias...)
public function obtener_preguntas_ep2(){
   $this->db->query("SELECT * 
   FROM seg_preguntas, seg_indicadores, seg_categorias, seg_preguntas_categorias
   WHERE seg_indicadores.id_indicador=seg_preguntas.id_indicador
   AND seg_categorias.id_categoria=seg_preguntas_categorias.id_categoria
   AND seg_preguntas_categorias.id_pregunta=seg_preguntas.id_pregunta
   AND indicador_corto='EP2';");
   return $this->db->registros();
}


// TRAE SOLO LOS NOMBRES DE LAS CATEGORIAS
public function solo_categorias(){
   $this->db->query("SELECT * FROM seg_categorias;");
   return $this->db->registros();
}



// TRAE EL NUMERO DE PREGUNTAS QUE HAY EN CADA CATEGORIA DEL EP2
public function numero_preguntas_categorias(){
   $this->db->query("SELECT id_categoria, COUNT(id_pregunta) AS cantidad_preguntas 
   FROM seg_preguntas_categorias GROUP BY id_categoria;");
   return $this->db->registros();
}


// TRAE LAS RESPUESTA DEL MODULO PARA EP2
public function obtener_respuestas_ep2($modulo){
   $this->db->query("SELECT * 
   FROM `seg_preguntas`, `seg_indicadores` , `seg_seguimiento_preguntas`, `seg_categorias`, `seg_preguntas_categorias`, seg_seguimiento_modulo
   WHERE `seg_preguntas`.id_indicador=seg_indicadores.id_indicador
   AND seg_seguimiento_preguntas.id_pregunta=seg_preguntas.id_pregunta
   AND seg_categorias.id_categoria=seg_preguntas_categorias.id_categoria
   AND seg_preguntas_categorias.id_pregunta=seg_preguntas.id_pregunta
   AND seg_indicadores.indicador_corto='EP2' 
   AND seg_seguimiento_modulo.id_seguimiento=seg_seguimiento_preguntas.id_seguimiento
   AND id_modulo=:modulo ORDER BY seg_seguimiento_modulo.id_seguimiento, seg_seguimiento_preguntas.id_pregunta;");
   $this->db->bind(":modulo",$modulo);
   return $this->db->registros();
}


// trae los totales de ep2
public function seg_totales_ep2($modulo){
   $this->db->query("SELECT * 
   FROM seg_totales 
   WHERE id_modulo=:id_modulo 
   AND id_indicador=2;");
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


public function hay_temas($id_modulo){
   $this->db->query("SELECT COUNT(*) as hay_temas FROM seg_temas 
   WHERE id_modulo = :id_modulo;");
   $this->db->bind(":id_modulo",$id_modulo);
   return $this->db->registros();
}


public function hay_horas($id_modulo){
   $this->db->query("SELECT COUNT(*) as hay_horas FROM seg_horario_modulo 
   WHERE id_modulo = :id_modulo;");
   $this->db->bind(":id_modulo",$id_modulo);
   return $this->db->registros();
}

public function hay_seguimiento($id_modulo){
   $this->db->query("SELECT COUNT(*) as hay_seguimiento FROM seg_seguimiento_modulo
   WHERE id_modulo = :id_modulo;");
   $this->db->bind(":id_modulo",$id_modulo);
   return $this->db->registros();
}


public function suma_temas($id_modulo) {
    $this->db->query("SELECT SUM(total_horas) AS suma FROM seg_temas 
    WHERE id_modulo = :id_modulo;");
    $this->db->bind(":id_modulo", $id_modulo);
    return $this->db->registros();
}




/********************************************************************************/
/***************************** BORRA EP2 ****************************************/
/********************************************************************************/

public function borrar_ep2($ids_seguimiento, $id_modulo, $id_lectivo){

   for ($i=0; $i<sizeof($ids_seguimiento); $i++) { 
      $this->db->query("DELETE 
      FROM seg_seguimiento_preguntas
      WHERE id_pregunta BETWEEN 1 AND 33 
      AND id_seguimiento =:id_seguimiento;");
      $this->db->bind(':id_seguimiento',$ids_seguimiento[$i]['id_seguimiento']);
      $this->db->execute();
   }

   for ($i=0; $i<sizeof($ids_seguimiento); $i++) { 
      $this->db->query("DELETE 
      FROM seg_totales
      WHERE id_seguimiento=:id_seguimiento 
      AND id_indicador=2;");
      $this->db->bind(":id_seguimiento",$ids_seguimiento[$i]['id_seguimiento']);
      $this->db->execute();
   }


   $this->db->query("DELETE 
   FROM his_total_modulo
   WHERE id_modulo = :id_modulo 
   AND id_indicador = 2 
   AND id_lectivo = :id_lectivo;");
   $this->db->bind(":id_modulo",$id_modulo);
   $this->db->bind(":id_lectivo",$id_lectivo);
   $this->db->execute();

   return true;
}



/********************************************************************************/
/***************************** INSERTA EP2 **************************************/
/********************************************************************************/


public function insertar_ep2($respuestas, $id_modulo, $total_ep2, $lectivo, $info_modulo) {

      $id_indicador = $respuestas[0]['indicador'];
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


      // eliminar respuestas anteriores para preguntas 1-32
      $id_seguimientos_unicos = [];
      foreach ($respuestas as $r) {
         if (!in_array($r['id_seguimiento'], $id_seguimientos_unicos)) {
            $id_seguimientos_unicos[] = $r['id_seguimiento'];
         }
      }

      foreach ($id_seguimientos_unicos as $id_seguimiento) {
         
         $this->db->query("DELETE FROM seg_seguimiento_preguntas 
         WHERE id_seguimiento = :id_seguimiento 
         AND id_pregunta BETWEEN 1 AND 32;");
         $this->db->bind(':id_seguimiento', $id_seguimiento);
         $this->db->execute();

         $this->db->query("DELETE FROM seg_totales
         WHERE id_seguimiento = :id_seguimiento 
         AND id_indicador = :id_indicador;");
         $this->db->bind(':id_seguimiento', $id_seguimiento);
         $this->db->bind(':id_indicador', $id_indicador);
         $this->db->execute();

      }


      foreach ($respuestas as $respuesta) {
         if ($respuesta['nulo'] == 'no') {
               $this->db->query("INSERT INTO seg_seguimiento_preguntas (id_seguimiento, id_pregunta, respuesta)
               VALUES (:id_seguimiento, :pregunta, :respuesta);");
               $this->db->bind(":pregunta", $respuesta['pregunta']);
               $this->db->bind(":respuesta", $respuesta['respuesta']);
               $this->db->bind(":id_seguimiento", $respuesta['id_seguimiento']);
               $this->db->execute();
         }
      }


      foreach ($total_ep2 as $total) {
         $this->db->query("INSERT INTO seg_totales (id_seguimiento, id_modulo, id_indicador, total) 
         VALUES (:id_seguimiento, :id_modulo, :id_indicador, :total);");
         $this->db->bind(":id_seguimiento", $total['id_seguimiento']);
         $this->db->bind(":id_modulo", $id_modulo);
         $this->db->bind(":id_indicador", $id_indicador);
         $this->db->bind(":total", $total['total']);
         $this->db->execute();
      }


      // borramos e insertamos en his_total_modulo
      $this->db->query("DELETE FROM his_total_modulo
      WHERE id_modulo = :id_modulo 
      AND id_indicador = :id_indicador
      AND id_lectivo=:id_lectivo;");
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






}