<?php


class PEnsenanza extends Controlador{

    private $ensenanzaModelo;



    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
        //$this->datos['rolesPermitidos'] = [10];          // Definimos los roles que tendran acceso

        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }

        $this->ensenanzaModelo = $this->modelo('EnsenanzaM');

    }




    public function index($id_modulo){

        // LECTIVO
        $this->datos['lectivo']=$this->ensenanzaModelo->obtener_lectivo();
        $lectivo = $this->datos['lectivo'];
        $id_lectivo = $lectivo[0]->id_lectivo;

        // info del modulo
        $id_profe = $this->datos['usuarioSesion']->id_profesor;
        $this->datos['datos_modulo']=$this->ensenanzaModelo->info_modulo($id_profe,$id_modulo, $id_lectivo);
        $info_modulo = $this->datos['datos_modulo'];

        // fechas evaluaciones e ids seguimiento para el modulo
        $this->datos['evaluaciones']=$this->ensenanzaModelo->obtener_id_seguimientos_evaluacion($id_modulo); 
        $evaluaciones = $this->datos['evaluaciones'];


        // ACTUALIZA EL INDICE HI: si cambia la fecha de evaluacion o el horario del modulo.....
        // PREGUNTA 38
        $intervalos_evaluaciones = intervalos_evaluaciones($lectivo,$evaluaciones);
        $this->ensenanzaModelo->pregunta_38($id_modulo, $intervalos_evaluaciones);   
        // PREGUNTA 39 Y 40
        $this->ensenanzaModelo->faltas_otros_x_evaluacion($intervalos_evaluaciones,$id_modulo);// actualiza 39 y 40
        $valores_hi = $this->ensenanzaModelo->respuestas_hi($intervalos_evaluaciones);
        $hi = $this->ensenanzaModelo->indice_hi(); // es el indicador
        $total_hi = calculo_total_hi($valores_hi, $id_modulo); // calculo por evaluacion
        $total_curso_hi = calculo_total_curso_hi($valores_hi); // calculo para el total
        $this->ensenanzaModelo->insertar_total_hi($hi, $total_hi, $id_modulo, $total_curso_hi, $lectivo, $info_modulo);


        // para actualizar la pregunta 36 "HORAS DE DOCENCIA PREVISTAS X ALUS EFECTIVOS" si cambia la fecha de evaluacion o el horario del modulo.....
        $hay_valor = $this->ensenanzaModelo->respuestas_aa_hi($id_modulo); 
        if(!empty($hay_valor)){
            //actualiza la pregunta 36
            $actualizar_aa = actualizar_calculo_aa($hay_valor);
            $this->ensenanzaModelo->actualizar_aa_36($actualizar_aa);
            // actualiza en seg_totales y seg_total_curso el indicador AA
            $resultados = valor_aa($actualizar_aa);
            $respuestas_aa = $this->ensenanzaModelo->obtener_respuestas_indicador($id_modulo, 'AA'); 
            $total_curso = actualizar_seg_total_curso_aa($respuestas_aa);
            $this->ensenanzaModelo->actualizar_total_resultados_aa($resultados, $id_modulo, $total_curso, $lectivo);
        }
    

         // INDICADORES Y RESPUESTAS
         $this->datos['indicador_aa'] = $this->ensenanzaModelo->indicador_preguntas('AA');
         $this->datos['respuestas_aa'] = $this->ensenanzaModelo->obtener_respuestas_indicador($id_modulo, 'AA'); 
 
         $this->datos['indicador_hi'] = $this->ensenanzaModelo->indicador_preguntas('HI');
         $this->datos['respuestas_hi'] = $this->ensenanzaModelo->obtener_respuestas_indicador($id_modulo, 'HI'); 
 
         $this->datos['indicador_ap'] = $this->ensenanzaModelo->indicador_preguntas('AP');
         $this->datos['respuestas_ap'] = $this->ensenanzaModelo->obtener_respuestas_indicador($id_modulo, 'AP'); 
 
         $this->datos['indicador_at'] = $this->ensenanzaModelo->indicador_preguntas('AT');
         $this->datos['respuestas_at'] = $this->ensenanzaModelo->obtener_respuestas_indicador($id_modulo, 'AT'); 
 
         $this->datos['indicador_ap2'] = $this->ensenanzaModelo->indicador_preguntas('AP2');
         $this->datos['respuestas_ap2'] = $this->ensenanzaModelo->obtener_respuestas_indicador($id_modulo, 'AP2'); 
         $this->datos['segui_ap2'] = $this->ensenanzaModelo->segui_ap2($id_modulo); // id_seguimiento para AP2
 
         // PORCENTAJES TOTALES        
         $this->datos['seg_totales']=$this->ensenanzaModelo->seg_totales($id_modulo);
         $this->datos['total_curso']=$this->ensenanzaModelo->obtener_total_curso($id_modulo, $lectivo);



        // funcion para bloquear enlaces si no hay horas o temas
        $this->datos['modulo'] = $this->ensenanzaModelo->obtener_modulos($id_profe, $id_lectivo);

        
        // BLOQUEAR ENLACES SI NO ESTA TODO OK
        $resultado = [];
        $modulo_profesor = $this->datos['datos_modulo'];
        $hay_temas = $this->ensenanzaModelo->hay_temas($modulo_profesor[0]->id_modulo);
        $hay_horas = $this->ensenanzaModelo->hay_horas($modulo_profesor[0]->id_modulo);
        $hay_seguimiento = $this->ensenanzaModelo->hay_seguimiento($modulo_profesor[0]->id_modulo);
        $hay_suma = $this->ensenanzaModelo->suma_temas($modulo_profesor[0]->id_modulo);
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
        $this->vista('profesores/ensenanza',$this->datos);
    }





