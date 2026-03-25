<?php



class DInformes extends Controlador{

    private $informesModelo;

    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
        // $this->datos['rolesPermitidos'] = [30];          // Definimos los roles que tendran acceso

        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }
        $this->informesModelo = $this->modelo('InformeM');
    }

    



/**********************************************************************************************/
/**********************************************************************************************/
/*********************************** VISTA GENERAL ********************************************/
/**********************************************************************************************/
/**********************************************************************************************/


public function index(){

    $this->datos['lectivo'] = $this->informesModelo->obtener_lectivo();
    $this->datos['solo_formacion'] = $this->informesModelo->solo_departamentos_formacion(); // DEP. FORMACION

    if (!empty($this->datos['lectivo']) && isset($this->datos['lectivo'][0]->id_lectivo)) {

        $id_lectivo = $this->datos['lectivo'][0]->id_lectivo;
        $this->datos['indicadores_grados'] = $this->informesModelo->obtener_indicadores_grados($id_lectivo); // PORCENTAJES CURSO ACTUAL

        // CALCULO PROMEDIOS CURSOS - LECTIVO ACTUAL
        $this->datos['resumen_modulos'] = $this->informesModelo->resumen_modulos($id_lectivo);
        $resumen = $this->datos['resumen_modulos'];
        $promedios = calcular_promedios($resumen); // calcular promedios 
        $this->informesModelo->insertar_his_total_curso($promedios); // inserta en tabla his_total_curso
        $this->datos['total_curso'] = $this->informesModelo->his_total_curso($id_lectivo); //recuperamos valores his_total_curso (curso actual)

        // CALCULO PROMEDIO DE TODO EL LECTIVO ACTUAL
        $datos = $this->datos['total_curso']; // valores del his_total_curso
        $promedios_curso_actual = promedios_anio_actual($datos); // calcular promedios 
        $this->informesModelo->insertar_promedio_anual($promedios_curso_actual, $id_lectivo); // inserta los promedios y recuepramos
        $this->datos['promedio_anual'] = $this->informesModelo->ver_promedio_anual($id_lectivo); //recuperamos valores his_anual - curso actual

    } else {

        $id_lectivo = '';
        $this->datos['total_curso'] = [];
        $this->datos['indicadores_grados']  = [];
        $this->datos['resumen_modulos']  = [];
        $this->datos['promedio_anual']  = [];

    }


    $this->vista('direccion/informes/informes', $this->datos);
}




/**********************************************************************************************/
/**********************************************************************************************/
/*********************************** VISTA POR DEPARTAMENTO ************************************/
/**********************************************************************************************/
/**********************************************************************************************/



public function por_departamento($departamento){

    //SEPARAMOS TODO LO QUE NOS LLEGA POR LA URL
    $info = explode('-',$departamento);
    $id_dep = $info[0];
    $this->datos['departamento_actual'] = $id_dep;
    $this->datos['lectivo'] = $this->informesModelo->obtener_lectivo();

    if (!empty($this->datos['lectivo']) && isset($this->datos['lectivo'][0]->id_lectivo)) {
        $id_lectivo = $this->datos['lectivo'][0]->id_lectivo;
        $this->datos['solo_formacion'] = $this->informesModelo->solo_departamentos_formacion(); // solo departamentos de formacion (nombres)
        $this->datos['indicadores_grados'] = $this->informesModelo->obtener_indicadores_grados($id_lectivo);  // info indicadores grados
        $this->datos['resumen_modulos'] = $this->informesModelo->his_total_modulo_dep($id_lectivo, $id_dep); 
    } else{
        $id_lectivo = '';
        $this->datos['solo_formacion'] = $this->informesModelo->solo_departamentos_formacion(); // solo departamentos de formacion (nombres)
        $this->datos['indicadores_grados'] = [];
        $this->datos['resumen_modulos'] = [];
    }

    $this->vista('direccion/informes/informes_departamento', $this->datos);
}




/**********************************************************************************************/
/**********************************************************************************************/
/*********************************** VISTA HISTORICOS  ****************************************/
/**********************************************************************************************/
/**********************************************************************************************/

public function historicos(){
    $this->datos['lectivo'] = $this->informesModelo->obtener_lectivo(); // lectivo actual
    $this->datos['his_anual'] = $this->informesModelo->his_anual(); // todo his_anual
    $this->datos['indicadores'] = $this->informesModelo->indicadores(); // nombres indicadores
    $this->vista('direccion/informes/historicos', $this->datos);
}




