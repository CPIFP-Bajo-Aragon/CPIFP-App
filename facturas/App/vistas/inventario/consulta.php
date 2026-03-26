<?php
include RUTA_APP . '/vistas/inc/header.php';

$f      = $datos['filtro'];
$base   = RUTA_URL . '/Inventario/consulta';
$cgGet  = $datos['cadenaGet'];
$numPag = $datos['numPaginas'];
$pagAct = $datos['paginaActual'];
$esED   = $datos['esED'];
?>

<div class="container-fluid mt-4 pt-4">

    <div class="row mb-3 align-items-center">
        <div class="col">
            <h4 class="fw-bold" style="color:#2C3E50">
                <i class="bi bi-search me-2" style="color:#0583c3"></i>Consultar Inventario
            </h4>
            <p class="text-muted mb-0">
                <?php echo $datos['total'] ?> registro<?php echo $datos['total'] != 1 ? 's' : '' ?> encontrado<?php echo $datos['total'] != 1 ? 's' : '' ?>
            </p>
        </div>
        <div class="col-auto">
            <a href="<?php echo RUTA_URL ?>/Inventario/index" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    <!-- FILTROS -->
    <form method="get" action="<?php echo $base ?>" class="mb-3">
        <div class="row g-2 align-items-end">

            <?php if ($esED): ?>
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label form-label-sm mb-1 fw-semibold">Destino / Departamento</label>
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

            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label form-label-sm mb-1 fw-semibold">Categoría</label>
                <select name="cat" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <?php foreach ($datos['categorias'] as $c): ?>
                    <option value="<?php echo $c->CodCat ?>"
                        <?php echo $f['cat'] == $c->CodCat ? 'selected' : '' ?>>
                        <?php echo htmlspecialchars($c->denominacion) ?>
                    </option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label form-label-sm mb-1 fw-semibold">Buscar</label>
                <input type="text" name="buscar" class="form-control form-control-sm"
                       placeholder="Artículo, descripción, factura…"
                       value="<?php echo htmlspecialchars($f['buscar']) ?>">
            </div>

            <div class="col-12 col-sm-4 col-md-2">
                <label class="form-label form-label-sm mb-1 fw-semibold">Estado</label>
                <select name="baja" class="form-select form-select-sm">
                    <option value=""    <?php echo $f['baja'] === ''     ? 'selected' : '' ?>>Activos</option>
                    <option value="1"   <?php echo $f['baja'] === '1'    ? 'selected' : '' ?>>Bajas</option>
                    <option value="todos" <?php echo $f['baja'] === 'todos' ? 'selected' : '' ?>>Todos</option>
                </select>
            </div>

            <div class="col-auto d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search"></i>
                </button>
                <a href="<?php echo $base ?>" class="btn btn-outline-secondary btn-sm" title="Limpiar filtros">
                    <i class="bi bi-eraser"></i>
                </a>
            </div>
        </div>
    </form>

    <?php if (isset($_GET['ok'])): ?>
    <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
        <i class="bi bi-check-circle me-2"></i>Operación realizada correctamente.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif ?>

    <!-- TABLA -->
    <?php if (empty($datos['registros'])): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay registros con los filtros seleccionados.
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
                <th class="d-none d-lg-table-cell">Ubicación</th>
                <th class="d-none d-lg-table-cell">Origen</th>
                <th class="d-none d-md-table-cell">Fecha alta</th>
                <th class="text-center">Estado</th>
                <?php if ($datos['puedeGest']): ?>
                <th class="text-center">Acciones</th>
                <?php endif ?>
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
                    <small><?php echo $r->NomLocal ? htmlspecialchars($r->NomLocal) : '—' ?></small>
                </td>
                <td class="d-none d-lg-table-cell">
                    <?php if ($r->NFactura): ?>
                        <small><i class="bi bi-receipt text-muted me-1"></i>
                            <?php echo htmlspecialchars($r->NFactura) ?>
                        </small><br>
                        <small class="text-muted"><?php echo htmlspecialchars($r->NombreOrigen ?? '') ?></small>
                    <?php elseif ($r->Procedencia): ?>
                        <small><i class="bi bi-heart text-muted me-1"></i>
                            <?php echo htmlspecialchars($r->Procedencia) ?>
                        </small>
                    <?php else: ?><small class="text-muted">—</small><?php endif ?>
                </td>
                <td class="d-none d-md-table-cell">
                    <small><?php echo $r->Fecha_Alta ? date('d/m/Y', strtotime($r->Fecha_Alta)) : '—' ?></small>
                </td>
                <td class="text-center">
                    <?php if ($r->Baja): ?>
                        <span class="badge bg-danger">Baja</span>
                    <?php else: ?>
                        <span class="badge bg-success">Activo</span>
                    <?php endif ?>
                </td>
                <?php if ($datos['puedeGest']): ?>
                <td class="text-center">
                    <?php if (!$r->Baja): ?>
                    <a href="<?php echo RUTA_URL ?>/Inventario/editarDetalle/<?php echo $r->id ?>"
                       class="btn btn-warning btn-sm" title="Modificar">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <button class="btn btn-danger btn-sm ms-1" title="Dar de baja"
                            onclick="abrirModalBaja(<?php echo $r->id ?>, '<?php echo addslashes(htmlspecialchars($r->NombreArt ?? '')) ?>')">
                        <i class="bi bi-trash3"></i>
                    </button>
                    <?php elseif ($esED): ?>
                    <button class="btn btn-success btn-sm" title="Reactivar"
                            onclick="reactivar(<?php echo $r->id ?>)">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                    <?php endif ?>
                </td>
                <?php endif ?>
            </tr>
            <?php if ($r->Baja && $r->Motivo_Baja): ?>
            <tr class="table-danger">
                <td class="d-none d-md-table-cell"></td>
                <td colspan="<?php echo $datos['puedeGest'] ? 9 : 8 ?>"
                    style="font-size:.80rem; padding-top:2px; padding-bottom:4px;">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    <em>Motivo baja: <?php echo htmlspecialchars($r->Motivo_Baja) ?></em>
                </td>
            </tr>
            <?php endif ?>
        <?php endforeach ?>
        </tbody>
    </table>
    </div>

    <!-- Paginación -->
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