/******************************************************************************/   
/******************************************************************************/   
/****************************** INDICADOR AA *********************************/   
/******************************************************************************/   
/******************************************************************************/   


public function insertar_aa($modulo){

    if($_SERVER['REQUEST_METHOD'] =='POST'){

        $lectivo = $this->ensenanzaModelo->obtener_lectivo();
        $id_lectivo = $lectivo[0]->id_lectivo;
    
        $id_profe = $this->datos['usuarioSesion']->id_profesor;
        $info_modulo = $this->ensenanzaModelo->info_modulo($id_profe, $modulo, $id_lectivo);
    
        $respuestas = $_POST['respuestas'];
        $id_indicador = 1;
        $indicador_corto = 'AA';

        $valores = [];
        foreach ($respuestas as $indice => $respuesta_value) {
            $partes = explode('-', $indice);
            if(count($partes)==3){ //verificacion que el indice tiene 3 partes
                list($indicador_corto, $pregunta, $id_seguimiento) = explode('-', $indice); // Descomponemos el índice
                $nuevo_objeto = new stdClass(); // Creamos un objeto 
                $nuevo_objeto->indicador_corto = $indicador_corto;
                $nuevo_objeto->pregunta = $pregunta;
                $nuevo_objeto->id_seguimiento = $id_seguimiento;
                $nuevo_objeto->respuesta = $respuesta_value;
                $valores[] = $nuevo_objeto; // Agrupamos las respuestas por id_evaluacion
            }
        };


        // respuestas 38
        $res_hi = $this->ensenanzaModelo->respuestas_38_hi($modulo); 

        // Agrupar respuestas por id_seguimiento
        $grouped = [];
        foreach ($valores as $item) {
            $grouped[$item->id_seguimiento][$item->pregunta] = $item->respuesta;
        }
        // agrupamos pregunta 38 por id_seguimiento
        $horasGrouped = [];
        foreach ($res_hi as $item) {
            $horasGrouped[$item->id_seguimiento] = $item->respuesta;
        }


        // Calcular la pregunta 35 y la pregunta 36 para cada id_seguimiento
        foreach ($grouped as $id => $respuestas) {
            if (isset($respuestas[33]) && isset($respuestas[34])) {
                // Calculamos la respuesta para la pregunta 35
                $resta = floatval($respuestas[33])  - floatval($respuestas[34]) ;
                
                // Crear el objeto para la pregunta 35
                $valores[] = (object)[
                    'indicador_corto' => 'AA',
                    'pregunta' => 35,
                    'id_seguimiento' => $id,
                    'respuesta' => $resta
                ];
        
                // Verificamos si existe la respuesta para las "Horas de docencia previstas" para este id_seguimiento
                if (isset($horasGrouped[$id])) {
                    $horasDocenciaRespuesta = $horasGrouped[$id];
                    
                    // Calculamos la respuesta para la pregunta 36 (multiplicación)
                    $respuesta36 = $resta * $horasDocenciaRespuesta;
        
                    // Crear el objeto para la pregunta 36
                    $valores[] = (object)[
                        'indicador_corto' => 'AA',
                        'pregunta' => 36,
                        'id_seguimiento' => $id,
                        'respuesta' => $respuesta36
                    ];
                }
            }
        }

            
        $total = calculos_aa ($valores); // para tabla SEG_TOTALES
        $total_curso = total_curso_aa ($valores);  // para tabla HIS_TOTAL_MODULO

        if($this->ensenanzaModelo->insertar_aa($valores, $total, $modulo, $id_indicador, $total_curso, $lectivo, $info_modulo)){
            redireccionar('/PEnsenanza'.'/'.$modulo);               
        }
    }else{
        $this->vista('profesores/ensenanza',$this->datos);
    }

}




