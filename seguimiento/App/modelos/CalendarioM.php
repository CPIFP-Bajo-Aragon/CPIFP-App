<?php

class CalendarioM{
    private $db;


    public function __construct(){
        $this->db = new Base;
    }



// TRAE EL CURSO LECTIVO ACTIVO
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


// CALENDARIO DEL MODULO CON FECHAS EVALUACION , DIAS DE CLASE Y TODO
public function calendario_bueno_modulo($id_modulo, $id_lectivo){
   $this->db->query("SELECT c.id_calendario, c.id_lectivo, DATE_FORMAT(c.fecha, '%d-%m-%Y') AS fecha, c.dia_semana, 
   CASE 
      WHEN f.id_calendario IS NOT NULL OR (c.dia_semana = 'D' OR c.dia_semana = 'S') THEN 'Sí' 
      ELSE 'No' 
   END AS es_festivo, f.id_festivo,
   CASE 
      WHEN ds.dia_corto = c.dia_semana AND hm.horas_dia > 0 THEN 'Sí' 
      ELSE 'No' 
   END AS tiene_clase, hm.horas_dia,cev.evaluacion, ev.id_evaluacion, ev.id_grado, ev.id_numero
   FROM seg_calendario c
   LEFT JOIN seg_festivos f ON c.id_calendario = f.id_calendario
   LEFT JOIN seg_dias_semana ds ON c.dia_semana = ds.dia_corto
   LEFT JOIN seg_horario_modulo hm ON ds.id_dia_semana = hm.id_dia_semana
   INNER JOIN cpifp_modulo cm ON cm.id_modulo = hm.id_modulo
   INNER JOIN cpifp_curso cp ON cp.id_curso = cm.id_curso
   INNER JOIN cpifp_ciclos ci ON ci.id_ciclo = cp.id_ciclo
   INNER JOIN cpifp_grados cg ON cg.id_grado = ci.id_grado
   LEFT JOIN seg_evaluaciones ev ON c.id_calendario = ev.id_calendario 
      AND ev.id_grado = cg.id_grado 
      AND ev.id_numero = cp.id_numero 
      AND ev.id_turno = ci.id_turno  
   LEFT JOIN cpifp_evaluaciones cev ON cev.id_evaluacion = ev.id_evaluacion
   WHERE cm.id_modulo = :id_modulo
   AND c.id_lectivo = :id_lectivo
   ORDER BY c.fecha;");
   $this->db->bind(':id_modulo',$id_modulo);
   $this->db->bind(':id_lectivo',$id_lectivo);
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




}