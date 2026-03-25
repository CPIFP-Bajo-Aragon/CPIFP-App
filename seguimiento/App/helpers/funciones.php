<?php


//Para redireccionar la pagina
function redireccionar($pagina){
    header('location: ' . RUTA_URL . $pagina);
}



function tienePrivilegios($rol_usuario, $rolesPermitidos){
    // si $rolesPermitidos es vacio, se tendran privilegios
    if (empty($rolesPermitidos) || in_array($rol_usuario, $rolesPermitidos)) {
        return true;
    }
}


function obtenerRol($roles){
    $id_rol = 0;
    foreach($roles as $rol){
        if($rol->id_rol==30 && $id_rol < $rol->id_rol){           // Jefe Departamento
            $id_rol = 30;
        }elseif($rol->id_rol==50 && $id_rol < $rol->id_rol){           // Equipo directivo
            $id_rol = 50;
        }elseif(($rol->id_rol==10 || $rol->id_rol==20 || $rol->id_rol==40) && $id_rol < $rol->id_rol){        // Profesor o Tutor
            $id_rol = 10;
        }
    }
    return $id_rol;
}


/**funcion que calcula el día siguiente al día dado */
function diaSiguiente($diaActual){
     // INCREMENTAMOS EN UNO EL DÍA ACTUAL 
     $fecha_sig=explode('-',$diaActual);
     $dia_sig= mktime(0, 0, 0, $fecha_sig[1] , $fecha_sig[2]+1, $fecha_sig[0]);
     $dia_siguiente = date ("Y-m-j",$dia_sig);
     return $dia_siguiente;
}





//***********************************/
//**********************************/
// CALENDARIO DEL CURSO
//*********************************/
//********************************/

function calendario_curso($fecha_inicio, $fecha_fin){

        $calen = (object)[];
        $calendario = array();

        $inicio_curso = new DateTime($fecha_inicio);
        $fin_curso = new DateTime($fecha_fin);

        $intervalo = new DateInterval('P1D'); //periodo de un dia
        $fin_curso->add($intervalo);
        $periodo = new DatePeriod($inicio_curso, $intervalo, $fin_curso);

        foreach($periodo as $fechas){
            $dia_sem=$fechas->format('w');
            switch ($dia_sem) {
                case '0':
                    $dia_sem="D";
                    break;
                case '1':
                    $dia_sem="L";
                    break;
                case '2':
                    $dia_sem="M";
                    break;
                case '3':
                    $dia_sem="X";
                    break;
                case '4':
                    $dia_sem="J";
                    break;
                case '5':
                    $dia_sem="V";
                    break;
                case '6':
                    $dia_sem="S";
                    break;  
            };
            $fech=$fechas->format('Y-m-d');
            $calen = ['fecha'=>$fech,'dia_semana'=>$dia_sem];
            array_push($calendario, $calen);
        }

    return $calendario;
}





//***********************************/
//**********************************/
// INTERVALOS EVALUACIONES
//*********************************/
//********************************/

function intervalos_evaluaciones($lectivo, $evaluaciones){

        $fecha_ini_curso=$lectivo[0]->fecha_inicio;
        $fecha_fin_curso=$lectivo[0]->fecha_fin;

        $ev = (object)[];
        $evas = array();
        $ultimo = sizeof($evaluaciones)-1;

        for($i=0;$i<sizeof($evaluaciones);$i++){

            if($i==0){

                $fecha_i = new DateTime($fecha_ini_curso);
                $fecha_inicio = $fecha_i->format('Y-m-d');

                $fecha_f = new DateTime($evaluaciones[$i]->fecha);
                $fecha_f->modify('-1 day');
                $fecha_fin = $fecha_f->format('Y-m-d');

            }else if($i==$ultimo){

                $fecha_inicio = new DateTime($evaluaciones[$i-1]->fecha);
                $fecha_inicio = $fecha_inicio->format('Y-m-d');

                $fecha_fin = new DateTime($evaluaciones[$i]->fecha);
                $fecha_fin = $fecha_fin->format('Y-m-d');

            }else{

                $fecha_inicio = new DateTime($evaluaciones[$i-1]->fecha);
                $fecha_inicio = $fecha_inicio->format('Y-m-d');

                $fecha_f=new DateTime($evaluaciones[$i]->fecha);
                $fecha_f ->modify('-1 day');
                $fecha_fin=$fecha_f->format('Y-m-d');
            }

            $ev=['id_modulo'=>$evaluaciones[$i]->id_modulo,
                'id_seguimiento'=>$evaluaciones[$i]->id_seguimiento,
                'evaluacion' => $evaluaciones[$i]->evaluacion,
                'fecha_inicio'=>$fecha_inicio,
                'fecha_fin'=>$fecha_fin];

            array_push($evas,$ev);
        };
        
        return $evas;

}



