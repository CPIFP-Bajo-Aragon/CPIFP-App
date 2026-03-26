<?php include RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container mt-4 pt-4">

    <div class="row mb-3 align-items-center">
        <div class="col">
            <h4 class="fw-bold" style="color:#2C3E50">
                <i class="bi bi-plus-circle me-2" style="color:#27ae60"></i>Nueva Alta de Inventario
            </h4>
            <p class="text-muted mb-0">Registra un bien procedente de factura o donación</p>
        </div>
        <div class="col-auto">
            <a href="<?php echo RUTA_URL ?>/Inventario/index"
               class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    <?php if (!empty($datos['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($datos['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif ?>

    <form method="post" action="<?php echo RUTA_URL ?>/Inventario/guardarAlta" id="formAlta">

        <!-- ════════════════════════════════════════════════
             BLOQUE 1 — Origen del bien
             ════════════════════════════════════════════ -->
        <div class="card shadow-sm mb-4" style="border-top:4px solid #0b2a85; border-radius:8px;">
            <div class="card-header" style="background:#0b2a85; color:#fff; font-weight:600;">
                <i class="bi bi-file-earmark-text me-2"></i>1. Origen del bien
            </div>
            <div class="card-body">

                <!-- Selector factura / donación -->
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Procedencia <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="origen"
                                       id="origenFactura" value="factura"
                                       <?php echo (($datos['post']['origen'] ?? '') !== 'donacion') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="origenFactura">
                                    <i class="bi bi-receipt me-1" style="color:#0b2a85"></i>
                                    <strong>Factura justificada</strong>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="origen"
                                       id="origenDonacion" value="donacion"
                                       <?php echo (($datos['post']['origen'] ?? '') === 'donacion') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="origenDonacion">
                                    <i class="bi bi-heart me-1" style="color:#27ae60"></i>
                                    <strong>Donación / otro origen</strong>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección Factura -->
                <div id="seccionFactura">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold" for="N_Asiento">
                                Nº Asiento <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="N_Asiento" name="N_Asiento">
                                <option value="">— Selecciona asiento —</option>
                                <?php foreach ($datos['facturasInv'] as $f): ?>
                                <option value="<?php echo $f->N_Asiento ?>"
                                    <?php echo (($datos['post']['N_Asiento'] ?? '') == $f->N_Asiento) ? 'selected' : '' ?>>
                                    Asiento <?php echo $f->N_Asiento ?>
                                    · <?php echo htmlspecialchars($f->nombre_proveedor) ?>
                                    (<?php echo htmlspecialchars($f->NFactura ?? '') ?>)
                                </option>
                                <?php endforeach ?>
                            </select>
                            <small class="text-muted">Solo facturas justificadas con Inventariable=Sí</small>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">Nº Factura</label>
                            <input type="text" class="form-control" id="NFactura" name="NFactura"
                                   readonly style="background:#f8f9fa"
                                   value="<?php echo htmlspecialchars($datos['post']['NFactura'] ?? '') ?>">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">Proveedor</label>
                            <input type="text" class="form-control" id="nombreProveedor"
                                   readonly style="background:#f8f9fa"
                                   value="<?php echo htmlspecialchars($datos['post']['nombreProveedor'] ?? '') ?>">
                            <input type="hidden" id="CIF" name="CIF"
                                   value="<?php echo htmlspecialchars($datos['post']['CIF'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <!-- Sección Donación -->
                <div id="seccionDonacion" style="display:none">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="Procedencia">
                                Procedencia / Donante <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="Procedencia" name="Procedencia"
                                   placeholder="Nombre del donante, entidad u organismo…"
                                   value="<?php echo htmlspecialchars($datos['post']['Procedencia'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="row g-3 mt-1">
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="Observaciones">Observaciones</label>
                        <textarea class="form-control" id="Observaciones" name="Observaciones"
                                  rows="2" placeholder="Notas adicionales…"
                                  ><?php echo htmlspecialchars($datos['post']['Observaciones'] ?? '') ?></textarea>
                    </div>
                </div>

            </div>
        </div>

        <!-- ════════════════════════════════════════════════
             BLOQUE 2 — Detalle del artículo
             ════════════════════════════════════════════ -->
        <div class="card shadow-sm mb-4" style="border-top:4px solid #0b2a85; border-radius:8px;">
            <div class="card-header" style="background:#0b2a85; color:#fff; font-weight:600;">
                <i class="bi bi-list-check me-2"></i>2. Detalle del artículo
            </div>
            <div class="card-body">
                <div class="row g-3">

                    <!-- Categoría -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold" for="CodCat">
                            Categoría <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="CodCat" name="CodCat" required>
                            <option value="">— Selecciona categoría —</option>
                            <?php foreach ($datos['categorias'] as $cat): ?>
                            <option value="<?php echo $cat->CodCat ?>"
                                <?php echo (($datos['post']['CodCat'] ?? '') == $cat->CodCat) ? 'selected' : '' ?>>
                                <?php echo htmlspecialchars($cat->denominacion) ?>
                            </option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <!-- Artículo (se carga por AJAX) -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold" for="CodArt">
                            Artículo <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="CodArt" name="CodArt" required>
                            <option value="">— Elige primero categoría —</option>
                        </select>
                    </div>

                    <!-- Unidades -->
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-semibold" for="Unidades">
                            Unidades <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" id="Unidades" name="Unidades"
                               min="1" value="<?php echo (int)($datos['post']['Unidades'] ?? 1) ?>" required>
                    </div>

                    <!-- Tipo Individual/Bloque -->
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-semibold" for="Individual">Tipo registro</label>
                        <select class="form-select" id="Individual" name="Individual">
                            <option value="B" <?php echo (($datos['post']['Individual'] ?? 'B') === 'B') ? 'selected' : '' ?>>Bloque</option>
                            <option value="I" <?php echo (($datos['post']['Individual'] ?? '') === 'I')  ? 'selected' : '' ?>>Individual</option>
                        </select>
                        <small class="text-muted">Individual: cada unidad lleva nº de serie propio</small>
                    </div>

                    <!-- Departamento / Destino responsable -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold" for="Dep_Responsable">Destino responsable</label>
                        <select class="form-select" id="Dep_Responsable" name="Dep_Responsable">
                            <option value="">— Sin asignar —</option>
                            <?php foreach ($datos['destinos'] as $dest): ?>
                            <option value="<?php echo $dest->Destino_Id ?>"
                                <?php echo (($datos['post']['Dep_Responsable'] ?? '') == $dest->Destino_Id) ? 'selected' : '' ?>>
                                <?php echo htmlspecialchars($dest->Depart_Servicio) ?>
                            </option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <!-- Ubicación -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold" for="Local_Ini">Ubicación inicial</label>
                        <select class="form-select" id="Local_Ini" name="Local_Ini">
                            <option value="">— Sin asignar —</option>
                            <?php foreach ($datos['ubicaciones'] as $ub): ?>
                            <option value="<?php echo $ub->id_destino ?>"
                                <?php echo (($datos['post']['Local_Ini'] ?? '') == $ub->id_destino) ? 'selected' : '' ?>>
                                <?php echo htmlspecialchars($ub->NomLocal) ?>
                            </option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <!-- Descripción -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold" for="Descripcion">Descripción / Modelo / Nº serie</label>
                        <input type="text" class="form-control" id="Descripcion" name="Descripcion"
                               placeholder="Características, modelo, nº serie…"
                               value="<?php echo htmlspecialchars($datos['post']['Descripcion'] ?? '') ?>">
                    </div>

                </div>
            </div>
        </div>

        <div class="d-flex gap-2 justify-content-end mb-5">
            <a href="<?php echo RUTA_URL ?>/Inventario/index" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-1"></i>Cancelar
            </a>
            <button type="submit" class="btn btn-success px-4">
                <i class="bi bi-floppy me-1"></i>Guardar alta
            </button>
        </div>

    </form>

</div><!-- /container -->

<script>
const RUTA_URL = '<?php echo RUTA_URL ?>';

// ── Toggle factura / donación ─────────────────────────────────────────────
function toggleOrigen() {
    const esFact = document.getElementById('origenFactura').checked;
    document.getElementById('seccionFactura').style.display  = esFact ? '' : 'none';
    document.getElementById('seccionDonacion').style.display = esFact ? 'none' : '';
    document.getElementById('N_Asiento').required   = esFact;
    document.getElementById('Procedencia').required = !esFact;
}
document.getElementById('origenFactura').addEventListener('change', toggleOrigen);
document.getElementById('origenDonacion').addEventListener('change', toggleOrigen);
toggleOrigen();

// ── AJAX datos de factura ────────────────────────────────────────────────
document.getElementById('N_Asiento').addEventListener('change', function() {
    if (!this.value) {
        ['NFactura','nombreProveedor','CIF'].forEach(id => document.getElementById(id).value = '');
        return;
    }
    fetch(RUTA_URL + '/Inventario/ajaxFactura?asiento=' + this.value)
        .then(r => r.json())
        .then(f => {
            if (f) {
                document.getElementById('NFactura').value        = f.NFactura           || '';
                document.getElementById('nombreProveedor').value = f.nombre_proveedor   || '';
                document.getElementById('CIF').value             = f.CIF                || '';
            }
        });
});

// ── AJAX cargar artículos de categoría ───────────────────────────────────
function cargarArticulos(cat, seleccionado) {
    const sel = document.getElementById('CodArt');
    sel.innerHTML = '<option>Cargando…</option>';
    if (!cat) { sel.innerHTML = '<option value="">— Elige primero categoría —</option>'; return; }
    fetch(RUTA_URL + '/Inventario/ajaxArticulos?cat=' + cat)
        .then(r => r.json())
        .then(arts => {
            sel.innerHTML = '<option value="">— Selecciona artículo —</option>';
            arts.forEach(a => {
                const o = document.createElement('option');
                o.value = a.CodArt; o.textContent = a.nombre;
                if (String(a.CodArt) === String(seleccionado)) o.selected = true;
                sel.appendChild(o);
            });
        });
}
document.getElementById('CodCat').addEventListener('change', function() {
    cargarArticulos(this.value, null);
});
<?php if (!empty($datos['post']['CodCat'])): ?>
cargarArticulos(<?php echo (int)$datos['post']['CodCat'] ?>, <?php echo (int)($datos['post']['CodArt'] ?? 0) ?>);
<?php endif ?>
</script>

<?php include RUTA_APP . '/vistas/inc/footer.php' ?>
