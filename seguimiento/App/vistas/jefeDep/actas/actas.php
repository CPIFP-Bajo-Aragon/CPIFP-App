

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_mi_departamento.php'?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/css/jefeDep/estilos_jefeDep.css">

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">



<?php

$umbrales = $datos['umbrales_indicadores'];
$data = $datos['info_actas'];

$indicadores = [];
$evaluaciones = [];  
$evaluaciones_nombres = [];

// Agrupar indicadores y evaluaciones
foreach ($data as $item) {
    $indicadores[$item->indicador] = $item->indicador_corto;
    $id_eval = (int) $item->id_evaluacion;

    if (!in_array($id_eval, $evaluaciones)) {
        $evaluaciones[] = $id_eval;
    }

    if (!isset($evaluaciones_nombres[$id_eval])) {
        $evaluaciones_nombres[$id_eval] = $item->evaluacion;
    }
}

// Agrupar módulos sin duplicar por profesor
$modulos = [];
foreach ($data as $item) {
    $key = $item->modulo . '-' . $item->ciclo . '-' . $item->id_modulo;
    if (!isset($modulos[$key])) {
        $modulos[$key] = [
            'ciclo' => $item->ciclo,
            'grado' => $item->nombre,
            'id_grado' => $item->id_grado,
            'turno' => $item->turno,
            'curso' => $item->numero . 'º',
            'modulo' => $item->modulo,
            'id_modulo' => $item->id_modulo,
            'profesores' => [],
            'indicadores' => [],
        ];
    }
    if (!in_array($item->nombre_completo, $modulos[$key]['profesores'])) {
        $modulos[$key]['profesores'][] = $item->nombre_completo;
    }
    $modulos[$key]['indicadores'][$item->indicador][(int)$item->id_evaluacion] = $item->total;
}

$grupo_modulos = [];
foreach ($modulos as $modulo) {
    $grupo_modulos[$modulo['ciclo']][] = $modulo;
}

$umbrales_indexados = [];
foreach ($umbrales as $u) {
    $umbrales_indexados[$u->id_grado][$u->id_indicador] = $u->porcentaje;
}

// para obtener id_indicador desde indicador_corto
$indicador_id_map = [];
foreach ($data as $item) {
    $indicador_id_map[$item->indicador_corto] = $item->id_indicador;
}
?>





<!-- Filtro -->
<div class="container mt-4">
    <div class="row align-items-center">
        <div class="col-md-6 col-lg-4">
            <div class="input-group">
                <span class="input-group-text">Filtrar datos</span>
                <select class="form-select" id="evaluacionesSelect" onchange="window.location.href=this.value;">
                    <option value="<?php echo RUTA_URL ?>/JDActas/actas" <?php echo (basename($_SERVER['REQUEST_URI']) === 'actas') ? 'selected' : ''; ?>>Ver todo</option>
                    <?php foreach ($datos['nombres_evaluaciones'] as $evaluaciones_obj): ?>
                        <option value="<?php echo RUTA_URL ?>/JDActas/actas_evaluacion/<?php echo $evaluaciones_obj->id_evaluacion ?>" 
                            <?php echo (strpos($_SERVER['REQUEST_URI'], 'actas_evaluacion/'.$evaluaciones_obj->id_evaluacion) !== false) ? 'selected' : ''; ?>>
                            <?php echo $evaluaciones_obj->evaluacion; ?>
                        </option>
                    <?php endforeach; ?>
                    <option value="<?php echo RUTA_URL ?>/JDActas/actas_ep1" <?php echo (strpos($_SERVER['REQUEST_URI'], 'actas_ep1') !== false) ? 'selected' : ''; ?>>EP1 (Contenidos impartidos)</option>
                </select>
            </div>
        </div>
    </div>
</div>




<!-- TablaS -->
<?php foreach ($grupo_modulos as $ciclo => $modulos_del_ciclo): 
    $grado = $modulos_del_ciclo[0]['grado'];
    $turno = $modulos_del_ciclo[0]['turno'];
