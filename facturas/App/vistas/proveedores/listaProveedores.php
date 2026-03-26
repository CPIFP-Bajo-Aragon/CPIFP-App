<?php require_once RUTA_APP . '/vistas/inc/header.php'; ?>

<style>
/* ===== TABLA ===== */
#tblProv { font-size: 1rem; border-collapse: separate; border-spacing: 0; }
#tblProv thead th {
    background-color: #0583c3; color: #fff;
    font-weight: 600; padding: 12px 16px; vertical-align: middle; border: none;
}
#tblProv tbody td {
    padding: 11px 16px; vertical-align: middle;
    border-bottom: 1px solid #dee2e6; font-size: 0.97rem;
}
#tblProv tbody tr:nth-child(odd)  { background-color: #f4f9fd; }
#tblProv tbody tr:nth-child(even) { background-color: #ffffff; }
#tblProv tbody tr:hover           { background-color: #dbeef9; }
.cif-cell { color: #6c757d; font-size: .85rem; font-family: monospace; }
.link-prov { color: #0583c3; font-weight: 600; text-decoration: none; font-size: 1rem; }
.link-prov:hover { color: #ff5722; text-decoration: underline; }

/* ===== BADGES MEDIA ===== */
.mbadge {
    display:inline-block; padding:4px 12px; border-radius:20px;
    font-size:.88rem; font-weight:700; min-width:56px; text-align:center;
}
.mb-alta  { background:#d4edda; color:#155724; }
.mb-media { background:#fff3cd; color:#856404; }
.mb-baja  { background:#f8d7da; color:#721c24; }
.mb-sin   { background:#e9ecef; color:#6c757d; }

/* ===== BOLAS ITEM (modal) ===== */
.iball {
    width:54px; height:54px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:1.15rem; font-weight:800; margin:0 auto 4px;
}
.ib-alta  { background:#d4edda; color:#155724; }
.ib-media { background:#fff3cd; color:#856404; }
.ib-baja  { background:#f8d7da; color:#721c24; }
.ib-sin   { background:#e9ecef; color:#6c757d; }
</style>


<div class="p-4 shadow border mt-4 mx-3 tarjeta">
<div class="container-fluid">

    <div class="row mb-3">
        <div class="col-12">
            <strong id="ciclo_encabezado">
                <i class="fas fa-store me-2"></i>Proveedores
            </strong>
        </div>
    </div>

    <!-- Filtro -->
    <form action="<?php echo RUTA_URL ?>/Proveedores/listaProveedores" method="POST" class="mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <div class="input-group">
                    <label class="input-group-text">CIF</label>
                    <input type="text" name="CIF" class="form-control" placeholder="Buscar por CIF..."
                           value="<?php echo htmlspecialchars($datos['busquedaCIF'] ?? '') ?>">
                </div>
            </div>
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <label class="input-group-text">Nombre</label>
                    <input type="text" name="Nombre" class="form-control" placeholder="Buscar por nombre..."
                           value="<?php echo htmlspecialchars($datos['busquedaNombre'] ?? '') ?>">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <button type="submit" class="btn btn-custom w-100">
                    <i class="fas fa-search me-1"></i>Buscar
                </button>
            </div>
            <?php if (!empty($datos['busquedaCIF']) || !empty($datos['busquedaNombre'])): ?>
            <div class="col-6 col-md-2">
                <a href="<?php echo RUTA_URL ?>/Proveedores/listaProveedores" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-times me-1"></i>Limpiar
                </a>
            </div>
            <?php endif ?>
        </div>
    </form>

    <!-- Leyenda -->
    <div class="d-flex gap-3 mb-3 flex-wrap align-items-center" style="font-size:.83rem">
        <span class="text-muted fw-semibold">Valoración:</span>
        <span class="mbadge mb-alta">≥ 7,00</span>
        <span class="mbadge mb-media">≥ 4,00</span>
        <span class="mbadge mb-baja">&lt; 4,00</span>
        <span class="mbadge mb-sin">Sin datos</span>
    </div>

    <?php dibujarBotonesPaginacion($datos['totalPaginas'], $datos['paginaAcual']) ?>

    <div class="table-responsive mt-3">
    <table class="table table-bordered" id="tblProv">
        <thead>
            <tr>
                <th style="width:12%">CIF</th>
                <th>Proveedor <small class="fw-normal opacity-75" style="font-size:.78rem">(pulsa el nombre para ver todos los datos)</small></th>
                <th class="text-center" style="width:13%">Media año</th>
                <th class="text-center" style="width:13%">Media total</th>
                <?php if ($datos['usuarioSesion']->id_rol == 500): ?>
                <th class="text-center" style="width:9%">Opciones</th>
                <?php endif ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($datos['proveedores'] as $prov):
            $media    = ($prov->MItem1!==null&&$prov->MItem2!==null&&$prov->MItem3!==null&&$prov->MItem4!==null)
                        ? ($prov->MItem1+$prov->MItem2+$prov->MItem3+$prov->MItem4)/4 : null;
            $mediaAnio= ($prov->MItem1_UltimoAnio!==null&&$prov->MItem2_UltimoAnio!==null&&$prov->MItem3_UltimoAnio!==null&&$prov->MItem4_UltimoAnio!==null)
                        ? ($prov->MItem1_UltimoAnio+$prov->MItem2_UltimoAnio+$prov->MItem3_UltimoAnio+$prov->MItem4_UltimoAnio)/4 : null;
            $cl = function($m){ if($m===null)return'mb-sin'; if($m>=7)return'mb-alta'; if($m>=4)return'mb-media'; return'mb-baja'; };
        ?>
        <tr>
            <td class="cif-cell"><?php echo htmlspecialchars($prov->CIF) ?></td>
            <td>
                <a href="#" class="link-prov"
                   data-cif="<?php echo htmlspecialchars($prov->CIF) ?>"
                   data-bs-toggle="modal" data-bs-target="#mVer">
                    <?php echo htmlspecialchars($prov->Nombre) ?>
                </a>
            </td>
            <td class="text-center">
                <span class="mbadge <?php echo $cl($mediaAnio) ?>">
                    <?php echo $mediaAnio!==null ? number_format($mediaAnio,2) : '—' ?>
                </span>
            </td>
            <td class="text-center">
                <span class="mbadge <?php echo $cl($media) ?>">
                    <?php echo $media!==null ? number_format($media,2) : '—' ?>
                </span>
            </td>
            <?php if ($datos['usuarioSesion']->id_rol == 500): ?>
            <td class="text-center text-nowrap">
                <a href="#" data-cif="<?php echo htmlspecialchars($prov->CIF) ?>"
                   data-bs-toggle="modal" data-bs-target="#mEditar" title="Editar">
                    <img class="icono" src="<?php echo RUTA_Icon ?>editar.png" alt="Editar">
                </a>
                <a href="#" data-cif="<?php echo htmlspecialchars($prov->CIF) ?>"
                   data-nombre="<?php echo htmlspecialchars($prov->Nombre) ?>"
                   data-bs-toggle="modal" data-bs-target="#mBorrar" title="Borrar">
                    <img class="icono" src="<?php echo RUTA_Icon ?>papelera.png" alt="Borrar">
                </a>
            </td>
            <?php endif ?>
        </tr>
        <?php endforeach ?>
        </tbody>
    </table>
    </div>

    <?php dibujarBotonesPaginacion($datos['totalPaginas'], $datos['paginaAcual']) ?>

</div>
</div>


<!-- ============================================================
  MODAL VER  (todos los campos + valoraciones, AJAX)
============================================================ -->
<div class="modal fade" id="mVer" tabindex="-1" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
<div class="modal-content rounded-3 shadow">
    <div class="modal-header">
        <p class="modal-title"><i class="fas fa-store me-2"></i><span id="vTitulo">Proveedor</span></p>
        <button type="button" class="btn-close me-3" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div id="vSpin" class="text-center py-5">
            <div class="spinner-border text-primary" style="width:3rem;height:3rem"></div>
            <p class="mt-3 text-muted">Cargando datos...</p>
        </div>
        <div id="vBody" class="d-none">
        <div class="row g-3 px-1">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <label class="input-group-text" style="min-width:92px">CIF</label>
                    <input id="vCIF"       type="text" class="form-control bg-light" readonly>
                </div>
            </div>
            <div class="col-12 col-md-7">
                <div class="input-group">
                    <label class="input-group-text" style="min-width:92px">Alias</label>
                    <input id="vAlias"     type="text" class="form-control bg-light" readonly>
                </div>
            </div>
            <div class="col-12">
                <div class="input-group">
                    <label class="input-group-text" style="min-width:92px">Nombre</label>
                    <input id="vNombre"    type="text" class="form-control bg-light" readonly>
                </div>
            </div>
            <div class="col-12">
                <div class="input-group">
                    <label class="input-group-text" style="min-width:92px">Dirección</label>
                    <input id="vDireccion" type="text" class="form-control bg-light" readonly>
                </div>
            </div>
            <div class="col-5 col-md-3">
                <div class="input-group">
                    <label class="input-group-text" style="min-width:92px">C.P.</label>
                    <input id="vCP"        type="text" class="form-control bg-light" readonly>
                </div>
            </div>
            <div class="col-7 col-md-9">
                <div class="input-group">
                    <label class="input-group-text" style="min-width:92px">Localidad</label>
                    <input id="vLocalidad" type="text" class="form-control bg-light" readonly>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <label class="input-group-text" style="min-width:92px">Provincia</label>
                    <input id="vProvincia" type="text" class="form-control bg-light" readonly>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <label class="input-group-text" style="min-width:92px">País</label>
                    <input id="vPais"      type="text" class="form-control bg-light" readonly>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <label class="input-group-text" style="min-width:92px">Teléfono</label>
                    <input id="vTelefono"  type="text" class="form-control bg-light" readonly>
                </div>
            </div>
            <!-- Valoraciones -->
            <div class="col-12 mt-1">
                <hr>
                <p class="fw-bold mb-3" style="color:#0583c3;font-size:.93rem">
                    <i class="fas fa-star me-2"></i>Valoración media histórica
                </p>
                <div class="row text-center g-2">
                    <div class="col-6 col-md-3">
                        <div class="small fw-bold text-primary mb-1">Item 1</div>
                        <div id="vI1" class="iball ib-sin">—</div>
                        <div class="text-muted" style="font-size:.72rem">Atención e información</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="small fw-bold text-primary mb-1">Item 2</div>
                        <div id="vI2" class="iball ib-sin">—</div>
                        <div class="text-muted" style="font-size:.72rem">Plazos de entrega</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="small fw-bold text-primary mb-1">Item 3</div>
                        <div id="vI3" class="iball ib-sin">—</div>
                        <div class="text-muted" style="font-size:.72rem">Transporte y entrega</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="small fw-bold text-primary mb-1">Item 4</div>
                        <div id="vI4" class="iball ib-sin">—</div>
                        <div class="text-muted" style="font-size:.72rem">Precio / Calidad</div>
                    </div>
                </div>
                <div id="vPen" class="text-center mt-3 d-none">
                    <span class="badge bg-danger fs-6">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Proveedor penalizado — Revisar con Calidad o Secretaría
                    </span>
                </div>
            </div>
        </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
    </div>
</div>
</div>
</div>


<?php if ($datos['usuarioSesion']->id_rol == 500): ?>

<!-- ============================================================
  MODAL EDITAR  (todos los campos, AJAX)
============================================================ -->
<div class="modal fade" id="mEditar" tabindex="-1" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
<div class="modal-content rounded-3 shadow">
    <div class="modal-header">
        <p class="modal-title"><i class="fas fa-edit me-2"></i>Editar proveedor</p>
        <button type="button" class="btn-close me-3" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div id="eSpin" class="text-center py-5">
            <div class="spinner-border text-primary" style="width:3rem;height:3rem"></div>
            <p class="mt-3 text-muted">Cargando datos...</p>
        </div>
        <div id="eBody" class="d-none">
        <form id="fmEdit" action="<?php echo RUTA_URL ?>/Proveedores/editarProveedor" method="POST">
            <input type="hidden" name="CIF" id="eCIF">
            <div class="row g-3 px-1">
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <label class="input-group-text" style="min-width:92px">CIF</label>
                        <input id="eCIFshow"   type="text" class="form-control bg-light" readonly>
                    </div>
                </div>
                <div class="col-12 col-md-7">
                    <div class="input-group">
                        <label class="input-group-text" style="min-width:92px">Alias</label>
                        <input id="eAlias"     name="Alias"     type="text" class="form-control">
                    </div>
                </div>
                <div class="col-12">
                    <div class="input-group">
                        <label class="input-group-text" style="min-width:92px">Nombre<sup class="text-danger">*</sup></label>
                        <input id="eNombre"    name="Nombre"    type="text" class="form-control" required>
                    </div>
                </div>
                <div class="col-12">
                    <div class="input-group">
                        <label class="input-group-text" style="min-width:92px">Dirección</label>
                        <input id="eDireccion" name="Direccion" type="text" class="form-control">
                    </div>
                </div>
                <div class="col-5 col-md-3">
                    <div class="input-group">
                        <label class="input-group-text" style="min-width:92px">C.P.</label>
                        <input id="eCP"        name="CP"        type="text" class="form-control" maxlength="5">
                    </div>
                </div>
                <div class="col-7 col-md-9">
                    <div class="input-group">
                        <label class="input-group-text" style="min-width:92px">Localidad</label>
                        <input id="eLocalidad" name="Localidad" type="text" class="form-control">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <label class="input-group-text" style="min-width:92px">Provincia</label>
                        <input id="eProvincia" name="Provincia" type="text" class="form-control">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <label class="input-group-text" style="min-width:92px">País</label>
                        <input id="ePais"      name="Pais"      type="text" class="form-control">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <label class="input-group-text" style="min-width:92px">Teléfono</label>
                        <input id="eTelefono"  name="Telefono"  type="text" class="form-control" maxlength="9">
                    </div>
                </div>
            </div>
        </form>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" form="fmEdit" class="btn" id="boton-modal">
            <i class="fas fa-save me-1"></i>Guardar cambios
        </button>
    </div>
</div>
</div>
</div>


<!-- ============================================================
  MODAL BORRAR
============================================================ -->
<div class="modal fade" id="mBorrar" tabindex="-1" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content rounded-3 shadow">
    <div class="modal-header">
        <p class="modal-title"><i class="fas fa-trash me-2"></i>Borrar proveedor</p>
        <button type="button" class="btn-close me-3" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body mt-1">
        <p>¿Estás seguro de que quieres borrar a <strong id="bNombre"></strong>?</p>
        <p class="text-danger small mb-0">
            <i class="fas fa-exclamation-triangle me-1"></i>
            Si el proveedor tiene facturas asociadas, la operación no se completará.
        </p>
    </div>
    <div class="modal-footer">
        <form action="<?php echo RUTA_URL ?>/Proveedores/borrarProveedor" method="POST">
            <input type="hidden" name="CIF" id="bCIF">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
            <input type="submit" class="btn btn-danger" value="Borrar proveedor">
        </form>
    </div>
</div>
</div>
</div>

<?php endif ?>


<script>
const _url = '<?php echo RUTA_URL ?>/Proveedores/verProveedor';

async function _ajax(cif) {
    const fd = new FormData();
    fd.append('CIF', cif);
    return await (await fetch(_url, { method:'POST', body:fd })).json();
}
const _t  = x => (x !== null && x !== undefined) ? String(x) : '';
const _d  = x => (x !== null && x !== undefined && String(x).trim() !== '') ? String(x) : '—';
const _cl = v => {
    if (v === null || v === undefined) return 'ib-sin';
    const n = parseFloat(v);
    return n >= 7 ? 'ib-alta' : n >= 4 ? 'ib-media' : 'ib-baja';
};

/* --- VER --- */
document.getElementById('mVer').addEventListener('show.bs.modal', async function(e) {
    const cif = e.relatedTarget.getAttribute('data-cif');
    document.getElementById('vSpin').classList.remove('d-none');
    document.getElementById('vBody').classList.add('d-none');

    const p = await _ajax(cif);

    document.getElementById('vTitulo').textContent   = _t(p.Nombre) || 'Proveedor';
    document.getElementById('vCIF').value            = _d(p.CIF);
    document.getElementById('vAlias').value          = _d(p.Alias);
    document.getElementById('vNombre').value         = _d(p.Nombre);
    document.getElementById('vDireccion').value      = _d(p.Direccion);
    document.getElementById('vCP').value             = _d(p.CP);
    document.getElementById('vLocalidad').value      = _d(p.Localidad);
    document.getElementById('vProvincia').value      = _d(p.Provincia);
    document.getElementById('vPais').value           = _d(p.Pais);
    document.getElementById('vTelefono').value       = _d(p.Telefono);

    [1,2,3,4].forEach(n => {
        const el = document.getElementById('vI'+n);
        const val = p['Item'+n];
        el.textContent = val !== null ? parseFloat(val).toFixed(2) : '—';
        el.className   = 'iball ' + _cl(val);
    });
    document.getElementById('vPen').classList.toggle('d-none', p.Penalizacion !== 'S');

    document.getElementById('vSpin').classList.add('d-none');
    document.getElementById('vBody').classList.remove('d-none');
});


<?php if ($datos['usuarioSesion']->id_rol == 500): ?>
/* --- EDITAR --- */
document.getElementById('mEditar').addEventListener('show.bs.modal', async function(e) {
    const cif = e.relatedTarget.getAttribute('data-cif');
    document.getElementById('eSpin').classList.remove('d-none');
    document.getElementById('eBody').classList.add('d-none');

    const p = await _ajax(cif);

    document.getElementById('eCIF').value       = _t(p.CIF);
    document.getElementById('eCIFshow').value   = _t(p.CIF);
    document.getElementById('eNombre').value    = _t(p.Nombre);
    document.getElementById('eAlias').value     = _t(p.Alias);
    document.getElementById('eDireccion').value = _t(p.Direccion);
    document.getElementById('eCP').value        = _t(p.CP);
    document.getElementById('eLocalidad').value = _t(p.Localidad);
    document.getElementById('eProvincia').value = _t(p.Provincia);
    document.getElementById('ePais').value      = _t(p.Pais);
    document.getElementById('eTelefono').value  = _t(p.Telefono);

    document.getElementById('eSpin').classList.add('d-none');
    document.getElementById('eBody').classList.remove('d-none');
});

/* --- BORRAR --- */
document.getElementById('mBorrar').addEventListener('show.bs.modal', function(e) {
    document.getElementById('bCIF').value          = e.relatedTarget.getAttribute('data-cif');
    document.getElementById('bNombre').textContent = e.relatedTarget.getAttribute('data-nombre');
});
<?php endif ?>
</script>


<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
