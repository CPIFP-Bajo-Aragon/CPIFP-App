<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


class PDiario extends Controlador{

    private $diarioModelo;



    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
        //$this->datos['rolesPermitidos'] = [10];          // Definimos los roles que tendran acceso

        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }
        $this->diarioModelo = $this->modelo('DiarioM');
    }





    public function index($id_modulo){

        //LECTIVO
        $this->datos['lectivo']=$this->diarioModelo->obtener_lectivo();
        $lectivo=$this->datos['lectivo'];
        $id_lectivo=$this->datos['lectivo'][0]->id_lectivo;

        // info del modulo
        $id_profe = $this->datos['usuarioSesion']->id_profesor;
        $this->datos['datos_modulo']=$this->diarioModelo->info_modulo($id_profe,$id_modulo,$id_lectivo);

        // RECOGE TODOS LOS IDS DE SEGUIMIENTO PARA CADA EVALUACION DE UN MODULO CONCRETO
        $this->datos['evaluaciones']=$this->diarioModelo->obtener_id_seguimientos_evaluacion($id_modulo);
        $evaluaciones = $this->datos['evaluaciones'];
        $intervalos_evaluaciones = intervalos_evaluaciones($lectivo, $evaluaciones);

        // TODOS LOS TEMAS DEL MODULO
        $this->datos['temas']=$this->diarioModelo->temas_del_modulo($id_modulo);

        // DEVUELVE EL HORARIO DE LA SEMANA DEL MODULO
        $this->datos['horario_semana'] = $this->diarioModelo->obtener_horario_semana_modulo($id_modulo);

        $this->datos['suma_horas_temas'] = $this->diarioModelo->suma_horas_x_temas($id_modulo); // SUMA TODAS LAS HORAS IMPARTIDAS DE UN TEMA

        // CALENDARIO DEL MODULO
        $this->datos['calendario']=$this->diarioModelo->calendario_bueno_modulo($id_modulo,$id_lectivo);

        // OBTENER DIARIO
        $this->datos['diario']=$this->diarioModelo->obtener_diario($id_modulo);

        // para actualizar la pregunta 38 "HORAS DE DOCENCIA PREVISTAS" si cambia la fecha de evaluacion o el horario del modulo.....
        $this->diarioModelo->pregunta_38($id_modulo, $intervalos_evaluaciones);   

        $this->datos['modulo'] = $this->diarioModelo->obtener_modulos($id_profe, $id_lectivo);

        // BLOQUEAR ENLACES SI NO ESTA TODO OK
        $resultado = [];
        $modulo_profesor = $this->datos['datos_modulo'];
        $hay_temas = $this->diarioModelo->hay_temas($modulo_profesor[0]->id_modulo);
        $hay_horas = $this->diarioModelo->hay_horas($modulo_profesor[0]->id_modulo);
        $hay_seguimiento = $this->diarioModelo->hay_seguimiento($modulo_profesor[0]->id_modulo);
        $hay_suma = $this->diarioModelo->suma_temas($modulo_profesor[0]->id_modulo);
        $resultado[] = [
            'id_modulo' => $modulo_profesor[0]->id_modulo,
            'hay_temas' => $hay_temas[0]->hay_temas,
            'hay_horas' => $hay_horas[0]->hay_horas,
            'hay_seguimiento' => $hay_seguimiento[0]->hay_seguimiento,
            'hay_suma' => $hay_suma[0]->suma,
            'horas_totales_modulo' => $modulo_profesor[0]->horas_totales
        ];
        $this->datos['resultado'] = $resultado;



        $this->vista('profesores/diario',$this->datos);

    }