/******************************************************************************/   
/******************************************************************************/   
/****************************** INDICADOR AP *********************************/   
/******************************************************************************/   
/******************************************************************************/   


public function insertar_ap($modulo){

    $lectivo = $this->ensenanzaModelo->obtener_lectivo();
    $id_lectivo = $lectivo[0]->id_lectivo;

    $id_profe = $this->datos['usuarioSesion']->id_profesor;
    $info_modulo = $this->ensenanzaModelo->info_modulo($id_profe,$modulo, $id_lectivo);


    if($_SERVER['REQUEST_METHOD'] =='POST'){

            $respuestas = $_POST['respuestas'];
            $id_indicador = 4;
            $indicador_corto = 'AP';


            $valores = [];
            foreach ($respuestas as $indice => $respuesta_value) {
                $partes = explode('-', $indice);
                if(count($partes)==3){ //verificacion que el indice tiene 3 partes
                    list($indicador_corto, $pregunta, $id_seguimiento) = explode('-', $indice); // Descomponemos el índice
                    $nuevo_objeto = new stdClass(); // Creamos un objeto 
                    $nuevo_objeto->indicador_corto = $indicador_corto;
                    $nuevo_objeto->pregunta = $pregunta;
                    $nuevo_objeto->id_seguimiento = $id_seguimiento;
                    $nuevo_objeto->respuesta = $respuesta_value;
                    $valores[] = $nuevo_objeto; // Agrupamos las respuestas por id_evaluacion
                }
            };

            $total = calculos_ap ($valores);

        if($this->ensenanzaModelo->insertar_ap($valores, $total, $modulo, $id_indicador, $lectivo, $info_modulo)){
            redireccionar('/PEnsenanza'.'/'.$modulo);               
        }

    }else{
        $this->vista('profesores/ensenanza',$this->datos);
    }
}



/******************************************************************************/   
/******************************************************************************/   
/****************************** INDICADOR AT **********************************/   
/******************************************************************************/   
/******************************************************************************/   


