<?php

//print_r($datos);

$html = '<!DOCTYPE html>';
$html.= '<html lang="es">';
$html.='<head>';
$html.='<meta charset="UTF-8">';
$html.='<meta name="viewport" content="width=device-width, initial-scale=1.0">';
$html.='<title>Conformidad de Factura</title>';
    
 $html.='<link rel="stylesheet" href="'.RUTA_APP.'./../public/css/estilos_pdf.css">';
$a='<link rel="stylesheet" href="'.RUTA_APP.'./../public/css/estilos_pdf.css">';
$html.='</head>';
$html.='<body>';
$html.='<div class="page">';
$html.='<h1>CONFORMIDAD DE FACTURA</h1>';
        $html.='<div class="cabecera">';
            $html.='<div><img class="logo" src="'.RUTA_APP.'./../public/img/icons/logo_cpifp.png"></div>';     
            $html.='<p>(archivar en el departamento)</p>';
        $html.='</div>';
        $html.='<div class="datosFactura">';
            $html.='<p><strong>Proveedor:</strong> VOICE CLOUD SL</p>';
            $html.='<p><strong>Nº Factura:</strong> 33629</p>';
            $html.='<p><strong>Fecha de factura:</strong> 16/11/2024</p>';
            $html.='<p><strong>Importe:</strong> 6,05 €</p>';
        $html.='</div>';
        
        $html.='<div class="detalle">';
            $html.='<p><strong>Fecha de aprobación:</strong> 10/01/2025</p>';
            $html.='<p><strong>Destinatario:</strong> INFORMÁTICA</p>';
            $html.='<p><strong>Realizado por:</strong> JAVIER ARENZANA ROMEO</p>';
            $html.='<p><strong>Inventariable:</strong> No</p>';
        $html.='</div>';

        $html.='<div class="valoracion">';
            $html.='<p><strong>Valoración del proveedor:</strong> 4.75</p>';
        $html.='</div>';
        
$html.='<h1>CONFORMIDAD DE PAGO</h1>';
$html.='<p class="subtitle">(entregar en Secretaría)</p>';
$html.='<p><strong>Destinatario:</strong> INFORMÁTICA</p>';
$html.='<p><strong>Proveedor:</strong> VOICE CLOUD SL</p>';
$html.='<p><strong>Nº Factura:</strong> 33629</p>';
$html.='<p><strong>Fecha de factura:</strong> 16/11/2024</p>';
$html.='<p><strong>Importe:</strong> 6,05 €</p>';
$html.='<p><strong>Fecha de aprobación:</strong> 10/01/2025</p>';
$html.='<p><strong>Nº Asiento:</strong> 11224</p>';
$html.='<p><strong>Inventariable:</strong> No</p>';
$html.='<p><strong>Gestionado por:</strong> JAVIER ARENZANA ROMEO</p>';
$html.='<p><strong>Firma:</strong> ___________________________</p>';

$html.='</div>'; //fin del class page
$html.='</body>';
$html.='</html>';


//echo $html;


require_once RUTA_APP.'/librerias/externas/html2pdf/vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

try {
   //ob_start();
    //include dirname(__FILE__).'/examples/res/example01.php';
    
   // $content="<h2>akjsdflkja</h2>";
    $html2pdf = new Html2Pdf('P', 'A4', 'es', true, 'UTF-8', 3);
    $html2pdf->writeHTML($html);
    $html2pdf->output('justificante.pdf');
} catch (Html2PdfException $e) {
    $html2pdf->clean();

    $formatter = new ExceptionFormatter($e);
    echo $formatter->getHtmlMessage();
}


?>
