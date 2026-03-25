<?php


class PHorasImpartidas extends Controlador{

    private $horasImpartidasModelo;



    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
        //$this->datos['rolesPermitidos'] = [10];          // Definimos los roles que tendran acceso

        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }

        $this->horasImpartidasModelo = $this->modelo('HorasImpartidasM');

    }




public function index($id_modulo){

        // LECTIVO
        $this->datos['lectivo']=$this->horasImpartidasModelo->obtener_lectivo();
        $lectivo = $this->datos['lectivo'];
        $id_lectivo = $this->datos['lectivo'][0]->id_lectivo;

        // INFO DEL MODULO
        $id_profe = $this->datos['usuarioSesion']->id_profesor;
        $this->datos['datos_modulo'] = $this->horasImpartidasModelo->info_modulo($id_profe, $id_modulo, $id_lectivo);
        $info_modulo = $this->datos['datos_modulo'];

        // HORAS DE CLASE AL MES
        $this->datos['horas_previstas_mes'] = $this->horasImpartidasModelo->horas_previstas_mes($id_modulo);
        $horas_previstas_mes = $this->datos['horas_previstas_mes'];

        $meses = array(
            (object)['numero'=>10, 'mes'=>'Octubre', 'pregunta'=>47],
            (object)['numero'=>11, 'mes'=>'Noviembre', 'pregunta'=>48],
            (object)['numero'=>12, 'mes'=>'Diciembre', 'pregunta'=>49],
            (object)['numero'=>1,  'mes'=>'Enero', 'pregunta'=>50],
            (object)['numero'=>2,  'mes'=>'Febrero', 'pregunta'=>51],
            (object)['numero'=>3,  'mes'=>'Marzo', 'pregunta'=>52],
            (object)['numero'=>4,  'mes'=>'Abril', 'pregunta'=>53],
            (object)['numero'=>5,  'mes'=>'Mayo', 'pregunta'=>54],
            (object)['numero'=>6,  'mes'=>'Junio', 'pregunta'=>55]
        );

        $this->datos['meses'] = $meses; 

        // TEMAS DEL MODULO
        $this->datos['temas'] = $this->horasImpartidasModelo->temas_del_modulo($id_modulo); // TODOS LOS TEMAS DEL MODULO
        $temas = $this->datos['temas'];

        // 1 TABLA (impartido por mes)
        $this->datos['valores'] = $this->horasImpartidasModelo->valores($id_modulo, $temas); // valores del diario
        $valores = $this->datos['valores'] ;
        $this->datos['horas_temas'] = $this->horasImpartidasModelo->suma_horas_x_temas($id_modulo); // SUMA TODAS LAS HORAS IMPARTIDAS DE UN TEMA
        $this->datos['horas_mes_temas'] = $this->horasImpartidasModelo->total_mes_temas($id_modulo); // SUMA TODAS LAS HORAS IMPARTIDAS EN UN MES (contenidos impartidos por cada mes)


        // 2 TABLA (evaluaciones)
        $this->datos['evaluaciones'] = $this->horasImpartidasModelo->obtener_id_seguimientos_evaluacion($id_modulo);
        $intervalos_evaluaciones = intervalos_evaluaciones($lectivo, $this->datos['evaluaciones']);
        // para actualizar la pregunta 38 "HORAS DE DOCENCIA PREVISTAS" si cambia la fecha de evaluacion o el horario del modulo.....
        $this->horasImpartidasModelo->pregunta_38($id_modulo, $intervalos_evaluaciones);   
        $this->datos['total_x_evaluaciones'] = $this->horasImpartidasModelo->total_x_evaluaciones($intervalos_evaluaciones); // horas que esta dando en cada evaluacion de los temas
        $this->datos['total_eva_contenidos'] = $this->horasImpartidasModelo->total_eva_contenidos($intervalos_evaluaciones);
        $this->datos['hrs_previstas_x_evaluacion'] = $this->horasImpartidasModelo->hrs_previstas_x_evaluacion($this->datos['evaluaciones']);

        // 3 TABLA (acumulados)
        $datos = $this->datos['valores']; //datos del diario
        $acumuladas_temas = acumuladas_ep1_temas($datos);
        $this->datos['acumuladas'] = $acumuladas_temas; // ACUMULADOS para los temas y los meses

        $this->datos['total_mes'] = acumuladas_total_mes($acumuladas_temas);
        $total_mes = $this->datos['total_mes']; // total acumulado contenidos impartidos

        $id_alto = $this->horasImpartidasModelo->id_mas_alto($id_modulo);
        $horas_previstas_mes =  $this->datos['horas_previstas_mes'];
        $previstas_acumuladas_mes = acumuladas_ep1_mes($horas_previstas_mes, $id_alto, $id_modulo);
        $this->datos['acumuladas_mes'] = $previstas_acumuladas_mes; // previstas acumuladas mes


        // 4 TABLA (edición acumuladas)
        $this->datos['verificacion_diario'] = $this->horasImpartidasModelo->diario_verificacion($id_modulo); // si hay o no datos en el diario
        $hay_edicion = $this->horasImpartidasModelo->hay_edicion_manual($id_modulo); // si de ha editado la tabla
        $this->datos['edicion_tema'] = $this->horasImpartidasModelo->edicion_tema($id_modulo);
        $this->datos['edicion_mes'] = $this->horasImpartidasModelo->edicion_mes($id_modulo);


        // INDICADOR EP1 - PARA TABLA HORAS IMPARTIDAS POR MES
        $valores_x_mes = $this->horasImpartidasModelo->valores_x_mes($id_modulo); // LOS VALORES DEL DIARIO
        $ep1_x_mes = funcion_ep1($temas, $valores_x_mes, $horas_previstas_mes, $info_modulo); // calculo ep1 (por si cambian festivos, datos....)
        $this->horasImpartidasModelo->insertar_ep1($ep1_x_mes, $id_modulo);
        $this->datos['valores_ep1'] = $this->horasImpartidasModelo->valores_ep1($id_modulo);
        $valores_ep1 = $this->datos['valores_ep1'];

        // MEDIA EP1: es el valor de junio.Revisamos si hay edicion o no.
        $junio_real = $this->horasImpartidasModelo->real_mes_junio($id_modulo); 
        $junio_edicion = $this->horasImpartidasModelo->edicion_mes_junio($id_modulo); 
        
        if(!empty($junio_real) || !empty($junio_edicion)){
            $media_ep1_real = 0;
            if($hay_edicion[0]->total > 0){
                $media_ep1_real = $junio_edicion; 
            } else{
                $media_ep1_real = $junio_real; 
            }
            $this->horasImpartidasModelo->insertar_media_ep1($media_ep1_real, $info_modulo, $lectivo);
        }

        // INSERTA O ACTUALIZA ACUMULADAS SI:
        // - Hay datos en el diario
        // - Y NO hay edición manual (es decir, el usuario no ha tocado la tabla)
        if (!empty($this->datos['verificacion_diario']) && $hay_edicion[0]->total==0) {

            // Si ya existen datos en las tablas, primero los eliminamos
            if (!empty($this->datos['edicion_tema']) || !empty($this->datos['edicion_mes'])) {
                $this->horasImpartidasModelo->eliminar_acumuladas($id_modulo);
            }

            $this->horasImpartidasModelo->insertar_acumuladas($valores_ep1, $previstas_acumuladas_mes, $total_mes, $acumuladas_temas, $id_modulo, $id_alto);
            $this->datos['edicion_tema'] = $this->horasImpartidasModelo->edicion_tema($id_modulo);
            $this->datos['edicion_mes'] = $this->horasImpartidasModelo->edicion_mes($id_modulo);
        }



        // funcion para bloquear enlaces si no hay horas o temas
        $this->datos['modulo'] = $this->horasImpartidasModelo->obtener_modulos($id_profe, $id_lectivo);

        
        // BLOQUEAR ENLACES SI NO ESTA TODO OK
        $resultado = [];
        $modulo_profesor = $this->datos['datos_modulo'];
        $hay_temas = $this->horasImpartidasModelo->hay_temas($modulo_profesor[0]->id_modulo);
        $hay_horas = $this->horasImpartidasModelo->hay_horas($modulo_profesor[0]->id_modulo);
        $hay_seguimiento = $this->horasImpartidasModelo->hay_seguimiento($modulo_profesor[0]->id_modulo);
        $hay_suma = $this->horasImpartidasModelo->suma_temas($modulo_profesor[0]->id_modulo);
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
        $this->vista('profesores/ep1',$this->datos);

}







