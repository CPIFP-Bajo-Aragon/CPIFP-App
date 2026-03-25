<?php


class PDatos extends Controlador{

    private $datosModelo;



        public function __construct(){
            Sesion::iniciarSesion($this->datos);
            $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
            if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
                redireccionar('/');
            }
            $this->datosModelo = $this->modelo('DatosM');
        }




        public function index($id_modulo){
            

            $this->datos['lectivo'] = $this->datosModelo->obtener_lectivo(); 
            $lectivo = $this->datos['lectivo'];
            $id_lectivo = $lectivo[0]->id_lectivo;
            $id_profe = $this->datos['usuarioSesion']->id_profesor;
            $this->datos['datos_modulo'] = $this->datosModelo->info_modulo($id_profe, $id_modulo, $lectivo[0]->id_lectivo); // trae toda la info de un modulo para el curso actual


            // PARA LAS HORAS DE CLASE AL DIA
            $this->datos['dias_semana'] = $this->datosModelo->obtener_dias_semana(); // trae los nombres de los dias de la semana
            $this->datos['horario_modulo'] = $this->datosModelo->obtener_horario_semana_modulo($id_modulo); // trae el horario semanal de un modulo cocreto para curso actual


            // PARA LOS TEMAS DEL MODULO
            $this->datos['temas'] = $this->datosModelo->temas_del_modulo($id_modulo); // trae los temas con examenes y dual (NO FALTAS, ACTIVIDADES y OTROS )
            $this->datos['total_temas'] = $this->datosModelo->contar_temas($id_modulo); // cuenta los temas (NO FALTAS, ACTIVIDADES, OTROS, EXAMENES , DUAL )
            $this->datos['suma_total']=$this->datosModelo->total_horas_temas($id_modulo); // suma las horas de los temas (NO FALTAS, ACTIVIDADES y OTROS )


            $this->datos['evaluaciones'] = $this->datosModelo->obtener_id_seguimientos_evaluacion($id_modulo);
            $intervalos_evaluaciones = intervalos_evaluaciones($lectivo, $this->datos['evaluaciones']);
            // para actualizar la pregunta 38 "HORAS DE DOCENCIA PREVISTAS" si cambia la fecha de evaluacion o el horario del modulo.....
            $this->datosModelo->pregunta_38($id_modulo, $intervalos_evaluaciones);   


            // PARA BLOQUER ENLACES SI NO HAY TEMAS U HORAS
            $this->datos['modulo'] = $this->datosModelo->obtener_modulos($id_profe, $id_lectivo);

            // BLOQUEAR ENLACES SI NO ESTA TODO OK
            $resultado = [];
            $modulo_profesor = $this->datos['datos_modulo'];
            $hay_temas = $this->datosModelo->hay_temas($modulo_profesor[0]->id_modulo);
            $hay_horas = $this->datosModelo->hay_horas($modulo_profesor[0]->id_modulo);
            $hay_seguimiento = $this->datosModelo->hay_seguimiento($modulo_profesor[0]->id_modulo);
            $hay_suma = $this->datosModelo->suma_temas($modulo_profesor[0]->id_modulo);
            $resultado[] = [
                'id_modulo' => $modulo_profesor[0]->id_modulo,
                'hay_temas' => $hay_temas[0]->hay_temas,
                'hay_horas' => $hay_horas[0]->hay_horas,
                'hay_seguimiento' => $hay_seguimiento[0]->hay_seguimiento,
                'hay_suma' => $hay_suma[0]->suma,
                'horas_totales_modulo' => $modulo_profesor[0]->horas_totales
            ];
            $this->datos['resultado'] = $resultado;
            

            $this->vista('profesores/datos',$this->datos);
        }






//***********************************************************************/
//***********************************************************************/
//****************** INSERT Y UPDATE HORAS AL DIA ***********************/
//***********************************************************************/
//***********************************************************************/


    public function horario_semana($id_modulo){

            $registros=$this->datosModelo->obtener_horario_semana_modulo($id_modulo); // buscamos si el modulo ya tienen un horario
            if(empty($registros)){
                $accion = 'insert';
            }else{
                $accion = 'update';
            };

            if($_SERVER['REQUEST_METHOD'] =='POST'){
                $id_horario = $_POST['id_horario'];
                $horas = $_POST['horas'];
                // SABADO
                array_push($id_horario,6);
                array_push($horas,0);
                // DOMINGO
                array_push($id_horario,7);
                array_push($horas,0);
                if($this->datosModelo->horario_semana($id_horario, $horas, $id_modulo, $accion)){
                    redireccionar('/PDatos'.'/'.$id_modulo);
                }else{
                    die('Algo ha fallado!!');
                }
            }else{
                $this->vista('profesores/datos'.$id_modulo,$this->datos);
            }
    }