/**********************************************************************************************/
/**********************************************************************************************/
/****************************************** DESCARGAS  ****************************************/
/**********************************************************************************************/
/**********************************************************************************************/



public function descargar_pdf() {

    $this->datos['lectivo'] = $this->informesModelo->obtener_lectivo();
    $this->datos['solo_formacion'] = $this->informesModelo->solo_departamentos_formacion();
    $id_lectivo = $this->datos['lectivo'][0]->id_lectivo;
    $this->datos['indicadores_grados'] = $this->informesModelo->obtener_indicadores_grados($id_lectivo);
    $this->datos['resumen_modulos'] = $this->informesModelo->resumen_modulos($id_lectivo);
    $resumen = $this->datos['resumen_modulos'];
    $promedios = calcular_promedios($resumen);
    $this->informesModelo->insertar_his_total_curso($promedios);
    $this->datos['total_curso'] = $this->informesModelo->his_total_curso($id_lectivo);
    $datos = $this->datos['total_curso'];
    $promedios_curso_actual = promedios_anio_actual($datos);
    $this->informesModelo->insertar_promedio_anual($promedios_curso_actual, $id_lectivo);
    $this->datos['promedio_anual'] = $this->informesModelo->ver_promedio_anual($id_lectivo);



    require_once('tcpdf/tcpdf.php');
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 8);


    $lectivo_nombre = $this->datos['lectivo'][0]->lectivo;
    $indicadores = [];
    foreach ($this->datos['total_curso'] as $item) {
        if (!in_array($item->indicador_corto, $indicadores)) {
            $indicadores[] = $item->indicador_corto;
        }
    }

    // Umbrales
    $umbrales = [];
    foreach ($this->datos['indicadores_grados'] as $umbral) {
        $umbrales[$umbral->id_grado][$umbral->indicador_corto] = $umbral->porcentaje;
    }

    // Agrupado por ciclo/curso
    $agrupado = [];
    foreach ($this->datos['total_curso'] as $item) {
        $key = $item->ciclo . '|' . $item->curso;
        if (!isset($agrupado[$key])) {
            $agrupado[$key] = [
                'ciclo' => $item->ciclo,
                'curso' => $item->curso,
                'id_grado' => $item->id_grado,
                'valores' => []
            ];
        }
        $agrupado[$key]['valores'][$item->indicador_corto] = [
            'valor' => $item->total,
            'id_indicador' => $item->id_indicador
        ];
    }

    // Promedios
    $promedios = [];
    foreach ($this->datos['promedio_anual'] as $item) {
        $promedios[$item->id_indicador] = $item->promedio;
    }

    $mapaIndicadores = [];
    foreach ($this->datos['total_curso'] as $item) {
        $mapaIndicadores[$item->indicador_corto] = $item->id_indicador;
    }



    $html = '<h2 style="text-align:center;">Informe de Indicadores - Curso ' . $lectivo_nombre . '</h2>';
    $html .= '<table border="1" cellpadding="4" style="border-collapse: collapse; width: 100%;">';
    $html .= '<thead><tr style="background-color:#f0f0f0;">';
    $html .= '<th>Ciclo Formativo</th><th>Curso</th>';
    foreach ($indicadores as $indicador) {
        $html .= '<th style="text-align:center;">' . htmlspecialchars($indicador) . '</th>';
    }
    $html .= '<th style="text-align:center;">ESTADO</th></tr></thead><tbody>';

    foreach ($agrupado as $fila) {
        $esConforme = true;
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($fila['ciclo']) . '</td>';
        $html .= '<td>' . htmlspecialchars($fila['curso']) . '</td>';

        foreach ($indicadores as $indicador_corto) {
            if (isset($fila['valores'][$indicador_corto])) {
                $valor = $fila['valores'][$indicador_corto]['valor'];
                $formato = number_format($valor, 2) . ' %';
                $id_grado = $fila['id_grado'];
                $umbral = $umbrales[$id_grado][$indicador_corto] ?? null;

                if ($umbral !== null && $valor < $umbral) {
                    $esConforme = false;
                    $html .= '<td style="font-weight:bold;text-align:center;">' . $formato . '</td>';
                } else {
                    $html .= '<td style="font-weight:bold; text-align:center;">' . $formato . '</td>';
                }
            } else {
                $html .= '<td style="font-weight:bold;">---</td>';
            }
        }

        $html .= '<td style="font-weight:bold;">' . ($esConforme ? 'CONFORME' : 'NO CONFORME') . '</td>';
        $html .= '</tr>';
    }

    // Fila de promedio general
    $html .= '<tr style="background-color:#0583c3; color:white; font-weight:bold;">';
    $html .= '<td colspan="2">Promedio general</td>';
    foreach ($indicadores as $indicador_corto) {
        $id_indicador = $mapaIndicadores[$indicador_corto] ?? null;
        if ($id_indicador && isset($promedios[$id_indicador])) {
            $html .= '<td style="text-align:center;">' . number_format($promedios[$id_indicador], 2) . ' %</td>';
        } else {
            $html .= '<td>---</td>';
        }
    }
    $html .= '<td></td></tr>';
    $html .= '</tbody></table>';


    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('informe_indicadores_' . date('Ymd') . '.pdf', 'I'); 
}




