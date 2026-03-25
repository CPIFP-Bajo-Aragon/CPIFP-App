

<?php require_once RUTA_APP . '/vistas/inc/header_general.php' ?>
<?php require_once RUTA_APP . '/vistas/inc/menu_informes.php' ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">




<?php

$tablaLectivos = [];
$indicadoresCortos = [];
$lectivosNombres = []; 

foreach ($datos['his_anual'] as $item) {

    $id_lectivo = $item->id_lectivo;
    $lectivo = $item->lectivo;
    $indicador = $item->indicador_corto;
    $promedio = $item->promedio;

    // todos los indicadores únicos
    if (!in_array($indicador, $indicadoresCortos)) {
        $indicadoresCortos[] = $indicador;
    }

    // nombre de cada lectivo 
    if (!isset($lectivosNombres[$id_lectivo])) {
        $lectivosNombres[$id_lectivo] = $lectivo;
    }

    // Agrupamos por id_lectivo
    if (!isset($tablaLectivos[$id_lectivo])) {
        $tablaLectivos[$id_lectivo] = [];
    }
    $tablaLectivos[$id_lectivo][$indicador] = $promedio;
}
?>




<div class="d-flex flex-wrap justify-content-between m-4" style="gap: 30px;">


    <!-- Primera tabla: Indicadores -->
    <div class="table-responsive" style="flex: 1 1 250px; min-width: 250px;">
    <table class='table tabla-formato' style='font-size: 13px;'>
        <thead>
            <tr>
                <th>Indicador</th>
                <th>Codigo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($datos['indicadores'] as $indicadores): ?>
                <tr>
                    <td><?php echo $indicadores->indicador?></td>
                    <td class="text-center"><?php echo $indicadores->indicador_corto?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>

    
    <!-- Segunda tabla: Cursos -->
    <?php if(!empty($datos['his_anual'])) : ?>
    <div class="table-responsive" style="flex: 3; min-width: 500px; overflow-x:auto;">
    <table class="table tabla-formato" style="white-space: nowrap; font-size: 16px;">
        <thead>
            <tr>
                <th colspan="<?php echo count($indicadoresCortos) + 1; ?>" style="text-align:center;">
                    COMPARATIVA POR CURSOS
                </th>
            </tr>
            <tr>
                <th>Curso lectivo</th>
                <?php foreach ($indicadoresCortos as $indicador): ?>
                    <th class="text-center"><?php echo htmlspecialchars($indicador); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tablaLectivos as $id_lectivo => $valores): ?>
                <tr>
                    <td><?php echo htmlspecialchars($lectivosNombres[$id_lectivo]);?></td>
                    <?php foreach ($indicadoresCortos as $indicador): ?>
                        <td class="text-center">
                            <?php echo isset($valores[$indicador]) ? number_format($valores[$indicador], 2) . ' %' : '---'; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>



</div>





<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
