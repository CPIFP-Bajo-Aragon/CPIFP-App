<?php



// **********************************************
//   INDICADOR HI - docencia impartidas (ID 3)
// **********************************************
// preguntas: 38,39,40
// 38 horas previstas
// 39 faltadas profes
// 40 perdidas otros
//
// 39 + 40 = SUMA
// 38 - SUMA = RESTA
//
// RESULTADO %  =  (RESTA * 100) / 38
//
// VIENE TODO CALCULADO DEL DIARIO 
//
// para el total SI HACE MEDIA





//******************** CALCULO HI ****************************/

function calculo_total_hi($hi, $id_modulo) {

    $agrupado = [];
    foreach ($hi as $respuesta) {
        $id = $respuesta->id_seguimiento;
        if (!isset($agrupado[$id])) {
            $agrupado[$id] = [];
        }
        $agrupado[$id][$respuesta->id_pregunta] = $respuesta->respuesta;
    }


    $val = [];
    foreach ($agrupado as $id_seguimiento => $respuestas) {

        $previsto = $respuestas[38] ?? 0;
        $profes = $respuestas[39] ?? 0;
        $otros = $respuestas[40] ?? 0;

        // Evitar división por cero
        if ($previsto == 0) {
            $total = 0;
        } else {
            $cuenta = $previsto - ($profes + $otros);
            $total = ($cuenta * 100) / $previsto;
            $total = round($total, 2, PHP_ROUND_HALF_UP);
        }

        $val[] = [
            "id_seguimiento" => $id_seguimiento,
            "id_modulo" => $id_modulo,
            "total" => $total
        ];
    }

    return $val;
}



//******************** CALCULO MEDIA HI ****************************/

function calculo_total_curso_hi($valores) {
    
    $suma_38 = 0;
    $suma_39 = 0;
    $suma_40 = 0;

    foreach ($valores as $respuesta) {
        switch ($respuesta->id_pregunta) {
            case 38:
                $suma_38 += $respuesta->respuesta;
                break;
            case 39:
                $suma_39 += $respuesta->respuesta;
                break;
            case 40:
                $suma_40 += $respuesta->respuesta;
                break;
        }
    }

    $suma = $suma_39 + $suma_40;

    // Evitar división por cero
    if ($suma_38 == 0) {
        return 0;
    }

    $resta = $suma_38 - $suma;
    $total_curso = round(($resta * 100) / $suma_38, 2, PHP_ROUND_HALF_UP);

    return $total_curso;
}

////////////////////////////////////////////
///////////////////////////////////////////
////////////////////////////////////////////


function calculo_total_hi_BUENA($hi, $id_modulo){

    print_r($hi);
    exit;

    foreach($hi as $valores):

        $seguimiento = $valores[0]->id_seguimiento;
        $previsto = $valores[0]->respuesta;
        $profes = $valores[1]->respuesta;
        $otros = $valores[2]->respuesta;

        $suma = $profes+$otros;
        $cuenta = $previsto - $suma;

        $total = ($cuenta * 100) / $previsto;
        $total = round($total,2,PHP_ROUND_HALF_UP );

        $val [] = [
            "id_seguimiento" => $seguimiento,
            "id_modulo" => $id_modulo,
            "total" => $total
        ];

    endforeach;
    
    return $val;

}




function calculo_total_curso_hi_buena($valores){

    $suma_38=0;
    $suma_39=0;
    $suma_40=0;

    for($i=0; $i<sizeof($valores); $i++):
        if($valores[$i][0]->id_pregunta=38){
            $suma_38+=$valores[$i][0]->respuesta;
        };
        if($valores[$i][1]->id_pregunta=39){
            $suma_39+=$valores[$i][1]->respuesta;
        };
        if($valores[$i][2]->id_pregunta=40){
            $suma_40+=$valores[$i][2]->respuesta;
        };
    endfor;

    $suma = $suma_39 + $suma_40;
    $resta = $suma_38 - $suma;
    $total_curso = round(($resta * 100) / $suma_38 , 2, PHP_ROUND_HALF_UP);

    return $total_curso;

}