?>

    <h4 class="mt-5 mb-3 color_principal" style="margin-left:20px;">
        <?php echo "$ciclo ( $grado - turno $turno )"; ?>
    </h4>

    <div class="table-responsive">
    <table class="table tabla-formato">

        <thead>
            <tr>
                <th rowspan="2" class="text-center">Curso</th>
                <th rowspan="2">Módulo</th>
                <th rowspan="2">Profesor</th>
                <?php foreach ($indicadores as $indice => $indicador): ?>
                    <th colspan="<?php echo count($evaluaciones) + 1; ?>" class="text-center">
                        <?php echo $indicador;?> <i class="bi bi-info-circle-fill circulo_info" title="<?php echo $indice;?>"></i>
                    </th>
                <?php endforeach; ?>
                <th colspan="10" class="text-center">
                    <?php echo $datos['preguntas_ep1'][0]->indicador_corto;?>
                    <i class="bi bi-info-circle-fill circulo_info" title="<?php echo $datos['preguntas_ep1'][0]->indicador?>"></i>
                </th>
            </tr>
            <tr>
                <?php foreach ($indicadores as $indicador): ?>
                    <?php foreach ($evaluaciones as $eval): 
                            $nombre = $evaluaciones_nombres[$eval] ?? 'Sin nombre';
                            $nombre_corto = str_replace('evaluacion', 'Eva.', strtolower($nombre));
                        ?>
                        <th class="text-center" style="white-space: nowrap;"><?php echo $nombre_corto; ?></th>
                    <?php endforeach; ?>
                        <th class="text-center">Global</th>
                <?php endforeach; ?>

                <?php foreach ($datos['preguntas_ep1'] as $pregunta): ?>
                    <th class="text-center"><?php echo $pregunta->pregunta; ?></th>
                <?php endforeach; ?>
                    <th class="text-center">Global</th>
            </tr>
        </thead>


        <tbody>
        <?php foreach ($modulos_del_ciclo as $modulo): ?>

            <tr>
                <td class="text-center"><?php echo $modulo['curso']; ?></td>
                <td style="white-space: nowrap;"><?php echo $modulo['modulo']; ?></td>
                <td style="white-space: nowrap;"><?php echo implode('<br>', $modulo['profesores']); ?></td>
     
                <!-- COLUMNAS DATOS POR EVALUACION -->
                <?php foreach ($indicadores as $indicador => $codigo): ?>

                    <?php foreach ($evaluaciones as $eval): 
                            $valor = isset($modulo['indicadores'][$indicador][$eval]) 
                                ? $modulo['indicadores'][$indicador][$eval] 
                                : null;

                            $id_indicador = $indicador_id_map[$codigo] ?? null;
                            $id_grado = $modulo['id_grado'] ?? null;

                            $umbral = ($id_grado && $id_indicador && isset($umbrales_indexados[$id_grado][$id_indicador]))
                                ? $umbrales_indexados[$id_grado][$id_indicador]
                                : null;

                            $clase = (is_numeric($valor) && is_numeric($umbral) && $valor < $umbral) 
                                ? 'umbral-bajo ' 
                                : 'umbral-alto ';
                        ?>
                        <td class="text-center <?php echo $clase;?>">
                            <?php echo $valor !== null ? $valor . ' %' : '---'; ?>
                        </td>
                    <?php endforeach; ?>


                    <!-- COLUMNA GLOBAL CURSO -->
                    <?php
                        $total = '---';
                        foreach ($datos['his_total_modulos'] as $historial) {
                            if ($modulo['id_modulo'] == $historial->id_modulo && $historial->indicador_corto == $codigo) {
                                $valor_total = $historial->total;
                                $total = $valor_total . ' %';
                                break;
                            }
                        }
                        ?>
                        <td class="text-center fila_promedios"><?php echo $total; ?></td>
                    <?php endforeach; ?>


                    <!-- VALORES DEL EP1 -->
                    <?php foreach ($datos['preguntas_ep1'] as $pregunta): ?>
                        <?php 
                        $respuesta = '---';
                        $clase_ep1 = '';
                        foreach ($datos['actas_ep1'] as $res) {
                            if ($res->id_modulo == $modulo['id_modulo'] && $res->id_pregunta == $pregunta->id_pregunta) {

                                $valor_ep1 = $res->ep1;
                                $respuesta = $valor_ep1 . ' %';

                                $id_grado = $modulo['id_grado'] ?? null;
                                $id_indicador = $pregunta->id_indicador ?? null;

                                $umbral = ($id_grado && $id_indicador && isset($umbrales_indexados[$id_grado][$id_indicador]))
                                    ? $umbrales_indexados[$id_grado][$id_indicador]
                                    : null;

                                $clase_ep1 = (is_numeric($valor_ep1) && is_numeric($umbral) && $valor_ep1 < $umbral) 
                                    ? 'umbral-bajo' 
                                    : 'umbral-alto';
                                break;
                            }
                        }
                        ?>
                        <td class="text-center <?php echo $clase_ep1;?>">
                            <?php echo $respuesta; ?>
                        </td>
                    <?php endforeach; ?>


                    <!-- COLUMNA GLOBAL CURSO EP1 -->
                    <?php
                        $total = '---';
                        foreach ($datos['his_total_modulos'] as $historial) {
                            if ($modulo['id_modulo'] == $historial->id_modulo && $historial->indicador_corto == 'EP1') {
                                $valor_total = $historial->total;
                                $total = $valor_total . ' %';
                                break;
                            }
                        }
                    ?>
                    <td class="text-center fila_promedios"><?php echo $total; ?></td>

            </tr>

        <?php endforeach; ?>
        </tbody>

    </table>
    </div>

<?php endforeach; ?>


<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>