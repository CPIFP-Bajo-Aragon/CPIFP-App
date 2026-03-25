<?php



class JDActas extends Controlador{

    private $actasModelo;

    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
        // $this->datos['rolesPermitidos'] = [30];          // Definimos los roles que tendran acceso

        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }
        $this->actasModelo = $this->modelo('jdActasM');
    }


    
//*****************************************************/
//*****************************************************/
// ************* ACTAS INICIO *************************/
//*****************************************************/
//*****************************************************/

public function index(){
    
        $this->datos['lectivo'] = $this->actasModelo->obtener_lectivo();
        $id_lectivo =  $this->datos['lectivo'][0]->id_lectivo;

        $id = $this->datos['usuarioSesion']->id_profesor;
        $datos = $this->actasModelo->departamentos_formacion($id);
        $id_dep = $datos[0]->id_departamento;

        $this->datos['nombres_evaluaciones'] = $this->actasModelo->nombre_evaluaciones();
        $this->datos['nombres_indicadores'] = $this->actasModelo->nombre_indicadores();
        $this->datos['preguntas_ep1'] = $this->actasModelo->preguntas_ep1();
        $this->datos['asignaturas'] = $this->actasModelo->obtener_asignaturas($id_dep);

        // RECOGEMOS TODOS LOS VALORES QUE NECESITAMOS
        $this->datos['info_actas'] = $this->actasModelo->info_actas($id_dep,$id_lectivo); // datos SEG_TOTALES (no llega conforme o no)
        $this->datos['actas_ep1'] = $this->actasModelo->info_actas_ep1($id_dep,$id_lectivo); // datos tabla EP1 (no llega conforme o no)

        if(!empty($this->datos['lectivo'])){
            $id_lectivo = $this->datos['lectivo'][0]->id_lectivo;
            $this->datos['umbrales_indicadores'] = $this->actasModelo->umbrales_indicadores($id_lectivo);
            $this->datos['his_total_modulos'] = $this->actasModelo->his_total_modulo($id_dep, $id_lectivo); // si llega conforme o no
        } else{
            $id_lectivo = '';
            $this->datos['umbrales_indicadores'] = [];
        }

      $this->vista('jefeDep/actas/actas', $this->datos);

}



//*****************************************************/
//*****************************************************/
// ************* ACTAS EVALUACION **********************/
//*****************************************************/
//*****************************************************/


public function actas_evaluacion($id_evaluacion){

    $this->datos['lectivo'] = $this->actasModelo->obtener_lectivo();
    $id_lectivo =  $this->datos['lectivo'][0]->id_lectivo;

    $id = $this->datos['usuarioSesion']->id_profesor;
    $datos = $this->actasModelo->departamentos_formacion($id);
    $id_dep = $datos[0]->id_departamento;

    $this->datos['nombres_evaluaciones'] = $this->actasModelo->nombre_evaluaciones();
    $this->datos['nombres_indicadores'] = $this->actasModelo->nombre_indicadores();
    $this->datos['preguntas_ep1'] = $this->actasModelo->preguntas_ep1();
    $this->datos['asignaturas'] = $this->actasModelo->obtener_asignaturas($id_dep);
    // RECOGEMOS TODOS LOS VALORES QUE NECESITAMOS - X EVALUACION
    $this->datos['info_actas'] = $this->actasModelo->info_actas_evaluacion($id_evaluacion,$id_dep,$id_lectivo);

     if(!empty($this->datos['lectivo'])){
        $id_lectivo = $this->datos['lectivo'][0]->id_lectivo;
        $this->datos['umbrales_indicadores'] = $this->actasModelo->umbrales_indicadores($id_lectivo);
    } else{
        $id_lectivo = '';
        $this->datos['umbrales_indicadores'] = [];
    }

    $this->vista('jefeDep/actas/actas_evaluacion', $this->datos);
}



//*****************************************************/
//*****************************************************/
// ************* ACTAS EP1 ****************************/
//*****************************************************/
//*****************************************************/


