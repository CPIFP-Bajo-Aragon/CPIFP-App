<?php

class HorasImpartidasM{
    private $db;


    public function __construct(){
        $this->db = new Base;
    }


// TRAE EL LECTIVO
public function obtener_lectivo(){
   $this->db->query("SELECT id_lectivo, lectivo, cerrado, 
   date_format(fecha_inicio,'%d-%m-%Y') AS fecha_inicio, date_format(fecha_fin,'%d-%m-%Y') AS fecha_fin
   FROM seg_lectivos where cerrado=0");
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






// HORAS DE CLASE AL MES (octubre 15 horas, noviembre 20 hrs....)
public function horas_previstas_mes($id_modulo){
   $this->db->query("SELECT YEAR(c.fecha) AS año, 
      CASE 
         WHEN MONTH(c.fecha) = 9 THEN 10 
         ELSE MONTH(c.fecha) 
      END AS mes, 
      SUM(hm.horas_dia) AS total_horas_clase
      FROM seg_calendario c
      LEFT JOIN seg_festivos f ON c.id_calendario = f.id_calendario
      LEFT JOIN seg_dias_semana ds ON c.dia_semana = ds.dia_corto
      LEFT JOIN seg_horario_modulo hm ON ds.id_dia_semana = hm.id_dia_semana
      WHERE hm.id_modulo = :id_modulo AND horas_dia > 0 AND festivo IS NULL
      GROUP BY YEAR(c.fecha), 
         CASE 
            WHEN MONTH(c.fecha) = 9 THEN 10 
            ELSE MONTH(c.fecha)
         END
      ORDER BY año, mes;");
    $this->db->bind(':id_modulo',$id_modulo);
    return $this->db->registros();
}



/****************************************************/
/****************************************************/
/************ TABLA HORAS IMPARTIDAS MES ************/
/****************************************************/
/****************************************************/


// TEMAS DEL MODULO
public function temas_del_modulo($id_modulo){
   $this->db->query("SELECT * FROM seg_temas 
   WHERE id_modulo=:id_modulo ORDER BY tema;");
   $this->db->bind(':id_modulo',$id_modulo);
   return $this->db->registros();
}


// LOS VALORES DEL DIARIO
public function valores_x_mes($id_modulo){
      $this->db->query("SELECT id_modulo, id_tema, YEAR(fecha) AS año,
         CASE 
            WHEN MONTH(fecha) = 9 THEN 10 
            ELSE MONTH(fecha)
         END AS mes,
      SUM(horas_dia) AS total_horas
      FROM seg_seguimiento_temas WHERE id_modulo = :id_modulo
      GROUP BY id_tema, YEAR(fecha), 
         CASE 
            WHEN MONTH(fecha) = 9 THEN 10  
            ELSE MONTH(fecha)
         END
      ORDER BY año ASC, mes ASC;");
      $this->db->bind(":id_modulo",$id_modulo);
      return $this->db->registros();
}




/****************************************************/
/****************************************************/
/************ TABLA 1 IMPARTIDAS MES ***************/
/****************************************************/
/****************************************************/


public function valores($id_modulo, $temas){

      $meses = array (
         (object)[ 'id_mes'=> 1, 'numero'=>10, 'mes'=>'Octubre'],
         (object)[ 'id_mes'=> 2, 'numero'=>11, 'mes'=>'Noviembre'],
         (object)[ 'id_mes'=> 3, 'numero'=>12, 'mes'=>'Diciembre'],
         (object)[ 'id_mes'=> 4, 'numero'=>1, 'mes'=>'Enero'],
         (object)[ 'id_mes'=> 5, 'numero'=>2, 'mes'=>'Febrero'],
         (object)[ 'id_mes'=> 6, 'numero'=>3, 'mes'=>'Marzo'],
         (object)[ 'id_mes'=> 7, 'numero'=>4, 'mes'=>'Abril'],
         (object)[ 'id_mes'=> 8, 'numero'=>5, 'mes'=>'Mayo'],
         (object)[ 'id_mes'=> 9, 'numero'=>6, 'mes'=>'Junio']
      );

         $this->db->query("SELECT id_modulo, id_tema, YEAR(fecha) AS año,
            CASE 
               WHEN MONTH(fecha) = 9 THEN 10 
               ELSE MONTH(fecha)
            END AS mes,
         SUM(horas_dia) AS total_horas
         FROM seg_seguimiento_temas WHERE id_modulo = :id_modulo
         GROUP BY id_tema, YEAR(fecha), 
            CASE 
               WHEN MONTH(fecha) = 9 THEN 10  
               ELSE MONTH(fecha)
            END
         ORDER BY año ASC, mes ASC;");
         $this->db->bind(":id_modulo",$id_modulo);

         $valores = $this->db->registros(); 


      // Agrupar datos por tema y mes para acceso rápido
      $horas_por_tema_mes = [];
      foreach ($valores as $fila) {
         $horas_por_tema_mes[$fila->id_tema][$fila->mes] = $fila->total_horas;
      }

      // Ahora construimos una estructura completa con todos los meses y temas
      $datos_completos = [];

      foreach ($temas as $tema) {
         $id_tema = $tema->id_tema;
         foreach ($meses as $mes) {
            $numero_mes = $mes->numero;
            $datos_completos[] = (object)[
                  'id_tema' => $id_tema,
                  'tema' => $tema->tema,
                  'descripcion' => $tema->descripcion,
                  'id_mes' => $mes->id_mes,
                  'mes' => $numero_mes,
                  'total_horas' => isset($horas_por_tema_mes[$id_tema][$numero_mes]) 
                     ? $horas_por_tema_mes[$id_tema][$numero_mes] 
                     : 0
            ];
         }
      }

      return $datos_completos;

}


// SUMA TODAS LAS HORAS IMPARTIDAS DE UN TEMA
public function suma_horas_x_temas($id_modulo){
   $this->db->query("SELECT id_modulo, id_tema, SUM(horas_dia) AS total_horas
   FROM  seg_seguimiento_temas 
   WHERE id_modulo=:id_modulo 
   GROUP BY id_tema ORDER BY id_tema;");
   $this->db->bind(":id_modulo",$id_modulo);
   return $this->db->registros();
}



// SUMA TODAS LAS HORAS IMPARTIDAS EN UN MES (contenidos impartidos por cada mes)
public function total_mes_temas($id_modulo){
   $this->db->query("SELECT YEAR(DATE_ADD(fecha, INTERVAL IF(MONTH(fecha) = 9, 1, 0) MONTH)) AS anio,
    MONTH(DATE_ADD(fecha, INTERVAL IF(MONTH(fecha) = 9, 1, 0) MONTH)) AS mes, SUM(horas_dia) AS total_horas
   FROM segui_diario_temas 
   WHERE id_modulo=:id_modulo 
   AND descripcion!='Otros' AND descripcion!='Faltas' AND descripcion!='Actividades'
   GROUP BY YEAR(DATE_ADD(fecha, INTERVAL IF(MONTH(fecha) = 9, 1, 0) MONTH)), MONTH(DATE_ADD(fecha, INTERVAL IF(MONTH(fecha) = 9, 1, 0) MONTH))
   ORDER BY anio DESC, mes DESC;");
   $this->db->bind(":id_modulo",$id_modulo);
   return $this->db->registros();
}




/****************************************************/
/****************************************************/
/************ TABLA 2 EVALUACIONES ******************/
/****************************************************/
/*************************************************/


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



// TABLA HORAS IMPARTIDAS X EVALUACION: suma cada tema x evaluacion
public function total_x_evaluaciones($intervalos_evaluaciones){

      $valores = array();

      for($i=0;$i<sizeof($intervalos_evaluaciones);$i++){

            $evaluacion = $intervalos_evaluaciones[$i]['id_seguimiento'];

            $this->db->query("SELECT id_modulo, id_tema, SUM(horas_dia) AS total_horas 
            FROM seg_seguimiento_temas
            WHERE fecha BETWEEN :fecha_inicio 
            AND :fecha_fin AND id_modulo=:id_modulo
            GROUP BY id_tema ORDER BY total_horas DESC;");
            $this->db->bind(":fecha_inicio",$intervalos_evaluaciones[$i]['fecha_inicio']);
            $this->db->bind(":fecha_fin",$intervalos_evaluaciones[$i]['fecha_fin']);
            $this->db->bind(":id_modulo",$intervalos_evaluaciones[$i]['id_modulo']);
            $registros = $this->db->registros();

            foreach ($registros as $objeto) {
               $objeto->id_seguimiento = $evaluacion; 
            }
            array_push($valores,$registros);
      }

   return $valores;
}




// TABLA HORAS IMPARTIDAS X EVALUACION: total contenidos impartidos (ACUMULA)
public function total_eva_contenidos($intervalos_evaluaciones){

   $valores=array();

   for($i=0;$i<sizeof($intervalos_evaluaciones);$i++){

      $evaluacion=$intervalos_evaluaciones[$i]['id_seguimiento'];
      $this->db->query("SELECT *, SUM(horas_dia) AS total_contenidos
      FROM segui_diario_temas 
      WHERE fecha BETWEEN :fecha_inicio 
      AND :fecha_fin AND id_modulo=:id_modulo
      AND descripcion!='Otros' AND descripcion!='Faltas' AND descripcion!='Actividades';");
      $this->db->bind(":fecha_inicio",$intervalos_evaluaciones[$i]['fecha_inicio']);
      $this->db->bind(":fecha_fin",$intervalos_evaluaciones[$i]['fecha_fin']);
      $this->db->bind(":id_modulo",$intervalos_evaluaciones[$i]['id_modulo']);
      $registros=$this->db->registros();

      foreach ($registros as $objeto) {
         $objeto->id_seguimiento = $evaluacion; 
      }
      array_push($valores,$registros);
   }
   return $valores;
}




// HORAS PREVISTAS PARA CADA EVALUACION (busca en la pregunta 38)
public function hrs_previstas_x_evaluacion($evaluaciones) {
   $valores = array(); 
   foreach($evaluaciones as $evas) {
       $this->db->query("SELECT * FROM seg_seguimiento_preguntas 
       WHERE id_pregunta = 38 
       AND id_seguimiento = :id_seguimiento;");
       $this->db->bind(":id_seguimiento", $evas->id_seguimiento);
       $registros = $this->db->registros();

       if (!empty($registros)) {
           array_push($valores, $registros[0]);
       }
   }

   return $valores;
}




/****************************************************/
/****************************************************/
/************ TABLA 3 ACUMULADAS *******************/
/****************************************************/
/**************************************************/


// TRAE EL ID_SEGUIMIENTO MAYOR PARA EL INDICADOR EP1
public function id_mas_alto($id_modulo){
   $this->db->query("SELECT MAX(id_seguimiento) as ep1 
   FROM `seg_seguimiento_modulo` 
   WHERE id_modulo=:id_modulo;");
   $this->db->bind(":id_modulo",$id_modulo);
   return $this->db->registros();
}


/************************************************************/
/************************************************************/
/************ TABLA 4 EDICION ACUMULADAS *******************/
/***********************************************************/
/***********************************************************/


// TRAE VALORES DE ACUMULADAS MES Y TEMAS
public function edicion_tema($id_modulo){
   $this->db->query("SELECT * FROM `seg_ep1_mes`, seg_ep1_tema 
   WHERE seg_ep1_tema.id_ep1_mes=seg_ep1_mes.id_ep1_mes
   AND id_modulo=:id_modulo;");
   $this->db->bind(":id_modulo",$id_modulo);
   return $this->db->registros();
}


public function edicion_mes($id_modulo){
   $this->db->query("SELECT * FROM `seg_ep1_mes`
   WHERE id_modulo=:id_modulo;");
   $this->db->bind(":id_modulo",$id_modulo);
   return $this->db->registros();
}


// TRAE DIARIO PARA VERIFICACION
public function diario_verificacion($id_modulo){
   $this->db->query("SELECT * FROM  seg_seguimiento_temas 
   WHERE id_modulo=:id_modulo;");
   $this->db->bind(":id_modulo",$id_modulo);
   return $this->db->registros();
}


// TRAE SI HAY EDICION EN ACUMULADAS
public function hay_edicion_manual($id_modulo){
   $this->db->query("SELECT COUNT(*) as total 
   FROM seg_ep1_mes
   WHERE id_modulo = :id AND edicion_mes = 1");
   $this->db->bind(':id', $id_modulo);
   return $this->db->registros();
}




//*************************************************************/
// INSERCION PARA LA TABLA ACUMULADAS 4 , SI NO HAY VALORES 
//*************************************************************/


// ELIMINAR ACUMULADAS
public function eliminar_acumuladas($id_modulo){

   $this->db->query("DELETE FROM seg_ep1_mes
   WHERE id_modulo=:id_modulo;");
   $this->db->bind(":id_modulo",$id_modulo);
   $this->db->execute();

   $this->db->query("DELETE FROM seg_ep1_real
   WHERE id_modulo=:id_modulo;");
   $this->db->bind(":id_modulo",$id_modulo);
   $this->db->execute();

   $this->db->query("DELETE FROM seg_ep1
   WHERE id_modulo=:id_modulo;");
   $this->db->bind(":id_modulo",$id_modulo);
   $this->db->execute();

}



// inserta acumuladas
public function insertar_acumuladas($valores_ep1, $previstas_acumuladas_mes, $total_mes, $acumuladas_temas, $id_modulo, $id_alto) {

   foreach ($previstas_acumuladas_mes as $acumulada) {

         $id_seguimiento = $acumulada->id_seguimiento;
         $id_pregunta = $acumulada->id_pregunta;
         $mes = $acumulada->mes;
         
         foreach ($valores_ep1 as $ep1) {
            if ($ep1->id_pregunta == $id_pregunta) {
                  $ep1_respuesta = $ep1->respuesta;
                  break;
            }
         }

         foreach ($total_mes as $total) {
            if ($total['id_pregunta'] == $id_pregunta) {
               $impartido_mes = $total['total_mes'];
               break;
            }
         }


      $this->db->query("INSERT INTO seg_ep1_real (id_seguimiento, id_modulo, id_pregunta, ep1)
      VALUES (:id_seguimiento, :id_modulo, :id_pregunta, :ep1);");
      $this->db->bind(":id_seguimiento", $id_seguimiento);
      $this->db->bind(":id_modulo",$id_modulo);
      $this->db->bind(":id_pregunta", $id_pregunta);
      $this->db->bind(":ep1", $ep1_respuesta);
      $this->db->execute();

      $this->db->query("INSERT INTO seg_ep1 (id_seguimiento, id_modulo, id_pregunta, ep1)
      VALUES (:id_seguimiento, :id_modulo, :id_pregunta, :ep1);");
      $this->db->bind(":id_seguimiento", $id_seguimiento);
      $this->db->bind(":id_modulo",$id_modulo);
      $this->db->bind(":id_pregunta", $id_pregunta);
      $this->db->bind(":ep1", $ep1_respuesta);
      $this->db->execute();


      $this->db->query("INSERT INTO seg_ep1_mes (id_seguimiento, id_modulo, id_pregunta, ajustes, contenidos_impartidos, ep1, edicion_mes) 
      VALUES (:id_seguimiento, :id_modulo, :id_pregunta, 0, :contenidos_impartidos, :ep1, 0)");
      $this->db->bind(":id_seguimiento", $id_seguimiento);
      $this->db->bind(":id_modulo", $id_modulo);
      $this->db->bind(":id_pregunta", $id_pregunta);
      $this->db->bind(":contenidos_impartidos", $impartido_mes);
      $this->db->bind(":ep1", $ep1_respuesta);
      $this->db->execute();

      $id = $this->db->ultimoIndice();

      foreach ($acumuladas_temas as $temas) {
         if($temas['pregunta'] == $acumulada->id_pregunta){
            $this->db->query("INSERT INTO seg_ep1_tema (id_ep1_mes, id_tema, id_pregunta, horas_acumuladas, edicion_tema) 
            VALUES (:id, :id_tema, :id_pregunta, :horas_acumuladas, 0)");
            $this->db->bind(":id_tema", $temas['id_tema']);
            $this->db->bind(":id_pregunta", $temas['pregunta']);
            $this->db->bind(":horas_acumuladas", $temas['horas_acumuladas']);
            $this->db->bind(":id", $id);
            $this->db->execute();
         }

      }
   }

   return true;
}






// INSERCION EP1 PARA CADA MES
public function insertar_ep1($ep1_x_mes, $id_modulo){

   // COGEMOS EL ID_SEGUIMIENTO MAS ALTO YA QUE ESTE INDICADOR NO TIENE ID_SEGUIMIENTO
   $this->db->query("SELECT MAX(id_seguimiento) as id_seguimiento FROM `seg_seguimiento_modulo` 
   WHERE id_modulo=:id_modulo;");
   $this->db->bind(":id_modulo",$id_modulo);
   $id_seguimiento = $this->db->registros();

   $this->db->query("SELECT * FROM seg_seguimiento_preguntas 
   WHERE id_seguimiento = :id_seguimiento AND id_pregunta BETWEEN 47 and 55;");
   $this->db->bind(":id_seguimiento",$id_seguimiento[0]->id_seguimiento);
   $hay_valores = $this->db->registros();


   if(empty($hay_valores)){

         foreach($ep1_x_mes as $ep1){

            $this->db->query("INSERT INTO seg_seguimiento_preguntas (id_seguimiento, id_pregunta, respuesta)
            VALUES (:id_seguimiento, :id_pregunta, :respuesta);");
            $this->db->bind(":id_seguimiento",$id_seguimiento[0]->id_seguimiento);
            $this->db->bind(":id_pregunta",$ep1->pregunta);
            $this->db->bind(":respuesta",$ep1->ep1);
            $this->db->execute();

            $this->db->query("INSERT INTO seg_ep1_real (id_seguimiento, id_modulo, id_pregunta, ep1)
            VALUES (:id_seguimiento, :id_modulo, :id_pregunta, :ep1);");
            $this->db->bind(":id_seguimiento",$id_seguimiento[0]->id_seguimiento);
            $this->db->bind(":id_pregunta",$ep1->pregunta);
            $this->db->bind(":id_modulo",$id_modulo);
            $this->db->bind(":ep1",$ep1->ep1);
            $this->db->execute();

            $this->db->query("INSERT INTO seg_ep1 (id_seguimiento, id_modulo, id_pregunta, ep1)
            VALUES (:id_seguimiento, :id_modulo, :id_pregunta, :ep1);");
            $this->db->bind(":id_seguimiento",$id_seguimiento[0]->id_seguimiento);
            $this->db->bind(":id_pregunta",$ep1->pregunta);
            $this->db->bind(":id_modulo",$id_modulo);
            $this->db->bind(":ep1",$ep1->ep1);
            $this->db->execute();
         }
         return true;

   } else {

         // siempre se va a actualizar seg_preguntas_seguimiento pero seg_ep1_real solo si edicion = 0
         // ya que seran los valores del diario

         $this->db->query("SELECT COUNT(*) as total FROM seg_ep1_mes
         WHERE id_modulo = :id AND edicion_mes = 1");
         $this->db->bind(':id', $id_modulo);
         $hay_edicion = $this->db->registros();


         if($hay_edicion[0]->total==0){

               foreach($ep1_x_mes as $ep1){

                  $this->db->query("UPDATE seg_seguimiento_preguntas SET respuesta=:respuesta
                  WHERE id_seguimiento=:id_seguimiento AND id_pregunta=:id_pregunta;");
                  $this->db->bind(":id_seguimiento",$id_seguimiento[0]->id_seguimiento);
                  $this->db->bind(":id_pregunta",$ep1->pregunta);
                  $this->db->bind(":respuesta",$ep1->ep1);
                  $this->db->execute();
      
                  $this->db->query("UPDATE seg_ep1_real SET ep1=:ep1
                  WHERE id_seguimiento=:id_seguimiento 
                  AND id_pregunta=:id_pregunta
                  AND id_modulo=:id_modulo;");
                  $this->db->bind(":id_seguimiento",$id_seguimiento[0]->id_seguimiento);
                  $this->db->bind(":id_pregunta",$ep1->pregunta);
                  $this->db->bind(":id_modulo",$id_modulo);
                  $this->db->bind(":ep1",$ep1->ep1);
                  $this->db->execute();

                  $this->db->query("UPDATE seg_ep1 SET ep1=:ep1
                  WHERE id_seguimiento=:id_seguimiento 
                  AND id_pregunta=:id_pregunta
                  AND id_modulo=:id_modulo;");
                  $this->db->bind(":id_seguimiento",$id_seguimiento[0]->id_seguimiento);
                  $this->db->bind(":id_pregunta",$ep1->pregunta);
                  $this->db->bind(":id_modulo",$id_modulo);
                  $this->db->bind(":ep1",$ep1->ep1);
                  $this->db->execute();

               }

         } else {

            foreach($ep1_x_mes as $ep1){

               $this->db->query("UPDATE seg_seguimiento_preguntas SET respuesta=:respuesta
               WHERE id_seguimiento=:id_seguimiento AND id_pregunta=:id_pregunta;");
               $this->db->bind(":id_seguimiento",$id_seguimiento[0]->id_seguimiento);
               $this->db->bind(":id_pregunta",$ep1->pregunta);
               $this->db->bind(":respuesta",$ep1->ep1);
               $this->db->execute();
   
            }

         }
    
         return true;
   }

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





//***********************************************************************/
// INSERCION PARA LA TABLA ACUMULADAS EDITABLE, SI SE MODIFICAN VALORES 
//***********************************************************************/


public function nuevo_ep1($acumuladas, $ep1, $id_alto, $id_modulo, $id_lectivo) {


   $this->db->query("DELETE FROM seg_ep1
   WHERE id_seguimiento = :id_seguimiento AND id_modulo=:id_modulo;");
   $this->db->bind(":id_seguimiento", $id_alto[0]->ep1);
   $this->db->bind(":id_modulo",$id_modulo);
   $this->db->execute();

   $this->db->query("DELETE FROM seg_ep1_mes
   WHERE id_seguimiento = :id_seguimiento AND id_modulo=:id_modulo;");
   $this->db->bind(":id_seguimiento", $id_alto[0]->ep1);
   $this->db->bind(":id_modulo",$id_modulo);
   $this->db->execute();


   for ($i = 0; $i < sizeof($ep1); $i++) {

      $this->db->query("INSERT INTO seg_ep1 (id_seguimiento, id_modulo, id_pregunta, ep1)
      VALUES (:id_seguimiento, :id_modulo, :id_pregunta, :ep1);");
      $this->db->bind(":id_seguimiento", $id_alto[0]->ep1);
      $this->db->bind(":id_modulo",$id_modulo);
      $this->db->bind(":id_pregunta", $ep1[$i]['id_pregunta']);
      $this->db->bind(":ep1", $ep1[$i]['ep1']);
      $this->db->execute();


      $this->db->query("INSERT INTO seg_ep1_mes (id_seguimiento, id_modulo, id_pregunta, ajustes, contenidos_impartidos, ep1, edicion_mes) 
      VALUES (:id_seguimiento, :id_modulo, :id_pregunta, :ajustes, :contenidos_impartidos, :ep1, 1)");
      $this->db->bind(":id_seguimiento", $id_alto[0]->ep1);
      $this->db->bind(":id_modulo", $id_modulo);
      $this->db->bind(":id_pregunta", $ep1[$i]['id_pregunta']);
      $this->db->bind(":ajustes", $ep1[$i]['ajustes']);
      $this->db->bind(":contenidos_impartidos", $ep1[$i]['contenidos']);
      $this->db->bind(":ep1", $ep1[$i]['ep1']);
      $this->db->execute();

      $id = $this->db->ultimoIndice();

      foreach ($acumuladas as $temas) {
         if($temas['id_pregunta'] == $ep1[$i]['id_pregunta']){
            $this->db->query("INSERT INTO seg_ep1_tema (id_ep1_mes, id_tema, id_pregunta, horas_acumuladas, edicion_tema) 
            VALUES (:id, :id_tema, :id_pregunta, :horas_acumuladas, 1)");
            $this->db->bind(":id", $id);
            $this->db->bind(":id_tema", $temas['id_tema']);
            $this->db->bind(":id_pregunta", $temas['id_pregunta']);
            $this->db->bind(":horas_acumuladas", $temas['horas_acumuladas']);
     
            $this->db->execute();
         }

      }

   }

   return true;
}





 public function insertar_media_ep1($media, $info_modulo, $lectivo){


   $id_grado = $info_modulo[0]->id_grado;
   $this->db->query("SELECT `seg_indicadores`.`id_indicador`,`indicador`,`indicador_corto`, `cpifp_grados`.`id_grado`, 
   `cpifp_grados`.`nombre` AS `nombre_grado`, `porcentaje`, id_lectivo
   FROM `seg_indicadores`, `cpifp_grados`, `seg_indicadores_grados`
   WHERE `seg_indicadores`.`id_indicador`=`seg_indicadores_grados`.`id_indicador` 
   AND `cpifp_grados`.`id_grado`=`seg_indicadores_grados`.`id_grado`
   AND seg_indicadores_grados.id_grado=:id_grado
   AND seg_indicadores.id_indicador=6
   AND seg_indicadores_grados.id_lectivo = :id_lectivo;");
   $this->db->bind(':id_grado',$id_grado);
   $this->db->bind(':id_lectivo', $lectivo[0]->id_lectivo);
   $info_indicador = $this->db->registros();


   $this->db->query("SELECT * FROM his_total_modulo
   WHERE id_modulo = :id_modulo 
   AND id_indicador=6
   AND id_lectivo = :id_lectivo;");
   $this->db->bind(':id_modulo', $info_modulo[0]->id_modulo);
   $this->db->bind(':id_lectivo', $lectivo[0]->id_lectivo);
   $hay_datos = $this->db->registros();


   if(empty($hay_datos)){

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

            $this->db->bind(':total', $media[0]->ep1);

            if($media[0]->ep1 >= $info_indicador[0]->porcentaje){
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

    }else{

         $this->db->query("UPDATE his_total_modulo SET total=:total , modulo_conforme=:modulo_conforme
         WHERE id_modulo=:id_modulo 
         AND id_indicador = :id_indicador 
         AND id_lectivo=:id_lectivo;");

         $this->db->bind(':id_modulo', $info_modulo[0]->id_modulo);
         $this->db->bind(":id_lectivo", $lectivo[0]->id_lectivo);
         $this->db->bind(":total", $media[0]->ep1);
         $this->db->bind(':id_indicador', $info_indicador[0]->id_indicador);

         if($media[0]->ep1 >= $info_indicador[0]->porcentaje){
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



/****************************************************/
/****************************************************/
/************ RESTAURAR TABLA ***********************/
/****************************************************/
/****************************************************/


// TRAE LOS VALORES DE EP1 DE SEG_SEGUIMIENTO_PREGUNTAS
public function valores_ep1 ($id_modulo){

   $this->db->query("SELECT MAX(id_seguimiento) as id_seguimiento
   FROM `seg_seguimiento_modulo` WHERE id_modulo=:id_modulo;");
   $this->db->bind(":id_modulo",$id_modulo);
   $id_seguimiento = $this->db->registros();

   $this->db->query("SELECT * 
   FROM seg_seguimiento_preguntas 
   WHERE id_seguimiento = :id_seguimiento and id_pregunta BETWEEN 47 and 55;");
   $this->db->bind(":id_seguimiento",$id_seguimiento[0]->id_seguimiento);
   return $this->db->registros();
}



public function real_mes($id_modulo){
   $this->db->query("SELECT * 
   FROM `seg_ep1_real`
   WHERE id_modulo=:id_modulo;");
   $this->db->bind(":id_modulo",$id_modulo);
   return $this->db->registros();
}


// PARA MEDIA E1 (es el valor de junio)

public function real_mes_junio($id_modulo){
   $this->db->query("SELECT * 
   FROM `seg_ep1_real`
   WHERE id_modulo=:id_modulo
   AND id_pregunta=55;");
   $this->db->bind(":id_modulo",$id_modulo);
   return $this->db->registros();
}

public function edicion_mes_junio($id_modulo){
   $this->db->query("SELECT * 
   FROM `seg_ep1_mes`
   WHERE id_modulo=:id_modulo
   AND id_pregunta=55;");
   $this->db->bind(":id_modulo",$id_modulo);
   return $this->db->registros();
}






/****************************************************/
/****************************************************/
/***************************************************/
/****************************************************/
/****************************************************/



public function acumuladas($id_modulo) {

   $this->db->query("SELECT id_modulo, id_tema, YEAR(fecha) AS año,
      CASE 
         WHEN MONTH(fecha) = 9 THEN 10 
         ELSE MONTH(fecha)
      END AS mes,
      SUM(horas_dia) AS total_horas
      FROM seg_seguimiento_temas 
      WHERE id_modulo = :id_modulo
      GROUP BY id_tema, YEAR(fecha), 
         CASE 
            WHEN MONTH(fecha) = 9 THEN 10  
            ELSE MONTH(fecha)
         END
      ORDER BY año ASC, mes ASC;");
   $this->db->bind(":id_modulo",$id_modulo);
   $valores = $this->db->registros();

   
   // Inicializamos estructura
   $temas = [];
   $meses = [10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre', 1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio'];

   // Preparamos acumuladores por tema
   $acumulados = [];
   foreach ($valores as $fila) {
      $tema = $fila->id_tema;
      $mes = $fila->mes;
      $horas = $fila->total_horas;

      if (!isset($acumulados[$tema])) {
         $acumulados[$tema] = [];
      }
      $acumulados[$tema][$mes] = $horas;
   }

   // Ahora generamos la tabla acumulada por mes
   $tabla_acumuladas = [];

   $temas_ids = array_keys($acumulados);
   sort($temas_ids); // Para que los temas vayan ordenados
   $suma_acumulada = [];

   foreach ($meses as $num_mes => $nombre_mes) {
      $fila = ['Fecha' => $nombre_mes];
      $total_mes = 0;

      foreach ($temas_ids as $tema) {
         if (!isset($suma_acumulada[$tema])) {
            $suma_acumulada[$tema] = 0;
         }
         $suma_acumulada[$tema] += $acumulados[$tema][$num_mes] ?? 0;
         $fila["Tem.$tema"] = $suma_acumulada[$tema];
         $total_mes += $acumulados[$tema][$num_mes] ?? 0;
      }

      $fila['Total Mes'] = $total_mes;
      $tabla_acumuladas[] = $fila;
   }


   return $tabla_acumuladas;
}

































}