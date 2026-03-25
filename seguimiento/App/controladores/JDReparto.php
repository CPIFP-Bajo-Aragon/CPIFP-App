<?php



class JDReparto extends Controlador{

    private $repartoModelo;

    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
        // $this->datos['rolesPermitidos'] = [30];          // Definimos los roles que tendran acceso

        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }
        $this->repartoModelo = $this->modelo('jdRepartoM');
    }

    


    public function index(){

        $this->datos['lectivo'] = $this->repartoModelo->obtener_lectivo(); //lectivo
        $lectivo = $this->datos['lectivo'];

        $id_profesor = $this->datos['usuarioSesion']->id_profesor;
        $this->datos['usuario'] = $this->repartoModelo->departamentos_formacion($id_profesor);
        $sin_ciclo = $this->datos['usuario'][0]->sin_ciclo;
        $id_dep = $this->datos['usuario'][0]->id_departamento;

        // SI ES FOL E INGLES
        if($sin_ciclo  == 1 ){

            $this->datos['modulos'] = $this->repartoModelo->modulos($id_dep);
            $this->datos['profes'] = $this->repartoModelo->obtener_profes($id_dep);

            if(!empty($this->datos['lectivo'])){
                $id_lectivo =  $this->datos['lectivo'][0]->id_lectivo;
                $this->datos['prof_mod'] = $this->repartoModelo->horas_profes_modulo($id_dep, $id_lectivo);
                // PROGRAMACIONES
                $this->datos['nuevas'] = $this->repartoModelo->nuevas_fol_leo($id_dep, $id_lectivo);
            }
            $this->vista('jefeDep/fol_leo', $this->datos);

        }else{

            if(!empty($this->datos['lectivo'])){
                $id_lectivo =  $this->datos['lectivo'][0]->id_lectivo;
                // PROGRAMACIONES
                $this->datos['nuevas'] = $this->repartoModelo->nuevas_por_ciclo($id_dep, $id_lectivo);
                // INSERTAR EN HIS_TOTAL_CURSO
                $this->datos['resumen_modulos'] = $this->repartoModelo->resumen_modulos($id_dep, $lectivo);
                $resumen = $this->datos['resumen_modulos'];
                $promedios = calcular_promedios($resumen); // calcular promedios 
                $this->repartoModelo->insertar_his_total_curso($promedios); // inserta en tabla historica
            } else{
                 $this->datos['nuevas'] = ''; 
                 $this->datos['ciclos'] = '';
            }
                $this->datos['ciclos'] = $this->repartoModelo->obtener_ciclos($id_dep);
                $this->datos['cursos'] = $this->repartoModelo->obtener_ciclos_cursos($id_dep);
            
            $this->vista('jefeDep/ciclos', $this->datos);

        }    
        
        
    }



/********************************** DEPARTAMENTOS SIN CICLO (FOL/LEO) *******************************************/


public function fol_leo_modulos(){

    // LECTIVO ACTUAL
    $this->datos['lectivo'] = $this->repartoModelo->obtener_lectivo();
    $id_lectivo =  $this->datos['lectivo'][0]->id_lectivo;

    $id_profesor = $this->datos['usuarioSesion']->id_profesor;
    $datos = $this->repartoModelo->departamentos_formacion($id_profesor);
    $id_dep = $datos[0]->id_departamento;


    // recogemos todos los modulos del departamento
    $modulos = $this->repartoModelo->modulos($id_dep);
    if(!empty($modulos)){
        for ($i=0; $i<sizeof($modulos); $i++) { 
            $id_grado = $modulos[$i]->id_grado;
            $id_turno = $modulos[$i]->id_turno;
            $id_numero = $modulos[$i]->id_numero;
            // verificamos que hay fechas de evaluaciones para ese modulo
            $hay_fechas = $this->repartoModelo->hay_fechas_evaluacion($id_grado, $id_turno, $id_numero); 
            // si hay fechas ponemos 1 si no 0 a la nueva propiedad habilitado
            $habilitado = !empty($hay_fechas) ? 1 : 0;
            $modulos[$i]->habilitado = $habilitado;
        }
        $this->datos['modulos'] = $modulos;
    } else {
         $this->datos['modulos'] = '';
    }


    $this->datos['nuevas'] = $this->repartoModelo->nuevas_fol_leo($id_dep, $id_lectivo); // badge programaciones
    $this->datos['profesores_departamento'] = $this->repartoModelo->obtener_profes($id_dep); // TODOS LOS PROFESORES DE UN DEPATAMENTO
    $this->datos['profesores_modulo'] = $this->repartoModelo->horas_profes_modulo($id_dep, $id_lectivo);  // PROFESORES QUE DAN HORAS EN UN MODULO
    $this->vista('jefeDep/reparto/fol_leo_modulos', $this->datos);
}



public function fol_leo_reparto($id_modulo){

    // LECTIVO ACTUAL
    $this->datos['lectivo'] = $this->repartoModelo->obtener_lectivo();
    $id_lectivo =  $this->datos['lectivo'][0]->id_lectivo;

    $id_profesor = $this->datos['usuarioSesion']->id_profesor;
    $datos = $this->repartoModelo->departamentos_formacion($id_profesor);
    $id_dep = $datos[0]->id_departamento;

    $this->datos['nuevas'] = $this->repartoModelo->nuevas_fol_leo($id_dep, $id_lectivo); // badge programaciones
    $this->datos['info_modulo'] = $this->repartoModelo->info_modulo($id_modulo); // trae toda la info de un modulo
    $this->datos['horario_modulo'] = $this->repartoModelo->obtener_horario_semana_modulo($id_modulo); // trae el horario semanal de un modulo cocretos
    $this->datos['profesores_departamento'] = $this->repartoModelo->obtener_profes($id_dep); // TODOS LOS PROFESORES DE UN DEPATAMENTO
    $this->datos['profesores_modulo'] = $this->repartoModelo->horas_profes_modulo($id_dep, $id_lectivo);  // PROFESORES QUE DAN HORAS EN UN MODULO
    $this->vista('jefeDep/reparto/fol_leo_reparto', $this->datos);
}



