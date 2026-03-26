<?php
include RUTA_APP . '/vistas/inc/header.php';

$f      = $datos['filtro'];
$base   = RUTA_URL . '/Inventario/bajas';
$cgGet  = $datos['cadenaGet'];
$numPag = $datos['numPaginas'];
$pagAct = $datos['paginaActual'];
$esED   = $datos['esED'];
?>

<div class="container-fluid mt-4 pt-4">

    <div class="row mb-3 align-items-center">
        <div class="col">
            <h4 class="fw-bold" style="color:#2C3E50">
                <i class="bi bi-trash3 me-2" style="color:#e74c3c"></i>Dar de baja artículos
            </h4>
            <p class="text-muted mb-0">Registra artículos obsoletos, rotos o extraviados</p>
        </div>
        <div class="col-auto">
            <a href="<?php echo RUTA_URL ?>/Inventario/index" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    <?php if (!$esED): ?>
    <div class="alert alert-info py-2 mb-3">
        <i class="bi bi-info-circle me-2"></i>
        Solo puedes dar de baja artículos asignados a tu destino / departamento.
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
                <th class="d-none d-lg-table-cell">Ubicación</th>
                <th class="d-none d-md-table-cell">Fecha alta</th>
                <th class="text-center">Baja</th>
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
            <td class="d-none d-md-table-cell">
                <small><?php echo $r->Fecha_Alta ? date('d/m/Y', strtotime($r->Fecha_Alta)) : '—' ?></small>
            </td>
            <td class="text-center">
                <button class="btn btn-danger btn-sm"
                        onclick="abrirModalBaja(
                            <?php echo $r->id ?>,
                            '<?php echo addslashes(htmlspecialchars($r->NombreArt ?? '')) ?>'
                        )"
                        title="Dar de baja">
                    <i class="bi bi-trash3"></i>
                    <span class="d-none d-md-inline ms-1">Baja</span>
                </button>
            </td>
        </tr>
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
</div>

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
                <label class="form-label fw-semibold">
                    Motivo de la baja <span class="text-muted fw-normal">(opcional)</span>
                </label>
                <textarea id="modalBajaMotivo" class="form-control" rows="3"
                          placeholder="Rotura, extravío, obsolescencia, donación a terceros…"></textarea>
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
        if(d.ok) {
            toast('✓ Baja registrada correctamente','ok');
            setTimeout(()=>location.reload(),1200);
        } else {
            toast('✗ ' + d.msg,'err');
        }
    }).catch(()=>toast('Error de red','err'));
});
</script>

<?php include RUTA_APP . '/vistas/inc/footer.php' ?>
