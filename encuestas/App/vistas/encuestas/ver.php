<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<?php
// Distribución puntuaciones 1-10 por pregunta
$distrib_por_preg = [];
foreach($datos['distribucion'] as $d){
    $distrib_por_preg[$d->id_pregunta][$d->puntuacion] = $d->cantidad;
}
// Distribución opciones por pregunta
$distrib_opciones = [];
foreach($datos['distribucion_opciones'] as $d){
    $distrib_opciones[$d->id_pregunta][$d->valor_opcion] = $d->cantidad;
}
// Media global solo de preguntas de puntuación
$medias_punt = array_filter((array)$datos['resumen'], fn($r) => ($r->tipo_respuesta ?? 'puntuacion') === 'puntuacion' && $r->media !== null);
$media_global = count($medias_punt) > 0
    ? round(array_sum(array_column($medias_punt, 'media')) / count($medias_punt), 2)
    : null;
?>

<div class="container-fluid px-4 py-4">

    <!-- Cabecera -->
    <div class="row mb-3 align-items-center">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <i class="fas fa-chart-bar me-2"></i>
                <?php echo htmlspecialchars($datos['encuesta']->titulo) ?>
            </span>
        </div>
        <div class="col-auto d-flex gap-2">
            <?php if($datos['usuarioSesion']->id_rol >= 200): ?>
            <a href="<?php echo RUTA_URL ?>/encuestas/editar/<?php echo $datos['encuesta']->id_encuesta ?>"
               class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-edit me-1"></i>Editar
            </a>
            <?php if($datos['encuesta']->activa): ?>
            <button class="btn btn-sm btn-warning" onclick="cerrarEncuesta(<?php echo $datos['encuesta']->id_encuesta ?>)">
                <i class="fas fa-lock me-1"></i>Cerrar encuesta
            </button>
            <?php else: ?>
            <button class="btn btn-sm btn-success" onclick="abrirEncuesta(<?php echo $datos['encuesta']->id_encuesta ?>)">
                <i class="fas fa-lock-open me-1"></i>Abrir encuesta
            </button>
            <?php endif; ?>
            <?php if($datos['usuarioSesion']->id_rol >= 300): ?>
            <button class="btn btn-sm btn-danger" onclick="eliminarEncuesta(<?php echo $datos['encuesta']->id_encuesta ?>)">
                <i class="fas fa-trash"></i>
            </button>
            <?php endif; ?>
            <?php if(!empty($datos['puedeEliminarForzado'])): ?>
            <button class="btn btn-sm text-white"
                    style="background:#7b0000; border-color:#5a0000;"
                    title="Eliminar con todos sus datos (solo calidad / directivo)"
                    onclick="eliminarForzado(<?php echo $datos['encuesta']->id_encuesta ?>, '<?php echo addslashes($datos['encuesta']->titulo) ?>')">
                <i class="fas fa-exclamation-triangle me-1"></i>Borrado total
            </button>
            <?php endif; ?>
            <?php endif; ?>
            <a href="<?php echo RUTA_URL ?>/encuestas" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <!-- Info de la encuesta -->
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6 col-md-4">
                            <div class="text-muted small">Tipo</div>
                            <div class="fw-bold"><?php echo htmlspecialchars($datos['encuesta']->tipo_encuesta) ?></div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="text-muted small">Curso académico</div>
                            <div class="fw-bold"><?php echo htmlspecialchars($datos['encuesta']->curso_academico) ?></div>
                        </div>
                        <?php if($datos['encuesta']->trimestre): ?>
                        <div class="col-6 col-md-4">
                            <div class="text-muted small">Trimestre</div>
                            <div class="fw-bold"><?php echo etiquetaTrimestre($datos['encuesta']->trimestre) ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if($datos['encuesta']->nombre_profesor): ?>
                        <div class="col-6 col-md-4">
                            <div class="text-muted small">Profesor</div>
                            <div class="fw-bold"><?php echo htmlspecialchars($datos['encuesta']->nombre_profesor) ?></div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="text-muted small">Módulo</div>
                            <div class="fw-bold"><?php echo htmlspecialchars($datos['encuesta']->nombre_modulo) ?></div>
                        </div>
                        <?php if($datos['encuesta']->nombre_ciclo ?? null): ?>
                        <div class="col-6 col-md-4">
                            <div class="text-muted small">Ciclo / Curso</div>
                            <div class="fw-bold"><?php echo htmlspecialchars($datos['encuesta']->nombre_ciclo . ' – ' . $datos['encuesta']->nombre_curso) ?></div>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                        <div class="col-6 col-md-4">
                            <div class="text-muted small">Fecha inicio</div>
                            <div class="fw-bold"><?php echo formatoFechaCorta($datos['encuesta']->fecha_inicio) ?></div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="text-muted small">Estado</div>
                            <?php if($datos['encuesta']->activa): ?>
                                <span class="badge bg-success fs-6">Abierta</span>
                            <?php else: ?>
                                <span class="badge bg-danger fs-6">Cerrada</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if($datos['encuesta']->descripcion): ?>
                    <hr>
                    <p class="mb-0 text-muted"><?php echo nl2br(htmlspecialchars($datos['encuesta']->descripcion)) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Enlace público -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="fw-bold mb-2"><i class="fas fa-link me-1 text-primary"></i>Enlace público</div>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="enlace_publico"
                               value="<?php echo $datos['enlace_publico'] ?>" readonly>
                        <button class="btn btn-outline-secondary" onclick="copiarEnlace()" title="Copiar">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <div class="mt-2">
                        <a href="<?php echo $datos['enlace_publico'] ?>" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                            <i class="fas fa-external-link-alt me-1"></i>Previsualizar formulario
                        </a>
                    </div>
                </div>
            </div>

            <?php if(!empty($datos['encuesta']->codigo_acceso)): ?>
            <!-- Código de acceso para alumnos -->
            <div class="card border-0 shadow-sm mb-3" style="background:#fffbf0; border:1px solid #ffe082 !important;">
                <div class="card-body text-center">
                    <div class="fw-bold mb-1" style="color:#b8860b;">
                        <i class="fas fa-key me-1"></i>Código de acceso para alumnos
                    </div>
                    <div style="font-size:2.8rem; font-weight:900; letter-spacing:0.35em; color:#333; font-family:monospace;">
                        <?php echo htmlspecialchars($datos['encuesta']->codigo_acceso) ?>
                    </div>
                    <div class="small text-muted mt-1">Comunica este código a los alumnos del grupo</div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Estadística rápida -->
            <div class="card border-0 shadow-sm" style="background:#0583c3; color:#fff;">
                <div class="card-body text-center">
                    <div style="font-size:3rem; font-weight:900;">
                        <?php echo $datos['encuesta']->total_respuestas ?>
                    </div>
                    <div>respuestas recibidas</div>
                    <?php if($media_global !== null): ?>
                    <hr style="border-color:rgba(255,255,255,0.4);">
                    <div style="font-size:2rem; font-weight:700;"><?php echo $media_global ?> / 10</div>
                    <div>media global (preguntas de valoración)</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Resultados por pregunta -->
    <?php if($datos['encuesta']->total_respuestas > 0): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header fw-bold" style="background:#0583c3; color:#fff;">
            <i class="fas fa-star me-1"></i>Resultados por pregunta
        </div>
        <div class="card-body">
            <?php foreach($datos['resumen'] as $res):
                $tipo = $res->tipo_respuesta ?? 'puntuacion';
            ?>
            <div class="mb-4 pb-3 border-bottom">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="badge bg-secondary me-1"><?php echo $res->orden ?></span>
                        <strong><?php echo htmlspecialchars($res->pregunta) ?></strong>
                    </div>
                    <small class="text-muted ms-3 flex-shrink-0"><?php echo $res->total_respuestas ?> resp.</small>
                </div>

                <?php if($tipo === 'puntuacion'): ?>
                <!-- ── Puntuación 1-10 ── -->
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="flex-grow-1 me-3">
                        <div class="progress" style="height:22px;">
                            <div class="progress-bar" role="progressbar"
                                 style="width:<?php echo ($res->media / 10) * 100 ?>%;
                                        background:<?php echo ($res->media >= 8) ? '#27ae60' : (($res->media >= 6) ? '#e67e22' : '#e74c3c') ?>;">
                            </div>
                        </div>
                    </div>
                    <span class="badge fs-6" style="background:<?php echo ($res->media >= 8) ? '#27ae60' : (($res->media >= 6) ? '#e67e22' : '#e74c3c') ?>">
                        <?php echo $res->media ?> / 10
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-end mt-2">
                    <small class="text-muted">Mín: <?php echo $res->minimo ?> · Máx: <?php echo $res->maximo ?></small>
                    <!-- Mini histograma -->
                    <div class="d-flex gap-1 align-items-end" style="height:30px;">
                        <?php
                        $max_cant = max(array_values($distrib_por_preg[$res->id_pregunta] ?? [1]) + [1]);
                        for($v = 1; $v <= 10; $v++):
                            $cant = $distrib_por_preg[$res->id_pregunta][$v] ?? 0;
                            $h = $max_cant > 0 ? round(($cant / $max_cant) * 28) : 0;
                        ?>
                        <div title="<?php echo $v ?>: <?php echo $cant ?> resp."
                             style="width:14px; height:<?php echo max($h, 2) ?>px;
                                    background:<?php echo ($v >= 8) ? '#27ae60' : (($v >= 6) ? '#e67e22' : '#e74c3c') ?>;
                                    border-radius:2px 2px 0 0; cursor:default;"></div>
                        <?php endfor; ?>
                    </div>
                </div>

                <?php elseif($tipo === 'opciones'): ?>
                <!-- ── Opciones múltiples ── -->
                <?php
                $opts      = !empty($res->opciones_json) ? (json_decode($res->opciones_json, true) ?: []) : [];
                $votos     = $distrib_opciones[$res->id_pregunta] ?? [];
                $total_vot = array_sum($votos);
                foreach($opts as $i => $etiqueta):
                    $n   = $votos[$i] ?? 0;
                    $pct = $total_vot > 0 ? round(($n / $total_vot) * 100) : 0;
                ?>
                <div class="mb-2">
                    <div class="d-flex justify-content-between mb-1">
                        <span>
                            <span class="badge bg-primary me-1"><?php echo chr(97+$i) ?></span>
                            <?php echo htmlspecialchars($etiqueta) ?>
                        </span>
                        <span class="text-muted small"><?php echo $n ?> (<?php echo $pct ?>%)</span>
                    </div>
                    <div class="progress" style="height:16px;">
                        <div class="progress-bar bg-primary" role="progressbar"
                             style="width:<?php echo $pct ?>%">
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php elseif($tipo === 'numerica'): ?>
                <!-- ── Numérica abierta ── -->
                <div class="d-flex gap-4 mt-1">
                    <div class="text-center px-3 py-2 rounded" style="background:#f0f7ff;">
                        <div class="fw-bold fs-5"><?php echo $res->media_num ?? '—' ?></div>
                        <small class="text-muted">media</small>
                    </div>
                    <div class="text-center px-3 py-2 rounded" style="background:#f0f7ff;">
                        <div class="fw-bold fs-5"><?php echo $res->minimo_num ?? '—' ?></div>
                        <small class="text-muted">mínimo</small>
                    </div>
                    <div class="text-center px-3 py-2 rounded" style="background:#f0f7ff;">
                        <div class="fw-bold fs-5"><?php echo $res->maximo_num ?? '—' ?></div>
                        <small class="text-muted">máximo</small>
                    </div>
                </div>
                <?php endif; ?>

            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Comentarios -->
    <?php if(!empty($datos['comentarios'])): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header fw-bold" style="background:#0583c3; color:#fff;">
            <i class="fas fa-comments me-1"></i>Comentarios recibidos
        </div>
        <div class="card-body">
            <?php foreach($datos['comentarios'] as $com): ?>
            <div class="mb-3 pb-3 border-bottom">
                <div class="text-muted small mb-2"><?php echo formatoFecha($com->fecha_respuesta) ?></div>
                <?php if($com->comentario_mejor): ?>
                <div class="mb-1">
                    <span class="badge bg-success me-1"><i class="fas fa-thumbs-up"></i> Lo mejor</span>
                    <?php echo htmlspecialchars($com->comentario_mejor) ?>
                </div>
                <?php endif; ?>
                <?php if($com->comentario_peor): ?>
                <div class="mb-1">
                    <span class="badge bg-danger me-1"><i class="fas fa-thumbs-down"></i> Lo peor</span>
                    <?php echo htmlspecialchars($com->comentario_peor) ?>
                </div>
                <?php endif; ?>
                <?php if($com->comentario_libre): ?>
                <div class="mb-1">
                    <span class="badge bg-secondary me-1"><i class="fas fa-comment"></i> Observaciones</span>
                    <?php echo htmlspecialchars($com->comentario_libre) ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-1"></i>
        Aún no se han recibido respuestas para esta encuesta.
        <?php if($datos['encuesta']->activa): ?>
        Comparte el enlace público con los encuestados para empezar a recopilar datos.
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Preguntas de la encuesta -->
    <div class="card border-0 shadow-sm">
        <div class="card-header fw-bold">
            <i class="fas fa-question-circle me-1"></i>Preguntas incluidas en esta encuesta
        </div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead style="background:#f4f9fd;">
                    <tr><th style="width:50px;">#</th><th>Pregunta</th></tr>
                </thead>
                <tbody>
                <?php foreach($datos['preguntas'] as $p): ?>
                <tr>
                    <td class="text-center text-muted"><?php echo $p->orden ?></td>
                    <td><?php echo htmlspecialchars($p->pregunta) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
