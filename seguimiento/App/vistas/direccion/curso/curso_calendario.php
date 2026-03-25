

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_configuracion.php'?>
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

    .calendar-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 32px;
    }

    .highlight {
        background-color: #e0e0e0;
        color: #0583c3;
        font-weight: bold;
    }

    .weekend , .holiday {
        background-color: #ffcccc;
    }

    .event-text {
        font-size: 10px;
        color: #0583c3;
    }


    .evaluation {
        background-color: #A8E6CF; 
        color: #0583c3;
        font-weight: bold;
    }

</style>



<?php if (!empty($datos['lectivo'])) : ?>

<div style="text-align:center;">
    <div class="mt-4" style="font-size: 24px; font-weight: 900; color: #0583c3; text-transform: uppercase; letter-spacing: 2px;">
        CALENDARIO ESCOLAR
    </div>
    <div style="font-size: 16px; font-weight: bold; color: #0583c3; margin-top: 6px;">
        NOTA: Coloca el ratón sobre los días señalados para ver observaciones
    </div>
</div>

<?php
    $meses = [1 => "Enero", 2 => "Febrero", 3 => "Marzo", 4 => "Abril", 5 => "Mayo", 6 => "Junio", 7 => "Julio", 8 => "Agosto", 9 => "Septiembre", 10 => "Octubre", 11 => "Noviembre", 12 => "Diciembre"];
    $semana = ["Lun", "Mar", "Mié", "Jue", "Vie", "Sáb", "Dom"];
    $fecha_inicio = DateTime::createFromFormat('d-m-Y', $datos['lectivo'][0]->fecha_inicio);
    $fecha_fin = DateTime::createFromFormat('d-m-Y', $datos['lectivo'][0]->fecha_fin);
    $start_month = $fecha_inicio->format('n'); // Mes de inicio
    $end_month = $fecha_fin->format('n');     // Mes de fin
    $start_year = $fecha_inicio->format('Y');  // Año de inicio
    $end_year = $fecha_fin->format('Y');      // Año de fin

    $festivos = [];
    foreach($datos['calendario_evas_fes'] as $fes):
    if($fes->esFestivo==1):
        array_push($festivos,$fes->fecha);
    endif; endforeach;

    $evaluaciones = [];
    foreach($datos['calendario_evas_fes'] as $evas):
    if($evas->esEvaluacion==1):
        array_push($evaluaciones,$evas->fecha);
    endif; endforeach;

?>


