<?php



class JefeDep extends Controlador{

    private $jefeDepModelo;

    public function __construct(){
        Sesion::iniciarSesion($this->datos);
        $this->datos['usuarioSesion']->id_rol = obtenerRol($this->datos['usuarioSesion']->roles);
        // $this->datos['rolesPermitidos'] = [30];          // Definimos los roles que tendran acceso

        if (!tienePrivilegios($this->datos['usuarioSesion']->id_rol, $this->datos['rolesPermitidos'])) {
            redireccionar('/');
        }
        $this->jefeDepModelo = $this->modelo('JefeDepM');
    }

    




    public function programaciones_modulos($id_ciclo){

        $id = $this->datos['usuarioSesion']->id_profesor;
        $datos = $this->jefeDepModelo->departamentos_formacion($id);
        $id_dep = $datos[0]->id_departamento;

        $this->datos['modulos_ciclo'] = $this->jefeDepModelo->modulos_ciclo($id_ciclo, $id_dep); // info de un ciclo
        $this->datos['programaciones_modulos_activas'] = $this->jefeDepModelo->programaciones_modulos_activas($id_dep); // todas las programaciones activas de los modulos de un departamento
        $this->datos['programaciones_departamento'] = $this->jefeDepModelo->programaciones_departamento($id_dep); // todas las programaciones de un departamento
        $this->datos['programaciones_ediciones_anteriores'] = $this->jefeDepModelo->programaciones_ediciones_anteriores($id_dep); // todas las programaciones de un departamento
        $this->vista('jefeDep/programaciones/programaciones_modulos', $this->datos);   
    }
    



    public function programaciones_fol_leo(){

        $id = $this->datos['usuarioSesion']->id_profesor;
        $datos = $this->jefeDepModelo->departamentos_formacion($id);
        $id_dep = $datos[0]->id_departamento;

        $this->datos['nuevas'] = $this->jefeDepModelo->nuevas_fol_leo($id_dep);
    
        $this->datos['modulos'] = $this->jefeDepModelo->modulos($id_dep);

        $this->datos['programaciones_modulos_activas'] = $this->jefeDepModelo->programaciones_modulos_activas_fol_leo($id_dep); // todas las programaciones activas de los modulos de un departamento
        $this->datos['programaciones_departamento'] = $this->jefeDepModelo->programaciones_departamento_fol_leo($id_dep); // todas las programaciones de un departamento
        $this->datos['programaciones_ediciones_anteriores'] = $this->jefeDepModelo->programaciones_ediciones_anteriores_fol_leo($id_dep); // todas las programaciones de un departamento

        $this->vista('jefeDep/programaciones/programaciones_fol_leo', $this->datos);
     
    }
    


    

// descargar y ver la programacion
public function descargar_programacion($id_modulo){
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $ruta_archivo = $_POST['ruta_archivo'];
        if ($ruta_archivo){

            // Ruta del archivo en el servidor
            //$rutaArchivo = "ruta/a/tu/archivo/$ruta_archivo"; 
            if (file_exists($ruta_archivo)) {
                header('Content-Type: application/pdf'); 
                header('Content-Disposition: attachment; filename="' . basename($ruta_archivo) . '"');
                header('Content-Length: ' . filesize($ruta_archivo));
                readfile($ruta_archivo);
                exit();
            } else {
                echo "El archivo no existe en el servidor.";
            }
        } else {
            echo "No se encontró el archivo en la base de datos.";
        }
        redireccionar('/jefeDep/modulo/'.$id_modulo);
    }
}



/************************************************************/
/************************************************************/
/*********************** ACTAS SEGUIMEINTO ******************/
/************************************************************/
/************************************************************/


// PAGINA INCIO INFORMES DEL DEPARTAMENTO
public function actas(){

    $id = $this->datos['usuarioSesion']->id_profesor;
    $datos = $this->jefeDepModelo->departamentos_formacion($id);
    $id_dep = $datos[0]->id_departamento;

    $this->datos['nombres_evaluaciones'] = $this->jefeDepModelo->nombre_evaluaciones();
    $this->datos['nombres_indicadores'] = $this->jefeDepModelo->nombre_indicadores();
    $this->datos['preguntas_ep1'] = $this->jefeDepModelo->preguntas_ep1();

    $this->datos['asignaturas'] = $this->jefeDepModelo->obtener_asignaturas($id_dep);
    $this->datos['info_actas'] = $this->jefeDepModelo->info_actas($id_dep);

 
    $this->datos['actas_ep1'] = $this->jefeDepModelo->info_actas_ep1($id_dep);
    $this->datos['preguntas_ep1'] = $this->jefeDepModelo->preguntas_ep1();

    $this->vista('jefeDep/actas/actas', $this->datos);
}