function copiarEnlace(){
    const input = document.getElementById('enlace_publico');
    input.select();
    document.execCommand('copy');
    alert('Enlace copiado al portapapeles');
}

function cerrarEncuesta(id){
    if(!confirm('¿Cerrar la encuesta? Los encuestados no podrán seguir respondiendo.')) return;
    fetch('<?php echo RUTA_URL ?>/encuestas/cerrar', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id_encuesta='+id
    }).then(r=>r.json()).then(ok=>{ if(ok) location.reload(); else alert('Error'); });
}

function abrirEncuesta(id){
    if(!confirm('¿Volver a abrir la encuesta?')) return;
    fetch('<?php echo RUTA_URL ?>/encuestas/abrir', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id_encuesta='+id
    }).then(r=>r.json()).then(ok=>{ if(ok) location.reload(); else alert('Error'); });
}

function eliminarEncuesta(id){
    if(!confirm('¿Eliminar la encuesta y TODOS sus datos? Esta acción es irreversible.')) return;
    fetch('<?php echo RUTA_URL ?>/encuestas/eliminar', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id_encuesta='+id
    }).then(r=>r.json()).then(ok=>{
        if(ok) window.location='<?php echo RUTA_URL ?>/encuestas';
        else alert('Error al eliminar');
    });
}

function eliminarForzado(id, titulo){
    const confirmMsg =
        '⚠️ BORRADO TOTAL — ACCIÓN IRREVERSIBLE\n\n' +
        'Vas a eliminar la encuesta:\n"' + titulo + '"\n\n' +
        'Se borrarán TODAS las respuestas y datos asociados.\n' +
        'Esta acción no se puede deshacer.\n\n' +
        '¿Confirmas el borrado total?';
    if(!confirm(confirmMsg)) return;
    // Segunda confirmación
    if(!confirm('Segunda confirmación requerida.\n¿Borrar definitivamente "' + titulo + '" con todos sus datos?')) return;

    fetch('<?php echo RUTA_URL ?>/encuestas/eliminar_forzado', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id_encuesta='+id
    }).then(r=>r.json()).then(data=>{
        if(data.ok) window.location='<?php echo RUTA_URL ?>/encuestas';
        else alert('Error: ' + data.msg);
    });
}
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