public function actas_ep1(){

    $this->datos['lectivo'] = $this->actasModelo->obtener_lectivo();
    $id_lectivo =  $this->datos['lectivo'][0]->id_lectivo;

    $id = $this->datos['usuarioSesion']->id_profesor;
    $datos = $this->actasModelo->departamentos_formacion($id);
    $id_dep = $datos[0]->id_departamento;

    $this->datos['nombres_evaluaciones'] = $this->actasModelo->nombre_evaluaciones();
    $this->datos['nombres_indicadores'] = $this->actasModelo->nombre_indicadores();
    $this->datos['asignaturas'] = $this->actasModelo->obtener_asignaturas($id_dep);

    // RECOGEMOS TODOS LOS VALORES QUE NECESITAMOS DEL EP1
    $this->datos['info_actas'] = $this->actasModelo->info_actas_ep1($id_dep,$id_lectivo);
    $this->datos['preguntas_ep1'] = $this->actasModelo->preguntas_ep1();

    if(!empty($this->datos['lectivo'])){
        $id_lectivo = $this->datos['lectivo'][0]->id_lectivo;
        $this->datos['umbrales_indicadores'] = $this->actasModelo->umbrales_indicadores($id_lectivo);
    } else{
        $id_lectivo = '';
        $this->datos['umbrales_indicadores'] = [];
    }


    $this->vista('jefeDep/actas/actas_ep1', $this->datos);
}




//*****************************************************/
//*****************************************************/
// ************* RESUMEN MEMORIAS **********************/
//*****************************************************/
//*****************************************************/



public function resumen_memoria(){

    $this->datos['lectivo'] = $this->actasModelo->obtener_lectivo();
    $id = $this->datos['usuarioSesion']->id_profesor;
    $datos = $this->actasModelo->departamentos_formacion($id);
    $id_dep = $datos[0]->id_departamento;
    $this->datos['indicadores_grados'] = $this->actasModelo->obtener_indicadores_grados();

    if(!empty($this->datos['lectivo'])){
        $id_lectivo = $this->datos['lectivo'][0]->id_lectivo;
        $this->datos['his_total_curso'] = $this->actasModelo->his_total_curso($id_dep, $id_lectivo);
        $this->datos['his_total_modulo'] = $this->actasModelo->his_total_modulo($id_dep, $id_lectivo);
    } else{
        $this->datos['lectivo']  = '';
        $this->datos['his_total_curso'] = '';
        $this->datos['his_total_modulo']  = '';
    }

    $this->vista('jefeDep/actas/resumen_memoria', $this->datos);
}






/********************************************************************************************************/
/********************************************************************************************************/
/*************************************** ACTAS EVALUACION ***********************************************/
/********************************************************************************************************/
/*******************************************************************************************************/


public function descargar_actas_csv() {

    $id = $this->datos['usuarioSesion']->id_profesor;
    $datos = $this->actasModelo->departamentos_formacion($id);
    $id_dep = $datos[0]->id_departamento;
    $id_evaluacion = $_POST['id_evaluacion'];
    $this->datos['lectivo'] = $this->actasModelo->obtener_lectivo();
    $id_lectivo =  $this->datos['lectivo'][0]->id_lectivo;
    $this->datos['actas_evaluacion'] = $this->actasModelo->info_actas_evaluacion($id_evaluacion, $id_dep, $id_lectivo);


    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="actas.csv"');
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM para UTF-8

    // Agrupar los datos
    $dataGrouped = [];
    foreach ($this->datos['actas_evaluacion'] as $item) {
        if (is_object($item)) {
            $dataGrouped[$item->evaluacion][$item->ciclo][$item->numero][$item->modulo][] = $item;
        }
    }

    foreach ($dataGrouped as $evaluacion => $ciclos) {
        foreach ($ciclos as $ciclo => $cursos) {
            // Obtener grado y turno del primer módulo del primer curso
            $primerCurso = reset($cursos);
            $primerModulo = reset($primerCurso);
            $primerItem = reset($primerModulo);
            $grado = $primerItem->nombre ?? '---';
            $turno = $primerItem->turno ?? '---';

            // Título de bloque
            $titulo = ("$evaluacion - $ciclo ($grado - $turno)");
            fputcsv($output, [$titulo], ";");


            // Determinar indicadores únicos
            $indicadores = [];
            foreach ($cursos as $curso => $modulos) {
                foreach ($modulos as $items) {
                    foreach ($items as $item) {
                        if (!in_array($item->indicador_corto, $indicadores)) {
                            $indicadores[] = $item->indicador_corto;
                        }
                    }
                }
            }

            // Encabezados de tabla
            $encabezado = array_merge(["Curso", "Modulo", "Profesor"], $indicadores);
            fputcsv($output, $encabezado, ";");

            // Datos por curso
            foreach ($cursos as $curso => $modulos) {
                $sumaIndicadores = array_fill_keys($indicadores, 0);
                $conteoModulos = 0;
                $rowsCurso = [];

                foreach ($modulos as $modulo => $items) {

                    $profesores = [];
                    $valoresIndicadores = [];

                    foreach ($items as $item) {
                        if (!in_array($item->nombre_completo, $profesores)) {
                            $profesores[] = $item->nombre_completo;
                        }
                        $nombre_curso = $item->curso;
                        $valoresIndicadores[$item->indicador_corto] = $item->total;
                    }

                    $nombreProfesor = implode(', ', $profesores);

                    $row = [$curso , $modulo, $nombreProfesor];


                    foreach ($indicadores as $indicador) {
                        $valor = isset($valoresIndicadores[$indicador]) ? $valoresIndicadores[$indicador] : '---';
                        $row[] = ($valor === '---') ? '---' : number_format($valor,2). ' %';
                        if (is_numeric($valor)) {
                            $sumaIndicadores[$indicador] += $valor;
                        }
                    }

                    $rowsCurso[] = $row;
                    $conteoModulos++;
                }

                // Escribir filas del curso
                foreach ($rowsCurso as $row) {
                    $row = array_map(function($value) {
                        return is_string($value) ? ($value) : $value;
                    }, $row);
                    fputcsv($output, $row, ";");
                }

                // Promedios
                $rowPromedio = ["Promedio $nombre_curso", '', ''];
                foreach ($indicadores as $indicador) {
                    $promedio = ($conteoModulos > 0 && is_numeric($sumaIndicadores[$indicador]))
                        ? round($sumaIndicadores[$indicador] / $conteoModulos, 2)
                        : '---';
                    $rowPromedio[] = ($promedio === '---') ? '---' : number_format($promedio,2). ' %';
                }

                $rowPromedio = array_map(function($value) {
                    return is_string($value) ? ($value) : $value;
                }, $rowPromedio);
                fputcsv($output, $rowPromedio, ";");
                fputcsv($output, [], ";"); // Línea en blanco entre ciclos
            }
        }
    }

    fclose($output);
    exit;
}