// PAGINA INCIO INFORMES DEL DEPARTAMENTO
public function actas_evaluacion($id_evaluacion){

    $id = $this->datos['usuarioSesion']->id_profesor;
    $datos = $this->jefeDepModelo->departamentos_formacion($id);
    $id_dep = $datos[0]->id_departamento;

    $this->datos['nombres_evaluaciones'] = $this->jefeDepModelo->nombre_evaluaciones();
    $this->datos['nombres_indicadores'] = $this->jefeDepModelo->nombre_indicadores();
    $this->datos['preguntas_ep1'] = $this->jefeDepModelo->preguntas_ep1();

    $this->datos['asignaturas'] = $this->jefeDepModelo->obtener_asignaturas($id_dep);

    $this->datos['info_actas'] = $this->jefeDepModelo->info_actas_evaluacion($id_evaluacion,$id_dep);

    $this->vista('jefeDep/actas/actas_evaluacion', $this->datos);
}


public function actas_ep1(){

    $id = $this->datos['usuarioSesion']->id_profesor;
    $datos = $this->jefeDepModelo->departamentos_formacion($id);
    $id_dep = $datos[0]->id_departamento;

    $this->datos['nombres_evaluaciones'] = $this->jefeDepModelo->nombre_evaluaciones();
    $this->datos['nombres_indicadores'] = $this->jefeDepModelo->nombre_indicadores();
  

    $this->datos['asignaturas'] = $this->jefeDepModelo->obtener_asignaturas($id_dep);

    $this->datos['info_actas'] = $this->jefeDepModelo->info_actas_ep1($id_dep);
    $this->datos['preguntas_ep1'] = $this->jefeDepModelo->preguntas_ep1();
    $this->vista('jefeDep/actas/actas_ep1', $this->datos);
}



// *************************************************************************
// *************************************************************************
// *************************************************************************

