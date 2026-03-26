<?php
include RUTA_APP . '/vistas/inc/header.php';

$f      = $datos['filtro'];
$base   = RUTA_URL . '/Inventario/modificar';
$cgGet  = $datos['cadenaGet'];
$numPag = $datos['numPaginas'];
$pagAct = $datos['paginaActual'];
$esED   = $datos['esED'];
?>

<div class="container-fluid mt-4 pt-4">

    <div class="row mb-3 align-items-center">
        <div class="col">
            <h4 class="fw-bold" style="color:#2C3E50">
                <i class="bi bi-pencil-square me-2" style="color:#f39c12"></i>Modificar Inventario
            </h4>
            <p class="text-muted mb-0">Selecciona el artículo que deseas editar</p>
        </div>
        <div class="col-auto">
            <a href="<?php echo RUTA_URL ?>/Inventario/index" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    <?php if (isset($_GET['ok'])): ?>
    <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
        <i class="bi bi-check-circle me-2"></i>Modificación guardada correctamente.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif ?>

    <?php if (!$esED): ?>
    <div class="alert alert-info py-2 mb-3">
        <i class="bi bi-info-circle me-2"></i>
        Solo puedes modificar artículos asignados a tu destino / departamento.
    </div>
    <?php endif ?>

    <!-- Filtros -->
    <form method="get" action="<?php echo $base ?>" class="mb-3">
        <div class="row g-2 align-items-end">
            <?php if ($esED): ?>
            <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label form-label-sm mb-1 fw-semibold">Destino</label>
                <select name="dep" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <?php foreach ($datos['destinos'] as $d): ?>
                    <option value="<?php echo $d->Destino_Id ?>"
                        <?php echo $f['dep'] == $d->Destino_Id ? 'selected' : '' ?>>
                        <?php echo htmlspecialchars($d->Depart_Servicio) ?>
                    </option>
                    <?php endforeach ?>
                </select>
            </div>
            <?php endif ?>
            <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label form-label-sm mb-1 fw-semibold">Buscar</label>
                <input type="text" name="buscar" class="form-control form-control-sm"
                       placeholder="Artículo, descripción…"
                       value="<?php echo htmlspecialchars($f['buscar'] ?? '') ?>">
            </div>
            <div class="col-auto d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
                <a href="<?php echo $base ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-eraser"></i></a>
            </div>
        </div>
    </form>

    <?php if (empty($datos['registros'])): ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-2"></i>No hay artículos activos con los filtros seleccionados.
    </div>
    <?php else: ?>

    <div class="table-responsive">
    <table class="table table-bordered table-hover table-sm align-middle" style="font-size:.88rem">
        <thead class="table-dark">
            <tr>
                <th class="d-none d-md-table-cell">NE</th>
                <th>Artículo</th>
                <th class="d-none d-md-table-cell">Categoría</th>
                <th class="text-center d-none d-lg-table-cell">Ud.</th>
                <th class="d-none d-md-table-cell">Destino</th>
                <th class="d-none d-lg-table-cell">Fecha alta</th>
                <th class="text-center">Editar</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($datos['registros'] as $r): ?>
        <tr>
            <td class="d-none d-md-table-cell text-muted">
                <small>NE-<?php echo str_pad($r->NEntrada, 4, '0', STR_PAD_LEFT) ?></small>
            </td>
            <td>
                <strong><?php echo htmlspecialchars($r->NombreArt ?? '') ?></strong>
                <?php if ($r->Descripcion): ?>
                <br><small class="text-muted"><?php echo htmlspecialchars($r->Descripcion) ?></small>
                <?php endif ?>
            </td>
            <td class="d-none d-md-table-cell">
                <small><?php echo htmlspecialchars($r->NombreCat ?? '') ?></small>
            </td>
            <td class="text-center d-none d-lg-table-cell">
                <strong><?php echo $r->Unidades ?></strong>
                <small class="text-muted"><?php echo $r->Individual === 'I' ? '(ind.)' : '(blq.)' ?></small>
            </td>
            <td class="d-none d-md-table-cell">
                <small><?php echo htmlspecialchars($r->NombreDep ?? '—') ?></small>
            </td>
            <td class="d-none d-lg-table-cell">
                <small><?php echo $r->Fecha_Alta ? date('d/m/Y', strtotime($r->Fecha_Alta)) : '—' ?></small>
            </td>
            <td class="text-center">
                <a href="<?php echo RUTA_URL ?>/Inventario/editarDetalle/<?php echo $r->id ?>"
                   class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil me-1"></i>Editar
                </a>
            </td>
        </tr>
        <?php endforeach ?>
        </tbody>
    </table>
    </div>

    <?php if ($numPag > 1): ?>
    <nav class="mt-3">
        <ul class="pagination pagination-sm justify-content-center flex-wrap">
            <li class="page-item <?php echo $pagAct <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?php echo $base ?>?<?php echo $cgGet ?>&pagina=<?php echo $pagAct-1 ?>">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
            <?php for ($p = max(1,$pagAct-2); $p <= min($numPag,$pagAct+2); $p++): ?>
            <li class="page-item <?php echo $p === $pagAct ? 'active' : '' ?>">
                <a class="page-link" href="<?php echo $base ?>?<?php echo $cgGet ?>&pagina=<?php echo $p ?>">
                    <?php echo $p ?>
                </a>
            </li>
            <?php endfor ?>
            <li class="page-item <?php echo $pagAct >= $numPag ? 'disabled' : '' ?>">
                <a class="page-link" href="<?php echo $base ?>?<?php echo $cgGet ?>&pagina=<?php echo $pagAct+1 ?>">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
    <?php endif ?>

    <?php endif ?>
</div>

<?php include RUTA_APP . '/vistas/inc/footer.php' ?>