</div><!-- /container-fluid -->

<!-- Modal Baja -->
<div class="modal fade" id="modalBaja" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Dar de baja</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="modalBajaTxt" class="mb-3"></p>
                <label class="form-label fw-semibold">Motivo <span class="text-muted fw-normal">(opcional)</span></label>
                <textarea id="modalBajaMotivo" class="form-control" rows="2"
                          placeholder="Rotura, extravío, obsolescencia…"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarBaja">
                    <i class="bi bi-check me-1"></i>Confirmar baja
                </button>
            </div>
        </div>
    </div>
</div>

<div id="toastContainer" style="position:fixed;bottom:20px;right:20px;z-index:9999"></div>

<script>
const RUTA_URL = '<?php echo RUTA_URL ?>';
let _bajaId = null;

function toast(msg, tipo) {
    const d = document.createElement('div');
    d.className = 'toast align-items-center border-0 mb-2 text-white bg-' + (tipo === 'ok' ? 'success' : 'danger');
    d.setAttribute('role','alert');
    d.innerHTML = `<div class="d-flex"><div class="toast-body fw-semibold">${msg}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
    document.getElementById('toastContainer').appendChild(d);
    new bootstrap.Toast(d, {delay:3000}).show();
    d.addEventListener('hidden.bs.toast', () => d.remove());
}

function abrirModalBaja(id, nombre) {
    _bajaId = id;
    document.getElementById('modalBajaTxt').textContent = '¿Confirmas la baja de: ' + nombre + '?';
    document.getElementById('modalBajaMotivo').value = '';
    new bootstrap.Modal(document.getElementById('modalBaja')).show();
}

document.getElementById('btnConfirmarBaja').addEventListener('click', () => {
    if (!_bajaId) return;
    const motivo = document.getElementById('modalBajaMotivo').value;
    bootstrap.Modal.getInstance(document.getElementById('modalBaja')).hide();
    fetch(RUTA_URL + '/Inventario/ajaxBaja', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id=' + _bajaId + '&motivo=' + encodeURIComponent(motivo)
    }).then(r=>r.json()).then(d => {
        if(d.ok) { toast('✓ ' + d.msg, 'ok'); setTimeout(()=>location.reload(),1200); }
        else toast('✗ ' + d.msg, 'err');
    }).catch(()=>toast('Error de red','err'));
});

function reactivar(id) {
    if (!confirm('¿Reactivar este artículo en el inventario?')) return;
    fetch(RUTA_URL + '/Inventario/ajaxReactivar', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id=' + id
    }).then(r=>r.json()).then(d => {
        if(d.ok) { toast('✓ Artículo reactivado','ok'); setTimeout(()=>location.reload(),1200); }
        else toast('✗ Error al reactivar','err');
    });
}
</script>

<?php include RUTA_APP . '/vistas/inc/footer.php' ?>