//*********************************************************/
//*********************************************************/
// PROMEDIOS TABLA HISTORICA Y RESUMEN (his_total_curso)
//********************************************************/
//********************************************************/

function calcular_promedios($resumen) {



    $agrupados = [];

    foreach ($resumen as $item) {
        $clave = "{$item->id_ciclo}-{$item->id_turno}-{$item->id_curso}-{$item->id_indicador}";
        if (!isset($agrupados[$clave])) {
            $agrupados[$clave] = (array) $item;
            $agrupados[$clave]['suma'] = 0;
            $agrupados[$clave]['cuenta'] = 0;
        }
        $agrupados[$clave]['suma'] += $item->total;
        $agrupados[$clave]['cuenta']++;
    }


    $promedios = [];
    foreach ($agrupados as $grupo) {
        $grupo['total'] = round($grupo['suma'] / $grupo['cuenta'], 2);
        unset($grupo['suma'], $grupo['cuenta']);
        $promedios[] = $grupo;
    }

    return $promedios;

}



// function floatToString($floatVal) {
//     if (is_float($floatVal) || is_numeric($floatVal)) {
//         return rtrim(rtrim(sprintf('%.10f', $floatVal), '0'), '.');
//     }
//     return (string)$floatVal;
// }


// function calcular_promedios($resumen) {
//     $agrupados = [];

//     foreach ($resumen as $item) {
//         $clave = "{$item->id_ciclo}-{$item->id_turno}-{$item->id_curso}-{$item->id_indicador}";

//         if (!isset($agrupados[$clave])) {
//             $agrupados[$clave] = (array) $item;
//             $agrupados[$clave]['suma'] = '0';  // iniciar en string para bcadd
//             $agrupados[$clave]['cuenta'] = 0;
//         }

//         // Convertir total a string decimal válido para bcadd
//         $totalStr = floatToString($item->total);

//         // Sumar con bcadd
//         $agrupados[$clave]['suma'] = bcadd($agrupados[$clave]['suma'], $totalStr, 10);
//         $agrupados[$clave]['cuenta']++;
//     }

//     $promedios = [];
//     foreach ($agrupados as $grupo) {
//         if ($grupo['cuenta'] > 0) {
//             $promedio = bcdiv($grupo['suma'], (string)$grupo['cuenta'], 10);
//             $grupo['total'] = round((float)$promedio, 2);
//         } else {
//             $grupo['total'] = 0;
//         }
//         unset($grupo['suma'], $grupo['cuenta']);
//         $promedios[] = $grupo;
//     }

//     return $promedios;
// }


//********************************************/
//********************************************/
// PROMEDIOS TODO EL AÑO ACTUAL 
//********************************************/
//********************************************/

function promedios_anio_actual($datos) {

        $agrupado = [];

        // Agrupar totales por id_indicador
        foreach ($datos as $fila) {
            $id = $fila->id_indicador;
            if (!isset($agrupado[$id])) {
                $agrupado[$id] = (object)[
                    'id_indicador' => $id,
                    'indicador' => $fila->indicador,
                    'indicador_corto' => $fila->indicador_corto,
                    'suma' => 0,
                    'cuenta' => 0
                ];
            }

            $agrupado[$id]->suma += $fila->total;
            $agrupado[$id]->cuenta++;
        }

        $resultado = [];
        foreach ($agrupado as $item) {
            $obj = new stdClass();
            $obj->id_indicador = $item->id_indicador;
            $obj->indicador = $item->indicador;
            $obj->indicador_corto = $item->indicador_corto;
            $obj->promedio = round($item->suma / $item->cuenta, 2);
            $resultado[] = $obj;
        }

    return $resultado;
}






















