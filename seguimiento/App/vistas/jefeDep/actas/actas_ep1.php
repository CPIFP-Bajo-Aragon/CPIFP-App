

<?php require_once RUTA_APP . '/vistas/inc/header_general.php' ?>
<?php require_once RUTA_APP . '/vistas/inc/menu_mi_departamento.php' ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/css/jefeDep/estilos_jefeDep.css">




<?php

$umbrales = $datos['umbrales_indicadores'];
$data = $datos['info_actas'];

// para agrupar datos por ciclo, curso y módulo
$ciclos = [];
foreach ($data as $respuesta) {

    $cicloKey = $respuesta->ciclo;
    $cursoKey = $respuesta->numero . 'º';
    $moduloKey = $respuesta->id_modulo;

    // Si el ciclo no existe, creamos su estructura
    if (!isset($ciclos[$cicloKey])) {
        $ciclos[$cicloKey] = [
            'grado' => $respuesta->nombre,
            'turno' => $respuesta->turno,
            'cursos' => []
        ];
    }

    // Si el curso dentro del ciclo no existe, creamos su estructura y promedios
    if (!isset($ciclos[$cicloKey]['cursos'][$cursoKey])) {
        $ciclos[$cicloKey]['cursos'][$cursoKey] = [
            'nombre_curso' => $respuesta->curso,
            'modulos' => [],
            // Inicializamos promedios y conteos para cada pregunta (id_pregunta)
            'promedios' => array_fill_keys(array_column($datos['preguntas_ep1'], 'id_pregunta'), 0),
            'conteo' => array_fill_keys(array_column($datos['preguntas_ep1'], 'id_pregunta'), 0)
        ];
    }

    // Si el módulo dentro del curso no existe, creamos estructura 
    if (!isset($ciclos[$cicloKey]['cursos'][$cursoKey]['modulos'][$moduloKey])) {
        $ciclos[$cicloKey]['cursos'][$cursoKey]['modulos'][$moduloKey] = [
            'modulo' => $respuesta->modulo,
            'profesores' => [], 
            'id_grado' => $respuesta->id_grado,
            'respuestas' => []
        ];
    }

    // Añadimos el profesor a la lista si no está ya (para evitar duplicados)
    if (!in_array($respuesta->nombre_completo, $ciclos[$cicloKey]['cursos'][$cursoKey]['modulos'][$moduloKey]['profesores'])) {
        $ciclos[$cicloKey]['cursos'][$cursoKey]['modulos'][$moduloKey]['profesores'][] = $respuesta->nombre_completo;
    }

    // Guardamos la respuesta para la pregunta actual dentro del módulo
    $ciclos[$cicloKey]['cursos'][$cursoKey]['modulos'][$moduloKey]['respuestas'][$respuesta->id_pregunta] = $respuesta->ep1;

    // Sumamos la respuesta para cálculo de promedios en el curso
    $ciclos[$cicloKey]['cursos'][$cursoKey]['promedios'][$respuesta->id_pregunta] += $respuesta->ep1;

    // Incrementamos el conteo de respuestas para esa pregunta en el curso
    $ciclos[$cicloKey]['cursos'][$cursoKey]['conteo'][$respuesta->id_pregunta]++;
}


// umbrales para acceso rápido: [id_grado][id_indicador] => porcentaje
$umbrales_indexados = [];
foreach ($umbrales as $u) {
    $umbrales_indexados[$u->id_grado][$u->id_indicador] = $u->porcentaje;
}


// para obtener id_indicador desde id_pregunta
$indicador_id_map = [];
foreach ($data as $item) {
    $indicador_id_map[$item->id_pregunta] = $item->id_indicador;
}

?>