public function descargar_csv() {

    $this->datos['lectivo'] = $this->informesModelo->obtener_lectivo();
    $this->datos['solo_formacion'] = $this->informesModelo->solo_departamentos_formacion();
    $id_lectivo = $this->datos['lectivo'][0]->id_lectivo;
    $this->datos['indicadores_grados'] = $this->informesModelo->obtener_indicadores_grados($id_lectivo);
    $this->datos['resumen_modulos'] = $this->informesModelo->resumen_modulos($id_lectivo);
    $resumen = $this->datos['resumen_modulos'];
    $promedios = calcular_promedios($resumen);
    $this->informesModelo->insertar_his_total_curso($promedios);
    $this->datos['total_curso'] = $this->informesModelo->his_total_curso($id_lectivo);
    $datos = $this->datos['total_curso'];
    $promedios_curso_actual = promedios_anio_actual($datos);
    $this->informesModelo->insertar_promedio_anual($promedios_curso_actual, $id_lectivo);
    $this->datos['promedio_anual'] = $this->informesModelo->ver_promedio_anual($id_lectivo);

    // Preparar CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="informe_indicadores_' . date('Ymd') . '.csv"');
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM para UTF-8

    $lectivo_nombre = $this->datos['lectivo'][0]->lectivo;
    $indicadores = [];

    foreach ($this->datos['total_curso'] as $item) {
        if (!in_array($item->indicador_corto, $indicadores)) {
            $indicadores[] = $item->indicador_corto;
        }
    }

    // Umbrales
    $umbrales = [];
    foreach ($this->datos['indicadores_grados'] as $umbral) {
        $umbrales[$umbral->id_grado][$umbral->indicador_corto] = $umbral->porcentaje;
    }

    // Agrupado por ciclo/curso
    $agrupado = [];
    foreach ($this->datos['total_curso'] as $item) {
        $key = $item->ciclo . '|' . $item->curso;
        if (!isset($agrupado[$key])) {
            $agrupado[$key] = [
                'ciclo' => $item->ciclo,
                'curso' => $item->curso,
                'id_grado' => $item->id_grado,
                'valores' => []
            ];
        }
        $agrupado[$key]['valores'][$item->indicador_corto] = [
            'valor' => $item->total,
            'id_indicador' => $item->id_indicador
        ];
    }

    // Promedios
    $promedios = [];
    foreach ($this->datos['promedio_anual'] as $item) {
        $promedios[$item->id_indicador] = $item->promedio;
    }

    $mapaIndicadores = [];
    foreach ($this->datos['total_curso'] as $item) {
        $mapaIndicadores[$item->indicador_corto] = $item->id_indicador;
    }

    // TÍTULO
    fputcsv($output, ["Informe de Indicadores - Curso " . $lectivo_nombre], ";");
    fputcsv($output, []); // Línea vacía

    // ENCABEZADOS
    $encabezado = ['Ciclo Formativo', 'Curso'];
    foreach ($indicadores as $indicador) {
        $encabezado[] = $indicador;
    }
    $encabezado[] = 'ESTADO';
    fputcsv($output, $encabezado, ";");

    // CUERPO
    foreach ($agrupado as $fila) {
        $esConforme = true;
        $linea = [
            $fila['ciclo'],
            $fila['curso']
        ];

        foreach ($indicadores as $indicador_corto) {
            if (isset($fila['valores'][$indicador_corto])) {
                $valor = $fila['valores'][$indicador_corto]['valor'];
                $formato = number_format($valor, 2) . ' %';
                $id_grado = $fila['id_grado'];
                $umbral = $umbrales[$id_grado][$indicador_corto] ?? null;

                if ($umbral !== null && $valor < $umbral) {
                    $esConforme = false;
                }

                $linea[] = $formato;
            } else {
                $linea[] = '---';
            }
        }

        $linea[] = $esConforme ? 'CONFORME' : 'NO CONFORME';
        fputcsv($output, $linea, ";");
    }

    // PROMEDIO GENERAL
    $promedio_linea = ['Promedio general', ''];
    foreach ($indicadores as $indicador_corto) {
        $id_indicador = $mapaIndicadores[$indicador_corto] ?? null;
        if ($id_indicador && isset($promedios[$id_indicador])) {
            $promedio_linea[] = number_format($promedios[$id_indicador], 2) . ' %';
        } else {
            $promedio_linea[] = '---';
        }
    }
    $promedio_linea[] = '';
    fputcsv($output, $promedio_linea, ";");

    fclose($output);
    exit;
}



