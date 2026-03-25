<?php


class PAnalisis extends Controlador{

    private $analisisModelo;



    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
        //$this->datos['rolesPermitidos'] = [10];          // Definimos los roles que tendran acceso
        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }
        $this->analisisModelo = $this->modelo('AnalisisM');
    }




    public function index($id_modulo){

        // LECTIVO
        $this->datos['lectivo'] = $this->analisisModelo->obtener_lectivo();
        $lectivo = $this->datos['lectivo'];
        $id_lectivo = $lectivo[0]->id_lectivo;

        // info del modulo
        $id_profe = $this->datos['usuarioSesion']->id_profesor;
        $this->datos['datos_modulo'] = $this->analisisModelo->info_modulo($id_profe,$id_modulo, $id_lectivo);
        $info_modulo = $this->datos['datos_modulo'];

        $id_grado = $this->datos['datos_modulo'][0]->id_grado;
        $this->datos['indicadores_grado'] = $this->analisisModelo->indicadores_grado($id_grado, $id_lectivo);

        //EVALUACIONES Y SUS IDS SEGUIMIENTO
        $this->datos['evaluaciones'] = $this->analisisModelo->obtener_id_seguimientos_evaluacion($id_modulo);
        $evaluaciones = $this->datos['evaluaciones'];

        // TODAS LAS CAUSAS Y SOLUCIONES
        $this->datos['causas_soluciones'] = $this->analisisModelo->obtener_causas_soluciones();
        
        // SOLO PREGUNTAS EP1
        $this->datos['preguntas_ep1'] = $this->analisisModelo->obtener_preguntas_ep1();



        // ACTUALIZA EL INDICE HI: si cambia la fecha de evaluacion o el horario del modulo.....
        // PREGUNTA 38
        $intervalos_evaluaciones = intervalos_evaluaciones($lectivo,$evaluaciones);
        $this->analisisModelo->pregunta_38($id_modulo, $intervalos_evaluaciones);   
        // PREGUNTA 39 Y 40
        $this->analisisModelo->faltas_otros_x_evaluacion($intervalos_evaluaciones,$id_modulo);// actualiza 39 y 40
        $valores_hi = $this->analisisModelo->respuestas_hi($intervalos_evaluaciones);
        $hi = $this->analisisModelo->indice_hi(); // es el indicador
        $total_hi = calculo_total_hi($valores_hi, $id_modulo); // calculo por evaluacion
        $total_curso_hi = calculo_total_curso_hi($valores_hi); // calculo para el total
        $this->analisisModelo->insertar_total_hi($hi, $total_hi, $id_modulo, $total_curso_hi, $lectivo, $info_modulo);

        // para actualizar la pregunta 36 "HORAS DE DOCENCIA PREVISTAS X ALUS EFECTIVOS" si cambia la fecha de evaluacion o el horario del modulo.....
        $hay_valor = $this->analisisModelo->respuestas_aa_hi($id_modulo); 
        if(!empty($hay_valor)){
            //actualiza la pregunta 36
            $actualizar_aa = actualizar_calculo_aa($hay_valor);
            $this->analisisModelo->actualizar_aa_36($actualizar_aa);
            // actualiza en seg_totales y seg_total_curso el indicador AA
            $resultados = valor_aa($actualizar_aa);
            $respuestas_aa = $this->analisisModelo->obtener_respuestas_indicador($id_modulo, 'AA'); 
            $total_curso = actualizar_seg_total_curso_aa($respuestas_aa);
            $this->analisisModelo->actualizar_total_resultados_aa($resultados, $id_modulo, $total_curso, $lectivo);
        }
    

    
        // INDICADOR EP1 - si se cambia datos, fechas....
        $temas = $this->analisisModelo->temas_del_modulo($id_modulo); // TODOS LOS TEMAS DEL MODULO
        $horas_previstas_mes = $this->analisisModelo->horas_previstas_mes($id_modulo); // HORAS DE CLASE AL MES
        $valores_x_mes = $this->analisisModelo->valores_x_mes($id_modulo); // LOS VALORES DEL DIARIO
        $ep1_x_mes = funcion_ep1($temas, $valores_x_mes, $horas_previstas_mes, $info_modulo); // calculo ep1 (por si cambian festivos, datos....)
        $this->analisisModelo->insertar_ep1($ep1_x_mes, $id_modulo);

        // MEDIA EP1: es el valor de junio.Revisamos si hay edicion o no.
        $hay_edicion = $this->analisisModelo->hay_edicion_manual($id_modulo); // si de ha editado la tabla
        $junio_real = $this->analisisModelo->real_mes_junio($id_modulo); 
        $junio_edicion = $this->analisisModelo->edicion_mes_junio($id_modulo); 
        if(!empty($junio_real) || !empty($junio_edicion)){
            $media_ep1_real = 0;
            if($hay_edicion[0]->total > 0){
                $media_ep1_real = $junio_edicion; 
            } else{
                $media_ep1_real = $junio_real; 
            }
            $this->analisisModelo->insertar_media_ep1($media_ep1_real, $info_modulo, $lectivo);
        }


        

        // PORCENTAJES TOTALES DEL MODULO POR EVALUACION (tabla seg_totales)
        $this->datos['seg_totales'] = $this->analisisModelo->seg_totales($id_modulo);
        // TOTAL DEL MODULO (his_total_modulo)
        $this->datos['total_curso'] = $this->analisisModelo->obtener_total_modulo($id_modulo, $lectivo);


        // VALORES EP1
        $this->datos['edicion_mes'] = $this->analisisModelo->edicion_mes($id_modulo);
        $hay_edicion = $this->analisisModelo->hay_edicion_manual($id_modulo); 
        // SI NO HAY VALORES EN ACUMULADAS EDICION MES
        if(empty($this->datos['edicion_mes'])){
            $this->datos['valores_ep1'] = $this->analisisModelo->valores_ep1($id_modulo);
            $this->datos['valoraciones'] = $this->analisisModelo->ver_valoraciones($id_modulo);
        }else{
            if($hay_edicion[0]->total==0){
                $this->datos['valores_ep1'] = $this->analisisModelo->valores_ep1($id_modulo);
                $this->datos['valoraciones'] = $this->analisisModelo->ver_valoraciones($id_modulo);
            } else{
                $this->datos['valores_ep1'] = $this->analisisModelo->valores_ep1_acumuladas_editadas($id_modulo);
                $this->datos['valoraciones'] = $this->analisisModelo->ver_valoraciones($id_modulo);
            }
        }

        $this->datos['valoraciones'] = $this->analisisModelo->ver_valoraciones($id_modulo);


        // BLOQUEAR ENLACES
        $this->datos['modulo'] = $this->analisisModelo->obtener_modulos($id_profe, $id_lectivo);
        
        // BLOQUEAR ENLACES SI NO ESTA TODO OK
        $resultado = [];
        $modulo_profesor = $this->datos['datos_modulo'];
        $hay_temas = $this->analisisModelo->hay_temas($modulo_profesor[0]->id_modulo);
        $hay_horas = $this->analisisModelo->hay_horas($modulo_profesor[0]->id_modulo);
        $hay_seguimiento = $this->analisisModelo->hay_seguimiento($modulo_profesor[0]->id_modulo);
        $hay_suma = $this->analisisModelo->suma_temas($modulo_profesor[0]->id_modulo);
        $resultado[] = [
            'id_modulo' => $modulo_profesor[0]->id_modulo,
            'hay_temas' => $hay_temas[0]->hay_temas,
            'hay_horas' => $hay_horas[0]->hay_horas,
            'hay_seguimiento' => $hay_seguimiento[0]->hay_seguimiento,
            'hay_suma' => $hay_suma[0]->suma,
            'horas_totales_modulo' => $modulo_profesor[0]->horas_totales
        ];
        $this->datos['resultado'] = $resultado;




        // VISTA PRINCIPAL
        $this->vista('profesores/analisis',$this->datos);
    }





    
