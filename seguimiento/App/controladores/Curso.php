<?php

class Curso extends Controlador{

    private $cursoModelo;


    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
       // $this->datos['rolesPermitidos'] = [50];          // Definimos los roles que tendran acceso
        
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }
        $this->cursoModelo = $this->modelo('CursoM');

    }






/**********************************************************************************************/
/**********************************************************************************************/
/************************************** CURSO *************************************************/
/**********************************************************************************************/
/**********************************************************************************************/


public function index(){
    $this->datos['lectivo'] = $this->cursoModelo->obtener_lectivo(); // curso actual
    $this->datos['cursos_lectivos']=$this->cursoModelo->cursos_lectivos(); // todos los cursos lectivos
    $this->vista('direccion/curso/curso', $this->datos);
}



/***************************** NUEVO *****************************/

public function nuevo_curso(){

        $this->datos['rolesPermitidos'] = [50];
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/usuarios');
        }
        
        if($_SERVER['REQUEST_METHOD'] =='POST'){

                $fecha_inicio = trim($_POST['fecha_ini']);
                $fecha_fin = trim($_POST['fecha_fin']);
                $numero_evaluaciones = trim($_POST['numero_evaluaciones']);

                // EXTRAIGO SOLO EL AÑO PARA PONER EL NOMBRE
                $a_ini = date('Y', strtotime($fecha_inicio)); 
                $a_fin = date('Y', strtotime($fecha_fin));
                $nombre = 'Curso '. $a_ini.' - '.$a_fin;

        if($this->cursoModelo->nuevo_curso($fecha_inicio, $fecha_fin, $nombre, $numero_evaluaciones)){
            redireccionar('/curso');
        }else{
            die('Algo ha fallado!!');
        }
    }else{
        $this->vista('direccion/curso/curso',$this->datos);
    }
}




/***************************** BORRAR *****************************/


public function borrar_curso($id_lectivo){

    $this->datos['rolesPermitidos'] = [50];
    if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
        redireccionar('/usuarios');
    }

    $lectivo = $this->cursoModelo->obtener_lectivo(); // curso actual
    $nombre_lectivo = $lectivo[0]->lectivo;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($this->cursoModelo->borra_todo_el_calendario_y_curso($id_lectivo)) {

            // BORRAR CARPETA DE PROGRAMACIONES
            $directorio_programaciones = $_SERVER['DOCUMENT_ROOT'] . '/archivos_programaciones/';
            $carpeta_anio = $id_lectivo.'-'.$nombre_lectivo;
            $ruta = $directorio_programaciones. $carpeta_anio;
            $this->eliminarCarpeta($ruta); 

            redireccionar('/curso');
        } else {
            die('Algo ha fallado!!!');
        }
    } else {
        $this->vista('direccion/curso/curso', $this->datos);
    }
}



// ELIMINAR CARPETAS Y SUBCARPETAS
private function eliminarCarpeta($ruta){

    if (!file_exists($ruta)) {
        return false;
    }

    if (is_file($ruta)) {
        return unlink($ruta);
    }

    $archivos = scandir($ruta);
    foreach ($archivos as $archivo) {
        if ($archivo != '.' && $archivo != '..') {
            $subruta = $ruta . '/' . $archivo;
            if (is_dir($subruta)) {
                $this->eliminarCarpeta($subruta); 
            } else {
                unlink($subruta);
            }
        }
    }

    return rmdir($ruta);
}





/***************************** CERRAR *****************************/

public function cerrar_curso($id_lectivo){

    $this->datos['rolesPermitidos'] = [50];
    if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
        redireccionar('/usuarios');
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($this->cursoModelo->cerrar_curso($id_lectivo)) {
            redireccionar('/curso');
        }else{
            die('Algo ha fallado!!!');
        }
    }else{
        $this->vista('direccion/curso/curso', $this->datos);
    }
}