public function nuevo_ep1($modulo){

    // utiliza el id_seguimiento mas alto 

    $lectivo = $this->horasImpartidasModelo->obtener_lectivo();
    $id_lectivo = $lectivo[0]->id_lectivo;

    $id_profe = $this->datos['usuarioSesion']->id_profesor;
    $info_modulo = $this->horasImpartidasModelo->info_modulo($id_profe,$modulo, $id_lectivo);
    $total_horas = $info_modulo[0]->horas_totales;
    $id_alto = $this->horasImpartidasModelo->id_mas_alto($modulo);



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $acumuladas = $_POST['acumuladas']; 
            $ajustes = $_POST['ajustes']; 
            $contenidos = $_POST['contenidos_impartidos']; 
            $previstas = $_POST['previstas'];


            // ACUMULADAS (para insert en tabla seg_ep1_tema)
            $acumuladas_final = [];
            foreach ($acumuladas as $mes => $temas) {
                switch ($mes) {
                    case '10': $id_pregunta = 47; break;
                    case '11': $id_pregunta = 48; break;
                    case '12': $id_pregunta = 49; break;
                    case '1':  $id_pregunta = 50; break;
                    case '2':  $id_pregunta = 51; break;
                    case '3':  $id_pregunta = 52; break;
                    case '4':  $id_pregunta = 53; break;
                    case '5':  $id_pregunta = 54; break;
                    case '6':  $id_pregunta = 55; break;
                    default: $id_pregunta = null; break;
                }

                if ($id_pregunta === null) continue;

                foreach ($temas as $id_tema => $horas_acumuladas) {
                    $acumuladas_final[] = [
                        'id_pregunta'      => $id_pregunta,
                        'id_tema'          => $id_tema,
                        'horas_acumuladas' => floatval($horas_acumuladas),
                    ];
                }
            }


            // ARRAY EP1 (para insert en tabla seg_ep1_mes)
            $ep1_array = [];
            foreach ($contenidos as $mes => $valorContenidos) {
                switch ($mes) {
                    case '10': $id_pregunta = 47; break;
                    case '11': $id_pregunta = 48; break;
                    case '12': $id_pregunta = 49; break;
                    case '1':  $id_pregunta = 50; break;
                    case '2':  $id_pregunta = 51; break;
                    case '3':  $id_pregunta = 52; break;
                    case '4':  $id_pregunta = 53; break;
                    case '5':  $id_pregunta = 54; break;
                    case '6':  $id_pregunta = 55; break;
                    default:   $id_pregunta = null; break;
                }
            
                if ($id_pregunta === null) continue;
            
                $ajuste = isset($ajustes[$mes]) ? floatval($ajustes[$mes]) : 0;
                $prevista = isset($previstas[$mes]) ? floatval($previstas[$mes]) : 0;
                $divisor = $mes == 6 ? $total_horas : $prevista;
                $ep1 = ($divisor > 0) ? (floatval($valorContenidos) / $divisor) * 100 : 0;
                $ep1_array[] = [
                    'id_pregunta' => $id_pregunta,
                    'mes' => $mes,
                    'contenidos' => floatval($valorContenidos),
                    'ajustes' => $ajuste,
                    'previstas' => $divisor,
                    'ep1' => round($ep1, 1)
                ];
            }

            if($this->horasImpartidasModelo->nuevo_ep1($acumuladas_final, $ep1_array, $id_alto, $modulo, $id_lectivo)){
                // MEDIA EP1 (es el valor de Junio)
                $junio_edicion = $this->horasImpartidasModelo->edicion_mes_junio($modulo);
                $this->horasImpartidasModelo->insertar_media_ep1($junio_edicion, $info_modulo, $lectivo);
                redireccionar('/PHorasImpartidas'.'/'.$modulo);               
            }

        }else{
            $this->vista('profesores/ep1',$this->datos);
        }

}




