<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container-fluid px-4 py-4">

    <div class="row mb-4 align-items-center">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <i class="fas fa-poll me-2"></i>Encuestas de Satisfacción
            </span>
        </div>
        <?php if($datos['usuarioSesion']->id_rol >= 200): ?>
        <div class="col-auto">
            <a class="btn btn-custom" href="<?php echo RUTA_URL ?>/encuestas/nueva">
                <i class="fas fa-plus me-1"></i>Nueva encuesta
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Tarjetas de acceso rápido -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <a href="<?php echo RUTA_URL ?>/encuestas" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 p-3 d-flex flex-row align-items-center gap-3">
                    <div style="font-size:2.5rem; color:#0583c3;"><i class="fas fa-user-graduate"></i></div>
                    <div>
                        <div class="fw-bold fs-5">Enc. de alumnos</div>
                        <div class="text-muted small">Ver y gestionar encuestas de alumnos</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-md-4">
            <a href="<?php echo RUTA_CPIFP ?>/otras_encuestas" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 p-3 d-flex flex-row align-items-center gap-3">
                    <div style="font-size:2.5rem; color:#8e44ad;"><i class="fas fa-poll"></i></div>
                    <div>
                        <div class="fw-bold fs-5">Otras encuestas</div>
                        <div class="text-muted small">Encuestas de empresas y otros tipos</div>
                    </div>
                </div>
            </a>
        </div>
        <?php if($datos['usuarioSesion']->id_rol >= 200): ?>
        <div class="col-12 col-md-4">
            <a href="<?php echo RUTA_URL ?>/encuestas/estadisticas" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 p-3 d-flex flex-row align-items-center gap-3">
                    <div style="font-size:2.5rem; color:#e67e22;"><i class="fas fa-chart-bar"></i></div>
                    <div>
                        <div class="fw-bold fs-5">Estadísticas</div>
                        <div class="text-muted small">Informes y gráficos por curso</div>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Últimas encuestas de alumnos -->
    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center"
             style="background:#0583c3; color:#fff; font-weight:bold;">
            <span><i class="fas fa-clock me-2"></i>Últimas encuestas de alumnos</span>
            <span class="badge bg-white text-primary" style="font-weight:600;">
                <?php echo $datos['encuestas']->total ?? 0 ?> en total
            </span>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0" style="font-size:.88rem;">
                <thead style="background:#e9f4fb;">
                    <tr>
                        <th>Profesor</th>
                        <th>Módulo</th>
                        <th>Grupo · Ciclo</th>
                        <th>Dpto.</th>
                        <th>Evaluación</th>
                        <th>Curso</th>
                        <th>Estado</th>
                        <th class="text-center">Resp.</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($datos['encuestas']->registros)): ?>
                    <tr><td colspan="9" class="text-center text-muted py-3">No hay encuestas aún.</td></tr>
                <?php else: ?>
                    <?php foreach($datos['encuestas']->registros as $enc): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($enc->nombre_profesor ?? '—') ?></td>
                        <td><?php echo htmlspecialchars($enc->nombre_modulo ?? '—') ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($enc->nombre_curso ?? '—') ?></strong>
                            <?php if(!empty($enc->nombre_ciclo)): ?>
                            <br><small class="text-muted"><?php echo htmlspecialchars($enc->nombre_ciclo) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?php echo htmlspecialchars($enc->departamento_corto ?? $enc->nombre_departamento ?? '—') ?>
                            </small>
                        </td>
                        <td>
                            <?php if(!empty($enc->nombre_evaluacion)): ?>
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($enc->nombre_evaluacion) ?></span>
                            <?php else: ?>—<?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($enc->curso_academico) ?></td>
                        <td>
                            <?php if($enc->activa): ?>
                                <span class="badge bg-success">Abierta</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Cerrada</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?php echo (int)($enc->total_respuestas??0) > 0 ? '' : 'bg-secondary' ?>"
                                  style="<?php echo (int)($enc->total_respuestas??0) > 0 ? 'background:#0583c3;' : '' ?>">
                                <?php echo (int)($enc->total_respuestas ?? 0) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo RUTA_URL ?>/encuestas/ver/<?php echo $enc->id_encuesta ?>"
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer text-end">
            <a href="<?php echo RUTA_URL ?>/encuestas" class="btn btn-sm btn-outline-secondary">
                Ver todas <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

</div>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