/**********************************************************************************************/
/**********************************************************************************************/
/************************************** EVALUACIONES ******************************************/
/**********************************************************************************************/
/**********************************************************************************************/


public function evaluaciones(){
    $this->datos['lectivo']=$this->cursoModelo->obtener_lectivo(); // lectivo actual
    $this->datos['turnos']=$this->cursoModelo->obtener_turnos(); // solo turnos
    $this->datos['grados']=$this->cursoModelo->obtener_grados(); // solo grados
    $this->datos['cursos']=$this->cursoModelo->obtener_cursos(); // solo cursos
    $this->datos['numero_evaluaciones']=$this->cursoModelo->obtener_nombres_evaluaciones(); // nomrbes evaluaciones

    $id_lectivo = $this->datos['lectivo'][0]->id_lectivo;
    $this->datos['fechas_evaluaciones'] = $this->cursoModelo->fechas_evaluaciones($id_lectivo);  // fechas evaluaciones
    
    $this->vista('direccion/curso/curso_evaluaciones', $this->datos);
}




public function fechas_evaluaciones($id_turno) {

    $this->datos['rolesPermitidos'] = [50];
    if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
        redireccionar('/usuarios');
    }


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $combinaciones = $_POST['combinacion']; 

        $nueva = [];
        foreach ($combinaciones as $combinacion => $fecha) {

            list($grado_id, $evaluacion_id, $curso_id) = explode('-', $combinacion);

            if (!empty($fecha)) {
                $combinacion_obj = (object)[
                    'id_evaluacion' => $evaluacion_id, 
                    'fecha' => $fecha, 
                    'turno' => $id_turno, 
                    'grado' => $grado_id, 
                    'curso' => $curso_id
                ];
                $nueva[] = $combinacion_obj;
            }
        }

        if ($this->cursoModelo->insertar_fechas_evaluaciones($nueva, $id_turno)) {
            redireccionar('/curso/evaluaciones');
        } else {
            die('Algo ha fallado!!');
        }
    } else {
        $this->vista('direccion/curso/curso_evaluaciones', $this->datos);
    }
}
        


/**********************************************************************************************/
/**********************************************************************************************/
/************************************** FESTIVOS **********************************************/
/**********************************************************************************************/
/**********************************************************************************************/


public function festivos(){
    $this->datos['lectivo'] = $this->cursoModelo->obtener_lectivo();
    $this->datos['calendario_festivos'] = $this->cursoModelo->calendario_festivos();  // trae SOLO festivos
    $this->vista('direccion/curso/curso_festivos', $this->datos);
}




public function nuevo_festivo(){

    $this->datos['rolesPermitidos'] = [50];
    if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
        redireccionar('/usuarios');
    }

    if($_SERVER['REQUEST_METHOD'] =='POST'){

            $f_inicio = new DateTime(trim($_POST['fecha_ini']));
            $f_fin = new DateTime(trim($_POST['fecha_fin']));
            $intervalo = new DateInterval('P1D'); //periodo de un dia
            $f_fin->add($intervalo);
            $periodo = new DatePeriod($f_inicio, $intervalo, $f_fin);

            $festivos = (object)[];
            $nuevos = array();

            foreach($periodo as $fechas){
                $dia_sem = $fechas->format('w');
                switch ($dia_sem) {
                    case '0':
                        $dia_sem = "D";
                        break;
                    case '1':
                        $dia_sem = "L";
                        break;
                    case '2':
                        $dia_sem = "M";
                        break;
                    case '3':
                        $dia_sem = "X";
                        break;
                    case '4':
                        $dia_sem = "J";
                        break;
                    case '5':
                        $dia_sem = "V";
                        break;
                    case '6':
                        $dia_sem = "S";
                        break;  
                };
                $fech = $fechas->format('Y-n-d');
                $festivos = ['festivo'=>trim($_POST['festivo']),
                            'fecha'=> $fech,
                            'dia_semana' => $dia_sem
                        ];
                array_push($nuevos, $festivos);
            }

        if($this->cursoModelo->nuevo_festivo($nuevos)){
            redireccionar('/curso/festivos');
        }else{
            die('Algo ha fallado!!');
        }
    }else{
        $this->vista('direccion/curso/curso_festivos',$this->datos);
    }
}




