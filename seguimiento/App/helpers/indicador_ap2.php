<?php


// *********************
//   INDICADOR AP2 
// ********************

// Alumnos aprobados en segunda convocatoria
// Se hace una sola vez al final del curso (no es mensual ni trimestral). 
// NO HACE MEDIA
// ID_INDICADOR: 7

// preguntas: 56,57
// 56 evaluados
// 57 aprobados

// RESULTADO %  =  (57 * 100) / 56





function total_ap2($respuestas) {

    $evaluados = 0;
    $aprobados = 0;
    $id_seguimiento = 0;

    foreach ($respuestas as $res) {
        if ($res->pregunta == 56) {  
            $evaluados = floatval($res->respuesta);
            $id_seguimiento = $res->id_seguimiento;
        } else if ($res->pregunta == 57) {
            $aprobados = floatval($res->respuesta);
        }
    }

    if($aprobados == 0 || $evaluados == 0){
        $total_ap2 = 0;
    } else {
        $total_ap2 = round(($aprobados * 100) / $evaluados, 2, PHP_ROUND_HALF_UP);
    }

    $resultado = new stdClass();
    $resultado->id_seguimiento = $id_seguimiento;
    $resultado->resultado = $total_ap2;

    return [$resultado];  
    
}