public function descargar_actas_pdf(){


        $id = $this->datos['usuarioSesion']->id_profesor;
        $datos = $this->actasModelo->departamentos_formacion($id);
        $id_dep = $datos[0]->id_departamento;
        $id_evaluacion = $_POST['id_evaluacion'];
        $this->datos['lectivo'] = $this->actasModelo->obtener_lectivo();
        $id_lectivo =  $this->datos['lectivo'][0]->id_lectivo;
        $this->datos['actas_evaluacion'] = $this->actasModelo->info_actas_evaluacion($id_evaluacion, $id_dep, $id_lectivo);


        $nombre_evaluacion = '';
        $departamento = '';
        foreach ($this->datos['actas_evaluacion'] as $actas) {
            $nombre_evaluacion = $actas->evaluacion;
            $departamento = $actas->departamento;
            break;
        }

        require_once('tcpdf/tcpdf.php');

        $pdf = new TCPDF();
        $pdf->SetPageOrientation('L');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('CPIFP BAJO ARAGON');
        $pdf->SetTitle('Actas de seguimiento');
        $pdf->SetSubject('Actas');
        $pdf->SetKeywords('TCPDF, PDF, ejemplo, actas');

        $pdf->SetMargins(15, 30, 15);
        $pdf->SetHeaderData('', 0, 'Actas de seguimiento', '', [0, 0, 0], [0, 0, 0]);

        $pdf->AddPage();
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->Cell(0, 10, $nombre_evaluacion, 0, 1, 'C');
        $pdf->Ln(10);

        // Agrupar los datos por ciclo
        $dataGrouped = [];
        foreach ($this->datos['actas_evaluacion'] as $item) {
            if (is_object($item)) {
                $dataGrouped[$item->evaluacion][$item->ciclo][$item->modulo][] = $item;
            }
        }

        foreach ($dataGrouped as $evaluacion => $ciclos) {
            foreach ($ciclos as $ciclo => $modulos) {
                // Obtener el primer ítem para grado y turno
                $primerModulo = reset($modulos);
                $primerItem = reset($primerModulo);
                $grado = $primerItem->nombre ?? '---';
                $turno = $primerItem->turno ?? '---';

                // $pdf->Cell(0, 10, "$ciclo ($grado - $turno)", 0, 1, 'C');

                $html = '<h2 style="text-align:left;">' . $ciclo . ' (' . $grado . ' - ' . $turno . ')</h2>';
                $pdf->writeHTML($html, true, false, true, false, '');

                // Encabezado de tabla
                $header = ['Curso', 'Módulo', 'Profesor'];

                // Crear lista única de indicadores
                $indicadores = [];
                foreach ($this->datos['actas_evaluacion'] as $item) {
                    if (is_object($item)) {
                        $indicadores[$item->id_indicador] = $item->indicador_corto;
                    }
                }
                foreach ($indicadores as $indicador) {
                    $header[] = $indicador;
                }

                $html = '<table border="1" cellpadding="5">
                            <thead><tr>';
                foreach ($header as $col) {
                    $html .= '<th style="font-weight:bold; text-align:center; background-color:#f0f0f0;">' . $col . '</th>';
                }
                $html .= '</tr></thead><tbody>';

                // Agrupar módulos por curso
                $modulosPorCurso = [];
                foreach ($modulos as $modulo => $evaluaciones) {
                    foreach ($evaluaciones as $item) {
                        if (isset($item->numero)) {
                            $curso = $item->numero . 'º';
                            $modulosPorCurso[$curso][$modulo][] = $item;
                        }
                    }
                }

                foreach ($modulosPorCurso as $curso => $modulosCurso) {
                    $suma_indicadores_curso = [];
                    $conteo_modulos_curso = 0;

                    foreach ($modulosCurso as $modulo => $evaluaciones) {

                        $profesores = [];
                        foreach ($evaluaciones as $item) {
                            if (isset($item->nombre_completo) && !in_array($item->nombre_completo, $profesores)) {
                                $profesores[] = $item->nombre_completo;
                                $nombre_curso = $item->curso;
                            }
                        }
                        $nombre_profesor = implode(', ', $profesores);


                        $html .= '<tr>';
                        $html .= '<td>' . $curso . '</td>';
                        $html .= '<td>' . $modulo . '</td>';
                        $html .= '<td>' . $nombre_profesor . '</td>';

                        foreach ($indicadores as $id_indicador => $indicador) {
                            $valor = '---';
                            foreach ($evaluaciones as $item) {
                                if (isset($item->id_indicador) && $item->id_indicador == $id_indicador) {
                                    $valor = $item->total;
                                }
                            }

                            if (!is_numeric($valor)) {
                                $valor = 0;
                            }

                            $suma_indicadores_curso[$id_indicador] = ($suma_indicadores_curso[$id_indicador] ?? 0) + $valor;
                            $html .= '<td style="text-align:center;">' . ($valor === 0 ? '---' : $valor . '%') . '</td>';
                        }

                        $html .= '</tr>';
                        $conteo_modulos_curso++;
                    }

                    // Promedio por curso
                    $html .= '<tr><td colspan="3" style="background-color:#f0f0f0;"><strong>Promedio ' . $nombre_curso. '</strong></td>';
                    foreach ($indicadores as $id_indicador => $indicador) {
                        $promedio = ($conteo_modulos_curso > 0) ? round($suma_indicadores_curso[$id_indicador] / $conteo_modulos_curso, 2) : 0;
                        $html .= '<td style="background-color:#f0f0f0; text-align:center; font-weight:bold;">' . $promedio . '%</td>';
                    }
                    $html .= '</tr>';
                }

                $html .= '</tbody></table>';
                $pdf->writeHTML($html, true, false, false, false, '');
                $pdf->AddPage();
            }
        }

        $pdf->Output('actas_evaluacion.pdf', 'I');

}





