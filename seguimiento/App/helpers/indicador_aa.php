<?php


// **********************
//     INDICADOR AA 
// **********************
//
// Indicador de asistencia de alumnos
// Se hace por cada evaluacion. 
// Para el total SI HACE MEDIA
// ID_INDICADOR: 1

// preguntas: 33,34,35,36,37
// 33 matriculados
// 34 asis.excepcional
// 35 efectivos = 33-34 (CALCULADO)
// 36 horas previ x alu efec = 38 (es del HI) * 35 (CALCULADO)
// 37 faltas


// RESULTADO % =  100 - ( ( 37 / 36 ) * 100 )






function calculos_aa($valores) {

    $grouped = [];
    foreach ($valores as $item) {
        $grouped[$item->id_seguimiento][$item->pregunta] = $item->respuesta;
    }

    $resultados = [];

    foreach ($grouped as $id => $respuestas) {
        if (isset($respuestas[37]) && isset($respuestas[36])) {

            $respuesta_37 = floatval($respuestas[37]);
            $respuesta_36 = floatval($respuestas[36]);

            if ($respuesta_36 == 0) {
                // División por cero: no se puede calcular
                $total = 0;
            } elseif ($respuesta_37 == 0) {
                // Caso especial: respuesta 37 es 0, resultado debe ser 100
                $total = 100;
            } else {
                // Cálculo normal
                $total = 100 - (($respuesta_37 / $respuesta_36) * 100);
                $total = round($total, 2);
            }

            $resultados[] = (object)[
                'id_seguimiento' => $id,
                'resultado' => $total
            ];
        }
    }

    return $resultados;
}








function total_curso_aa($valores) {

    $suma_36 = 0;
    $suma_37 = 0;

    foreach($valores as $val){
        if ($val->pregunta == 36) {
            $suma_36 += floatval($val->respuesta); 
        }
        if ($val->pregunta == 37) {
            $suma_37 += floatval($val->respuesta); 
        }
    }

    if ($suma_36 == 0) {
        // División por cero: no se puede calcular
        $total_curso = 0;
    } elseif ($suma_37 == 0) {
        // Caso especial: suma_37 es 0, resultado debe ser 100
        $total_curso = 100;
    } else {
        // Cálculo normal
        $total = 100 - (($suma_37 / $suma_36) * 100);
        $total_curso = round($total, 2);
    }

    return $total_curso;
}








//***********************************************************************/
//***********************************************************************/
//********************* ACTUALIZA CALCULOS AA ***************************/
//***********************************************************************/
//***********************************************************************/


function actualizar_calculo_aa($data) {

    $seguimientos = [];

    foreach ($data as $item) {
        $seguimientos[$item->id_seguimiento][] = $item;
    }

    foreach ($seguimientos as $items) {

        $alumnos_efectivos = null;
        $horas_docencia_previstas = null;

        foreach ($items as $item) {
            if ($item->id_pregunta == 35) {
                $alumnos_efectivos = $item->respuesta;
            }
            if ($item->id_pregunta == 38) {
                $horas_docencia_previstas = $item->respuesta;
            }
        }

        if ($alumnos_efectivos !== null && $horas_docencia_previstas !== null) {
            foreach ($items as $item) {
                if ($item->id_pregunta == 36) {
                    $item->respuesta = $alumnos_efectivos * $horas_docencia_previstas;
                }
            }
        }
    }

    return $data;
}






function valor_aa($valores) {

    $seguimientos = [];


    // Agrupamos las respuestas por id_seguimiento
    foreach ($valores as $item) {
        $seguimientos[$item->id_seguimiento][] = $item;
    }

    foreach ($seguimientos as $items) {

        $respuesta_pregunta_36 = null;
        $respuesta_pregunta_37 = null;


        foreach ($items as $item) {

            if ($item->id_pregunta == 36) {
                $respuesta_pregunta_36 = floatval($item->respuesta);
            }
            if ($item->id_pregunta == 37) {
                $respuesta_pregunta_37 = floatval($item->respuesta);
            }
        }

        if ($respuesta_pregunta_36 === null || $respuesta_pregunta_37 === null) {
            // Faltan datos para calcular
            $total = 0;
        } elseif ($respuesta_pregunta_36 == 0) {
            // División por cero
            $total = 0;
        } elseif ($respuesta_pregunta_37 == 0) {
            // Caso especial: resultado debe ser 100
            $total = 100;
        } else {
            // Cálculo normal
            $total = 100 - (($respuesta_pregunta_37 / $respuesta_pregunta_36) * 100);
            $total = round($total, 2);
        }

        $resultados[] = (object)[
            'id_seguimiento' => $item->id_seguimiento,
            'resultado' => $total
        ];
    }

    return $resultados;
}







function actualizar_seg_total_curso_aa($valores) {

    $suma_36 = 0;
    $suma_37 = 0;

    for ($i = 0; $i < sizeof($valores); $i++) {
        if ($valores[$i]->id_pregunta == 36) {
            $suma_36 += floatval($valores[$i]->respuesta); 
        } 
        if ($valores[$i]->id_pregunta == 37) {
            $suma_37 += floatval($valores[$i]->respuesta); 
        }
    }

    if ($suma_36 == 0) {
        // División por cero: resultado no calculable
        $total_curso = 0;
    } elseif ($suma_37 == 0) {
        // Caso especial
        $total_curso = 100;
    } else {
        // Cálculo normal
        $total = 100 - (($suma_37 / $suma_36) * 100);
        $total_curso = round($total, 2);
    }

    return $total_curso;
}














