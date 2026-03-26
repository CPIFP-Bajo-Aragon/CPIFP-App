<?php

/**
 * facturaPdf.php
 * Genera el justificante de factura en PDF con FPDF.
 *
 * Corrección de codificación: FPDF trabaja con ISO-8859-1 (latin1).
 * Toda cadena que venga de PHP (UTF-8) debe pasar por enc() antes de
 * entregarse a FPDF. Nunca mezclar utf8_decode() suelto por el código.
 */

require_once RUTA_APP . '/librerias/externas/fpdf/fpdf.php';


/**
 * Convierte una cadena UTF-8 a ISO-8859-1 para FPDF.
 * Usar en TODOS los textos que se pasan a Cell(), MostrarDato(), etc.
 */
function enc($str) {
    return mb_convert_encoding((string)$str, 'ISO-8859-1', 'UTF-8');
}

/**
 * Formatea una fecha YYYY-MM-DD a DD/MM/YYYY.
 */
function FechaWeb($fecha) {
    if (empty($fecha)) return '';
    $partes = explode('-', trim($fecha));
    if (count($partes) !== 3) return $fecha;
    return $partes[2] . '/' . $partes[1] . '/' . $partes[0];
}


class PDF extends FPDF
{
    private $ancho  = 163;
    private $alto   = 105;
    private $aviso  = 'N';


    function Inicio($x, $y) {
        $this->SetFont('Arial', 'I', 6);
        $this->SetXY($x, $y - 8);
        $this->Cell($this->ancho, 10, 'F-7A-A-01-ed02', 0, 0, 'R');
    }


    function Cabecera($x, $y, $alto, $ancho, $texto) {
        // Marco exterior
        $this->SetXY($x - 1, $y - 1);
        $this->SetFont('Arial', 'BI', 10);
        $this->Cell($ancho + 2, $alto + 2, '', 1, 2);
        // Marco interior
        $this->SetXY($x, $y);
        $this->Cell($ancho, $alto, '', 1, 2);
        // Logo
        $this->SetXY($x, $y);
        $anchoIm = 50;
        $src = RUTA_APP . './../public/img/icons/logo_cpifp.png';
        $this->Image($src, $x + 1, $y + 1, $anchoIm);
        $this->Cell($anchoIm + 5, 15, '', 'R');
        // Título
        $this->SetXY($x + $anchoIm + 8, $y);
        $this->Cell($x + $anchoIm + 10, 16, $texto);   // $texto ya viene con enc()
        // Líneas divisorias
        $this->Line($x + $anchoIm + 4, $y, $x + $anchoIm + 4, $y + 15);
        $this->Line($x, $y + 15, $x + $ancho, $y + 15);
        $this->Line($x, $y + 16, $x + $ancho, $y + 16);
    }


    function MostrarDato($z, $etiqueta, $contenido) {
        $this->SetFont('Times', 'BI', 12);
        $this->Cell($z, 15, $etiqueta, 0, 0);       // etiqueta ya con enc()
        $this->SetFont('Times', 'I', 12);
        $this->Cell(strlen($contenido), 15, $contenido, 0, 0);  // contenido ya con enc()
    }


    function ParaDep($x, $y, $data) {
        $ancho  = $this->ancho;
        $alto   = $this->alto;

        $this->Cabecera($x, $y, $alto, $ancho,
            enc('CONFORMIDAD DE FACTURA (archivar en el departamento)'));

        $this->SetXY($x + 2, $y + 18);
        $this->MostrarDato(21, enc('Proveedor:'), enc($data['Nombre']));

        $this->SetXY($x + 2, $y + 29);
        $this->MostrarDato(23, enc('Nº Factura:'), enc($data['NFactura']));

        $this->SetXY($x + 60, $y + 29);
        $this->MostrarDato(32, enc('Fecha de factura:'), enc(FechaWeb($data['Ffactura'])));

        $this->SetXY($x + 120, $y + 29);
        $importe = str_replace('.', ',', $data['Importe']) . ' EUR';
        $this->MostrarDato(16, enc('Importe:'), enc($importe));

        $this->Line($x, $y + 42, $x + $ancho, $y + 42);
        $this->Line($x, $y + 43, $x + $ancho, $y + 43);

        $this->SetXY($x + 2, $y + 44);
        $this->MostrarDato(40, enc('Fecha de aprobación:'), enc(FechaWeb($data['Faprobacion'])));

        $this->SetXY($x + 2, $y + 55);
        $this->MostrarDato(72, enc('Destinatario (Departamento o Servicio):'),
            enc($data['Depart_Servicio']));

        $this->SetXY($x + 2, $y + 66);
        $this->MostrarDato(28, enc('Realizado por:'), enc($data['Responsable']));

        $this->SetXY($x + 2, $y + 77);
        $inv = ($data['Inventariable'] == 'S') ? 'Sí' : 'No';
        $this->MostrarDato(29, enc('Inventariable:'), enc($inv));

        if ($data['Inventariable'] == 'S') {
            $this->SetXY($x + 48, $y + 77);
            $this->MostrarDato(40, enc('Nº para inventario:'), enc($data['N_Asiento']));
        }

        $this->Line($x, $y + 90, $x + $ancho, $y + 90);
        $this->Line($x, $y + 91, $x + $ancho, $y + 91);

        $this->SetXY($x + 2, $y + 91);
        $puntuacion = ($data['Item1'] + $data['Item2'] + $data['Item3'] + $data['Item4']) / 4;
        $penalizado = ($data['Item1'] == 1 || $data['Item2'] == 1
                    || $data['Item3'] == 1 || $data['Item4'] == 1
                    || $puntuacion < 4);

        if ($penalizado) {
            $this->MostrarDato(46, enc('Valoración del proveedor:'),
                enc('Penalizado (Revisar con el Dep. Calidad o Secretaría)'));
            $this->aviso = 'S';
        } else {
            $this->MostrarDato(46, enc('Valoración del proveedor:'),
                enc(number_format($puntuacion, 2)));
            $this->aviso = 'N';
        }
    }