/**********************************************************************************************/
/**********************************************************************************************/
/***************************** DESCARGAS DEPARTAMENTO  ****************************************/
/**********************************************************************************************/
/**********************************************************************************************/


public function descargar_pdf_departamento($departamento) {

    $id_dep = $departamento;
    $this->datos['lectivo'] = $this->informesModelo->obtener_lectivo();
    $id_lectivo = $this->datos['lectivo'][0]->id_lectivo;
    $this->datos['indicadores_grados'] = $this->informesModelo->obtener_indicadores_grados($id_lectivo);
    $this->datos['resumen_modulos'] = $this->informesModelo->his_total_modulo_dep($id_lectivo, $id_dep);

    require_once('tcpdf/tcpdf.php');
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Sistema');
    $pdf->SetTitle('Informe Departamento');
    $pdf->SetMargins(15, 20, 15);
    $pdf->SetAutoPageBreak(TRUE, 20);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 8);

    // Preparar datos de indicadores
    $indicadores = [];
    foreach ($this->datos['indicadores_grados'] as $indicador) {
        $indicadores[$indicador->indicador_corto] = $indicador->indicador;
    }

    // Agrupar módulos por grupo
    $grupos = [];
    foreach ($this->datos['resumen_modulos'] as $item) {
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

    // Comenzar a generar contenido PDF
    $html = '';

    foreach ($grupos as $grupo => $datosGrupo) {
        $grado = $datosGrupo['grado'];
        $modulos = $datosGrupo['modulos'];

        $html .= "<h3 style='text-align:center;'>$grupo ($grado)</h3>";
        $html .= '<table border="1" cellpadding="4" cellspacing="0" width="100%">';
        $html .= '<thead><tr style="background-color:#e0e0e0; text-align:center;">';
        $html .= '<th>Curso</th><th>Módulo</th><th>Código módulo</th>';

        foreach ($indicadores as $codigo => $desc) {
            $html .= "<th>$codigo</th>";
        }

        $html .= '<th>ESTADO</th></tr></thead><tbody>';

        // Agrupar por curso
        $modulosPorCurso = [];
        foreach ($modulos as $modulo) {
            $modulosPorCurso[$modulo['curso']][] = $modulo;
        }

        foreach ($modulosPorCurso as $curso => $modulosCurso) {
            $acumulados = [];
            $cuentas = [];

            foreach ($modulosCurso as $modulo) {
                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($modulo['curso']) . '</td>';
                $html .= '<td>' . htmlspecialchars($modulo['modulo']) . '</td>';
                $html .= '<td style="text-align:center;">' . htmlspecialchars($modulo['nombre_corto']) . '</td>';

                $estadoArray = [];

                foreach ($indicadores as $codigo => $desc) {
                    if (isset($modulo['indicadores'][$codigo])) {
                        $valor = number_format($modulo['indicadores'][$codigo]['valor'], 2);
                        $html .= "<td style='text-align:center;'>$valor %</td>";

                        $acumulados[$codigo] = ($acumulados[$codigo] ?? 0) + $modulo['indicadores'][$codigo]['valor'];
                        $cuentas[$codigo] = ($cuentas[$codigo] ?? 0) + 1;

                        $estadoArray[] = $modulo['indicadores'][$codigo]['conforme'];
                    } else {
                        $html .= "<td style='text-align:center;'>---</td>";
                    }
                }

                $esConforme = !in_array(0, $estadoArray);
                $estadoTexto = $esConforme ? 'CONFORME' : 'NO CONFORME';
                $html .= "<td style='text-align:center; font-weight:bold;'>$estadoTexto</td>";
                $html .= '</tr>';
            }

            // Promedio del curso
            $html .= '<tr style="background-color:#f0f0f0; font-weight:bold; text-align:center;">';
            $html .= '<td colspan="3">Promedio ' . htmlspecialchars($curso) . '</td>';
            foreach ($indicadores as $codigo => $desc) {
                if (!empty($cuentas[$codigo])) {
                    $media = number_format($acumulados[$codigo] / $cuentas[$codigo], 2);
                    $html .= "<td>$media %</td>";
                } else {
                    $html .= "<td>---</td>";
                }
            }
            $html .= '<td></td>'; // Estado vacío en promedio
            $html .= '</tr>';
        }

        $html .= '</tbody></table><br/><br/>';
    }

    // Renderizar HTML
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('informe_departamento.pdf', 'I');
}





