<?php

class pProgramacionM{

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




// trae la programacion activa del modulo
public function obtener_programacion_modulo($id_modulo){
   $this->db->query("SELECT * FROM seg_programaciones 
   WHERE id_modulo=:id_modulo 
   AND activa=1;");
   $this->db->bind(":id_modulo",$id_modulo);
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


// cuenta los temas que hay
public function hay_temas($id_modulo){
   $this->db->query("SELECT COUNT(*) as hay_temas FROM seg_temas 
   WHERE id_modulo = :id_modulo;");
   $this->db->bind(":id_modulo",$id_modulo);
   return $this->db->registros();
}


// cuenta las horas que hay a la semana
public function hay_horas($id_modulo){
   $this->db->query("SELECT COUNT(*) as hay_horas FROM seg_horario_modulo 
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
/************************ REFERENTE A LAS PROGRAMACIONES *************************/
//********************************************************************************/
//********************************************************************************/



// trae la programacion ULTIMA EDICION
public function ultima_edicion($id_modulo){
   $this->db->query("SELECT * FROM `seg_programaciones` 
   WHERE id_modulo=:id_modulo 
   AND num_version = (SELECT MAX(num_version) FROM seg_programaciones WHERE id_modulo=:id_modulo);");
   $this->db->bind(":id_modulo",$id_modulo);
   return $this->db->registros();
}


// se usa cuando un modulo no tienen ninguna progaramacion
// public function subir_programacion($programacion, $id_modulo){
//    $this->db->query("INSERT INTO seg_programaciones (id_modulo, id_profesor, id_lectivo, 
//    codigo_programacion, fecha, ruta, num_version, id_programacion_base, nueva, editada,
//    activa, codigo_verificacion, verificada_profesor, verificada_jefe_dep) 
//    VALUES (:id_modulo, :id_profesor, :id_lectivo, :codigo_programacion, :fecha, :ruta, :num_version, :id_programacion_base, :nueva, :editada,
//    :activa, :codigo_verificacion, :verificada_profesor, :verificada_jefe_dep);");
//    $this->db->bind(':id_modulo',$id_modulo);
//    $this->db->bind(':id_profesor',$programacion['id_profesor']);
//    $this->db->bind(':id_lectivo',$programacion['id_lectivo']);
//    $this->db->bind(':codigo_programacion',$programacion['programacion']);
//    $this->db->bind(':fecha',$programacion['fecha']);
//    $this->db->bind(':ruta',$programacion['ruta']);
//    $this->db->bind(':num_version','');
//    $this->db->bind(':id_programacion_base',null);
//    $this->db->bind(':nueva',1);
//    $this->db->bind(':editada',0);
//    $this->db->bind(':activa',1);
//    $this->db->bind(':codigo_verificacion','');
//    $this->db->bind(':verificada_profesor',0);
//    $this->db->bind(':verificada_jefe_dep',0);
//    if ($this->db->execute()){
//       return true;
//    }else{
//       return false;
//    }
// }




// se usa cuando cambia una programacion
public function cambia_programacion($programacion, $id_modulo){

      //recogemos todas las programaciones del modulo y ponemos a cero tanto nueva como activa
      $this->db->query("SELECT * FROM seg_programaciones 
      WHERE id_modulo=:id_modulo;");
      $this->db->bind(":id_modulo",$id_modulo);
      $programaciones = $this->db->registros();

      foreach($programaciones as $program):
         $this->db->query("UPDATE seg_programaciones SET nueva= 0, activa= 0 
         WHERE id_modulo=:id_modulo;");
         $this->db->bind(':id_modulo',$id_modulo);
         $this->db->execute();    
      endforeach;

      $this->db->query("INSERT INTO seg_programaciones (id_modulo, id_profesor, id_lectivo, 
      codigo_programacion, fecha, ruta, num_version, id_programacion_base, nueva, editada,
      activa, codigo_verificacion, verificada_profesor, verificada_jefe_dep) 
      VALUES (:id_modulo, :id_profesor, :id_lectivo, :codigo_programacion, :fecha, :ruta, :num_version, :id_programacion_base, :nueva, :editada,
      :activa, :codigo_verificacion, :verificada_profesor, :verificada_jefe_dep);");
      $this->db->bind(':id_modulo',$id_modulo);
      $this->db->bind(':id_profesor',$programacion['id_profesor']);
      $this->db->bind(':id_lectivo',$programacion['id_lectivo']);
      $this->db->bind(':codigo_programacion',$programacion['programacion']);
      $this->db->bind(':fecha',$programacion['fecha']);
      $this->db->bind(':ruta',$programacion['ruta']);
      $this->db->bind(':num_version',$programacion['edicion']);
      $this->db->bind(':id_programacion_base',null);
      $this->db->bind(':nueva',1);
      $this->db->bind(':editada',0);
      $this->db->bind(':activa',1);
      $this->db->bind(':codigo_verificacion','');
      $this->db->bind(':verificada_profesor',0);
      $this->db->bind(':verificada_jefe_dep',0);
      $this->db->execute();    

      $this->db->query("UPDATE cpifp_profesor_modulo SET cambia_programacion=1 
      WHERE id_modulo=:id_modulo
      AND id_lectivo = :id_lectivo;");
      $this->db->bind(':id_modulo',$id_modulo);
      $this->db->bind(':id_lectivo',$programacion['id_lectivo']);
      if ($this->db->execute()){
         return true;
      }else{
         return false;
      }
}



// se usa para cuando no va a cambiar la programacion. Pone nueva a cero
public function no_cambia_programacion($programacion, $id_modulo){

      $this->db->query("SELECT * FROM seg_programaciones 
      WHERE id_modulo = :id_modulo
      AND num_version = (SELECT MAX(num_version) FROM seg_programaciones 
      WHERE id_modulo = :id_modulo);");
      $this->db->bind(":id_modulo",$id_modulo);
      $max_edicion = $this->db->registros();

      // $this->db->query("UPDATE seg_programaciones SET nueva=0, activa=0
      // WHERE id_modulo=:id_modulo AND num_version=:num_version;");
      // $this->db->bind(':num_version',$max_edicion[0]->num_version);
      // $this->db->bind(':id_modulo',$id_modulo);
      // $this->db->execute(); 

      $this->db->query("INSERT INTO seg_programaciones (id_modulo, id_profesor, id_lectivo, 
      codigo_programacion, fecha, ruta, num_version, id_programacion_base, nueva, editada,
      activa, codigo_verificacion, verificada_profesor, verificada_jefe_dep) 
      VALUES (:id_modulo, :id_profesor, :id_lectivo, :codigo_programacion, :fecha, :ruta, :num_version, :id_programacion_base, :nueva, :editada,
      :activa, :codigo_verificacion, :verificada_profesor, :verificada_jefe_dep);");
      $this->db->bind(':id_modulo',$id_modulo);
      $this->db->bind(':id_profesor',$max_edicion[0]->id_profesor);
      $this->db->bind(':id_lectivo',$programacion['id_lectivo']);
      $this->db->bind(':codigo_programacion',$programacion['programacion']);
      $this->db->bind(':fecha',$programacion['fecha']);
      $this->db->bind(':ruta',$programacion['ruta']);
      $this->db->bind(':num_version',$max_edicion[0]->num_version);
      $this->db->bind(':id_programacion_base',$max_edicion[0]->id_programacion);
      $this->db->bind(':nueva',0);
      $this->db->bind(':editada',0);
      $this->db->bind(':activa',1);
      $this->db->bind(':codigo_verificacion','');
      $this->db->bind(':verificada_profesor',0);
      $this->db->bind(':verificada_jefe_dep',0);
      $this->db->execute();    

      $this->db->query("UPDATE cpifp_profesor_modulo SET cambia_programacion=0 
      WHERE id_modulo=:id_modulo
      AND id_lectivo = :id_lectivo;");
      $this->db->bind(':id_modulo',$id_modulo);
      $this->db->bind(':id_lectivo',$programacion['id_lectivo']);
      if ($this->db->execute()){
         return true;
      }else{
         return false;
      }
}



public function es_mismo_anio($id_modulo, $id_lectivo){
      $this->db->query("UPDATE cpifp_profesor_modulo 
      SET cambia_programacion=0 
      WHERE id_modulo=:id_modulo
      AND id_lectivo = :id_lectivo;");
      $this->db->bind(':id_modulo',$id_modulo);
      $this->db->bind(':id_lectivo',$id_lectivo);
      if ($this->db->execute()){
         return true;
      }else{
         return false;
      }
}






// envio codigo verificacion para programacion
public function enviar_codigo_verificacion($id_modulo){
   $this->db->query("UPDATE seg_programaciones SET verificada_profesor = 1
   WHERE id_modulo=:id_modulo AND activa=1;");
   $this->db->bind(':id_modulo',$id_modulo);
   $this->db->execute(); 
   if ($this->db->execute()){
      return true;
   }else{
      return false;
   }
}




























}