/********************************************************************************************************/
/********************************************************************************************************/
/***************************************** ACTAS EP1  ***************************************************/
/********************************************************************************************************/
/*******************************************************************************************************/


public function descargar_actas_ep1() {

        $id = $this->datos['usuarioSesion']->id_profesor;
        $datos = $this->actasModelo->departamentos_formacion($id);
        $id_dep = $datos[0]->id_departamento;
        $this->datos['lectivo'] = $this->actasModelo->obtener_lectivo();
        $id_lectivo =  $this->datos['lectivo'][0]->id_lectivo;
        $this->datos['info_actas'] = $this->actasModelo->info_actas_ep1($id_dep, $id_lectivo);
        $this->datos['preguntas_ep1'] = $this->actasModelo->preguntas_ep1();


        $nombre_indicador = $this->datos['preguntas_ep1'][0]->indicador . ' - ' . $this->datos['preguntas_ep1'][0]->indicador_corto;

        require_once('tcpdf/tcpdf.php');

        $pdf = new TCPDF();
        $pdf->SetPageOrientation('L');  
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('CPIFP BAJO ARAGON');
        $pdf->SetTitle('Actas de seguimiento');
        $pdf->SetSubject('Actas');
        $pdf->SetKeywords('TCPDF, PDF, ejemplo, actas');

        $pdf->SetMargins(15, 30, 15);
        $pdf->SetHeaderData('', 0, 'Actas de seguimiento', '', array(0,0,0), array(0,0,0));

        $pdf->AddPage();
        $pdf->SetFont('Helvetica', '', 10);

        $pdf->Cell(0, 10, $nombre_indicador, 0, 1, 'C');
        $pdf->Ln(10);

        // Agrupar respuestas por ciclo y luego por módulo
        $ciclos = [];
        foreach ($this->datos['info_actas'] as $item) {
            if (is_object($item)) {
                $ciclos[$item->ciclo][$item->id_modulo][] = $item;
            }
        }

        foreach ($ciclos as $nombre_ciclo => $modulos) {

            // Obtener grado (nombre) y turno 
            $primer_modulo = reset($modulos);
            $primer_respuesta = reset($primer_modulo);
            $grado = isset($primer_respuesta->nombre) ? $primer_respuesta->nombre : '';
            $turno = isset($primer_respuesta->turno) ? $primer_respuesta->turno : '';

            // Encabezado de tabla
            $header = ['Curso', 'Módulo', 'Profesor'];
            foreach ($this->datos['preguntas_ep1'] as $indicador) {
                $header[] = $indicador->pregunta;
            }

            // Título con grado y turno
            $html = '<h2 style="text-align:left;">' . $nombre_ciclo . ' (' . $grado . ' - ' . $turno . ')</h2>';

            $html .= '<table border="1" cellpadding="5" style="font-size:9pt;">
                <thead>
                    <tr>';
            foreach ($header as $col) {
                $html .= '<th style="background-color:#f0f0f0;">' . $col . '</th>';
            }
            
            $html .= '</tr>
                </thead>
                <tbody>';

            // Agrupar módulos por curso dentro del ciclo
            $cursos = [];
            foreach ($modulos as $modulo_id => $respuestas_modulo) {
                $primer_respuesta = reset($respuestas_modulo);
                $curso = $primer_respuesta->numero;
                $cursos[$curso][$modulo_id] = $respuestas_modulo;
            }

            foreach ($cursos as $numero_curso => $modulos_curso) {
                $respuestas_totales = [];

                foreach ($modulos_curso as $modulo_id => $respuestas_modulo) {
                    $primer_respuesta = reset($respuestas_modulo);
                    $html .= '<tr>';
                    $html .= '<td>' . $primer_respuesta->numero . 'º' . '</td>';
                    $html .= '<td>' . $primer_respuesta->modulo . '</td>';
                    $html .= '<td>' . $primer_respuesta->nombre_completo . '</td>';

                    foreach ($this->datos['preguntas_ep1'] as $pregunta) {

                        // TOMAR SOLO UN VALOR POR PREGUNTA
                        $valor_pregunta = null;

                        foreach ($respuestas_modulo as $respuesta) {
                            if ($respuesta->id_pregunta == $pregunta->id_pregunta && isset($respuesta->ep1)) {
                                $valor_pregunta = floatval($respuesta->ep1);
                                break; // ← evita duplicados
                            }
                        }

                        // Guardar para promedio
                        if ($valor_pregunta !== null) {
                            $respuestas_totales[$pregunta->id_pregunta][] = $valor_pregunta;
                            $html .= '<td style="text-align:center">' . $valor_pregunta . ' %</td>';
                        } else {
                            $html .= '<td style="text-align:center">-</td>';
                        }
                    }

                    $html .= '</tr>';
                }

                // Fila de promedio
                $html .= '<tr style="font-weight:bold; background-color:#f0f0f0;">';
                $html .= '<td colspan="3">Promedio ' . $primer_respuesta->curso . '</td>';

                foreach ($this->datos['preguntas_ep1'] as $pregunta) {
                    if (isset($respuestas_totales[$pregunta->id_pregunta]) && count($respuestas_totales[$pregunta->id_pregunta]) > 0) {
                        $promedio = array_sum($respuestas_totales[$pregunta->id_pregunta]) / count($respuestas_totales[$pregunta->id_pregunta]);
                        $html .= '<td style="text-align:center">' . number_format($promedio, 2) . ' %</td>';
                    } else {
                        $html .= '<td style="text-align:center">0 %</td>';
                    }
                }

                $html .= '</tr>';
            }

            $html .= '</tbody></table><br><br>';

            $pdf->writeHTML($html, true, false, false, false, '');
        }

        $pdf->Output('actas_evaluacion.pdf', 'I');
}




