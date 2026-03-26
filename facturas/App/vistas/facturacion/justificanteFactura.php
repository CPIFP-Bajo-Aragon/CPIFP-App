<?php require_once RUTA_APP.'/librerias/externas/fpdf/fpdf.php'; ?>

<?php

//print_r($datos['factura']);

//require('fpdf.php');

class PDF extends FPDF
{
private $ancho=163;
private $alto=105;
private $aviso="N";




function Inicio($x,$y)
{
	$ancho=$this->ancho;
	$alto=10;
   //Select Arial
    $this->SetFont('Arial','I',6);
	$this->SetXY($x,$y-8);
    $this->Cell($ancho,10,'F-7A-A-01-ed02',0,0,'R');
}

function Cabecera($x,$y,$alto,$ancho,$texto){
	$this->SetXY($x-1,$y-1);
	$this->SetFont('Arial','BI',10);
	$this->Cell($ancho+2,$alto+2,'',1,2);
	$this->SetXY($x,$y);
	$this->Cell($ancho,$alto,'',1,2);
	$this->SetXY($x,$y);
	$anchoIm=50;	
	$this->Image('images/LOGOTIPO.png',$x+1,$y+1,$anchoIm);
	$this->Cell($anchoIm+5,15,'','R');
	$this->SetXY($x+$anchoIm+8,$y);
	$this->Cell($x+$anchoIm+10,16,$texto);
	$this->Line($x+$anchoIm+4,$y,$x+$anchoIm+4,$y+15);
	$this->Line($x,$y+15,$x+$ancho,$y+15);
	$this->Line($x,$y+16,$x+$ancho,$y+16);

}

function MostrarDato($z,$etiqueta,$contenido){
	$this->SetFont('Times','BI',12);
	$this->Cell($z,15,$etiqueta,0,0);
	$this->SetFont('Times','I',12);
	$this->Cell(strlen($contenido),15,$contenido,0,0);

}
function ParaDep($x,$y,$data)
{
	$ancho=$this->ancho;
	$alto=$this->alto;
	$texto='CONFORMIDAD DE FACTURA (archivar en el departamento)';
	$this->Cabecera($x,$y,$alto,$ancho,$texto);
	$this->SetXY($x+2,$y+18);
	$this->MostrarDato(21,'Proveedor:',utf8_decode($data['Nombre']));
	$this->SetXY($x+2,$y+29);
	$this->MostrarDato(23,'N� Factura:',$data['NFactura']);
	$this->SetXY($x+60,$y+29);
	$Ffactura=FechaWeb($data["Ffactura"]);
	$this->MostrarDato(32,'Fecha de factura:',$Ffactura);
	$this->SetXY($x+120,$y+29);
	$Importe2= str_replace(".",",",$data['Importe']);
	$this->MostrarDato(16,'Importe:',$Importe2." �");
	$this->Line($x,$y+42,$x+$ancho,$y+42);
	$this->Line($x,$y+43,$x+$ancho,$y+43);
	$this->SetXY($x+2,$y+44);
	$Faprobacion=FechaWeb($data["Faprobacion"]);
	$this->MostrarDato(40,'Fecha de aprobaci�n:',$Faprobacion);
	$this->SetXY($x+2,$y+55);
	$this->MostrarDato(72,'Destinatario (Departamento o Servicio):',utf8_decode($data['Depart_Servicio']));
	$this->SetXY($x+2,$y+66);
	$this->MostrarDato(28,'Realizado por:',utf8_decode($data['Responsable']));
	$this->SetXY($x+2,$y+77);
	if($data['Inventariable']=='S')
		$this->MostrarDato(29,'Inventariable ->', 'Si');
	else
		$this->MostrarDato(29,'Inventariable ->', 'No');
	if($data['Inventariable']=='S'){
		$this->SetXY($x+48,$y+77);
		$this->MostrarDato(40,'N� para inventario ->', $data['N_Asiento']);
	}
	$this->Line($x,$y+90,$x+$ancho,$y+90);
	$this->Line($x,$y+91,$x+$ancho,$y+91);
	$this->SetXY($x+2,$y+91);
	$Puntuacion = ($data["Item1"] + $data["Item2"] + $data["Item3"] + $data["Item4"])/4;
	if ($data["Item1"] == 1 || $data["Item2"]==1 || $data["Item3"]==1 || $data["Item4"]==1 || $Puntuacion < 4){
			$Penalizacion = "S";
			$this->MostrarDato(46,'Valoraci�n del proveedor:', ' Penalizado (Revisar con el Dep. Calidad o Secretaría)');
			$this->aviso="S";
	}
	else{
			$Penalizacion = "N";
			$this->MostrarDato(46,'Valoraci�n del proveedor:', $Puntuacion);
			$this->aviso="N";
	}
	
	
}
function division($x,$y){
		//$this->Image('images/tijera.jpg',$x-10,$y+122,10);
		$this->Line($x,$y+126,$x+162,$y+126);
}

function ParaSecre($x,$y,$data)
{
	$ancho=$this->ancho;
	$alto=$this->alto;
	$texto='CONFORMIDAD DE PAGO (entregar en Secretar�a)';
	$this->Cabecera($x,$y,$alto,$ancho,$texto);
	$this->SetXY($x+2,$y+18);
	$this->MostrarDato(72,'Destinatario (Departamento o Servicio):',$data['Depart_Servicio']);
	$this->SetXY($x+2,$y+29);
	$this->MostrarDato(21,'Proveedor:',$data['Nombre']);
	$this->SetXY($x+2,$y+40);
	$this->MostrarDato(23,'N�Factura:',$data['NFactura']);
	$this->SetXY($x+60,$y+40);
	$Ffactura=FechaWeb($data["Ffactura"]);
	$this->MostrarDato(32,'Fecha de factura:',$Ffactura);
	$this->SetXY($x+120,$y+40);
	$Importe2= str_replace(".",",",$data['Importe']);
	$this->MostrarDato(16,'Importe:',$Importe2." �");
	$this->Line($x,$y+54,$x+$ancho,$y+54);
	$this->Line($x,$y+55,$x+$ancho,$y+55);
	$this->SetXY($x+2,$y+57);
	$Faprobacion=FechaWeb($data["Faprobacion"]);
	$this->MostrarDato(40,'Fecha de aprobaci�n:',$Faprobacion);
	$this->SetXY($x+120,$y+57);
	$N_Asiento = $data["N_Asiento"];
	$this->MostrarDato(22,'(N� Asiento:',$N_Asiento.")");

	$this->SetXY($x+2,$y+68);
	if($data['Inventariable']=='S')
		$this->MostrarDato(29,'Inventariable ->', 'Si');
	else
		$this->MostrarDato(29,'Inventariable ->', 'No');
	//ESto lo he añadido
	if($data['Inventariable']=='S'){
		$this->SetXY($x+48,$y+68);
		$this->MostrarDato(40,'N� para inventario ->', $data['N_Asiento']);
	}
	$this->SetXY($x+2,$y+79);
	$this->MostrarDato(30,'Gestionado por:',$data['Responsable']);
	$this->SetXY($x+120,$y+79);
	$this->Cell(100,15,'Firma:  ',0,1);
	if ($this->aviso == "S") {
		$this->SetXY($x+2,$y+88);
		$this->MostrarDato(5,'',"��Revisar valoraci�n del proveedor!!");
	}
		
	
}

}
function FechaWeb($fecha)
{
	$wDFecha=explode("-",Trim($fecha));
	// Cambia los formatos de fecha a Dia/Mes/Año para la Web.
	$wDF=$wDFecha[2]. "/". $wDFecha[1]. "/". $wDFecha[0];
	return $wDF;
}

$pdf=new PDF();
///Carga de datos
//require('conexion.php');
//$data=$pdf->LoadData($_GET["N_Asiento"],$servidor,$usuario, $pass,$base_datos );
$pdf->SetFont('Arial','',6);
//$pdf->AddPage();
$x=30;
$y=18;
//$pdf->Inicio($x,$y);
//$pdf->ParaDep($x,$y,$data);
$pdf->Division($x,$y);
$y=164;
//$pdf->ParaSecre($x,$y,$data);
$pdf->Output();
?>
