<?php

// **************************************************
//     INDICADOR EP1 - contenidos impartidas (ID 6)
// **************************************************
// Son los contenido impartidos por meses
// 
// Preguntas:
// 47: octubre (septiemrbe tambien va aqui)
// 48: noviembre
// 49: dicimebre
// 50: enero
// 51: febereo
// 52: marzo
// 53: abril
// 54: mayo
// 55: junio
// 
// 
// El total por mes se calcula:
//
// contenido impartido  = suma de todas las horas, de todos los temas dados en un mes (del diario) 
// horas mes  = todas las horas de clase que tiene en un mes 
// 
// % ep1 = (contenido impartido mes acumulado / horas mes acumualdo) * 100
//
// OJO ! porque Junio se calcula diferente
// 
// MEDIA EP1: es el valor total de JUNIO
// 




//******************************************************/
//  CALCULA EP1 CUANDO INSERTAMOS EL DIARIO
//******************************************************/


function funcion_ep1($d_temas, $d_valores, $d_horas_previstas_mes, $info_modulo) {

    $horas_totales_modulo = $info_modulo[0]->horas_totales;

    $meses = array(
        (object)['numero' => 10, 'mes' => 'Octubre', 'pregunta' => 47],
        (object)['numero' => 11, 'mes' => 'Noviembre', 'pregunta' => 48],
        (object)['numero' => 12, 'mes' => 'Diciembre', 'pregunta' => 49],
        (object)['numero' => 1, 'mes' => 'Enero', 'pregunta' => 50],
        (object)['numero' => 2, 'mes' => 'Febrero', 'pregunta' => 51],
        (object)['numero' => 3, 'mes' => 'Marzo', 'pregunta' => 52],
        (object)['numero' => 4, 'mes' => 'Abril', 'pregunta' => 53],
        (object)['numero' => 5, 'mes' => 'Mayo', 'pregunta' => 54],
        (object)['numero' => 6, 'mes' => 'Junio', 'pregunta' => 55]
    );

    $ep1_por_mes = [];

    $acumulado_contenido = 0;
    $acumulado_previsto = 0;

    foreach ($meses as $mes) {
        $total_mes = 0;

        foreach ($d_temas as $tema) {
            if (!in_array($tema->descripcion, ['Otros', 'Faltas', 'Actividades'])) {
                foreach ($d_valores as $valor) {
                    if ($valor->mes == $mes->numero && $valor->id_tema == $tema->id_tema) {
                        $total_mes += $valor->total_horas;
                    }
                }
            }
        }

        $acumulado_contenido += $total_mes;

        // Acumulamos las horas previstas para los meses diferentes de junio
        if ($mes->numero != 6) {
            foreach ($d_horas_previstas_mes as $previstas) {
                if ($previstas->mes == $mes->numero) {
                    $acumulado_previsto += $previstas->total_horas_clase;
                }
            }
        }

        // Cálculo especial si el mes es junio
        if ($mes->numero == 6) {
            $ep1 = ($horas_totales_modulo > 0)
                ? round(($acumulado_contenido / $horas_totales_modulo) * 100, 2, PHP_ROUND_HALF_UP)
                : 0;
        } else {
            $ep1 = ($acumulado_previsto > 0)
                ? round(($acumulado_contenido / $acumulado_previsto) * 100, 2, PHP_ROUND_HALF_UP)
                : 0;
        }

        $ep1_por_mes[] = (object)[
            'mes' => $mes->numero,
            'pregunta' => $mes->pregunta,
            'ep1' => $ep1
        ];
    }

    return $ep1_por_mes;
}





//*************************************************/
// PARA TABLA ACUMULADAS (al mes) HORAS PREVISTAS
//*************************************************/

function acumuladas_ep1_mes($horas_previstas_mes, $id_alto, $id_modulo) {

    $acumulado = 0;
    $horas_acumuladas = [];
    $resultados = [];

    
    // Calcular acumulado por mes HORAS PREVISTAS
    foreach ($horas_previstas_mes as $item) {
        $acumulado += $item->total_horas_clase;
        $obj = new stdClass();
        $obj->mes = $item->mes;
        $obj->previstas_acumuladas = $acumulado;
        $horas_acumuladas[] = $obj;
    }


    foreach ($horas_acumuladas as $item) {
        
        $mes = $item->mes;
        switch ($mes) {
            case 10: $id_pregunta = 47; break;
            case 11: $id_pregunta = 48; break;
            case 12: $id_pregunta = 49; break;
            case 1:  $id_pregunta = 50; break;
            case 2:  $id_pregunta = 51; break;
            case 3:  $id_pregunta = 52; break;
            case 4:  $id_pregunta = 53; break;
            case 5:  $id_pregunta = 54; break;
            case 6:  $id_pregunta = 55; break;
            default: $id_pregunta = null; break;
        }

        if ($id_pregunta !== null) {

            $resultado = new stdClass();
            $resultado->id_seguimiento = $id_alto[0]->ep1;
            $resultado->id_modulo = $id_modulo;
            $resultado->id_pregunta = $id_pregunta;
            $resultado->previstas_acumuladas = $item->previstas_acumuladas;
            $resultado->mes = $mes;
            $resultados[] = $resultado;
        }
    }

    return $resultados;
}





//***********************************************************/
// PARA TABLA ACUMULADAS (al mes) CONTENIDOS IMPARTIDOS
//***********************************************************/