public function descargar_actas_csv_ep1() {

        $id = $this->datos['usuarioSesion']->id_profesor;
        $datos = $this->actasModelo->departamentos_formacion($id);
        $id_dep = $datos[0]->id_departamento;
        $this->datos['lectivo'] = $this->actasModelo->obtener_lectivo();
        $id_lectivo =  $this->datos['lectivo'][0]->id_lectivo;
        $this->datos['info_actas'] = $this->actasModelo->info_actas_ep1($id_dep, $id_lectivo);
        $this->datos['preguntas_ep1'] = $this->actasModelo->preguntas_ep1();


        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="actas_ep1.csv"');
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

        // Agrupar por ciclo → módulo
        $ciclos = [];
        foreach ($this->datos['info_actas'] as $item) {
            if (is_object($item)) {
                $ciclos[$item->ciclo][$item->id_modulo][] = $item;
            }
        }

        foreach ($ciclos as $nombre_ciclo => $modulos) {
            $primer_modulo = reset($modulos);
            $primer_respuesta = reset($primer_modulo);

            $grado = $primer_respuesta->nombre ?? '';
            $turno = $primer_respuesta->turno ?? '';

            // Título del bloque
            fputcsv($output, []);
            fputcsv($output, [$nombre_ciclo . " (" . $grado . " - Turno " . $turno . ")"]);
            fputcsv($output, []);

            // Cabecera
            $header = ['Curso', 'Módulo', 'Profesor'];
            foreach ($this->datos['preguntas_ep1'] as $indicador) {
                $header[] = $indicador->pregunta;
            }

            fputcsv($output, $header, ";");

            // Agrupar por curso
            $cursos = [];
            foreach ($modulos as $modulo_id => $respuestas_modulo) {
                $primer = reset($respuestas_modulo);
                $curso = $primer->numero;
                $cursos[$curso][$modulo_id] = $respuestas_modulo;
            }

            foreach ($cursos as $numero_curso => $modulos_curso) {
                $respuestas_totales = [];

                foreach ($modulos_curso as $modulo_id => $respuestas_modulo) {

                    $primer = reset($respuestas_modulo);
                    $row = [
                        $primer->numero . "º",
                        $primer->modulo,
                        $primer->nombre_completo
                    ];

                    foreach ($this->datos['preguntas_ep1'] as $pregunta) {

                        // TOMAR SOLO UN VALOR POR PREGUNTA
                        $valor_pregunta = null;

                        foreach ($respuestas_modulo as $respuesta) {
                            if ($respuesta->id_pregunta == $pregunta->id_pregunta && isset($respuesta->ep1)) {
                                $valor_pregunta = number_format($respuesta->ep1,2);
                                break; 
                            }
                        }

                        // Guardar para promedio
                        if ($valor_pregunta !== null) {
                            $respuestas_totales[$pregunta->id_pregunta][] = $valor_pregunta;
                            $row[] = number_format($valor_pregunta,2) . "%";
                        } else {
                            $row[] = "-";
                        }
                    }

                    fputcsv($output, $row, ";");
                }

                // Fila de promedio
                $fila_promedio = ["Promedio " . $primer->curso, '', ''];

                foreach ($this->datos['preguntas_ep1'] as $pregunta) {
                    if (!empty($respuestas_totales[$pregunta->id_pregunta])) {
                        $promedio = array_sum($respuestas_totales[$pregunta->id_pregunta]) 
                                / count($respuestas_totales[$pregunta->id_pregunta]);
                        $fila_promedio[] = number_format($promedio, 2)."%";
                    } else {
                        $fila_promedio[] = "0 %";
                    }
                }

                fputcsv($output, $fila_promedio, ";");
                fputcsv($output, []);
            }

            fputcsv($output, []);
        }

        fclose($output);
        exit;
}