/********************************************************************************/
/***************************** INSERTA CAUSAS Y SOLUCIONES ***********************/
/********************************************************************************/
    
public function causas_soluciones($id_modulo){

        $this->datos['lectivo'] = $this->analisisModelo->obtener_lectivo();
        $id_lectivo = $this->datos['lectivo'][0]->id_lectivo;
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
            $causa = isset($_POST['causa']) ? $_POST['causa'] : [];
            $causa2 = isset($_POST['causa2']) ? $_POST['causa2'] : [];
            $solucion = isset($_POST['solucion']) ? $_POST['solucion'] : [];
            $solucion2 = isset($_POST['solucion2']) ? $_POST['solucion2'] : [];
            $solucion3 = isset($_POST['solucion3']) ? $_POST['solucion3'] : [];
            $observaciones = isset($_POST['observaciones']) ? $_POST['observaciones'] : [];
            $otro1 = isset($_POST['otro1']) ? $_POST['otro1'] : [];
            $otro2 = isset($_POST['otro2']) ? $_POST['otro2'] : [];
            $otro3 = isset($_POST['otro3']) ? $_POST['otro3'] : [];
    
            $resultado = [];

            // Iteramos por cada evaluación y creamos un objeto por cada evaluacion
            foreach ($causa as $key => $causa_value) {

                $obj = new stdClass();
                $obj->id_lectivo = $id_lectivo;
                $obj->id_seguimiento = $key;
                $obj->id_modulo = $id_modulo;
                $obj->causa = isset($causa[$key]) ? $causa[$key] : null;
                $obj->causa2 = isset($causa2[$key]) ? $causa2[$key] : null;
                $obj->otro1 = isset($otro1[$key]) ? $otro1[$key] : null;
                $obj->otro2 = isset($otro2[$key]) ? $otro2[$key] : null;
                $obj->otro3 = isset($otro3[$key]) ? $otro3[$key] : null;
                $obj->solucion = isset($solucion[$key]) ? $solucion[$key] : null;
                $obj->solucion2 = isset($solucion2[$key]) ? $solucion2[$key] : null;
                $obj->solucion3 = isset($solucion3[$key]) ? $solucion3[$key] : null;
                $obj->observaciones = isset($observaciones[$key]) ? $observaciones[$key] : null;
    
                $resultado[] = $obj;
            }
    
            $this->analisisModelo->insertar_valoraciones($resultado, $id_modulo);
            redireccionar('/PAnalisis'.'/'. $id_modulo);

        } else {

            $this->vista('profesores/analisis/' . $id_modulo, $this->datos);
            
        }
}
    
    
    


/********************************************************************************/
/***************************** GUARDA OBSERVACIONES ******************************/
/********************************************************************************/

public function guardar_observaciones($id_modulo){

    $id_seguimiento = $this->analisisModelo->id_seguimiento_ep1($id_modulo);
    $id_seguimiento =  $id_seguimiento[0]->id_seguimiento;

    if($_SERVER['REQUEST_METHOD'] =='POST'){
        $observaciones = $_POST['observaciones']; 
        $id_pregunta = $_POST['id_pregunta']; 
        $this->analisisModelo->guardar_observaciones($id_seguimiento, $id_pregunta, $observaciones);
        redireccionar('/PAnalisis'.'/'.$id_modulo);
    }else{
        $this->vista('profesores/analisis/'.$id_modulo,$this->datos);
    }
}





}