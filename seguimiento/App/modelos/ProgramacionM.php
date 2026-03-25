<?php

class ProgramacionM{

    private $db;

    public function __construct(){
        $this->db = new Base;
    }




// TRAE EL CURSO LECTIVO QUE NO ESTA CERRADO
public function obtener_lectivo(){
    $this->db->query("SELECT id_lectivo, lectivo, date_format(fecha_inicio,'%d-%m-%Y') AS fecha_inicio, 
    date_format(fecha_fin,'%d-%m-%Y') AS fecha_fin, 
     cerrado FROM seg_lectivos WHERE cerrado = 0");
    return $this->db->registros();
}


public function nuevas_por_departamento($id_lectivo){
    $this->db->query("SELECT sdm.id_departamento, sdm.departamento, COUNT(DISTINCT p.id_modulo) AS suma
    FROM segui_departamento_modulo sdm
    JOIN seg_programaciones p ON sdm.id_modulo = p.id_modulo
    WHERE p.nueva = 1
    AND p.id_lectivo = :id_lectivo
    GROUP BY sdm.id_departamento, sdm.departamento
    ORDER BY sdm.departamento;");
    $this->db->bind(':id_lectivo',$id_lectivo);
    return $this->db->registros();
}


public function verificadas_por_departamento($id_lectivo){
    $this->db->query("SELECT sdm.id_departamento, sdm.departamento, COUNT(DISTINCT p.id_modulo) AS suma
    FROM segui_departamento_modulo sdm
    JOIN seg_programaciones p ON sdm.id_modulo = p.id_modulo
    WHERE p.nueva = 1 AND verificada_profesor=1
    AND p.id_lectivo = :id_lectivo
    GROUP BY sdm.id_departamento, sdm.departamento
    ORDER BY sdm.departamento;");
    $this->db->bind(':id_lectivo',$id_lectivo);
    return $this->db->registros();
}



public function departamentos_formacion(){
    $this->db->query("SELECT * FROM cpifp_departamento 
    WHERE isFormacion=1;");
    return $this->db->registros();
}



//*****************************************/
// VISTA: CICLOS de un departamento
//***************************************/


 public function departamento_por_id($id_dep){
    $this->db->query("SELECT * FROM cpifp_departamento
    WHERE id_departamento=:id_departamento;");
    $this->db->bind(':id_departamento', $id_dep);
    return $this->db->registros();
 }
 


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


public function nuevas_por_ciclo($id_dep,$id_lectivo){
    $this->db->query("SELECT m.id_departamento,  m.departamento,m.id_ciclo,COUNT(*) AS suma     
    FROM segui_departamento_modulo m
    JOIN seg_programaciones p ON p.id_modulo = m.id_modulo
    WHERE p.nueva = 1
    AND id_lectivo=:id_lectivo
    AND m.id_departamento =:id_departamento
    GROUP BY m.id_departamento, m.id_ciclo;;");
    $this->db->bind(':id_departamento',$id_dep);
    $this->db->bind(':id_lectivo',$id_lectivo);
    return $this->db->registros();
}


public function verificadas_por_ciclo($id_dep,$id_lectivo){
    $this->db->query("SELECT m.id_departamento, m.departamento,m.id_ciclo,COUNT(*) AS suma     
    FROM segui_departamento_modulo m
    JOIN seg_programaciones p ON p.id_modulo = m.id_modulo
    WHERE p.nueva = 1 AND verificada_profesor=1
    AND id_lectivo=:id_lectivo
    AND m.id_departamento=:id_departamento
    GROUP BY m.id_departamento, m.id_ciclo;;");
    $this->db->bind(':id_departamento',$id_dep);
    $this->db->bind(':id_lectivo',$id_lectivo);
    return $this->db->registros();
}



//*****************************************/
// VISTAS: todos MODULOS de un ciclo
//***************************************/


// todos los modulos de un ciclo incluido FOL y LEO
public function modulos_ciclo($id_ciclo){
    $this->db->query("SELECT * FROM segui_departamento_modulo 
    WHERE id_ciclo=:id_ciclo;");
    $this->db->bind(':id_ciclo',$id_ciclo);
    return $this->db->registros();
}



public function numero_programaciones_ciclo($id_ciclo, $id_lectivo){
    $this->db->query("SELECT sdm.*, COUNT(sp.id_programacion) AS total_programaciones, sp.id_lectivo
    FROM segui_departamento_modulo sdm
    JOIN cpifp_ciclos c ON sdm.id_ciclo = c.id_ciclo
    LEFT JOIN seg_programaciones sp ON sp.id_modulo = sdm.id_modulo
    LEFT JOIN cpifp_profesor p ON sp.id_profesor = p.id_profesor
    WHERE sdm.id_ciclo = :id_ciclo
    AND sdm.id_departamento = c.id_departamento
    AND sp.id_lectivo = :id_lectivo
    GROUP BY sdm.id_modulo, sdm.id_ciclo, sdm.id_departamento;");
    $this->db->bind(':id_ciclo',$id_ciclo);
    $this->db->bind(':id_lectivo',$id_lectivo);
    return $this->db->registros();
}




public function programaciones_ediciones_anteriores($id_ciclo){
    $this->db->query("SELECT p1.*, sdm.*, cpf.*         
    FROM seg_programaciones p1
    JOIN segui_departamento_modulo sdm ON p1.id_modulo = sdm.id_modulo
    LEFT JOIN cpifp_profesor cpf ON p1.id_profesor = cpf.id_profesor 
    WHERE p1.id_programacion = (
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
    AND sdm.id_ciclo = :id_ciclo;");
    $this->db->bind(':id_ciclo',$id_ciclo);
    return $this->db->registros();
}



// solo las activas de cada modulo de un CICLO, sin importar el AÑO
public function programaciones_modulos_activas($id_ciclo){
    $this->db->query("SELECT * FROM segui_departamento_modulo, seg_programaciones
    WHERE seg_programaciones.id_modulo=segui_departamento_modulo.id_modulo 
    AND segui_departamento_modulo.id_ciclo=:id_ciclo
    AND seg_programaciones.activa=1;");
    $this->db->bind(':id_ciclo',$id_ciclo);
    return $this->db->registros();
}


// todas las programaciones de cada modulo de un CICLO, DEL AÑO ANTERIOR
public function programaciones_modulos_anio_anterior($id_ciclo, $id_lectivo){
    $this->db->query("SELECT * FROM segui_departamento_modulo, seg_programaciones
    WHERE seg_programaciones.id_modulo=segui_departamento_modulo.id_modulo 
    AND segui_departamento_modulo.id_ciclo=:id_ciclo
    AND seg_programaciones.id_lectivo = (
            SELECT id_lectivo
            FROM seg_lectivos WHERE id_lectivo < :id_lectivo ORDER BY id_lectivo DESC LIMIT 1);");
    $this->db->bind(':id_ciclo',$id_ciclo);
    $this->db->bind(':id_lectivo',$id_lectivo);
    return $this->db->registros();
}


// profesores AÑO ANTERIOR
public function profesor_anterior($id_ciclo, $id_lectivo){
    $this->db->query("SELECT * 
    FROM segui_departamento_modulo
    JOIN cpifp_profesor_modulo ON segui_departamento_modulo.id_modulo = cpifp_profesor_modulo.id_modulo
    JOIN cpifp_profesor ON cpifp_profesor_modulo.id_profesor = cpifp_profesor.id_profesor
    WHERE cpifp_profesor_modulo.id_lectivo = (
            SELECT id_lectivo
            FROM seg_lectivos WHERE id_lectivo < :id_lectivo ORDER BY id_lectivo DESC LIMIT 1)
    AND segui_departamento_modulo.id_ciclo = :id_ciclo;");
    $this->db->bind(':id_ciclo',$id_ciclo);
    $this->db->bind(':id_lectivo',$id_lectivo);
    return $this->db->registros();
}


// profesor año actual
public function profesor_modulo_ciclo($id_ciclo, $id_lectivo){
    $this->db->query("SELECT * FROM segui_departamento_modulo, cpifp_profesor_modulo, cpifp_profesor
    WHERE segui_departamento_modulo.id_modulo=cpifp_profesor_modulo.id_modulo
    AND cpifp_profesor.id_profesor=cpifp_profesor_modulo.id_profesor
    AND segui_departamento_modulo.id_ciclo=:id_ciclo
    AND cpifp_profesor_modulo.id_lectivo=:id_lectivo;");
    $this->db->bind(':id_ciclo',$id_ciclo);
    $this->db->bind(':id_lectivo',$id_lectivo);
    return $this->db->registros();
}



//*********************************************/
// VISTA: PROGRAMACIONES un MODULO concreto
//*********************************************/


public function un_modulo($id_modulo){
    $this->db->query("SELECT * FROM segui_departamento_modulo 
    WHERE id_modulo=:id_modulo;");
    $this->db->bind(':id_modulo',$id_modulo);
    return $this->db->registros();
}


// TRAE LAS PROGRAMACIONES DE UN MODULO
public function programaciones_modulo($id_modulo){
    $this->db->query("SELECT m.id_modulo, m.modulo, m.nombre_corto, m.horas_totales, m.cuerpo, m.id_curso, m.horas_semanales, m.id_departamento, 
    sp.id_programacion, sp.codigo_programacion, sp.fecha, sp.ruta, sp.nueva, sp.activa, sp.num_version, sp.id_profesor, p.nombre_completo
    FROM cpifp_modulo m
    LEFT JOIN seg_programaciones sp ON m.id_modulo = sp.id_modulo
    LEFT JOIN cpifp_profesor p ON sp.id_profesor = p.id_profesor
    WHERE m.id_modulo = :id_modulo");
    $this->db->bind(':id_modulo',$id_modulo);
    return $this->db->registros();
}






/*********************** CODIGO VERIFICACION ********************************/


public function codigo_verificacion($id_modulo, $codigo_verificacion){
    $this->db->query("UPDATE seg_programaciones SET codigo_verificacion = :codigo_verificacion
    WHERE id_modulo = :id_modulo AND activa = 1;");
    $this->db->bind(':id_modulo',$id_modulo);
    $this->db->bind(':codigo_verificacion',$codigo_verificacion);
    if ($this->db->execute()){
        return true;
    }else{
        return false;
    }
}


/*********************** SUBIR PROGRAMACION ********************************/

public function ultima_edicion($id_modulo){
    $this->db->query("SELECT * FROM `seg_programaciones` 
    WHERE id_modulo=:id_modulo 
    AND num_version = (SELECT MAX(num_version) FROM seg_programaciones WHERE id_modulo=:id_modulo);");
    $this->db->bind(":id_modulo",$id_modulo);
    return $this->db->registros();
 }
 

 public function profesor_modulo($id_modulo, $id_lectivo){
    $this->db->query("SELECT * FROM cpifp_profesor_modulo 
    WHERE id_modulo=:id_modulo 
    AND id_lectivo = :id_lectivo;");
    $this->db->bind(":id_modulo",$id_modulo);
    $this->db->bind(":id_lectivo",$id_lectivo);
    return $this->db->registros();
 }
 



 public function subir_programacion($programacion){

        $id_modulo = $programacion['id_modulo'];
        $this->db->query("SELECT * FROM seg_programaciones 
        WHERE id_modulo=:id_modulo;");
        $this->db->bind(':id_modulo',$id_modulo);
        $hay_valor = $this->db->registros();

        if(empty($hay_valor)){


            $this->db->query("INSERT INTO seg_programaciones (id_modulo, id_lectivo, id_profesor,
            codigo_programacion, fecha, ruta, num_version, id_programacion_base, nueva, editada,
            activa, codigo_verificacion, verificada_profesor, verificada_jefe_dep) 
            VALUES (:id_modulo, :id_lectivo,:id_profesor, :codigo_programacion, :fecha, :ruta, :num_version, :id_programacion_base, :nueva, :editada,
            :activa, :codigo_verificacion, :verificada_profesor, :verificada_jefe_dep);");
            $this->db->bind(':id_modulo',$id_modulo);
            $this->db->bind(':id_lectivo',$programacion['id_lectivo']);
            $this->db->bind(':id_profesor', null); 
            $this->db->bind(':codigo_programacion',$programacion['codigo_programacion']);
            $this->db->bind(':fecha',$programacion['fecha']);
            $this->db->bind(':ruta',$programacion['ruta']);
            $this->db->bind(':num_version',$programacion['num_version']);
            $this->db->bind(':id_programacion_base',null);
            $this->db->bind(':nueva',$programacion['nueva']);
            $this->db->bind(':editada',0);
            $this->db->bind(':activa',1);
            $this->db->bind(':codigo_verificacion','');
            $this->db->bind(':verificada_profesor',0);
            $this->db->bind(':verificada_jefe_dep',0);

            if ($this->db->execute()){
                return true;
            }else{
                return false;
            }

        } else{

            for($i=0;$i<sizeof($hay_valor);$i++){
                $this->db->query("UPDATE seg_programaciones SET nueva=0 , activa=0
                WHERE id_modulo=:id_modulo
                AND id_lectivo=:id_lectivo;");
                $this->db->bind(':id_modulo',$id_modulo);
                $this->db->bind(':id_lectivo',$programacion['id_lectivo']);
                $this->db->execute();
            }

            $this->db->query("INSERT INTO seg_programaciones (id_modulo, id_lectivo, id_profesor
            codigo_programacion, fecha, ruta, num_version, id_programacion_base, nueva, editada,
            activa, codigo_verificacion, verificada_profesor, verificada_jefe_dep) 
            VALUES (:id_modulo, :id_lectivo, :id_profesor, :codigo_programacion, :fecha, :ruta, :num_version, :id_programacion_base, :nueva, :editada,
            :activa, :codigo_verificacion, :verificada_profesor, :verificada_jefe_dep);");
            $this->db->bind(':id_modulo',$id_modulo);
            $this->db->bind(':id_lectivo',$programacion['id_lectivo']);
            $this->db->bind(':id_profesor', null); 
            $this->db->bind(':codigo_programacion',$programacion['codigo_programacion']);
            $this->db->bind(':fecha',$programacion['fecha']);
            $this->db->bind(':ruta',$programacion['ruta']);
            $this->db->bind(':num_version',$programacion['num_version']);
            $this->db->bind(':id_programacion_base',null);
            $this->db->bind(':nueva',$programacion['nueva']);
            $this->db->bind(':editada',0);
            $this->db->bind(':activa',1);
            $this->db->bind(':codigo_verificacion','');
            $this->db->bind(':verificada_profesor',0);
            $this->db->bind(':verificada_jefe_dep',0);

            if ($this->db->execute()){
                return true;
            }else{
                return false;
            }
        }
  
}
    



// se usa para cuando no va a cambiar la programacion. Pone nueva a cero
public function no_cambia_programacion($id_modulo){

    $this->db->query("SELECT * FROM seg_programaciones 
    WHERE id_modulo = :id_modulo
    AND EDICION = (SELECT MAX(EDICION) FROM seg_programaciones WHERE id_modulo = :id_modulo);");
    $this->db->bind(":id_modulo",$id_modulo);
    $max_edicion = $this->db->registros();

    $this->db->query("UPDATE seg_programaciones 
    SET nueva=0, activa=1 
    WHERE id_modulo=:id_modulo 
    AND edicion=:edicion;");
    $this->db->bind(':edicion',$max_edicion[0]->edicion);
    $this->db->bind(':id_modulo',$id_modulo);
    $this->db->execute(); 

    $this->db->query("UPDATE cpifp_profesor_modulo 
    SET cambia_programacion=0 
    WHERE id_modulo=:id_modulo;");
    $this->db->bind(':id_modulo',$id_modulo);
    $this->db->execute();  
    if ($this->db->execute()){
        return true;
    }else{
        return false;
    }

}



/*********************** BORRAR PROGRAMACION ********************************/

public function borrar_programacion($id_programacion){
    $this->db->query("DELETE FROM seg_programaciones 
    WHERE id_programacion = :id_programacion;");
    $this->db->bind(":id_programacion", $id_programacion);
    return $this->db->execute();
}
 




 


}