/********************************************************************************************************/
/*******************************************************************************************************/
/**************************************** RESUMENES PARA MEMORIA ***************************************/
/******************************************************************************************************/
/*****************************************************************************************************/


public function descargar_resumen_pdf() {

    $this->datos['lectivo'] = $this->actasModelo->obtener_lectivo();
    $id_lectivo =  $this->datos['lectivo'][0]->id_lectivo;
    $id = $this->datos['usuarioSesion']->id_profesor;
    $datos = $this->actasModelo->departamentos_formacion($id);
    $id_dep = $datos[0]->id_departamento;
    $this->datos['his_total_curso'] = $this->actasModelo->his_total_curso($id_dep, $id_lectivo);
    $this->datos['his_total_modulo'] = $this->actasModelo->his_total_modulo($id_dep, $id_lectivo);
    $this->datos['indicadores_grados'] = $this->actasModelo->obtener_indicadores_grados();

    require_once('tcpdf/tcpdf.php');

    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Sistema');
    $pdf->SetTitle('Resumen Indicadores');
    $pdf->SetMargins(15, 20, 15);
    $pdf->SetAutoPageBreak(TRUE, 20);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 8);

    // indicadores
    $indicadores = [];
    foreach ($this->datos['indicadores_grados'] as $indicador) {
        $indicadores[$indicador->indicador_corto] = $indicador->indicador;
    }

    // grupos y módulos
    $grupos = [];
    foreach ($this->datos['his_total_modulo'] as $item) {
        $clave = $item->ciclo . ' - ' . $item->turno;
        $modulo_id = $item->id_modulo;

        if (!isset($grupos[$clave])) {
            $grupos[$clave] = [
                'grado' => $item->grado,
                'modulos' => []
            ];
        }

        if (!isset($grupos[$clave]['modulos'][$modulo_id])) {
            $grupos[$clave]['modulos'][$modulo_id] = [
                'curso' => $item->curso,
                'modulo' => $item->modulo,
                'nombre_corto' => $item->nombre_corto,
                'indicadores' => [],
            ];
        }

        $grupos[$clave]['modulos'][$modulo_id]['indicadores'][$item->indicador_corto] = [
            'valor' => $item->total,
            'conforme' => $item->modulo_conforme
        ];
    }

    // Totales curso
    $totales_curso = [];
    foreach ($this->datos['his_total_curso'] as $item) {
        $curso = $item->curso;
        $codigo = $item->indicador_corto;
        if (!isset($totales_curso[$curso])) {
            $totales_curso[$curso] = [];
        }
        $totales_curso[$curso][$codigo] = [
            'valor' => $item->total,
            'conforme' => $item->conforme
        ];
    }


    $html = '';

        foreach ($grupos as $grupo => $datosGrupo) {
            $grado = $datosGrupo['grado'];
            $modulos = $datosGrupo['modulos'];

            $html .= "<h3 style='text-align:center;'>$grupo ($grado)</h3>";
            $html .= '<table border="1" cellpadding="4" cellspacing="0" width="100%">';
            $html .= '<thead><tr style="background-color:#e0e0e0; text-align:center;">';
            $html .= '<th>Curso</th><th>Módulo</th><th>Código módulo</th>';

            foreach ($indicadores as $codigo => $desc) {
                $html .= "<th style='text-align:center;'>$codigo</th>";
            }

            $html .= '<th style="text-align:center;">ESTADO</th>';
            $html .= '</tr></thead><tbody>';

            // Agrupar módulos por curso
            $modulosPorCurso = [];
            foreach ($modulos as $modulo) {
                $modulosPorCurso[$modulo['curso']][] = $modulo;
            }

            foreach ($modulosPorCurso as $curso => $modulosCurso) {
                foreach ($modulosCurso as $modulo) {
                    $html .= '<tr>';
                    $html .= '<td>' . htmlspecialchars($modulo['curso']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($modulo['modulo']) . '</td>';
                    $html .= '<td style="text-align:center;">' . htmlspecialchars($modulo['nombre_corto']) . '</td>';

                    $estadoConformeArray = [];

                    foreach ($indicadores as $codigo => $desc) {
                        $valor = '---';
                        if (isset($modulo['indicadores'][$codigo])) {
                            $valor = number_format($modulo['indicadores'][$codigo]['valor'], 2) . ' %';
                            $estadoConformeArray[] = $modulo['indicadores'][$codigo]['conforme'];
                        }
                        $html .= "<td style='text-align:center;'>$valor</td>";
                    }

                    // Determinar estado
                    $esConforme = !in_array(0, $estadoConformeArray);
                    $estadoTexto = $esConforme ? 'CONFORME' : 'NO CONFORME';
                    $html .= '<td style="text-align:center; font-weight:bold;">' . $estadoTexto . '</td>';
                    $html .= '</tr>';
                }

                // Fila de promedios curso
                $html .= '<tr style="font-weight:bold; background-color:#f0f0f0; text-align:center;">';
                $html .= '<td colspan="3">Promedio ' . htmlspecialchars($curso) . '</td>';
                foreach ($indicadores as $codigo => $desc) {
                    if (isset($totales_curso[$curso][$codigo])) {
                        $valor = number_format($totales_curso[$curso][$codigo]['valor'], 2) . ' %';
                    } else {
                        $valor = '---';
                    }
                    $html .= "<td style='text-align:center;'>$valor</td>";
                }
                $html .= '<td></td>'; // columna vacía para ESTADO en promedio
                $html .= '</tr>';
            }

            $html .= '</tbody></table><br/><br/>';
        }

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('resumen_indicadores.pdf', 'I');
}





