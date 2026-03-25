<?php


// **********************
//     INDICADOR AP 
// **********************
//
// Indicador de aprobados
// Se hace por cada evaluacion. 
// NO HACE MEDIA. El total es el de la ultima evaluacion
// ID_INDICADOR: 4

// preguntas: 41,42
// 41 evaluados
// 42 aprobados

// RESULTADO %  =  (42 * 100) / 41





function calculos_ap($respuestas) {

        $agrupado = []; 

        foreach ($respuestas as $item) {
            if (!isset($agrupado[$item->id_seguimiento])) {
                $agrupado[$item->id_seguimiento] = [];
            }
            $agrupado[$item->id_seguimiento][] = $item;
        }

        $resultados = [];
    

        foreach ($agrupado as $id_seguimiento => $items) {
            
            if (count($items) == 2) {  //  hay dos respuestas

                $primer_valor = floatval($items[0]->respuesta); 
                $segundo_valor = floatval($items[1]->respuesta);  
                
                if (is_numeric($primer_valor) && is_numeric($segundo_valor)) {
                    if ($primer_valor != 0) {
                        $resultado = round(($segundo_valor * 100) / $primer_valor, 2 , PHP_ROUND_HALF_UP);

                        $resultados[] = (object) [
                            'id_seguimiento' => $id_seguimiento,
                            'resultado' => $resultado,
                        ];

                    } else {

                        $resultado=0;
                        $resultados[] = (object) [
                            'id_seguimiento' => $id_seguimiento,
                            'resultado' => $resultado,
                        ];
                    }
                } else {
                    $resultado=0;
                    $resultados[] = (object) [
                        'id_seguimiento' => $id_seguimiento,
                        'resultado' => $resultado,
                    ];
                }
            } else {
            $resultado=0;
            $resultados[] = (object) [
                'id_seguimiento' => $id_seguimiento,
                'resultado' => $resultado,
                ];
            }
        }

    return $resultados;

}




    