public function insertar_diario ($id_modulo){
     
        $lectivo = $this->diarioModelo->obtener_lectivo();
        $id_lectivo = $lectivo[0]->id_lectivo;
        $diario_entero = $this->diarioModelo->obtener_diario($id_modulo);
        $id_profe = $this->datos['usuarioSesion']->id_profesor;
        $info_modulo = $this->diarioModelo->info_modulo($id_profe,$id_modulo, $id_lectivo);


        if($_SERVER['REQUEST_METHOD'] =='POST'){

            $diario = $_POST['diario'];
            $diario = array_filter($diario);
            $plan = $_POST['plan'];
            $plan = array_filter($plan);
            $actividad = $_POST['actividad'];
            $actividad = array_filter($actividad);
            $observaciones = $_POST['observaciones'];
            $observaciones = array_filter($observaciones);

            $envio = [];

            foreach ($diario as $key => $hora) {

                $fech = explode('@', $key);
                $fecha = $fech[0];
                $tema = $fech[1];
                
                $obj = [
                    'modulo' => $id_modulo,  
                    'profesor' => $id_profe,  
                    'fecha' => $fecha,
                    'tema' => $tema,
                    'horas' => $hora,
                    'plan' => '', 
                    'actividad' => '', 
                    'observaciones' => ''
                ];
                          
                foreach ($plan as $plan_key => $plan_value) {
                    if (strpos($plan_key, "{$fecha}@plan") !== false ) {
                        $obj['plan'] = $plan_value;
                    }
                }

                foreach ($actividad as $actividad_key => $actividad_value) {
                    if (strpos($actividad_key, "{$fecha}@actividad") !== false ) {
                        $obj['actividad'] = $actividad_value;
                    }
                }

                foreach ($observaciones as $observaciones_key => $observaciones_value) {
                    if (strpos($observaciones_key, "{$fecha}@observaciones") !== false ) {
                        $obj['observaciones'] = $observaciones_value;
                    }
                }

                $envio[] = $obj;
            }


            // PRIMERO INSERTAMOS EL DIARIO
            $this->diarioModelo->insertar_actualizar_diario($envio, $id_modulo, $diario_entero);


            // INDICADOR EP1 - PARA TABLA HORAS IMPARTIDAS POR MES
            $temas = $this->diarioModelo->temas_del_modulo($id_modulo); // TODOS LOS TEMAS DEL MODULO
            $valores = $this->diarioModelo->valores_x_mes($id_modulo); // LOS VALORES DEL DIARIO
            $horas_previstas_mes = $this->diarioModelo->horas_previstas_mes($id_modulo); // horas de clase previstas al mes
            $ep1_x_mes = funcion_ep1($temas, $valores, $horas_previstas_mes, $info_modulo); // calculo ep1
            $this->diarioModelo->insertar_ep1($ep1_x_mes, $id_modulo);

            
            // MEDIA EP1: es el valor de junio.Revisamos si hay edicion o no.
            $junio_real = $this->diarioModelo->valores_junio_real($id_modulo); // valores de seg_ep1_real
            $junio_edicion = $this->diarioModelo->valores_junio_edicion($id_modulo); // valores de seg_ep1_edicion
            $hay_edicion = $this->diarioModelo->hay_edicion_manual($id_modulo); // valores de seg_ep1_edicion

            $media_ep1_real = 0;
            if($hay_edicion[0]->total > 0){
                $media_ep1_real = $junio_edicion; 
            } else{
                $media_ep1_real = $junio_real; 
            }
            $this->diarioModelo->insertar_media_ep1($media_ep1_real, $info_modulo, $lectivo);


            // INDICADOR HI
            // recuperamos la suma de las hotas de faltas profe y de faltas otros por cada evaluacion
            // y guardamos en HORAS. Actualizamos el valor de las preguntas 39 y 40 que tenian valor a 0
            $evaluaciones = $this->diarioModelo->obtener_id_seguimientos_evaluacion($id_modulo);
            $evas = intervalos_evaluaciones($lectivo, $evaluaciones);
            $this->diarioModelo->faltas_otros_x_evaluacion($evas,$id_modulo); // actualiza 39 y 40
            $valores_hi = $this->diarioModelo->respuestas_hi($evas);
            $hi = $this->diarioModelo->indice_hi(); // es el indicador
            $total_hi = calculo_total_hi($valores_hi, $id_modulo); // calculo por evaluacion
            $total_curso_hi = calculo_total_curso_hi($valores_hi); // calculo para el total
                                
            if($this->diarioModelo->insertar_total_hi($hi, $total_hi, $id_modulo, $total_curso_hi, $lectivo, $info_modulo)){
                redireccionar('/PDiario'.'/'.$id_modulo);
            }else{
                die('Algo ha fallado!!');
            }
        }else{
            $this->vista('profeSegui/diario'.$id_modulo,$this->datos);
        }
    }




}