<div class="calendar-container mt-5">
    <?php
    $current_year = $start_year;
    $current_month = $start_month;

    // Iterar entre los meses del curso, considerando que el curso puede abarcar más de un año
    while ($current_year < $end_year || ($current_year == $end_year && $current_month <= $end_month)) {
        // Obtener el primer día y la cantidad de días en el mes
        $first_day = strtotime("{$current_year}-{$current_month}-01");
        $days_in_month = date("t", $first_day);
        $first_weekday = date("N", $first_day); // Día de la semana del 1er día del mes (1 = Lunes, 7 = Domingo)

        // Formatear las fechas de inicio y fin en el formato adecuado para comparar
        $formatted_start_date = $fecha_inicio->format('Y-m-d');
        $formatted_end_date = $fecha_fin->format('Y-m-d');
    ?>

    <table>
        <thead>
            <tr colspan="7">
                <th style="background-color:#0583c3; color:white" colspan="7">
                    <strong><?php echo $meses[$current_month] . " " . $current_year;?></strong>
                </th>
            </tr>
            <tr>
                <?php foreach ($semana as $sem) { ?>
                    <th style="background-color:#0583c3; color:white"><?php echo $sem;?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php
                // Imprimir espacios vacíos antes del primer día del mes
                for ($i = 1; $i < $first_weekday; $i++) : ?>
                    <td></td>
                <?php endfor;

                // Imprimir los días del mes
                for ($day = 1; $day <= $days_in_month; $day++) {
                    $current_date = "$current_year-" . str_pad($current_month, 2, "0", STR_PAD_LEFT) . "-" . str_pad($day, 2, "0", STR_PAD_LEFT);

                    // Verificar si es el primer o último día del curso
                    $highlight_class = '';
                    if ($current_date == $formatted_start_date) {
                        $highlight_class = 'highlight';
                    }
                    if ($current_date == $formatted_end_date) {
                        $highlight_class = 'highlight';
                    }

                    // Verificar si es fin de semana (sábado o domingo)
                    $day_of_week = date("N", strtotime($current_date));
                    $weekend_class = '';
                    if ($day_of_week == 6 || $day_of_week == 7) {
                        $weekend_class = 'weekend';
                    }

                    $holiday_class = '';
                    $holiday_title = '';
                    foreach ($datos['calendario_evas_fes'] as $fes) {
                        if ($fes->fecha == $current_date && $fes->esFestivo == 1) {
                            $holiday_class = 'holiday';
                            $holiday_title = $fes->descripcion;
                            break;
                        }
                    }

                    // Verificar si el día es de evaluación
                    $evaluation_class = '';
                    if (in_array($current_date, $evaluaciones)) {
                        $evaluation_class = 'evaluation';
                    }

                    // Combinamos todas las clases
                    $classes = $highlight_class . ' ' . $weekend_class . ' ' . $holiday_class . ' ' . $evaluation_class;
        

                    // Construir tooltip para ese día con festivos y evaluaciones
                    $tooltip = '';
                    if ($holiday_title) {
                        $tooltip .= $holiday_title;
                    }
                    $evals_for_day = [];
                    foreach ($datos['calendario_evas_fes'] as $evas) {
                        if ($evas->fecha == $current_date && $evas->esEvaluacion == 1) {
                            $evals_for_day[] = $evas->numero.'º '.$evas->grado.' - '.$evas->turno; 
                        }
                    }

                    if (count($evals_for_day) > 0) {
                        if ($tooltip != '') $tooltip .= "\n"; // salto de línea si ya hay festivo
                        $tooltip .= implode("\n", $evals_for_day);
                    }
                ?>


                <td class="<?php echo $classes; ?>" <?php if ($tooltip) echo 'title="'.htmlspecialchars($tooltip).'"'; ?>>

                    <?php echo $day; ?>

                    <!-- Mostrar "Inicio" si es el primer día del curso -->
                    <?php if ($current_date == $formatted_start_date) : ?>
                        <div class="event-text">Inicio Curso</div>
                    <?php endif; ?>

                    <!-- Mostrar "Fin" si es el último día del curso -->
                    <?php if ($current_date == $formatted_end_date) : ?>
                        <div class="event-text">Fin Curso</div>
                    <?php endif; ?>

                    <!-- Mostrar el nombre de la evaluación si es día de evaluación -->
                    <?php if ($evaluation_class) : ?>
                        <div class="event-text">
                            <?php
                            $eval_name = '';
                            foreach ($datos['calendario_evas_fes'] as $evas) {
                                if ($evas->fecha == $current_date && $evas->esEvaluacion == 1) {
                                    $eval_name = $evas->descripcion;
                                    break;
                                }
                            }
                            echo $eval_name;
                            ?>
                        </div>
                    <?php endif; ?>

                </td>

                <?php if (($first_weekday - 1 + $day) % 7 == 0) { ?>
                    </tr><tr>
                <?php }
                }

                // Rellenar los espacios vacíos después del último día del mes
                while (($first_weekday - 1 + $days_in_month) % 7 != 0) { ?>
                    <td></td>
                    <?php $days_in_month++;
                }
                ?>
            </tr>
        </tbody>
    </table>

        <?php
        // Si estamos en diciembre, se debe ajustar al siguiente año
        if ($current_month == 12) {
            $current_month = 1;
            $current_year++;
        } else {
            $current_month++;
        }

        // Cambiar de fila después de dos meses
        if ($current_month % 2 == 0) { ?>
            <div style='flex-basis: 100%;'></div>
        <?php }
        }
        ?>
</div>

<?php endif; ?>




<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>
