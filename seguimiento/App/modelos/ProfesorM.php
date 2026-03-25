<?php

class ProfesorM{
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



// MODULOS DEL AÑO ACTUAL
public function obtener_modulos($id_profe, $id_lectivo) {
   $this->db->query("SELECT cpifp_profesor.id_profesor, cpifp_profesor.nombre_completo,
   cpifp_modulo.id_modulo,cpifp_modulo.modulo,cpifp_modulo.nombre_corto, cpifp_modulo.horas_totales, cpifp_modulo.cuerpo, 
   cpifp_modulo.horas_semanales, cpifp_modulo.id_departamento AS departamento_modulo,
   cpifp_profesor_modulo.horas_profesor,cpifp_modulo.id_curso, 
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
   AND id_lectivo=:id_lectivo;");
   $this->db->bind(':id_profe', $id_profe);
   $this->db->bind(':id_lectivo',$id_lectivo);  
   return $this->db->registros();
}


//*************************/
// PARA CAPAR LOS ENLACES
//*************************/

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


public function codigo_verificacion($id_profe, $id_lectivo) {
   $this->db->query("SELECT * FROM seg_programaciones, cpifp_profesor_modulo
   WHERE seg_programaciones.id_modulo = cpifp_profesor_modulo.id_modulo
   AND cpifp_profesor_modulo.id_lectivo = :id_lectivo
   AND cpifp_profesor_modulo.id_profesor = :id_profe;");
   $this->db->bind(':id_lectivo',$id_lectivo);  
   $this->db->bind(':id_profe', $id_profe);
   return $this->db->registros();
}


public function tiene_programacion($id_profe, $id_lectivo) {
   $this->db->query("SELECT m.id_modulo, m.modulo,
      CASE WHEN p.id_programacion IS NOT NULL THEN 1 ELSE 0 END AS tiene_programacion
   FROM cpifp_profesor_modulo pm
   INNER JOIN cpifp_modulo m ON pm.id_modulo = m.id_modulo
   LEFT JOIN seg_programaciones p ON p.id_modulo = m.id_modulo AND p.activa = 1  
   WHERE pm.id_profesor = :id_profe
   AND pm.id_lectivo = :id_lectivo
   GROUP BY m.id_modulo;");
   $this->db->bind(':id_lectivo',$id_lectivo);  
   $this->db->bind(':id_profe', $id_profe);
   return $this->db->registros();
}



}