public function restaurar_tabla($id_modulo){

    $lectivo = $this->horasImpartidasModelo->obtener_lectivo();
    $id_lectivo = $lectivo[0]->id_lectivo;

    $id_profe = $this->datos['usuarioSesion']->id_profesor;
    $info_modulo = $this->horasImpartidasModelo->info_modulo($id_profe,$id_modulo, $id_lectivo);
    $total_horas = $info_modulo[0]->horas_totales;


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // ELIMINAMOS LOS VALORES DE LAS TABLAS ACUMULADAS 
            $this->horasImpartidasModelo->eliminar_acumuladas($id_modulo);

            // VALORES EP1
            $valores_ep1 = $this->horasImpartidasModelo->valores_ep1($id_modulo);

            // ID_ALTO
            $id_alto = $this->horasImpartidasModelo->id_mas_alto($id_modulo);

            //PREVISTAS ACUMULADAS MES
            $horas_previstas_mes = $this->horasImpartidasModelo->horas_previstas_mes($id_modulo);
            $previstas_acumuladas_mes = acumuladas_ep1_mes($horas_previstas_mes, $id_alto, $id_modulo);

            // ACUMULADAS TEMAS
            $temas = $this->horasImpartidasModelo->temas_del_modulo($id_modulo); 
            $datos = $this->horasImpartidasModelo->valores($id_modulo, $temas); 
            $acumuladas_temas = acumuladas_ep1_temas($datos);

            // TOTAL MES
            $total_mes = acumuladas_total_mes($acumuladas_temas);

            if($this->horasImpartidasModelo->insertar_acumuladas($valores_ep1, $previstas_acumuladas_mes, $total_mes, $acumuladas_temas, $id_modulo, $id_alto)){
                // MEDIA EP1 (es el valor de junio)
                $junio_ep1 = $this->horasImpartidasModelo->real_mes_junio($id_modulo);   
                if(!empty($junio_ep1)) {
                    $this->horasImpartidasModelo->insertar_media_ep1($junio_ep1, $info_modulo, $lectivo);
                }    
                redireccionar('/PHorasImpartidas'.'/'.$id_modulo);               
            }
            
        }else{
            $this->vista('profesores/ep1',$this->datos);
        }

}








}