public function insertar_at($id_modulo){

    $lectivo = $this->ensenanzaModelo->obtener_lectivo();
    $id_lectivo = $lectivo[0]->id_lectivo;

    // info del modulo
    $id_profe = $this->datos['usuarioSesion']->id_profesor;
    $info_modulo = $this->ensenanzaModelo->info_modulo($id_profe,$id_modulo, $id_lectivo);


    if($_SERVER['REQUEST_METHOD'] =='POST'){

            $respuestas = $_POST['respuestas'];
            $id_indicador = 5;
            $indicador_corto = 'AT';

            $valores = [];
            foreach ($respuestas as $indice => $respuesta_value) {
                $partes = explode('-', $indice);
                if(count($partes)==3){ //verificacion que el indice tiene 3 partes
                    list($indicador_corto, $pregunta, $id_seguimiento) = explode('-', $indice); // Descomponemos el índice
                    $nuevo_objeto = new stdClass(); // Creamos un objeto 
                    $nuevo_objeto->indicador_corto = $indicador_corto;
                    $nuevo_objeto->pregunta = $pregunta;
                    $nuevo_objeto->id_seguimiento = $id_seguimiento;
                    $nuevo_objeto->respuesta = $respuesta_value;
                    $valores[] = $nuevo_objeto; // Agrupamos las respuestas por id_evaluacion
                }
            };

            $total = calculos_at ($valores);

 
        if($this->ensenanzaModelo->insertar_at($valores, $total, $id_modulo, $id_indicador, $lectivo, $info_modulo)){
            redireccionar('/PEnsenanza'.'/'.$id_modulo);               
        }
    }else{
   
        $this->vista('profesores/ensenanza',$this->datos);
    }
}



/******************************************************************************/   
/******************************************************************************/   
/****************************** INDICADOR AP2 *********************************/   
/******************************************************************************/   
/******************************************************************************/   


public function insertar_ap2($id_modulo){

    $lectivo = $this->ensenanzaModelo->obtener_lectivo();
    $id_lectivo = $lectivo[0]->id_lectivo;

    $id_profe = $this->datos['usuarioSesion']->id_profesor;
    $info_modulo = $this->ensenanzaModelo->info_modulo($id_profe,$id_modulo, $id_lectivo);


    if($_SERVER['REQUEST_METHOD'] =='POST'){

        $respuestas = $_POST['respuestas'];
        $id_indicador = 7;
        $indicador_corto = 'AP2';

        // SI NO HAY VALORES NO HACEMOS NADA
        $valores_no_vacios = array_filter($respuestas, function($valor) {
            return !empty($valor);
        });

        if (empty($valores_no_vacios)) {
            $id_seguimiento = "";
            foreach ($respuestas as $indice => $valor) {
                $partir = explode("-", $indice);
                if (isset($partir[2])) {
                    $id_seguimiento = $partir[2];
                }
            }
            $this->ensenanzaModelo->borrar_ap2($id_seguimiento, $id_indicador, $id_modulo, $id_lectivo);
            redireccionar('/PEnsenanza'.'/'.$id_modulo);   
            return; 
        }

        // SI HAY RESPUESTAS
        $valores = [];
        foreach ($respuestas as $indice => $respuesta_value) {
            $partes = explode('-', $indice); // descomponemos indice y contamos cuantas partes hay
            if(count($partes)==3){ // verificamos que hay 3 partes
                list($indicador_corto, $pregunta, $id_seguimiento) = explode('-', $indice); // Descomponemos el índice y asignamos variables
                $nuevo_objeto = new stdClass(); // Creamos un objeto 
                $nuevo_objeto->indicador_corto = $indicador_corto;
                $nuevo_objeto->pregunta = $pregunta;
                $nuevo_objeto->id_seguimiento = $id_seguimiento;
                $nuevo_objeto->respuesta = $respuesta_value;
                $valores[] = $nuevo_objeto; // Agrupamos las respuestas por id_evaluacion
            }
        };


        $total = total_ap2($valores); 
        $total_curso = $total[0]->resultado;

        if($this->ensenanzaModelo->insertar_ap2($valores, $total, $id_modulo, $id_indicador, $total_curso, $lectivo, $info_modulo)){
            redireccionar('/PEnsenanza'.'/'.$id_modulo);               
        }
    }else{
        $this->vista('profesores/ensenanza',$this->datos);
    }

}




}