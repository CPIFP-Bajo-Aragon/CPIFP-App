<?php


// *********************
//   INDICADOR AT
// ********************

// Ambiente de trabajo
// Se hace por cada evaluacion y el total es el de la ultima. 
// NO HACE MEDIA. El total es el de la ultima evaluacion
// ID_INDICADOR: 5

// preguntas: 43,44,45,46 (valor de 0 a 10)
// 43 interes
// 44 comportamiento
// 45 puntualidad
// 46 limpieza

// 43 + 44 + 45 + 46 = SUMA
// RESULTADO % =  (SUMA * 100) / 40 





function calculos_at($respuestas) {

        // AGRUPAMOS POR ID_SEGUIMIENTO
        $agrupado = [];
        foreach ($respuestas as $item) {
            if (!isset($agrupado[$item->id_seguimiento])) {
                $agrupado[$item->id_seguimiento] = [];
            }
            $agrupado[$item->id_seguimiento][] = $item;
        }

        // HACEMOS LOS CALCULOS PARA CADA ID_SEGUIMIENTO Y MONTAMOS UN OBJETO CON SU TOTAL POR EVALUACION
        $resultados = [];
        foreach ($agrupado as $id_seguimiento => $items) {
            $suma_respuestas = 0;
            foreach ($items as $item) {
                $suma_respuestas += floatval($item->respuesta); 
            }
            if ($suma_respuestas != 0) {
                $resultado = round(($suma_respuestas * 100) / 40, 2, PHP_ROUND_HALF_UP);
            } else {
                $resultado = 0; 
            }
            $resultados[] = (object) [
                'id_seguimiento' => $id_seguimiento,
                'resultado' => $resultado,
            ];
        }

    return $resultados;
}
















