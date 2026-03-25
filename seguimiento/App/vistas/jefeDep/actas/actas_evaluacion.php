
<?php require_once RUTA_APP . '/vistas/inc/header_general.php' ?>
<?php require_once RUTA_APP . '/vistas/inc/menu_mi_departamento.php' ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/css/jefeDep/estilos_jefeDep.css">

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">



<?php
$data = $datos['info_actas'];
$umbrales = $datos['umbrales_indicadores'];

$nombre_evaluacion = '';
$id_evaluacion = '';
foreach ($data as $item) {
    if (empty($nombre_evaluacion)) {
        $nombre_evaluacion = $item->evaluacion;
        $id_evaluacion = $item->id_evaluacion;
    }
}

$indicadores = [];
$ciclos = [];
$indicador_id_map = [];

// Agrupar datos por ciclo > curso > módulos
foreach ($data as $item) {
    $indicadores[$item->indicador] = $item->indicador_corto;
    $indicador_id_map[$item->indicador_corto] = $item->id_indicador;

    $cursoKey = $item->numero;
    $moduloKey = $item->modulo . '-' . $item->id_modulo;

    if (!isset($ciclos[$item->ciclo])) {
        $ciclos[$item->ciclo] = [];
    }

    if (!isset($ciclos[$item->ciclo][$cursoKey])) {
        $ciclos[$item->ciclo][$cursoKey] = [
            'curso' => $item->numero . 'º',
            'nombre_curso' => $item->curso,
            'grado' => $item->nombre,
            'id_grado' => $item->id_grado,
            'turno' => $item->turno,
            'modulos' => []
        ];
    }

    if (!isset($ciclos[$item->ciclo][$cursoKey]['modulos'][$moduloKey])) {
        $ciclos[$item->ciclo][$cursoKey]['modulos'][$moduloKey] = [
            'modulo' => $item->modulo,
            'id_modulo' => $item->id_modulo,
            'profesores' => [],
            'grado' => $item->nombre,
            'id_grado' => $item->id_grado,
            'turno' => $item->turno,
            'indicadores' => []
        ];
    }

    if (!in_array($item->nombre_completo, $ciclos[$item->ciclo][$cursoKey]['modulos'][$moduloKey]['profesores'])) {
        $ciclos[$item->ciclo][$cursoKey]['modulos'][$moduloKey]['profesores'][] = $item->nombre_completo;
    }

    $ciclos[$item->ciclo][$cursoKey]['modulos'][$moduloKey]['indicadores'][$item->indicador] = $item->total;
}

$umbrales_indexados = [];
foreach ($umbrales as $u) {
    $umbrales_indexados[$u->id_grado][$u->id_indicador] = $u->porcentaje;
}
?>





<div class="container mt-4">
<div class="row gy-3">

        <div class="col-12 col-md-4">
            <div class="input-group">
                <span class="input-group-text">Filtrar datos</span>
                <select class="form-select" id="evaluacionesSelect" onchange="window.location.href=this.value;">
                    <option value="<?php echo RUTA_URL ?>/JDActas/actas" <?php echo (basename($_SERVER['REQUEST_URI']) === 'actas') ? 'selected' : ''; ?>>Ver todo</option>
                    <?php foreach ($datos['nombres_evaluaciones'] as $evaluaciones): ?>
                        <option value="<?php echo RUTA_URL?>/JDActas/actas_evaluacion/<?php echo $evaluaciones->id_evaluacion ?>"
                            <?php echo (strpos($_SERVER['REQUEST_URI'], 'actas_evaluacion/' . $evaluaciones->id_evaluacion) !== false) ? 'selected' : ''; ?>>
                            <?php echo $evaluaciones->evaluacion; ?>
                        </option>
                    <?php endforeach; ?>
                    <option value="<?php echo RUTA_URL?>/JDActas/actas_ep1" <?php echo (strpos($_SERVER['REQUEST_URI'], 'actas_ep1') !== false) ? 'selected' : ''; ?>>EP1 (Contenidos impartidos)</option>
                </select>
            </div>
        </div>

        <div class="col-12 col-md-8 d-flex flex-wrap align-items-center justify-content-start gap-2">
            <form method="post" action="<?php echo RUTA_URL?>/JDActas/descargar_actas_csv">
                <input type="hidden" name="id_evaluacion" value="<?php echo $id_evaluacion; ?>">
                <button type="submit" class="btn btn-custom">
                    <i class="fas fa-file-csv"></i> Descargar actas (CSV)
                </button>
            </form>
            <form method="post" action="<?php echo RUTA_URL?>/JDActas/descargar_actas_pdf" target="_blank">
                <input type="hidden" name="id_evaluacion" value="<?php echo $id_evaluacion; ?>">
                <button type="submit" class="btn btn-custom">
                    <i class="fas fa-file-pdf"></i> Descargar actas (PDF)
                </button>
            </form>
        </div>