/********************************** RESTO DEPARTAMENTOS *******************************************/

public function modulos_ciclo($id_ciclo){

    // LECTIVO ACTUAL
    $this->datos['lectivo'] = $this->repartoModelo->obtener_lectivo();
    $id_lectivo =  $this->datos['lectivo'][0]->id_lectivo;

    $id_profesor = $this->datos['usuarioSesion']->id_profesor;
    $datos = $this->repartoModelo->departamentos_formacion($id_profesor);
    $id_dep = $datos[0]->id_departamento;

    $modulos_ciclo = $this->repartoModelo->modulos_ciclo($id_ciclo, $id_dep); // info de un ciclo
    if(!empty($modulos_ciclo)){

        for ($i=0; $i<sizeof($modulos_ciclo); $i++) { 
            $id_grado = $modulos_ciclo[$i]->id_grado;
            $id_turno = $modulos_ciclo[$i]->id_turno;
            $id_numero = $modulos_ciclo[$i]->id_numero;
            $hay_fechas = $this->repartoModelo->hay_fechas_evaluacion($id_grado, $id_turno, $id_numero); // verificamos que hay fechas de evaluaciones para ese modulo

            $habilitado = !empty($hay_fechas) ? 1 : 0;
            $modulos_ciclo[$i]->habilitado = $habilitado;
        }

        $this->datos['modulos_ciclo'] = $modulos_ciclo;

    } else{
         $this->datos['modulos_ciclo'] = '';
    }

    $this->datos['profesores_departamento'] = $this->repartoModelo->obtener_profes($id_dep); // TODOS LOS PROFESORES DE UN DEPATAMENTO
    $this->datos['profesores_modulo'] = $this->repartoModelo->horas_profes_modulo($id_dep, $id_lectivo);  // PROFESORES QUE DAN HORAS EN UN MODULO PARA ESE AÑO

    $this->vista('jefeDep/reparto/modulos_ciclo', $this->datos);
}



public function modulo_reparto($id_modulo){

    // LECTIVO ACTUAL
    $this->datos['lectivo'] = $this->repartoModelo->obtener_lectivo();
    $id_lectivo = $this->datos['lectivo'][0]->id_lectivo;

    $id_profesor = $this->datos['usuarioSesion']->id_profesor;
    $datos = $this->repartoModelo->departamentos_formacion($id_profesor);
    $id_dep = $datos[0]->id_departamento;

    $this->datos['info_modulo'] = $this->repartoModelo->info_modulo($id_modulo); // trae toda la info de un modulo
    $this->datos['horario_modulo'] = $this->repartoModelo->obtener_horario_semana_modulo($id_modulo); // trae el horario semanal de un modulo cocretos
    $this->datos['profesores_departamento'] = $this->repartoModelo->obtener_profes($id_dep); // TODOS LOS PROFESORES DE UN DEPATAMENTO
    $this->datos['profesores_modulo'] = $this->repartoModelo->horas_profes_modulo($id_dep,$id_lectivo);  // PROFESORES QUE DAN HORAS EN UN MODULO
    $this->vista('jefeDep/reparto/modulo_reparto', $this->datos);
}




/**************************************************************************************/
/**************************************************************************************/
/********************************** REPARTO *******************************************/
/**************************************************************************************/
/**************************************************************************************/


public function reparto($id_modulo){

        // info del lectivo
        $this->datos['lectivo'] = $this->repartoModelo->obtener_lectivo();
        $id_lectivo = $this->datos['lectivo'][0]->id_lectivo;

        // info del profesor y su departamento
        $id_profesor = $this->datos['usuarioSesion']->id_profesor;
        $datos = $this->repartoModelo->departamentos_formacion($id_profesor);
        $sin_ciclo = $datos[0]->sin_ciclo;
    
        // array de profesores y horas asignadas
        $array = array();
        $tam = sizeof($_POST['profes']);
        for($i=0;$i<$tam;$i++){
            if(!empty($_POST['horas'][$i])){
                $obj=(object) [
                    'profe' => $_POST['profes'][$i],
                    'horas' => $_POST['horas'][$i],
                ];
                array_push($array,$obj);
            }
        };


        // si el array llega vacio, borra la asignacion
        if(empty($array)){
            $this->repartoModelo->borrar_asignacion($id_modulo, $id_lectivo);
            redireccionar('/JDReparto/modulo_reparto/'.$id_modulo);

        }else{

            $hay_valor = $this->repartoModelo->registros($id_modulo, $id_lectivo); // devuelve si hay profesores asignados a ese modulo

            if(empty($hay_valor)){
                $this->repartoModelo->reparto($id_modulo, $array,  $id_lectivo);
                if($sin_ciclo==1){
                    redireccionar('/JDReparto/fol_leo_modulos');
                }else{
                    redireccionar('/JDReparto/modulo_reparto/'.$id_modulo);
                }
            }else{
                $this->repartoModelo->actualizar_reparto($id_modulo, $array, $id_lectivo);
                if($sin_ciclo==1){
                    redireccionar('/JDReparto/fol_leo_modulos');
                }else{
                    redireccionar('/JDReparto/modulo_reparto/'.$id_modulo);
                }
            }

        }

}




}


