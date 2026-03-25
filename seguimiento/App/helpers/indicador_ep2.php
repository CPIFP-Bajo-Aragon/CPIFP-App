<?php



// **************************************************
//     INDICADOR EP2 - grado de cumplimiento (ID 2)
// **************************************************
// Hay 32 preguntas: de la 1 a la 32
//
// NO HACE MEDIA para el total cpge el valor de la ultima evaluacion
// 
// no o casi = 0
// poco = 2.5
// a medias = 5
// bastante = 7.5
// si o casi = 10
// 
// El calculo se hace por cada categoria
// Se suman las respuestas y se dividen por el numero de preguntas que hay en cada categoria
//
// Hay excepcion con la categoria 9 y 10 (ver codigo)



function calculo_total_ep2($ep2, $categorias, $id_modulo, $id_indicador){

    $datos_ep2 = [];
    // agrupamos por id_seguimiento
    foreach ($ep2 as $item) {
        $id_seguimiento = $item['id_seguimiento'];
        if (!isset($datos_ep2[$id_seguimiento])) {
            $datos_ep2[$id_seguimiento] = [];
        }
        $datos_ep2[$id_seguimiento][] = $item;
    }


        $total_por_seguimiento = [];
        // cálculos para cada id_seguimiento
        foreach ($datos_ep2 as $id_seguimiento => $items) {

                $valores = []; // Para guardar los cálculos de cada categoría
                foreach ($categorias as $cat) {

                    $suma_respuestas = 0;
                    $calculo_categoria = null;  // Inicializamos como null, para casos donde no haya respuestas válidas
                    $hay_respuestas_validas = false;  // para verificar si hay respuestas válidas

                    foreach ($items as $item) {
                        if ($item['categoria'] == $cat->id_categoria) {
                            if ($item['nulo'] === 'no' && isset($item['respuesta']) && $item['respuesta'] !== null && $item['respuesta'] !== '') {
                                $respuesta = floatval($item['respuesta']);  
                                $suma_respuestas += $respuesta;
                                $hay_respuestas_validas = true;  // Marcamos que hemos encontrado respuestas válidas
                            }
                        }
                    }


                    if ($hay_respuestas_validas && $cat->cantidad_preguntas > 0) {
                        $calculo_categoria = $suma_respuestas / $cat->cantidad_preguntas;
                    }

                    if (!$hay_respuestas_validas) {
                        $calculo_categoria = null;
                    }

                    $valores[] = [
                        'id_categoria' => $cat->id_categoria,
                        'valor' => $calculo_categoria  
                    ];
                }


                $suma_categorias = 0;
                $categoria_9_valor = $valores[8]['valor'];  
                $categoria_10_valor = $valores[9]['valor']; 

                foreach ($valores as $categoria) {
                    $suma_categorias += $categoria['valor'];
                }


                if ($categoria_9_valor === null && $categoria_10_valor === null) {
                    // Si ambas categorías tienen valor null, dividimos entre 8
                    $total_ep2 = ($suma_categorias / 8) * 10;
                } else {
                    // Si ambas categorías tienen valores válidos (mayores o iguales a 0)
                    if ($categoria_9_valor !== null && $categoria_10_valor !== null) {
                        $total_ep2 = ($suma_categorias / 10) * 10;
                    } else {
                        // Si solo una categoría tiene valor válido, dividimos entre 9
                        $total_ep2 = ($suma_categorias / 9) * 10;
                    }
                }
                
  
            $total_por_seguimiento[] = [
                'id_seguimiento' => $id_seguimiento, 
                'id_modulo' => $id_modulo, 
                'id_indicador' => $id_indicador, 
                'total' => round($total_ep2, 2, PHP_ROUND_HALF_UP)
            ];

        }

    return  $total_por_seguimiento;
}