//***************************************************************************/
//***************************************************************************/
//********** REFERENTE A LOS TEMAS Y LA ASIGNACION HORAS ********************/
//**************************************************************************/
//**************************************************************************/




/************************ NUEVO TEMA ****************************/
        
    // public function nuevo_tema($id_modulo){

    //     if($_SERVER['REQUEST_METHOD'] =='POST'){

    //         $nuevo = [
    //             'id_modulo' => $id_modulo,
    //             'numero_tema' => trim($_POST['numero_tema']),
    //             'descripcion' => trim($_POST['descripcion']),
    //             'total_horas' => trim($_POST['total_horas'])
    //         ];

    //         if($this->datosModelo->nuevo_tema($nuevo)){
    //             redireccionar('/PDatos'.'/'.$id_modulo);
    //         }else{
    //             die('Algo ha fallado!!');
    //         }
    //     }else{
    //         $this->vista('profeSegui/datos_modulo/'.$id_modulo,$this->datos);
    //     }
    // }


/************************ NUEVO TEMA ****************************/


public function nuevo_tema($id_modulo){

    if($_SERVER['REQUEST_METHOD'] =='POST'){

        $descripcion = trim($_POST['descripcion']);
        $prohibidas = ['actividades','otros','faltas','examenes','dual'];

        $desc_normalizada = mb_strtolower($this->quitarAcentos($descripcion));

        if(in_array($desc_normalizada, $prohibidas)){
            die("Error: la descripción está reservada por el sistema.");
        }

        $nuevo = [
            'id_modulo' => $id_modulo,
            'numero_tema' => trim($_POST['numero_tema']),
            'descripcion' => $descripcion,
            'total_horas' => trim($_POST['total_horas'])
        ];

        if($this->datosModelo->nuevo_tema($nuevo)){
            redireccionar('/PDatos'.'/'.$id_modulo);
        }else{
            die('Algo ha fallado!!');
        }

    }else{
        $this->vista('profeSegui/datos_modulo/'.$id_modulo,$this->datos);
    }
}



/************************ BORRAR TEMA ****************************/

    public function borrar_tema($id_tema){

        $id_modulo = $_POST['id_modulo'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->datosModelo->borrar_tema($id_tema)) {
                redireccionar('/PDatos'.'/'.$id_modulo);
            }else{
                die('Algo ha fallado!!!');
            }
        }else{
            $this->vista('profeSegui/datos_modulo/'.$id_modulo,$this->datos);
        }
    }



/************************ EDITAR TEMA ****************************/

    // public function editar_tema($id_tema){

    //     $id_modulo = $_POST['id_modulo'];
        
    //     if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //         $editar = [
    //             'id_modulo' => $id_modulo,
    //             'numero_tema' => trim($_POST['numero_tema']),
    //             'descripcion' => trim($_POST['descripcion']),
    //             'total_horas' => trim($_POST['total_horas'])
    //         ];

    //         if($this->datosModelo->editar_tema($editar,$id_tema)){
    //             redireccionar('/PDatos'.'/'.$id_modulo);
    //         }else{
    //             die('Algo ha fallado!!');
    //         }
    //     }else{
    //         $this->vista('profeSegui/datos_modulo/'.$id_modulo,$this->datos);
    //     }
    // }


/************************ EDITAR TEMA ****************************/


public function editar_tema($id_tema){

    $id_modulo = $_POST['id_modulo'];
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $descripcion = trim($_POST['descripcion']);
        $prohibidas = ['actividades','otros','faltas','examenes','dual'];

        $desc_normalizada = mb_strtolower($this->quitarAcentos($descripcion));

        if(in_array($desc_normalizada, $prohibidas)){
            die("Error: la descripción está reservada por el sistema.");
        }

        $editar = [
            'id_modulo' => $id_modulo,
            'numero_tema' => trim($_POST['numero_tema']),
            'descripcion' => $descripcion,
            'total_horas' => trim($_POST['total_horas'])
        ];

        if($this->datosModelo->editar_tema($editar,$id_tema)){
            redireccionar('/PDatos'.'/'.$id_modulo);
        }else{
            die('Algo ha fallado!!');
        }

    }else{
        $this->vista('profeSegui/datos_modulo/'.$id_modulo,$this->datos);
    }
}



/************************ FUNCION AUXILIAR ****************************/

private function quitarAcentos($str){
    if (!class_exists('Normalizer')) {
        return $str;
    }
    $str = Normalizer::normalize($str, Normalizer::FORM_D);
    return preg_replace('/[\x{0300}-\x{036f}]/u', '', $str);
}



}