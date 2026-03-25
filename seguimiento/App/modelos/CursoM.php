<?php

class CursoM
{
    private $db;

    public function __construct(){
        $this->db = new Base;
    }




/**********************************************************************************************/
/**********************************************************************************************/
/**************************************** CURSOS **********************************************/
/**********************************************************************************************/
/**********************************************************************************************/



// CURSO LECTIVO ACTUAL
public function obtener_lectivo(){
    $this->db->query("SELECT id_lectivo, lectivo, date_format(fecha_inicio,'%d-%m-%Y') AS fecha_inicio, 
    date_format(fecha_fin,'%d-%m-%Y') AS fecha_fin,
    cerrado FROM seg_lectivos WHERE cerrado = 0;");
    return $this->db->registros();
}


// TODOS LOS CURSOS LECTIVOS
public function cursos_lectivos(){
    $this->db->query("SELECT id_lectivo, lectivo, 
    date_format(fecha_inicio,'%d-%m-%Y') AS fecha_inicio, 
    date_format(fecha_fin,'%d-%m-%Y') AS fecha_fin,
    cerrado FROM seg_lectivos;");
    return $this->db->registros();
}


/**************************** NUEVO CURSO *************************************/

public function nuevo_curso($inicio, $fin, $nombre, $numero_evaluaciones){

        $this->db->query("INSERT INTO seg_lectivos (lectivo, fecha_inicio, fecha_fin, cerrado)
        VALUES (:nombre,:inicio,:fin, 0);");
        $this->db->bind(':nombre', $nombre);
        $this->db->bind(':inicio', $inicio);
        $this->db->bind(':fin', $fin);
        $this->db->execute();
        $id_lectivo = $this->db->ultimoIndice();

        // CARPETAS PARA PROGRAMACIONES
        $directorio_programaciones = $_SERVER['DOCUMENT_ROOT'] . '/archivos_programaciones/';
        $carpeta_anio = $id_lectivo.'-'.$nombre;
        $directorio_destino = $directorio_programaciones. $carpeta_anio.'/';
        if (!is_dir($directorio_programaciones)) {
            mkdir($directorio_programaciones, 0777, true);
        }
        if (!is_dir($directorio_destino)) {
            mkdir($directorio_destino, 0777, true);
        }


        // ponemos indicadores a cero para ese curso
        $this->db->query("INSERT INTO `seg_indicadores_grados` (`id_indicador`, `id_grado`, `id_lectivo`, `porcentaje`) VALUES
        (1,1,:id_lectivo,0),(1,2,:id_lectivo,0),(1,3,:id_lectivo,0),
        (2,1,:id_lectivo,0),(2,2,:id_lectivo,0),(2,3,:id_lectivo,0),
        (3,1,:id_lectivo,0),(3,2,:id_lectivo,0),(3,3,:id_lectivo,0),
        (4,1,:id_lectivo,0),(4,2,:id_lectivo,0),(4,3,:id_lectivo,0),
        (5,1,:id_lectivo,0),(5,2,:id_lectivo,0),(5,3,:id_lectivo,0),
        (6,1,:id_lectivo,0),(6,2,:id_lectivo,0),(6,3,:id_lectivo,0),
        (7,1,:id_lectivo,0),(7,2,:id_lectivo,0),(7,3,:id_lectivo,0);");
        $this->db->bind(':id_lectivo', $id_lectivo);
        $this->db->execute();


        // insertamos numero evaluaciones
        for($i=0;$i<$numero_evaluaciones;$i++):
            $this->db->query("INSERT INTO cpifp_evaluaciones (evaluacion)
            VALUES (:evaluacion);");
            $this->db->bind(':evaluacion', ($i+1).'ª Evaluacion');
            $this->db->execute();
        endfor;


        // llamamos a la funcion de crear calendario
        $calendario = calendario_curso($inicio,$fin);
        for($i=0;$i<sizeof($calendario);$i++):
            $this->db->query("INSERT INTO seg_calendario (id_lectivo, fecha, dia_semana)
            VALUES (:lectivo,:fecha,:dia_semana);");
            $this->db->bind(':lectivo', $id_lectivo);
            $this->db->bind(':fecha',$calendario[$i]['fecha']);
            $this->db->bind(':dia_semana',$calendario[$i]['dia_semana']);
            $this->db->execute();
        endfor;

    return true;

}



/**************************** BORRAR CURSO *************************************/

public function borra_todo_el_calendario_y_curso($id_lectivo){

        $this->db->query("DELETE FROM seg_valoraciones 
        WHERE id_lectivo=:id_lectivo;");
        $this->db->bind(':id_lectivo',$id_lectivo);
        $this->db->execute();

        // borramos el lectivo
        $this->db->query("DELETE FROM seg_lectivos 
        WHERE id_lectivo=:id_lectivo;");
        $this->db->bind(':id_lectivo',$id_lectivo);
        $this->db->execute();

        // borramos los nombres evaluaciones
        $this->db->query("DELETE FROM cpifp_evaluaciones");
        $this->db->execute();


        $this->db->query("DELETE FROM seg_seguimiento_modulo;");
        $this->db->execute();

        // borramos los seguimientos
        $this->db->query("DELETE FROM seg_seguimiento_preguntas;");
        $this->db->execute();

        $this->db->query("DELETE FROM seg_horario_modulo;");
        $this->db->execute();
        $this->db->query("DELETE FROM seg_temas;");
        $this->db->execute();


        // borramos historicos de ese año
        $this->db->query("DELETE FROM his_total_modulo 
        WHERE id_lectivo=:id_lectivo;");
        $this->db->bind(':id_lectivo',$id_lectivo);
        $this->db->execute();
        $this->db->query("DELETE FROM his_total_curso 
        WHERE id_lectivo=:id_lectivo;");
        $this->db->bind(':id_lectivo',$id_lectivo);
        $this->db->execute();
        $this->db->query("DELETE FROM his_anual 
        WHERE id_lectivo=:id_lectivo;");
        $this->db->bind(':id_lectivo',$id_lectivo);
        $this->db->execute();

        $this->db->query("DELETE FROM seg_indicadores_grados 
        WHERE id_lectivo=:id_lectivo;");
        $this->db->bind(':id_lectivo',$id_lectivo);
        $this->db->execute();

        if ($this->db->execute()){
            return true;
        }else{
            return false;
        }
}



/**************************** CERRAR CURSO *************************************/

public function cerrar_curso($id_lectivo){


    // todas las programaciones las cambiamos a nuevas no
    $this->db->query("SELECT * FROM seg_programaciones
    WHERE id_lectivo=:id_lectivo;");
    $this->db->bind(':id_lectivo',$id_lectivo);
    $programaciones = $this->db->registros();

    foreach($programaciones as $prog){
        $this->db->query("UPDATE seg_programaciones SET nueva = 0;");
        $this->db->execute();
    }


    // cerramos el curso y lo ponemos a 0
    $this->db->query("UPDATE seg_lectivos SET cerrado = 1 
    WHERE id_lectivo=:id_lectivo;");
    $this->db->bind(':id_lectivo',$id_lectivo);
    $this->db->execute();

    // borramos cpifp_evaluaciones -> seg_evaluaciones, seg_seguimiento_modulo, seg_seguimiento_preguntas
    $this->db->query("DELETE FROM cpifp_evaluaciones");
    $this->db->execute();

    // borramos seg_calendario -> el seg_festivos
    $this->db->query("DELETE FROM seg_calendario");
    $this->db->execute();

    // borramos el horario del modulo
    $this->db->query("DELETE FROM seg_horario_modulo;");
    $this->db->execute();

    // borramos seg_temas -> seg_seguimiento_temas
    $this->db->query("DELETE FROM seg_temas;");
    if ($this->db->execute()){
        return true;
    }else{
        return false;
    }
}




/********************************************************************************/
/********************************************************************************/
/***************************** EVALUACIONES **************************************/
/********************************************************************************/
/********************************************************************************/


// TRAE LOS TURNOS
public function obtener_turnos(){
    $this->db->query("SELECT * FROM cpifp_turnos;");
    return $this->db->registros();
}

// TRAE LOS GRADOS
public function obtener_grados(){
    $this->db->query("SELECT * FROM cpifp_grados WHERE nombre != 'Especializacion';");
    return $this->db->registros();
}

// TRAE LOS CURSOS
public function obtener_cursos(){
    $this->db->query("SELECT DISTINCT sn.id_numero, numero, nombre_curso
    FROM seg_numero sn INNER JOIN cpifp_curso c ON sn.id_numero = c.id_numero;");
    return $this->db->registros();
}


// RETORNA NOMBRES EVALUACIONES
public function obtener_nombres_evaluaciones(){
    $this->db->query("SELECT * FROM cpifp_evaluaciones;");
    return $this->db->registros();
}


// FECHAS DE LAS EVALUACIONES
public function fechas_evaluaciones($id_lectivo){
    $this->db->query("SELECT * FROM `seg_evaluaciones`, seg_calendario , cpifp_grados, cpifp_turnos, seg_numero, cpifp_evaluaciones
    WHERE seg_evaluaciones.id_calendario=seg_calendario.id_calendario
    AND cpifp_turnos.id_turno=seg_evaluaciones.id_turno
    AND cpifp_grados.id_grado=seg_evaluaciones.id_grado
    AND seg_numero.id_numero=seg_evaluaciones.id_numero
    AND cpifp_evaluaciones.id_evaluacion=seg_evaluaciones.id_evaluacion
    AND seg_calendario.id_lectivo = :id_lectivo;");
    $this->db->bind(':id_lectivo', $id_lectivo);
    return $this->db->registros();
}




//***********************************************************************************************************/
//************************************ INSERTAR FECHAS DE EVALUACIONES **************************************/
//***********************************************************************************************************/


public function insertar_fechas_evaluaciones($nuevo){

    foreach ($nuevo as $item) {

        // 1. Obtener id_calendario para la fecha recibida
        $this->db->query("SELECT id_calendario FROM seg_calendario WHERE fecha = :fecha");
        $this->db->bind(':fecha', $item->fecha);
        $res = $this->db->registros();
        $fecha_nueva = $res[0]->id_calendario;

        // 2. Ver si ya existe esa combinación en seg_evaluaciones
        $this->db->query("SELECT * FROM seg_evaluaciones 
        WHERE id_turno = :turno 
        AND id_grado = :grado 
        AND id_evaluacion = :eval 
        AND id_numero = :curso");

        $this->db->bind(':turno', $item->turno);
        $this->db->bind(':grado', $item->grado);
        $this->db->bind(':eval', $item->id_evaluacion);
        $this->db->bind(':curso', $item->curso);
        $existe = $this->db->registros();


        // SI NO EXISTE ESA COMBINACION : INSERTAMOS
        if (empty($existe)) {
            $this->db->query("INSERT INTO seg_evaluaciones (id_evaluacion, id_calendario, id_grado, id_turno, id_numero)
            VALUES (:id_evaluacion, :id_calendario, :id_grado, :id_turno, :id_numero)");
            $this->db->bind(':id_evaluacion', $item->id_evaluacion);
            $this->db->bind(':id_calendario', $fecha_nueva);
            $this->db->bind(':id_grado', $item->grado);
            $this->db->bind(':id_turno', $item->turno);
            $this->db->bind(':id_numero', $item->curso);
            $this->db->execute();
        } else {
        // SI EXISTE: comprobar si la fecha (id_calendario) es diferente y si es diferente la cambiamos
            if ($existe[0]->id_calendario != $fecha_nueva) {
                $this->db->query("UPDATE seg_evaluaciones 
                SET id_calendario = :id_calendario 
                WHERE id_seg_evaluacion = :id_seg_evaluacion");
                $this->db->bind(':id_calendario', $fecha_nueva);
                $this->db->bind(':id_seg_evaluacion', $existe[0]->id_seg_evaluacion);
                $this->db->execute();
            }
        }
    }

    return true;
}





/********************************************************************************/
/********************************************************************************/
/***************************** FESTIVOS *****************************************/
/********************************************************************************/
/********************************************************************************/


// TRAE TODOS LOS FESTIVOS
public function calendario_festivos(){

    $this->db->query("SELECT fe.festivo AS descripcion,
    DATE_FORMAT(MIN(c.fecha), '%d-%m-%Y') AS fecha_inicio,
    DATE_FORMAT(MAX(c.fecha), '%d-%m-%Y') AS fecha_fin,
    (SELECT fe_inicio.id_festivo FROM seg_festivos fe_inicio
    LEFT JOIN seg_calendario c_inicio ON c_inicio.id_calendario = fe_inicio.id_calendario
    WHERE c_inicio.fecha = MIN(c.fecha) AND fe_inicio.festivo = fe.festivo
    LIMIT 1) AS id_festivo_inicio,
    (SELECT fe_fin.id_festivo FROM seg_festivos fe_fin
    LEFT JOIN seg_calendario c_fin ON c_fin.id_calendario = fe_fin.id_calendario
    WHERE c_fin.fecha = MAX(c.fecha) AND fe_fin.festivo = fe.festivo
    LIMIT 1) AS id_festivo_fin
    FROM seg_calendario c
    LEFT JOIN seg_festivos fe ON c.id_calendario = fe.id_calendario
    WHERE fe.festivo IS NOT NULL
    GROUP BY fe.festivo
    ORDER BY MIN(c.fecha);");

    return $this->db->registros();
}




/************************ NUEVO FESTIVO ****************************/

public function nuevo_festivo($nuevos){

    for ($i=0; $i < sizeof($nuevos) ; $i++) {

        $this->db->query("SELECT id_calendario 
        FROM seg_calendario 
        WHERE fecha=:fecha;");
        $this->db->bind(':fecha',$nuevos[$i]['fecha']);
        $id_calendario = $this->db->registros();
        $id_calendario = $id_calendario[0]->id_calendario;

        $this->db->query("INSERT INTO seg_festivos (id_calendario,festivo, dia_semana)
        VALUES (:id_calendario,:festivo,:dia_semana);");
        $this->db->bind(':id_calendario', $id_calendario);
        $this->db->bind(':festivo', $nuevos[$i]['festivo']);
        $this->db->bind(':dia_semana', $nuevos[$i]['dia_semana']);
        $this->db->execute();
    }
    return true;
}



/************************ BORRA FESTIVO ****************************/

public function borrar_festivo($id_inicio, $id_fin){

    $this->db->query("DELETE FROM seg_festivos 
    WHERE id_festivo>=:id_inicio AND id_festivo<=:id_fin;");
    $this->db->bind(':id_inicio', $id_inicio);
    $this->db->bind(':id_fin', $id_fin);

    if ($this->db->execute()){
        return true;
    }else{
        return false;
    }

}



/**********************************************************************************************/
/**********************************************************************************************/
/*********************************** INDICADORES **********************************************/
/**********************************************************************************************/
/**********************************************************************************************/


// NOMBRES DE LOS INDICADORES
public function obtener_indicadores(){
    $this->db->query("SELECT * FROM `seg_indicadores`;");
    return $this->db->registros();
}


// INDICADORES AÑO ANTERIOR
public function indicadores_ano_anterior($id_lectivo){
    $this->db->query("SELECT * FROM seg_indicadores_grados 
    WHERE id_lectivo = (SELECT MAX(id_lectivo) 
    FROM seg_indicadores_grados WHERE id_lectivo < :id_lectivo)");
    $this->db->bind(':id_lectivo', $id_lectivo);
    return $this->db->registros();
}

    
// INDICADORES, GRADOS Y SU PORCENTAJE PARA CURSO ACTUAL
public function obtener_indicadores_grados($id_lectivo){
    $this->db->query("SELECT `seg_indicadores`.`id_indicador`,`indicador`, `cpifp_grados`.`id_grado`, 
    `cpifp_grados`.`nombre` AS `nombre_grado`, `porcentaje`
    FROM `seg_indicadores`, `cpifp_grados`, `seg_indicadores_grados`
    WHERE `seg_indicadores`.`id_indicador`=`seg_indicadores_grados`.`id_indicador` 
    AND `cpifp_grados`.`id_grado`=`seg_indicadores_grados`.`id_grado`
    AND id_lectivo = :id_lectivo;");
    $this->db->bind(':id_lectivo', $id_lectivo);
    return $this->db->registros();
}



//************************ EDITAR INDICADOR ****************************/

public function editar_indicador($editar, $id_indicador, $id_lectivo){

    foreach($editar as $e){

        $this->db->query("UPDATE seg_indicadores_grados SET porcentaje = :porcentaje 
        WHERE id_grado = :id_grado AND id_indicador = :id_indicador
        AND id_lectivo = :id_lectivo;");
        $this->db->bind(':porcentaje', $e->porcentaje);
        $this->db->bind(':id_indicador',$id_indicador);
        $this->db->bind(':id_grado',$e->id_grado);    
        $this->db->bind(':id_lectivo',$id_lectivo);              
        $this->db->execute();
    }

    $this->db->query("SELECT * FROM seg_indicadores_grados
    WHERE id_lectivo = :id_lectivo;");
    $this->db->bind(':id_lectivo',$id_lectivo);   
    $indicadores = $this->db->registros();

    $this->db->query("SELECT * FROM his_total_modulo
    WHERE id_lectivo = :id_lectivo;");
    $this->db->bind(':id_lectivo',$id_lectivo);   
    $total_modulo = $this->db->registros();

    if(!empty($total_modulo)){
        // tenemos que actulizar el campo 
        $conforme = '';
        foreach ($total_modulo as $modulo) {
            foreach ($indicadores as $indicador) {

                if ($modulo->id_grado == $indicador->id_grado && $modulo->id_indicador == $indicador->id_indicador) {
                    
                    // Determinar si es conforme o no
                    $conforme = ($modulo->total < $indicador->porcentaje) ? 0 : 1;

                    // Ejecutar el UPDATE
                    $this->db->query("UPDATE his_total_modulo 
                    SET modulo_conforme = :conforme 
                    WHERE id_grado = :id_grado AND id_indicador = :id_indicador AND id_lectivo = :id_lectivo");
                    $this->db->bind(':conforme', $conforme);
                    $this->db->bind(':id_grado', $indicador->id_grado);
                    $this->db->bind(':id_indicador', $indicador->id_indicador);
                    $this->db->bind(':id_lectivo', $indicador->id_lectivo); 

                    $this->db->execute();
                }
            }
        }

    } 

    return true; 
}




//************************ IMPORTAR INDICADORES ****************************/

public function importar_porcentajes($id_lectivo){


    // Obtener % del año anterior respecto al curso actual
    $this->db->query("SELECT * FROM seg_indicadores_grados 
    WHERE id_lectivo = (SELECT MAX(id_lectivo) 
    FROM seg_indicadores_grados WHERE id_lectivo < :id_lectivo)");
    $this->db->bind(':id_lectivo', $id_lectivo);
    $editar = $this->db->registros();
    if (empty($editar)) {
        return false;
    }

    $this->db->query("DELETE FROM seg_indicadores_grados 
    WHERE id_lectivo = :id_lectivo;");
    $this->db->bind(':id_lectivo', $id_lectivo);
    $this->db->execute();

    foreach ($editar as $e) {
        $this->db->query("INSERT INTO seg_indicadores_grados (id_grado, id_indicador, id_lectivo, porcentaje) 
        VALUES (:id_grado, :id_indicador, :id_lectivo, :porcentaje)");
        $this->db->bind(':id_grado', $e->id_grado);
        $this->db->bind(':id_indicador', $e->id_indicador);
        $this->db->bind(':id_lectivo', $id_lectivo);
        $this->db->bind(':porcentaje', $e->porcentaje);
        $this->db->execute();
    }

    
    $this->db->query("SELECT * FROM seg_indicadores_grados
    WHERE id_lectivo = :id_lectivo;");
    $this->db->bind(':id_lectivo',$id_lectivo);   
    $indicadores = $this->db->registros();

    $this->db->query("SELECT * FROM his_total_modulo
    WHERE id_lectivo = :id_lectivo;");
    $this->db->bind(':id_lectivo',$id_lectivo);   
    $total_modulo = $this->db->registros();

    if(!empty($total_modulo)){
        // tenemos que actulizar el campo 
        $conforme = '';
        foreach ($total_modulo as $modulo) {
            foreach ($indicadores as $indicador) {

                if ($modulo->id_grado == $indicador->id_grado && $modulo->id_indicador == $indicador->id_indicador) {
                    
                    // Determinar si es conforme o no
                    $conforme = ($modulo->total < $indicador->porcentaje) ? 1 : 0;

                    // Ejecutar el UPDATE
                    $this->db->query("UPDATE his_total_modulo 
                                    SET modulo_conforme = :conforme 
                                    WHERE id_grado = :id_grado AND id_indicador = :id_indicador AND id_lectivo = :id_lectivo");
                    $this->db->bind(':conforme', $conforme);
                    $this->db->bind(':id_grado', $indicador->id_grado);
                    $this->db->bind(':id_indicador', $indicador->id_indicador);
                    $this->db->bind(':id_lectivo', $indicador->id_lectivo); 

                    $this->db->execute();
                }
            }
        }

    } 

    return true; 
}





/**********************************************************************************************/
/**********************************************************************************************/
/*********************************** CALENDARIO **********************************************/
/**********************************************************************************************/
/**********************************************************************************************/

//CAMBIO CALENDARIO 02 OCTUBRE 2025
// TRAE CALENDARIO GENERAL CON EVALUACIONES Y FESTIVOS
// public function calendario_evas_fes(){
//     $this->db->query("SELECT c.id_calendario, c.fecha, c.dia_semana, c.id_lectivo,
//     CASE
//         WHEN fe.festivo IS NOT NULL THEN fe.festivo
//         WHEN cev.evaluacion IS NOT NULL THEN cev.evaluacion
//         END AS descripcion,
//     CASE
//         WHEN fe.festivo IS NOT NULL THEN 1
//         ELSE 0
//         END AS esFestivo,
//     CASE
//         WHEN cev.evaluacion IS NOT NULL THEN 1
//         ELSE 0
//         END AS esEvaluacion
//     FROM seg_calendario c
//     LEFT JOIN seg_festivos fe ON c.id_calendario = fe.id_calendario
//     LEFT JOIN seg_evaluaciones ev ON c.id_calendario = ev.id_calendario
//     LEFT JOIN cpifp_evaluaciones cev ON cev.id_evaluacion = ev.id_evaluacion
//     ORDER BY c.fecha;");
//     return $this->db->registros();
// }



public function calendario_evas_fes(){

    $this->db->query("SELECT c.id_calendario, c.fecha, c.dia_semana, c.id_lectivo,
        CASE
            WHEN fe.festivo IS NOT NULL THEN fe.festivo
            WHEN cev.evaluacion IS NOT NULL THEN cev.evaluacion
        END AS descripcion,
        CASE
            WHEN fe.festivo IS NOT NULL THEN 1
            ELSE 0
        END AS esFestivo,
        CASE
            WHEN cev.evaluacion IS NOT NULL THEN 1
            ELSE 0
        END AS esEvaluacion,
        g.nombre AS grado,
        cu.numero AS numero,
        t.turno AS turno
    FROM seg_calendario c
    LEFT JOIN seg_festivos fe ON c.id_calendario = fe.id_calendario
    LEFT JOIN seg_evaluaciones ev ON c.id_calendario = ev.id_calendario
    LEFT JOIN cpifp_evaluaciones cev ON cev.id_evaluacion = ev.id_evaluacion
    LEFT JOIN cpifp_grados g ON ev.id_grado = g.id_grado
    LEFT JOIN seg_numero cu ON ev.id_numero = cu.id_numero
    LEFT JOIN cpifp_turnos t ON ev.id_turno = t.id_turno
    ORDER BY c.fecha;");

    return $this->db->registros();
 }















}