public function descargar_resumen_csv() {

    $id = $this->datos['usuarioSesion']->id_profesor;
    $datos = $this->actasModelo->departamentos_formacion($id);
    $id_dep = $datos[0]->id_departamento;
    $this->datos['lectivo'] = $this->actasModelo->obtener_lectivo();
    $id_lectivo =  $this->datos['lectivo'][0]->id_lectivo;
    $this->datos['his_total_curso'] = $this->actasModelo->his_total_curso($id_dep, $id_lectivo);
    $this->datos['his_total_modulo'] = $this->actasModelo->his_total_modulo($id_dep, $id_lectivo);
    $this->datos['indicadores_grados'] = $this->actasModelo->obtener_indicadores_grados();


    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="resumen_indicadores.csv"');
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

    // indicadores
    $indicadores = [];
    foreach ($this->datos['indicadores_grados'] as $indicador) {
        $indicadores[$indicador->indicador_corto] = $indicador->indicador;
    }

    // grupos y módulos
    $grupos = [];
    foreach ($this->datos['his_total_modulo'] as $item) {
        $clave = $item->ciclo . ' - ' . $item->turno;
        $modulo_id = $item->id_modulo;

        if (!isset($grupos[$clave])) {
            $grupos[$clave] = [
                'grado' => $item->grado,
                'modulos' => []
            ];
        }

        if (!isset($grupos[$clave]['modulos'][$modulo_id])) {
            $grupos[$clave]['modulos'][$modulo_id] = [
                'curso' => $item->curso,
                'modulo' => $item->modulo,
                'nombre_corto' => $item->nombre_corto,
                'indicadores' => [],
            ];
        }

        $grupos[$clave]['modulos'][$modulo_id]['indicadores'][$item->indicador_corto] = [
            'valor' => $item->total,
            'conforme' => $item->modulo_conforme
        ];
    }

    // Totales curso
    $totales_curso = [];
    foreach ($this->datos['his_total_curso'] as $item) {
        $curso = $item->curso;
        $codigo = $item->indicador_corto;
        if (!isset($totales_curso[$curso])) {
            $totales_curso[$curso] = [];
        }
        $totales_curso[$curso][$codigo] = [
            'valor' => $item->total,
            'conforme' => $item->conforme
        ];
    }

    // CABECERA
    $header = ['Grupo', 'Grado', 'Curso', 'Módulo', 'Código módulo'];
    foreach ($indicadores as $codigo => $desc) {
        $header[] = $codigo;
    }

    $header[] = 'ESTADO'; 

    fputcsv($output, $header, ";");

    // CONTENIDO
    foreach ($grupos as $grupo => $datosGrupo) {
        $grado = $datosGrupo['grado'];
        $modulos = $datosGrupo['modulos'];

        // Agrupar módulos por curso
        $modulosPorCurso = [];
        foreach ($modulos as $modulo) {
            $modulosPorCurso[$modulo['curso']][] = $modulo;
        }

        foreach ($modulosPorCurso as $curso => $modulosCurso) {
            foreach ($modulosCurso as $modulo) {
                $fila = [$grupo, $grado, $modulo['curso'], $modulo['modulo'], $modulo['nombre_corto']];
                
                // Indicadores
                foreach ($indicadores as $codigo => $desc) {
                    if (isset($modulo['indicadores'][$codigo])) {
                        $fila[] = number_format($modulo['indicadores'][$codigo]['valor'], 2) . ' %';
                    } else {
                        $fila[] = '---';
                    }
                }

                // ESTADO: CONFORME o NO CONFORME
                $esConforme = !in_array(0, array_column($modulo['indicadores'], 'conforme'));
                $fila[] = $esConforme ? 'CONFORME' : 'NO CONFORME';

                fputcsv($output, $fila, ";");
            }

            // Fila de promedio
            $fila_promedio = ["Promedio " . $curso, '', '', '', ''];
            foreach ($indicadores as $codigo => $desc) {
                if (isset($totales_curso[$curso][$codigo])) {
                    $fila_promedio[] = number_format($totales_curso[$curso][$codigo]['valor'], 2) . ' %';
                } else {
                    $fila_promedio[] = '---';
                }
            }
            fputcsv($output, $fila_promedio, ";");
            fputcsv($output, []); // línea vacía
        }
    }

    fclose($output);
    exit;
}







}