public function descargar_actas_csv(){
    
    $id = $this->datos['usuarioSesion']->id_profesor;
    $datos = $this->jefeDepModelo->departamentos_formacion($id);
    // $datos = $this->jefeDepModelo->obtenerDatosId($id);
    $id_dep = $datos[0]->id_departamento;
    
    $id_evaluacion = $_POST['id_evaluacion'];
    $this->datos['actas_evaluacion'] = $this->jefeDepModelo->info_actas_evaluacion( $id_evaluacion, $id_dep);


    // Establecemos los encabezados para la descarga del archivo CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="actas.csv"');
    
    // Abrir la salida de PHP para escribir directamente el archivo
    $output = fopen('php://output', 'w');
    
    // Aseguramos que Excel pueda detectar correctamente la codificación UTF-8 al inicio del archivo
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // Esto escribe la BOM para UTF-8
    
    // CABECERAS DE LAS TABLAS
    $indicadores = [];
    foreach ($this->datos['actas_evaluacion'] as $item) {
        if (is_object($item)) {
            $indicadores[$item->id_indicador] = $item->indicador_corto;
        }
    }
    
    // Crear el encabezado del CSV con los nombres de las columnas
    $header = ['Evaluación', 'Ciclo','Grado', 'Curso', 'Módulo', 'Profesor'];
    foreach ($indicadores as $indicador) {
        $header[] = $indicador; // Agregar los indicadores al encabezado
    }
    // Escribir el encabezado en el archivo CSV
    fputcsv($output, $header, ";");  // Usa punto y coma como delimitador
    
    // Agrupar los datos por evaluación
    $dataGrouped = [];
    foreach ($this->datos['actas_evaluacion'] as $item) {
        if (is_object($item)) {
            $dataGrouped[$item->evaluacion][$item->ciclo][$item->modulo][] = $item;
        }
    }
    
    // Recorrer los datos y escribir cada fila
    foreach ($dataGrouped as $evaluacion => $ciclos) {
        foreach ($ciclos as $ciclo => $modulos) {
            foreach ($modulos as $modulo => $evaluaciones) {
                // Comienza la fila con los datos generales
                $row = [$evaluacion, $ciclo, $evaluaciones[0]->nombre, $evaluaciones[0]->numero.'º', $modulo]; 
    
                // Obtener el nombre del profesor
                $nombre_profesor = '';
                foreach ($evaluaciones as $item) {
                    if (isset($item->nombre_completo)) {
                        $nombre_profesor = $item->nombre_completo;
                        break;
                    }
                }
                // Agregar el nombre del profesor a la fila
                $row[] = $nombre_profesor;
    
                // Ahora, agregar los valores de cada indicador a la fila
                foreach ($indicadores as $id_indicador => $indicador) {
                    $valor = '0'; // Valor por defecto
                    
                    // Buscar el valor del indicador
                    foreach ($evaluaciones as $item) {
                        if (isset($item->id_indicador) && $item->id_indicador == $id_indicador) {
                            $valor = $item->total;
                            break;
                        }
                    }
                    // Agregar el valor del indicador a la fila
                    $row[] = $valor.'%';
                }
    
                // Escribir la fila en el archivo CSV
                fputcsv($output, $row, ";");
            }
        }
    }
    
    // Cerrar el archivo CSV
    fclose($output);
    exit;
}






    public function descargar_actas_pdf(){

        $id = $this->datos['usuarioSesion']->id_profesor;
        $datos = $this->jefeDepModelo->departamentos_formacion($id);
        // $datos = $this->jefeDepModelo->obtenerDatosId($id);
        $id_dep = $datos[0]->id_departamento;

        $id_evaluacion = $_POST['id_evaluacion'];
      
        // $this->datos['info_actas'] = $this->jefeDepModelo->info_actas();
        $this->datos['actas_evaluacion'] = $this->jefeDepModelo->info_actas_evaluacion( $id_evaluacion, $id_dep);

        $nombre_evaluacion = '';
        $departamento = '';
        foreach($this->datos['actas_evaluacion'] as $actas):
            $nombre_evaluacion = $actas->evaluacion;
            $departamento = $actas->departamento;
            break;
        endforeach;


        require_once('tcpdf/tcpdf.php'); 

        $pdf = new TCPDF();
        $pdf->SetPageOrientation('L');  // 'L' para landscape (horizontal), 'P' para portrait (vertical)

        
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('CPIFP BAJO ARAGON');
        $pdf->SetTitle('Actas de seguimiento');
        $pdf->SetSubject('Actas');
        $pdf->SetKeywords('TCPDF, PDF, ejemplo, actas');
        
        // Establecer márgenes
        $pdf->SetMargins(15, 30, 15);  // (izquierda, arriba, derecha)
        $pdf->SetHeaderData('', 0, 'Actas de seguimiento', 'CPIFP BAJO ARAGON', array(0,0,0), array(0,0,0));
        
        // Agregar una página
        $pdf->AddPage();
        
        // Configuración de la fuente
        $pdf->SetFont('Helvetica', '', 10);
        
        // Título
        $pdf->Cell(0, 10, $nombre_evaluacion, 0, 1, 'C');
        
        // Salto de línea
        $pdf->Ln(10);
        
        // Tabla de datos
        $header = ['Ciclo','Modulo', 'Grado', 'Curso', 'Profesor'];
        
        // Agregar los indicadores al encabezado
        $indicadores = [];
        foreach ($this->datos['actas_evaluacion'] as $item) {
            if (is_object($item)) {
                $indicadores[$item->id_indicador] = $item->indicador_corto;
            }
        }
        foreach ($indicadores as $indicador) {
            $header[] = $indicador;
        }
        
        // Crear una tabla HTML con los datos
        $html = '<table border="1" cellpadding="5">
            <thead>
                <tr>';
        foreach ($header as $col) {
            $html .= '<th>' . $col . '</th>';
        }
        $html .= '</tr>
            </thead>
            <tbody>';
        
        // Agrupar los datos por evaluación
        $dataGrouped = [];
        foreach ($this->datos['actas_evaluacion'] as $item) {
            if (is_object($item)) {
                $dataGrouped[$item->evaluacion][$item->ciclo][$item->modulo][] = $item;
            }
        }
        
        // Recorrer los datos y agregarlos a la tabla HTML
        foreach ($dataGrouped as $evaluacion => $ciclos) {
            foreach ($ciclos as $ciclo => $modulos) {
                foreach ($modulos as $modulo => $evaluaciones) {
                    // Obtener el nombre del profesor
                    $nombre_profesor = '';
                    foreach ($evaluaciones as $item) {
                        if (isset($item->nombre_completo)) {
                            $nombre_profesor = $item->nombre_completo;
                        }
                        if (isset($item->nombre)) {
                            $grado = $item->nombre;
                        }
                        if (isset($item->numero)) {
                            $curso = $item->numero.'º';
                        }
                    }
        
                    $html .= '<tr>';
                    $html .= '<td>' . $ciclo . '</td>';
                    $html .= '<td>' . $modulo . '</td>';
                    $html .= '<td>' . $grado . '</td>';
                    $html .= '<td>' . $curso . '</td>';
                    $html .= '<td>' . $nombre_profesor . '</td>';
        
                    // Mostrar los valores de cada indicador
                    foreach ($indicadores as $id_indicador => $indicador) {
                        $valor = '0'; 
                        foreach ($evaluaciones as $item) {
                            if (isset($item->id_indicador) && $item->id_indicador == $id_indicador) {
                                $valor = $item->total;
                                break;
                            }
                        }
                        $html .= '<td>' . $valor.'%' . '</td>';
                    }
                    $html .= '</tr>';
                }
            }
        }
        
        $html .= '</tbody></table>';
        
        // Escribir el HTML en el PDF
        $pdf->writeHTML($html, true, false, false, false, '');
        
        // Cerrar y mostrar el PDF
        $pdf->Output('actas_evaluacion.pdf', 'I');  // 'I' para mostrar en el navegador, 'D' para forzar la descarga
        
            
    }