    function Division($x, $y) {
        $src = RUTA_APP . './../public/img/icons/tijera.jpg';
        if (file_exists($src)) {
            $this->Image($src, $x - 10, $y + 122, 10);
        }
        $this->Line($x, $y + 126, $x + 162, $y + 126);
    }


    function ParaSecre($x, $y, $data) {
        $ancho = $this->ancho;
        $alto  = $this->alto;

        $this->Cabecera($x, $y, $alto, $ancho,
            enc('CONFORMIDAD DE PAGO (entregar en Secretaría)'));

        $this->SetXY($x + 2, $y + 18);
        $this->MostrarDato(72, enc('Destinatario (Departamento o Servicio):'),
            enc($data['Depart_Servicio']));

        $this->SetXY($x + 2, $y + 29);
        $this->MostrarDato(21, enc('Proveedor:'), enc($data['Nombre']));

        $this->SetXY($x + 2, $y + 40);
        $this->MostrarDato(23, enc('Nº Factura:'), enc($data['NFactura']));

        $this->SetXY($x + 60, $y + 40);
        $this->MostrarDato(32, enc('Fecha de factura:'), enc(FechaWeb($data['Ffactura'])));

        $this->SetXY($x + 120, $y + 40);
        $importe = str_replace('.', ',', $data['Importe']) . ' EUR';
        $this->MostrarDato(16, enc('Importe:'), enc($importe));

        $this->Line($x, $y + 54, $x + $ancho, $y + 54);
        $this->Line($x, $y + 55, $x + $ancho, $y + 55);

        $this->SetXY($x + 2, $y + 57);
        $this->MostrarDato(40, enc('Fecha de aprobación:'), enc(FechaWeb($data['Faprobacion'])));

        $this->SetXY($x + 120, $y + 57);
        $this->MostrarDato(22, enc('(Nº Asiento:'), enc($data['N_Asiento'] . ')'));

        $this->SetXY($x + 2, $y + 68);
        $inv = ($data['Inventariable'] == 'S') ? 'Sí' : 'No';
        $this->MostrarDato(29, enc('Inventariable:'), enc($inv));

        if ($data['Inventariable'] == 'S') {
            $this->SetXY($x + 48, $y + 68);
            $this->MostrarDato(40, enc('Nº para inventario:'), enc($data['N_Asiento']));
        }

        $this->SetXY($x + 2, $y + 79);
        $this->MostrarDato(30, enc('Gestionado por:'), enc($data['Responsable']));

        $this->SetXY($x + 120, $y + 79);
        $this->Cell(100, 15, enc('Firma:'), 0, 1);

        if ($this->aviso == 'S') {
            $this->SetXY($x + 2, $y + 88);
            $this->MostrarDato(5, '', enc('¡¡Revisar valoración del proveedor!!'));
        }
    }
}


// ---- Preparar datos ----
$factura = $datos['factura'];

$data = [
    'Depart_Servicio' => $factura->Depart_Servicio,
    'Nombre'          => $factura->Nombre,
    'NFactura'        => $factura->NFactura,
    'Ffactura'        => $factura->Ffactura,
    'Importe'         => $factura->Importe,
    'Faprobacion'     => $factura->Faprobacion,
    'N_Asiento'       => $factura->N_Asiento,
    'Inventariable'   => $factura->Inventariable,
    'Responsable'     => $factura->Responsable,
    'Item1'           => $factura->Item1,
    'Item2'           => $factura->Item2,
    'Item3'           => $factura->Item3,
    'Item4'           => $factura->Item4,
];

// ---- Generar PDF ----
$pdf = new PDF();
$pdf->SetFont('Arial', '', 6);
$pdf->AddPage();

$x = 30;
$y = 18;

$pdf->Inicio($x, $y);
$pdf->ParaDep($x, $y, $data);
$pdf->Division($x, $y);

$y = 164;
$pdf->ParaSecre($x, $y, $data);

$pdf->Output();