function acumuladas_total_mes($acumuladas_temas) {
    
    $result = [];
    $preguntas_por_mes = [
        10 => 47,
        11 => 48,
        12 => 49,
        1  => 50,
        2  => 51,
        3  => 52,
        4  => 53,
        5  => 54,
        6  => 55
    ];
    
    foreach ($acumuladas_temas as $item) {

        $mes = $item['numero_mes'];

        if (isset($result[$item['numero_mes']])) {
            $result[$item['numero_mes']]['total_mes'] += $item['horas_acumuladas'];
        } else {
            $result[$item['numero_mes']] = [
                'numero_mes' => $item['numero_mes'],
                'total_mes' => $item['horas_acumuladas'],
                'id_pregunta' => $preguntas_por_mes[$mes] ?? null
            ];
        }
    }

    return $result;
}





//**************************************/
// PARA TABLA ACUMULADAS (por temas)
//**************************************/

function acumuladas_ep1_temas($datos) {

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

    $acumulados = [];

    foreach ($datos as $registro) {
        if ($registro->descripcion === "Otros" || $registro->descripcion === "Faltas" || $registro->descripcion === "Actividades") {
            continue;
        }

        if (!isset($acumulados[$registro->id_tema])) {
            // Inicializamos las horas por mes
            $mesesIniciales = [];
            foreach ($meses as $mes) {
                $mesesIniciales[$mes->numero] = 0;
            }

            $acumulados[$registro->id_tema] = [
                'tema' => $registro->id_tema,
                'horas' => 0,
                'meses' => $mesesIniciales
            ];
        }

        // Sumar las horas al mes correspondiente si existe
        foreach ($meses as $mes) {
            if ($registro->mes == $mes->numero) {
                $acumulados[$registro->id_tema]['meses'][$mes->numero] += $registro->total_horas;
                break;
            }
        }
    }


    $acumuladosArray = [];
    foreach ($acumulados as $temaId => $acumulado) {
        $totalAcumulado = 0;
        foreach ($meses as $mesObj) {
            $totalAcumulado += $acumulado['meses'][$mesObj->numero];
            $acumuladosArray[] = [
                'id_tema' => $acumulado['tema'],
                'mes' => $mesObj->mes,
                'numero_mes' => $mesObj->numero,
                'pregunta' => $mesObj->pregunta, 
                'horas_acumuladas' => $totalAcumulado
            ];
        }
    }

    return $acumuladosArray;
}








/*********************************************************************************** */




function funcion_ep1_nueva($d_valores, $d_horas_previstas_mes) {

    $meses = array (
        (object)[ 'numero'=>10, 'mes'=>'Octubre', 'pregunta'=>47 ],(object)[ 'numero'=>11, 'mes'=>'Noviembre', 'pregunta'=>48 ],
        (object)[ 'numero'=>12, 'mes'=>'Diciembre', 'pregunta'=>49 ],(object)[ 'numero'=>1, 'mes'=>'Enero', 'pregunta'=>50 ],
        (object)[ 'numero'=>2, 'mes'=>'Febrero', 'pregunta'=>51] ,(object)[ 'numero'=>3, 'mes'=>'Marzo','pregunta'=>52 ],
        (object)[ 'numero'=>4, 'mes'=>'Abril', 'pregunta'=>53 ],(object)[ 'numero'=>5, 'mes'=>'Mayo', 'pregunta'=>54 ],
        (object)[ 'numero'=>6, 'mes'=>'Junio', 'pregunta'=>55 ]
    );


    $ep1_por_mes = [];
    
    for ($i = 0; $i < sizeof($meses); $i++) { //recorremos los meses

        $total_mes = 0; 

        foreach ($d_valores as $temas) {  // Recorremos los temas
            // Filtramos los temas que no son "Otros", "Faltas" o "Actividades"
            if ($temas->descripcion != "Otros" && $temas->descripcion != "Faltas" && $temas->descripcion != "Actividades") {

                $acumulado_mes = 0; // Acumula las horas del mes para este tema

                    // Comprobamos si el mes y el tema coinciden
                    if ($meses[$i]->numero == $temas->mes && $temas->id_tema == $temas->id_tema) {
                        $acumulado_mes += $temas->total_horas; // Sumamos las horas para este tema en este mes
                    }
                
                // Sumamos las horas del tema al total del mes
                $total_mes += $acumulado_mes;
            }
        }


        // El contenido impartido es el total de horas de todos los temas del mes
        $contenido_impartido = $total_mes;

        // Recorremos las horas previstas para el mes
        foreach ($d_horas_previstas_mes as $horas_previstas_mes) {
            if ($meses[$i]->numero == $horas_previstas_mes->mes) {

                $horas_mes = $horas_previstas_mes->total_horas_clase;

                // Calculamos el porcentaje de horas impartidas con respecto a las horas previstas
                if ($horas_mes > 0) {
                    $ep1 = round(($contenido_impartido / $horas_mes) * 100, 2, PHP_ROUND_HALF_UP);
                } else {
                    $ep1 = 0; // Si las horas previstas son 0, no calculamos el porcentaje
                }

                $ep1_por_mes[] = (object) [
                    'mes' => $meses[$i]->numero,
                    'id_pregunta' => $meses[$i]->pregunta,
                    'ajustes' => 0,
                    'contenidos' => $contenido_impartido,
                    'ep1' => $ep1
                ];
            }
        }
    }

    return $ep1_por_mes;
}





