<!-- Filtros y botones de descarga -->
<div class="container mt-4">
<div class="row gy-3">

    <div class="col-12 col-md-4">
        <div class="input-group">
            <span class="input-group-text">Filtrar datos</span>
            <select class="form-select" id="evaluacionesSelect" onchange="window.location.href=this.value;">
                <!-- Opción para ver todo -->
                <option value="<?php echo RUTA_URL ?>/JDActas/actas" <?php echo (basename($_SERVER['REQUEST_URI']) === 'actas') ? 'selected' : ''; ?>>Ver todo</option>
                <!-- Opciones de evaluaciones -->
                <?php foreach ($datos['nombres_evaluaciones'] as $evaluacion): ?>
                    <option value="<?php echo RUTA_URL ?>/JDActas/actas_evaluacion/<?php echo $evaluacion->id_evaluacion ?>" 
                        <?php echo (strpos($_SERVER['REQUEST_URI'], 'actas_evaluacion/'.$evaluacion->id_evaluacion) !== false) ? 'selected' : ''; ?>>
                        <?php echo $evaluacion->evaluacion; ?>
                    </option>
                <?php endforeach; ?>
                <option value="<?php echo RUTA_URL ?>/JDActas/actas_ep1" <?php echo (strpos($_SERVER['REQUEST_URI'], 'actas_ep1') !== false) ? 'selected' : ''; ?>>EP1 (Contenidos impartidos)</option>
            </select>
        </div>
    </div>

    <!-- Botones para descargar actas CSV y PDF si hay un lectivo activo -->
    <?php if (!empty($datos['lectivo'])): ?>
    <div class="col-12 col-md-8 d-flex flex-wrap align-items-center justify-content-start gap-2">
        <form method="post" action="<?php echo RUTA_URL ?>/JDActas/descargar_actas_csv_ep1">
            <input type="hidden" name="id_evaluacion" value="<?php echo $id_evaluacion; ?>">
            <button type="submit" class="btn btn-custom">
                <i class="fas fa-file-csv"></i> Descargar actas (CSV)
            </button>
        </form>
        <form method="post" action="<?php echo RUTA_URL ?>/JDActas/descargar_actas_ep1" target="_blank">
            <input type="hidden" name="id_evaluacion" value="<?php echo $id_evaluacion; ?>">
            <button type="submit" class="btn btn-custom">
                <i class="fas fa-file-pdf"></i> Descargar actas (PDF)
            </button>
        </form>
    </div>
    <?php endif; ?>

</div>
</div>





<?php foreach ($ciclos as $nombre_ciclo => $ciclo): ?>
<div class="table-responsive mt-5">
<table class="table tabla-formato">

        <thead>
            <tr>
                <th colspan="<?php echo count($datos['preguntas_ep1']) + 2; ?>" class="text-center fondo_principal_texto_blanco">
                    <?php echo "$nombre_ciclo ({$ciclo['grado']} - Turno {$ciclo['turno']})"; ?>
                </th>
            </tr>
            <tr>
                <!-- Encabezados: módulo, profesor(es) y preguntas -->
                <th style="width: 20%;">Módulo</th>
                <th style="width: 20%;">Profesor</th>
                <?php foreach ($datos['preguntas_ep1'] as $pregunta): ?>
                    <th class="text-center"><?php echo $pregunta->pregunta; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>


        <tbody>
        <?php foreach ($ciclo['cursos'] as $nombre_curso => $curso): ?>
                <!-- Recorremos módulos de cada curso -->
                <?php foreach ($curso['modulos'] as $modulo): ?>
                <tr>
                        <!-- Nombre del módulo -->
                        <td><?php echo $modulo['modulo']; ?></td>
                        <!-- Profesores -->
                        <td><?php echo implode('<br> ', $modulo['profesores']);?></td>
                        <!-- Resultados por pregunta -->
                        <?php foreach ($datos['preguntas_ep1'] as $pregunta): 
                         
                            $valor = $modulo['respuestas'][$pregunta->id_pregunta] ?? null;
                            $id_grado = $modulo['id_grado'] ?? null;

                            // Obtener el umbral correspondiente para esta pregunta y grado
                            $id_indicador = $indicador_id_map[$pregunta->id_pregunta] ?? null;

                            if ($id_grado && $id_indicador && isset($umbrales_indexados[$id_grado][$id_indicador])) {
                                $umbral = $umbrales_indexados[$id_grado][$id_indicador];
                            } else {
                                $umbral = null;
                            }

                            $color_umbral = '';
                            if ($valor !== null && $umbral !== null) {
                                $color_umbral = ($valor < $umbral) ? 'umbral-bajo' : 'umbral-alto';
                            }?>
                            <td class="text-center <?php echo $color_umbral; ?>">
                                <?php echo $valor !== null ? $valor . ' %' : '-'; ?>
                            </td>
                        <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>


                <!-- FILA PROMEDIOS (se calcula directamente) -->
                <tr>
                    <td colspan="2" class="text-center fila_promedios">
                        Promedio <?php echo $curso['nombre_curso']; ?>
                    </td>
                    <?php foreach ($datos['preguntas_ep1'] as $pregunta): ?>
                        <?php
                        $total = ($curso['conteo'][$pregunta->id_pregunta] > 0)
                            ? round($curso['promedios'][$pregunta->id_pregunta] / $curso['conteo'][$pregunta->id_pregunta], 2)
                            : null;
                        ?>
                        <td class="text-center fila_promedios">
                            <?php echo is_numeric($total) ? $total . ' %' : '-'; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>

        <?php endforeach; ?>
        </tbody>


</table>
</div>
<?php endforeach; ?>





<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
