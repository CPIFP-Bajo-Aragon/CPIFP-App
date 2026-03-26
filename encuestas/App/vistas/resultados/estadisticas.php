<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container-fluid px-4 py-4">

    <div class="row mb-3 align-items-center">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <i class="fas fa-chart-bar me-2"></i>Estadísticas generales
            </span>
        </div>
    </div>

    <!-- Selector de curso académico -->
    <form method="get" action="<?php echo RUTA_URL ?>/encuestas/estadisticas" class="mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label fw-bold mb-0">Curso académico</label>
                <select name="curso_academico" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Todos --</option>
                    <?php foreach($datos['cursos'] as $c): ?>
                    <option value="<?php echo $c->curso_academico ?>"
                        <?php echo ($datos['curso_sel'] == $c->curso_academico) ? 'selected' : '' ?>>
                        <?php echo htmlspecialchars($c->curso_academico) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>

    <?php if(!empty($datos['encuestas_curso']->registros)): ?>

    <!-- Tabla resumen por encuesta -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header fw-bold" style="background:#0583c3; color:#fff;">
            <i class="fas fa-table me-1"></i>
            Resumen de encuestas – Curso <?php echo htmlspecialchars($datos['curso_sel']) ?>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead style="background:#e9f4fb;">
                    <tr>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Profesor / Empresa</th>
                        <th>Módulo</th>
                        <th>Trimestre</th>
                        <th>Respuestas</th>
                        <th>Ver</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($datos['encuestas_curso']->registros as $enc): ?>
                <tr>
                    <td><?php echo htmlspecialchars($enc->titulo) ?></td>
                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($enc->tipo_encuesta ?? '-') ?></span></td>
                    <td><?php echo htmlspecialchars($enc->nombre_profesor ?? $enc->nombre_empresa ?? '-') ?></td>
                    <td><?php echo htmlspecialchars($enc->nombre_modulo ?? '-') ?></td>
                    <td><?php echo $enc->trimestre ? etiquetaTrimestre($enc->trimestre) : '-' ?></td>
                    <td class="text-center">
                        <span class="badge" style="background:#0583c3;">
                            <?php echo $enc->total_respuestas ?? 0 ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?php echo RUTA_URL ?>/encuestas/ver/<?php echo $enc->id_encuesta ?>"
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-chart-bar"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php else: ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-1"></i>
        Selecciona un curso académico para ver las estadísticas, o aún no hay encuestas registradas.
    </div>
    <?php endif; ?>

</div>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