</div>
</div>




<?php foreach ($ciclos as $ciclo => $cursos): ?>
<div class="table-responsive mt-5">
<table class="table tabla-formato">

        <thead>
            <tr>
                <th class="text-center fondo_principal_texto_blanco" colspan="<?php echo count($indicadores) + 3; ?>">
                    <?php 
                        $primerCurso = reset($cursos);
                        $grado = $primerCurso['grado'] ?? '---';
                        $turno = $primerCurso['turno'] ?? '---';
                        echo "$ciclo ($grado - $turno)";
                    ?>
                </th>
            </tr>
            <tr>
                <th class="text-center" style="width: 5%;">Curso</th>
                <th style="width: 20%;">Módulo</th>
                <th style="width: 20%;">Profesor</th>
                <?php foreach ($indicadores as $indice => $indicador): ?>
                    <th class="text-center">
                        <?php echo $indicador;?> <i class="bi bi-info-circle-fill circulo_info" title="<?php echo $indice;?>"></i>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($cursos as $curso): 

                $suma_indicadores = [];
                $conteo_modulos = 0;

                foreach ($curso['modulos'] as $modulo): ?>
                    <tr>
                        <td class="text-center"><?php echo $curso['curso']; ?></td>
                        <td><?php echo $modulo['modulo']; ?></td>
                        <td><?php echo implode('<br>', $modulo['profesores']); ?></td>
                        <!-- COLUMNA DATOS EVALUACION -->
                        <?php foreach ($indicadores as $indicador => $codigo): 
                                $valor = $modulo['indicadores'][$indicador] ?? null;
                                $id_indicador = $indicador_id_map[$codigo] ?? null;
                                $id_grado = $modulo['id_grado'] ?? null;
                                $umbral = $umbrales_indexados[$id_grado][$id_indicador] ?? null;
                                $clase_color = (is_numeric($valor) && is_numeric($umbral) && $valor < $umbral) ? 'umbral-bajo' : 'umbral-alto';

                                if ($valor !== null) {
                                    $suma_indicadores[$indicador] = ($suma_indicadores[$indicador] ?? 0) + $valor;
                                }
                        ?>
                        <td class="text-center <?php echo $clase_color; ?>">
                            <?php echo $valor !== null ? $valor . '%' : '---'; ?>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php $conteo_modulos++; ?>
                <?php endforeach; ?>


                <!-- FILA PROMEDIOS POR EVALUACION (se calcula directamente) -->
                <tr class="texto_fuerte">
                    <td colspan="3" class="fila_promedios">Promedio <?php echo $curso['nombre_curso'];?></td>
                    <?php foreach ($indicadores as $indicador => $codigo): 
                        
                        $id_indicador = $indicador_id_map[$codigo] ?? null;
                        $id_grado = $curso['id_grado'] ?? null;
                        $umbral = $umbrales_indexados[$id_grado][$id_indicador] ?? null;

                        if (!empty($suma_indicadores[$indicador]) && $conteo_modulos > 0) {
                            $promedio = round($suma_indicadores[$indicador] / $conteo_modulos, 2);
                            echo "<td class='text-center fila_promedios'>$promedio%</td>";
                        } else {
                            echo "<td class='text-center fila_promedios'>---</td>";
                        }

                    endforeach; ?>
                </tr>

        <?php endforeach; ?>
        </tbody>

</table>
</div>
<?php endforeach; ?>




<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
