<?php


class PCumplimiento extends Controlador{

    private $cumplimientoModelo;



    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
        //$this->datos['rolesPermitidos'] = [10];          // Definimos los roles que tendran acceso
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }
        $this->cumplimientoModelo = $this->modelo('CumplimientoM');
    }




    public function index($id_modulo){

        // año lectivo
        $this->datos['lectivo']=$this->cumplimientoModelo->obtener_lectivo();
        $id_lectivo = $this->datos['lectivo'][0]->id_lectivo;

        // info del modulo
        $id_profe = $this->datos['usuarioSesion']->id_profesor;
        $this->datos['datos_modulo'] = $this->cumplimientoModelo->info_modulo($id_profe,$id_modulo, $id_lectivo);

        // fechas evaluaciones e ids seguimiento para el modulo
        $this->datos['evaluaciones'] = $this->cumplimientoModelo->obtener_id_seguimientos_evaluacion($id_modulo);

        // toda la info del EP2 (categorias, preguntas....)
        $this->datos['preguntas'] = $this->cumplimientoModelo->obtener_preguntas_ep2();

        // trae solo los nombres de las categorias
        $this->datos['solo_categorias'] = $this->cumplimientoModelo->solo_categorias();

        // trae el numero de preguntas para cada categoria
        $this->datos['cantidad_categorias'] = $this->cumplimientoModelo->numero_preguntas_categorias();

        // trae todas las respuestas del EP2 para cada evaluacion e id_seguimiento
        $this->datos['ep2'] = $this->cumplimientoModelo->obtener_respuestas_ep2($id_modulo);

        // LOS TOTALES DE CADA EVALUACION (tabla seg_totales)
        $this->datos['total_ep2'] = $this->cumplimientoModelo->seg_totales_ep2($id_modulo);


        
        // funcion para bloquear enlaces si no hay horas o temas
        $this->datos['modulo'] = $this->cumplimientoModelo->obtener_modulos($id_profe, $id_lectivo);

        
        // BLOQUEAR ENLACES SI NO ESTA TODO OK
        $resultado = [];
        $modulo_profesor = $this->datos['datos_modulo'];
        $hay_temas = $this->cumplimientoModelo->hay_temas($modulo_profesor[0]->id_modulo);
        $hay_horas = $this->cumplimientoModelo->hay_horas($modulo_profesor[0]->id_modulo);
        $hay_seguimiento = $this->cumplimientoModelo->hay_seguimiento($modulo_profesor[0]->id_modulo);
        $hay_suma = $this->cumplimientoModelo->suma_temas($modulo_profesor[0]->id_modulo);
        $resultado[] = [
            'id_modulo' => $modulo_profesor[0]->id_modulo,
            'hay_temas' => $hay_temas[0]->hay_temas,
            'hay_horas' => $hay_horas[0]->hay_horas,
            'hay_seguimiento' => $hay_seguimiento[0]->hay_seguimiento,
            'hay_suma' => $hay_suma[0]->suma,
            'horas_totales_modulo' => $modulo_profesor[0]->horas_totales
        ];
        $this->datos['resultado'] = $resultado;



        // vista principal
        $this->vista('profesores/cumplimiento',$this->datos);
    }




/********************************************************************************/
/***************************** INSERTA EP2 **************************************/
/********************************************************************************/


public function insertar_cumplimiento($id_modulo){

        $lectivo = $this->cumplimientoModelo->obtener_lectivo();
        $id_lectivo = $lectivo[0]->id_lectivo;

        $id_profe = $this->datos['usuarioSesion']->id_profesor;
        $info_modulo = $this->cumplimientoModelo->info_modulo($id_profe,$id_modulo, $id_lectivo);


        if($_SERVER['REQUEST_METHOD'] =='POST'){

                $array_respuestas = $_POST;
                $valores_no_vacios = array_filter($array_respuestas, function($valor) {
                    return !empty($valor);
                });

                // si no hay respuestas (SI LLEGA TODO A VACIO)
                if (empty($valores_no_vacios)) {
                    $ids_final = [];
                    foreach ($array_respuestas as $indice => $valor) {
                        $partir = explode("-", $indice);
                        if (isset($partir[3])) {
                            $ids_final[] = ["id_seguimiento" => $partir[3]];
                        }
                    }
                    $ids_seguimiento = array_unique($ids_final, SORT_REGULAR);
                    $this->cumplimientoModelo->borrar_ep2($ids_seguimiento, $id_modulo, $id_lectivo);
                    redireccionar('/PCumplimiento'.'/'.$id_modulo);
                    return;
                }
                
                $respuestas = [];
                $respuesta=0;
                $nulo ='';
                foreach($array_respuestas as $indice => $valor){
                    $partir = explode("-", $indice);
                    if (is_numeric($valor)) {
                        $respuesta = floatval($valor);
                        $nulo = 'no';
                    } else {
                        $respuesta = null;
                        $nulo = 'si';
                    }
                    $respuestas[] = [
                        "indicador" => $partir[0],
                        "categoria" => $partir[1],
                        "pregunta" => $partir[2],
                        "id_seguimiento" => $partir[3],
                        "respuesta" => $respuesta,
                        "nulo" => $nulo
                    ];
                }

                $id_indicador = $respuestas[0]['indicador'];
                $num_categorias = $this->cumplimientoModelo->numero_preguntas_categorias();
                $total_ep2 = calculo_total_ep2($respuestas, $num_categorias, $id_modulo, $id_indicador);

                if($this->cumplimientoModelo->insertar_ep2($respuestas, $id_modulo, $total_ep2, $lectivo, $info_modulo)){
                    redireccionar('/PCumplimiento'.'/'.$id_modulo);
                }else{
                    die('Algo ha fallado!!');
                }

        }else {

            $this->vista('profesores/cumplimiento',$this->datos);

        }

    }






}