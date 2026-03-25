

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_seguimiento.php'?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">



<style>
    table {
        width: 35%;
        border-collapse: collapse;
        text-align: center;
        margin: 5px;
    }

    th, td {
        width: 14%;
        height: 30px;
        border: 1px solid #ccc;
        font-size: 14px;
        vertical-align: center;
    }

    .calendario-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 32px;
    }

    .weekend {
        background-color: #ffcccc;
    }

    .event-text {
        font-size: 10px;
        color: #0583c3;
    }

    .tiene-clase {
        background-color: #f0f0f0;
    }

    .inicio-fin {
        background-color: #A8E6CF;
        color: #0583c3;
        font-weight: bold;
    }
</style>





<?php if (!empty($datos['lectivo'])) :

$festivos = [];
$con_clase = [];
$sin_clase = [];
$horas_por_fecha = [];
$evaluaciones = [];

foreach ($datos['calendario'] as $evento) {

    $fecha_convertida = DateTime::createFromFormat('d-m-Y', $evento->fecha);
    if (!$fecha_convertida) continue;
    $clave_fecha = $fecha_convertida->format('Y-m-d');

    if (isset($evento->es_festivo) && strtolower($evento->es_festivo) === 'sí') {
        $festivos[$clave_fecha] = true;
    } elseif (isset($evento->tiene_clase) && strtolower($evento->tiene_clase) === 'sí') {
        $con_clase[$clave_fecha] = true;
    } elseif (isset($evento->tiene_clase) && strtolower($evento->tiene_clase) === 'no') {
        $sin_clase[$clave_fecha] = true;
    }

    if (isset($evento->horas_dia)) {
        $horas_por_fecha[$clave_fecha] = $evento->horas_dia;
    }

    if (isset($evento->evaluacion) && strtolower($evento->evaluacion) != '') {
        $evaluaciones[$clave_fecha] = $evento->evaluacion;
    }
}

$meses = [1 => "Enero", 2 => "Febrero", 3 => "Marzo", 4 => "Abril", 5 => "Mayo", 6 => "Junio", 7 => "Julio", 8 => "Agosto", 9 => "Septiembre", 10 => "Octubre", 11 => "Noviembre", 12 => "Diciembre"];
$dias_semana = ["Lun", "Mar", "Mié", "Jue", "Vie", "Sáb", "Dom"];

$fecha_inicio = DateTime::createFromFormat('d-m-Y', $datos['lectivo'][0]->fecha_inicio);
$fecha_fin = DateTime::createFromFormat('d-m-Y', $datos['lectivo'][0]->fecha_fin);
$mes_inicio = $fecha_inicio->format('n');
$mes_fin = $fecha_fin->format('n');
$anio_inicio = $fecha_inicio->format('Y');
$anio_fin = $fecha_fin->format('Y');
?>



<div class="calendario-container mt-5">
    <?php
    $anio_actual = $anio_inicio;
    $mes_actual = $mes_inicio;

    while ($anio_actual < $anio_fin || ($anio_actual == $anio_fin && $mes_actual <= $mes_fin)) {
        $primer_dia = strtotime("{$anio_actual}-{$mes_actual}-01");
        $dias_en_mes = date("t", $primer_dia);
        $dia_semana_inicio = date("N", $primer_dia);

        $formato_inicio = $fecha_inicio->format('Y-m-d');
        $formato_fin = $fecha_fin->format('Y-m-d');
    ?>
    <table>
        <thead>
            <tr colspan="7">
                <th style="background-color:#0583c3; color:white" colspan="7">
                    <strong><?php echo $meses[$mes_actual] . " " . $anio_actual;?></strong>
                </th>
            </tr>
            <tr>
                <?php foreach ($dias_semana as $dia) { ?>
                    <th style="background-color:#0583c3; color:white"><?php echo $dia;?></th>
                <?php } ?>
            </tr>
        </thead>

        <tbody>
            <tr>
                <?php for ($i = 1; $i < $dia_semana_inicio; $i++) : ?>
                    <td></td>
                <?php endfor;

                for ($dia = 1; $dia <= $dias_en_mes; $dia++) {
                    $fecha_actual = "$anio_actual-" . str_pad($mes_actual, 2, "0", STR_PAD_LEFT) . "-" . str_pad($dia, 2, "0", STR_PAD_LEFT);

                    $clases = '';
                    if ($fecha_actual == $formato_inicio || $fecha_actual == $formato_fin) {
                        $clases .= 'inicio-fin ';
                    }

                    $dia_semana = date("N", strtotime($fecha_actual));
                    if ($dia_semana == 6 || $dia_semana == 7) {
                        $clases .= 'weekend ';
                    }

                    if (isset($festivos[$fecha_actual])) {
                        $clases .= 'weekend ';
                    }

                    if (isset($sin_clase[$fecha_actual])) {
                        $clases .= 'tiene-clase ';
                    }

                    if (isset($evaluaciones[$fecha_actual])) {
                        $clases .= 'evaluation ';
                    }

                    $clases = trim($clases);
                ?>

                <td class="<?php echo $clases; ?>">
                    <?php echo $dia; ?>

                    <?php if (
                        isset($horas_por_fecha[$fecha_actual]) &&
                        isset($con_clase[$fecha_actual]) &&
                        !isset($festivos[$fecha_actual])
                    ) : ?>
                        <div class="event-text">
                            <?php echo htmlspecialchars($horas_por_fecha[$fecha_actual]); ?> horas
                        </div>
                    <?php endif; ?>

                    <?php if (isset($evaluaciones[$fecha_actual])): ?>
                        <div class="event-text" style="color: green; font-weight: bold;">
                            <?php echo $evaluaciones[$fecha_actual]; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($fecha_actual == $formato_inicio) : ?>
                        <div class="event-text">Inicio Curso</div>
                    <?php endif; ?>

                    <?php if ($fecha_actual == $formato_fin) : ?>
                        <div class="event-text">Fin Curso</div>
                    <?php endif; ?>
                </td>

                <?php if (($dia_semana_inicio - 1 + $dia) % 7 == 0) { ?>
                    </tr><tr>
                <?php }
                }

                while (($dia_semana_inicio - 1 + $dias_en_mes) % 7 != 0) { ?>
                    <td></td>
                    <?php $dias_en_mes++;
                }
                ?>
            </tr>
        </tbody>
    </table>

    <?php
        if ($mes_actual == 12) {
            $mes_actual = 1;
            $anio_actual++;
        } else {
            $mes_actual++;
        }

        if ($mes_actual % 2 == 0) { ?>
            <div style='flex-basis: 100%;'></div>
    <?php }
    }
    ?>
</div>

<?php endif; ?>
<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>