/********************************************************************** */
/********************************************************************** */
/********************************************************************** */



public function descargar_actas_ep1() {

    $id = $this->datos['usuarioSesion']->id_profesor;
    $datos = $this->jefeDepModelo->departamentos_formacion($id);
    $id_dep = $datos[0]->id_departamento;

    // Obtener los datos de las actas y las preguntas del EP1
    $this->datos['info_actas'] = $this->jefeDepModelo->info_actas_ep1($id_dep);
    $this->datos['preguntas_ep1'] = $this->jefeDepModelo->preguntas_ep1();

    $nombre_indicador = $this->datos['preguntas_ep1'][0]->indicador . ' - ' . $this->datos['preguntas_ep1'][0]->indicador_corto;

    require_once('tcpdf/tcpdf.php');

    $pdf = new TCPDF();
    $pdf->SetPageOrientation('L');  // 'L' para landscape (horizontal), 'P' para portrait (vertical)
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('CPIFP BAJO ARAGON');
    $pdf->SetTitle('Actas de seguimiento');
    $pdf->SetSubject('Actas');
    $pdf->SetKeywords('TCPDF, PDF, ejemplo, actas');

    // Establecer márgenes
    $pdf->SetMargins(15, 30, 15);  // (izquierda, arriba, derecha)
    $pdf->SetHeaderData('', 0, 'Actas de seguimiento', 'CPIFP BAJO ARAGON', array(0,0,0), array(0,0,0));

    // Agregar una página
    $pdf->AddPage();

    // Configuración de la fuente
    $pdf->SetFont('Helvetica', '', 10);

    // Título
    $pdf->Cell(0, 10, $nombre_indicador, 0, 1, 'C');
    $pdf->Ln(10);

    // Tabla de datos
    $header = ['Ciclo', 'Grado', 'Curso', 'Módulo', 'Profesor'];

    // Agregar los nombres de las preguntas al encabezado
    foreach ($this->datos['preguntas_ep1'] as $indicador) {
        $header[] = $indicador->pregunta;
    }

    // Crear una tabla HTML con los datos
    $html = '<table border="1" cellpadding="5">
        <thead>
            <tr>';
    foreach ($header as $col) {
        $html .= '<th>' . $col . '</th>';
    }
    $html .= '</tr>
        </thead>
        <tbody>';

    // Agrupar las respuestas por id_modulo
    $modulos = [];
    foreach ($this->datos['info_actas'] as $item) {
        if (is_object($item)) {
            // Agrupar por id_modulo
            $modulos[$item->id_modulo][] = $item;
        }
    }

    // Recorrer los módulos y escribir cada fila
    foreach ($modulos as $modulo_id => $respuestas_modulo) {
        // Tomar la primera respuesta para obtener los datos comunes del módulo
        $primer_respuesta = reset($respuestas_modulo);
        $html .= '<tr>';
        $html .= '<td>' . $primer_respuesta->ciclo . '</td>';
        $html .= '<td>' . $primer_respuesta->nombre . '</td>'; // Se agrega el grado
        $html .= '<td>' . $primer_respuesta->numero.'º'. '</td>'; // Se agrega el curso
        $html .= '<td>' . $primer_respuesta->modulo . '</td>'; // Módulo
        $html .= '<td>' . $primer_respuesta->nombre_completo . '</td>'; // Profesor

        // Ahora, agregar las respuestas para cada pregunta (indicador) de este módulo
        foreach ($this->datos['preguntas_ep1'] as $pregunta) {
            $respuestas_pregunta = [];
            foreach ($respuestas_modulo as $respuesta) {
                // Buscar la respuesta para esta pregunta y módulo
                if ($respuesta->id_pregunta == $pregunta->id_pregunta) {
                    $respuestas_pregunta[] = $respuesta->respuesta;
                }
            }

            // Si no hay respuestas para esta pregunta, mostrar un guion
            $html .= '<td style="text-align:center">' . (count($respuestas_pregunta) > 0 ? implode(", ", $respuestas_pregunta) . "%" : '-') . '</td>';
        }

        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    // Escribir el HTML en el PDF
    $pdf->writeHTML($html, true, false, false, false, '');

    // Cerrar y mostrar el PDF
    $pdf->Output('actas_evaluacion.pdf', 'I');  // 'I' para mostrar en el navegador, 'D' para forzar la descarga
}


// public function descargar_actas_ep1() {

//     $id = $this->datos['usuarioSesion']->id_profesor;
//     $datos = $this->jefeDepModelo->departamentos_formacion($id);
//     $id_dep = $datos[0]->id_departamento;

//     $this->datos['info_actas'] = $this->jefeDepModelo->info_actas_ep1($id_dep);
//     $this->datos['preguntas_ep1'] = $this->jefeDepModelo->preguntas_ep1();

//     $nombre_indicador = $this->datos['preguntas_ep1'][0]->indicador . ' - ' . $this->datos['preguntas_ep1'][0]->indicador_corto;

//     require_once('tcpdf/tcpdf.php');

//     $pdf = new TCPDF();
//     $pdf->SetPageOrientation('L');  // 'L' para landscape (horizontal), 'P' para portrait (vertical)

//     $pdf->SetCreator(PDF_CREATOR);
//     $pdf->SetAuthor('CPIFP BAJO ARAGON');
//     $pdf->SetTitle('Actas de seguimiento');
//     $pdf->SetSubject('Actas');
//     $pdf->SetKeywords('TCPDF, PDF, ejemplo, actas');

//     // Establecer márgenes
//     $pdf->SetMargins(15, 30, 15);  // (izquierda, arriba, derecha)
//     $pdf->SetHeaderData('', 0, 'Actas de seguimiento', 'CPIFP BAJO ARAGON', array(0,0,0), array(0,0,0));

//     // Agregar una página
//     $pdf->AddPage();

//     // Configuración de la fuente
//     $pdf->SetFont('Helvetica', '', 10);

//     // Título
//     $pdf->Cell(0, 10, $nombre_indicador, 0, 1, 'C');

//     // Salto de línea
//     $pdf->Ln(10);

//     // Tabla de datos
//     $header = ['Ciclo', 'Grado', 'Curso','Módulo', 'Profesor'];

//     // Agregar los nombres de las preguntas al encabezado
//     foreach ($this->datos['preguntas_ep1'] as $indicador) {
//         $header[] = $indicador->pregunta;
//     }

//     // Crear una tabla HTML con los datos
//     $html = '<table border="1" cellpadding="5">
//         <thead>
//             <tr>';
//     foreach ($header as $col) {
//         $html .= '<th>' . $col . '</th>';
//     }
//     $html .= '</tr>
//         </thead>
//         <tbody>';

//     // Agrupar las respuestas por id_modulo
//     $modulos = [];
//     foreach ($this->datos['info_actas'] as $item) {
//         if (is_object($item)) {
//             // Agrupar por id_modulo
//             $modulos[$item->id_modulo][] = $item;
//         }
//     }

//     // Recorrer los módulos y escribir cada fila
//     foreach ($modulos as $modulo_id => $respuestas_modulo) {
//         // Tomar la primera respuesta para obtener los datos comunes del módulo
//         $primer_respuesta = reset($respuestas_modulo);
//         $html .= '<tr>';
//         $html .= '<td>' . $primer_respuesta->ciclo . '</td>';
//         $html .= '<td>' . $primer_respuesta->modulo . '</td>';

//         // Obtener el nombre del profesor solo una vez para este módulo
//         $html .= '<td>' . $primer_respuesta->nombre_completo . '</td>';

//         // Ahora, agregar las respuestas para cada pregunta (indicador) de este módulo
//         foreach ($this->datos['preguntas_ep1'] as $pregunta) {
//             $respuestas_pregunta = [];
//             foreach ($respuestas_modulo as $respuesta) {
//                 // Buscar la respuesta para esta pregunta y módulo
//                 if ($respuesta->id_pregunta == $pregunta->id_pregunta) {
//                     $respuestas_pregunta[] = $respuesta->respuesta;
//                 }
//             }
            
//             // Si no hay respuestas para esta pregunta, mostrar un guion
//             $html .= '<td style="text-align:center">' . (count($respuestas_pregunta) > 0 ? implode(", ", $respuestas_pregunta) . "%" : '-') . '</td>';
//         }

//         $html .= '</tr>';
//     }

//     $html .= '</tbody></table>';

//     // Escribir el HTML en el PDF
//     $pdf->writeHTML($html, true, false, false, false, '');

//     // Cerrar y mostrar el PDF
//     $pdf->Output('actas_evaluacion.pdf', 'I');  // 'I' para mostrar en el navegador, 'D' para forzar la descarga
// }


public function descargar_actas_csv_ep1() {

    $id = $this->datos['usuarioSesion']->id_profesor;
    $datos = $this->jefeDepModelo->departamentos_formacion($id);
    $id_dep = $datos[0]->id_departamento;
    
    // Obtener los datos de las actas y las preguntas
    $this->datos['info_actas'] = $this->jefeDepModelo->info_actas_ep1($id_dep);
    $this->datos['preguntas_ep1'] = $this->jefeDepModelo->preguntas_ep1();

    // Establecemos los encabezados para la descarga del archivo CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="actas.csv"');
    
    // Abrir la salida de PHP para escribir directamente el archivo
    $output = fopen('php://output', 'w');
    
    // Aseguramos que Excel pueda detectar correctamente la codificación UTF-8 al inicio del archivo
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // Esto escribe la BOM para UTF-8
    
    // CABECERAS DE LAS TABLAS
    // Crear el encabezado del CSV con los nombres de las columnas
    $header = ['Ciclo', 'Grado', 'Curso', 'Módulo', 'Profesor'];
    
    // Agregar las preguntas (indicadores) al encabezado
    foreach ($this->datos['preguntas_ep1'] as $indicador) {
        $header[] = $indicador->pregunta;
    }

    // Escribir el encabezado en el archivo CSV
    fputcsv($output, $header, ";");  // Usa punto y coma como delimitador
    
    // Agrupar las respuestas por id_modulo
    $modulos = [];
    foreach ($this->datos['info_actas'] as $item) {
        if (is_object($item)) {
            $modulos[$item->id_modulo][] = $item;
        }
    }

    // Recorrer los datos de los módulos y escribir cada fila
    foreach ($modulos as $modulo_id => $respuestas_modulo) {
        // Usamos la primera respuesta del módulo para obtener los datos comunes (ciclo, módulo, profesor)
        $primer_respuesta = reset($respuestas_modulo);
        $row = [];

        // Agregar los datos del ciclo, grado, curso, módulo y profesor
        $row[] = $primer_respuesta->ciclo;
        $row[] = $primer_respuesta->nombre; // Se añade el grado
        $row[] = $primer_respuesta->numero.'º'; // Se añade el curso
        $row[] = $primer_respuesta->modulo;
        $row[] = $primer_respuesta->nombre_completo;

        // Ahora, agregar las respuestas para cada pregunta (indicador) de este módulo
        foreach ($this->datos['preguntas_ep1'] as $pregunta) {
            $respuestas_pregunta = [];
            foreach ($respuestas_modulo as $respuesta) {
                // Buscar la respuesta para esta pregunta y módulo
                if ($respuesta->id_pregunta == $pregunta->id_pregunta) {
                    $respuestas_pregunta[] = $respuesta->respuesta;
                }
            }

            // Si no hay respuestas para esta pregunta, mostrar un guion
            $row[] = (count($respuestas_pregunta) > 0 ? implode(", ", $respuestas_pregunta) . "%" : '-');
        }

        // Escribir la fila en el archivo CSV
        fputcsv($output, $row, ";");
    }

    // Cerrar el archivo CSV
    fclose($output);
    exit;
}



// public function descargar_actas_csv_ep1() {

//     $id = $this->datos['usuarioSesion']->id_profesor;
//     $datos = $this->jefeDepModelo->obtenerDatosId($id);
//     $id_dep = $datos[0]->id_departamento;
//     // Obtenemos los datos de las actas y las preguntas
//     $this->datos['info_actas'] = $this->jefeDepModelo->info_actas_ep1($id_dep);
//     $this->datos['preguntas_ep1'] = $this->jefeDepModelo->preguntas_ep1();

//     // Establecemos los encabezados para la descarga del archivo CSV
//     header('Content-Type: text/csv; charset=utf-8');
//     header('Content-Disposition: attachment; filename="actas.csv"');
    
//     // Abrir la salida de PHP para escribir directamente el archivo
//     $output = fopen('php://output', 'w');
    
//     // Aseguramos que Excel pueda detectar correctamente la codificación UTF-8 al inicio del archivo
//     fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // Esto escribe la BOM para UTF-8
    
//     // CABECERAS DE LAS TABLAS
//     // Crear el encabezado del CSV con los nombres de las columnas
//     $header = ['Ciclo', 'Grado','Curso','Módulo', 'Profesor'];
//     foreach ($this->datos['preguntas_ep1'] as $indicador) {
//         $header[] = $indicador->pregunta; // Agregar las preguntas (indicadores) al encabezado
//     }
//     // Escribir el encabezado en el archivo CSV
//     fputcsv($output, $header, ";");  // Usa punto y coma como delimitador
    
//     // Agrupar las respuestas por id_modulo
//     $modulos = [];
//     foreach ($this->datos['info_actas'] as $item) {
//         if (is_object($item)) {
//             $modulos[$item->id_modulo][] = $item;
//         }
//     }

//     // Recorrer los datos de los módulos y escribir cada fila
//     foreach ($modulos as $modulo_id => $respuestas_modulo) {
//         // Usamos la primera respuesta del módulo para obtener los datos comunes (ciclo, módulo, profesor)
//         $primer_respuesta = reset($respuestas_modulo);
//         $row = [];

//         // Agregar los datos del ciclo, módulo y profesor
//         $row[] = $primer_respuesta->ciclo;
//         $row[] = $primer_respuesta->modulo;
//         $row[] = $primer_respuesta->nombre_completo;

//         // Ahora, agregar las respuestas para cada pregunta (indicador) de este módulo
//         foreach ($this->datos['preguntas_ep1'] as $pregunta) {
//             $respuestas_pregunta = [];
//             foreach ($respuestas_modulo as $respuesta) {
//                 // Buscar la respuesta para esta pregunta y módulo
//                 if ($respuesta->id_pregunta == $pregunta->id_pregunta) {
//                     $respuestas_pregunta[] = $respuesta->respuesta;
//                 }
//             }

//             // Si no hay respuestas para esta pregunta, mostrar un guion
//             $row[] = (count($respuestas_pregunta) > 0 ? implode(", ", $respuestas_pregunta) . "%" : '-');
//         }

//         // Escribir la fila en el archivo CSV
//         fputcsv($output, $row, ";");
//     }

//     // Cerrar el archivo CSV
//     fclose($output);
//     exit;
// }


    }