public function descargar_csv_departamento($departamento) {

    $id_dep = $departamento;
    $this->datos['lectivo'] = $this->informesModelo->obtener_lectivo();
    $id_lectivo = $this->datos['lectivo'][0]->id_lectivo;
    $this->datos['indicadores_grados'] = $this->informesModelo->obtener_indicadores_grados($id_lectivo);
    $this->datos['resumen_modulos'] = $this->informesModelo->his_total_modulo_dep($id_lectivo, $id_dep);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="informe_departamento_' . date('Ymd') . '.csv"');
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM para UTF-8

    // Preparar indicadores
    $indicadores = [];
    foreach ($this->datos['indicadores_grados'] as $ind) {
        $indicadores[$ind->indicador_corto] = $ind->indicador;
    }

    // Agrupar módulos
    $grupos = [];
    foreach ($this->datos['resumen_modulos'] as $item) {
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
                'indicadores' => []
            ];
        }

        $grupos[$clave]['modulos'][$modulo_id]['indicadores'][$item->indicador_corto] = [
            'valor' => $item->total,
            'conforme' => $item->modulo_conforme
        ];
    }

    // Cabecera
    $cabecera = ['Grupo', 'Grado', 'Curso', 'Módulo', 'Código módulo'];
    foreach ($indicadores as $codigo => $desc) {
        $cabecera[] = $codigo;
    }
    $cabecera[] = 'ESTADO';
    fputcsv($output, $cabecera, ";");

    // Contenido
    foreach ($grupos as $grupo => $infoGrupo) {
        $grado = $infoGrupo['grado'];
        $modulos = $infoGrupo['modulos'];

        // Agrupado por curso
        $modPorCurso = [];
        foreach ($modulos as $modulo) {
            $modPorCurso[$modulo['curso']][] = $modulo;
        }

        foreach ($modPorCurso as $curso => $listaModulos) {

            // Acumuladores para promedio
            $suma = [];
            $cuenta = [];

            foreach ($listaModulos as $modulo) {
                $fila = [$grupo, $grado, $curso, $modulo['modulo'], $modulo['nombre_corto']];
                $esConforme = true;

                foreach ($indicadores as $codigo => $desc) {
                    if (isset($modulo['indicadores'][$codigo])) {
                        $valor = number_format($modulo['indicadores'][$codigo]['valor'], 2);
                        $fila[] = $valor . ' %';

                        $suma[$codigo] = ($suma[$codigo] ?? 0) + $modulo['indicadores'][$codigo]['valor'];
                        $cuenta[$codigo] = ($cuenta[$codigo] ?? 0) + 1;

                        if ($modulo['indicadores'][$codigo]['conforme'] == 0) {
                            $esConforme = false;
                        }
                    } else {
                        $fila[] = '---';
                    }
                }

                $fila[] = $esConforme ? 'CONFORME' : 'NO CONFORME';
                fputcsv($output, $fila, ";");
            }

            // Fila de promedio por curso
            $filaProm = ["Promedio " . $curso, '', '', '', ''];
            foreach ($indicadores as $codigo => $desc) {
                if (!empty($cuenta[$codigo])) {
                    $media = number_format($suma[$codigo] / $cuenta[$codigo], 2);
                    $filaProm[] = $media . ' %';
                } else {
                    $filaProm[] = '---';
                }
            }
            $filaProm[] = '';
            fputcsv($output, $filaProm, ";");
            fputcsv($output, []); // línea en blanco
        }
    }

    fclose($output);
    exit;
}






}