public function borrar_festivo($id){

    $info = explode('-',$id); // separamos todo lo que nos llega por la url
    $id_inicio = $info[0];
    $id_fin = $info[1];

    $this->datos['rolesPermitidos'] = [50];
    if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
        redireccionar('/usuarios');
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($this->cursoModelo->borrar_festivo($id_inicio, $id_fin)) {
            redireccionar('/curso/festivos');
        }else{
            die('Algo ha fallado!!!');
        }
    }else{
        $this->vista('direccion/curso/curso_festivos', $this->datos);
    }
}




/**********************************************************************************************/
/**********************************************************************************************/
/************************************** UMBRALES **********************************************/
/**********************************************************************************************/
/**********************************************************************************************/


public function curso_indicadores(){

    $this->datos['lectivo'] = $this->cursoModelo->obtener_lectivo();
    $id_lectivo = $this->datos['lectivo'][0]->id_lectivo;

    $this->datos['grados'] = $this->cursoModelo->obtener_grados(); // solo nombres grados
    $this->datos['indicadores'] = $this->cursoModelo->obtener_indicadores(); // solo nombres indicadores
    $this->datos['indicadores_ano_anterior'] = $this->cursoModelo->indicadores_ano_anterior($id_lectivo); // indicadores año anterior
    $this->datos['indicadores_grados'] = $this->cursoModelo->obtener_indicadores_grados($id_lectivo); // indicadores año actual

    $this->vista('direccion/curso/curso_indicadores', $this->datos);
}




    
public function editar_indicador($id_indicador){

    $this->datos['lectivo'] = $this->cursoModelo->obtener_lectivo();
    $id_lectivo = $this->datos['lectivo'][0]->id_lectivo;

    $this->datos['rolesPermitidos'] = [50];
    if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
        redireccionar('/usuarios');
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        $editar=array();
        $tam=sizeof($_POST['grado']);

        for($i=0;$i<$tam;$i++){
             $nuevo_objeto=(object) [
                 'id_grado' => $_POST['grado'][$i],
                 'porcentaje' => $_POST['porcentaje'][$i],
            ];
            array_push($editar,$nuevo_objeto);
        };

        if($this->cursoModelo->editar_indicador($editar, $id_indicador, $id_lectivo)){
            redireccionar('/curso/curso_indicadores');
        }else{
            die('Algo ha fallado!!');
        }

    }else{
        $this->vista('direccion/curso/curso_indicadores',$this->datos);
    }
}




public function importar_porcentajes(){

    $this->datos['lectivo'] = $this->cursoModelo->obtener_lectivo();
    $id_lectivo = $this->datos['lectivo'][0]->id_lectivo;

    $this->datos['rolesPermitidos'] = [50];
    if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
        redireccionar('/usuarios');
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        if($this->cursoModelo->importar_porcentajes($id_lectivo)){
            redireccionar('/curso/curso_indicadores');
        }else{
            die('Algo ha fallado!!');
        }

    }else{
        $this->vista('direccion/curso/curso_indicadores',$this->datos);
    }
}





/**********************************************************************************************/
/**********************************************************************************************/
/************************************** CALENDARIO *********************************************/
/**********************************************************************************************/
/**********************************************************************************************/


public function calendario(){
    $this->datos['lectivo']=$this->cursoModelo->obtener_lectivo();
    $this->datos['calendario_evas_fes'] = $this->cursoModelo->calendario_evas_fes();  // trae calendario entero con evaluaciones y festivos
    $this->vista('direccion/curso/curso_calendario', $this->datos);
}





}





