<?php
include RUTA_APP . '/vistas/inc/header.php';
$r   = $datos['detalle'];
$esED = $datos['esED'];
?>

<div class="container mt-4 pt-4">

    <div class="row mb-3 align-items-center">
        <div class="col">
            <h4 class="fw-bold" style="color:#2C3E50">
                <i class="bi bi-pencil-square me-2" style="color:#f39c12"></i>Modificar artículo
            </h4>
            <p class="text-muted mb-0">
                NE-<?php echo str_pad($r->NEntrada, 4, '0', STR_PAD_LEFT) ?>
                · <?php echo htmlspecialchars($r->NombreArt ?? '') ?>
            </p>
        </div>
        <div class="col-auto">
            <a href="<?php echo RUTA_URL ?>/Inventario/modificar"
               class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    <?php if (!empty($datos['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($datos['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif ?>

    <!-- Info origen (solo lectura) -->
    <div class="card shadow-sm mb-4" style="border-radius:8px; border-top:4px solid #6c757d">
        <div class="card-header" style="background:#6c757d; color:#fff; font-weight:600;">
            <i class="bi bi-info-circle me-2"></i>Información de la entrada (solo lectura)
        </div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-6 col-md-3">
                    <small class="text-muted d-block">Nº Entrada</small>
                    <strong>NE-<?php echo str_pad($r->NEntrada, 4, '0', STR_PAD_LEFT) ?></strong>
                </div>
                <div class="col-6 col-md-3">
                    <small class="text-muted d-block">Fecha alta</small>
                    <strong><?php echo $r->Fecha_Alta ? date('d/m/Y', strtotime($r->Fecha_Alta)) : '—' ?></strong>
                </div>
                <div class="col-12 col-md-6">
                    <small class="text-muted d-block">Origen</small>
                    <?php if ($r->NFactura): ?>
                        <strong><i class="bi bi-receipt me-1" style="color:#0b2a85"></i>
                            Factura <?php echo htmlspecialchars($r->NFactura) ?>
                        </strong> — <?php echo htmlspecialchars($r->NombreOrigen ?? '') ?>
                    <?php elseif ($r->Procedencia): ?>
                        <strong><i class="bi bi-heart me-1" style="color:#27ae60"></i>
                            Donación: <?php echo htmlspecialchars($r->Procedencia) ?>
                        </strong>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>

    <form method="post" action="<?php echo RUTA_URL ?>/Inventario/guardarModificacion">
        <input type="hidden" name="id" value="<?php echo $r->id ?>">

        <div class="card shadow-sm mb-4" style="border-radius:8px; border-top:4px solid #0b2a85">
            <div class="card-header" style="background:#0b2a85; color:#fff; font-weight:600;">
                <i class="bi bi-list-check me-2"></i>Datos del artículo
            </div>
            <div class="card-body">
                <div class="row g-3">

                    <!-- Categoría -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold" for="CodCat">
                            Categoría <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="CodCat" name="CodCat" required>
                            <option value="">— Selecciona —</option>
                            <?php foreach ($datos['categorias'] as $c): ?>
                            <option value="<?php echo $c->CodCat ?>"
                                <?php echo $c->CodCat == $r->CodCat ? 'selected' : '' ?>>
                                <?php echo htmlspecialchars($c->denominacion) ?>
                            </option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <!-- Artículo -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold" for="CodArt">
                            Artículo <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="CodArt" name="CodArt" required>
                            <?php foreach ($datos['articulos'] as $a): ?>
                            <option value="<?php echo $a->CodArt ?>"
                                <?php echo $a->CodArt == $r->CodArt ? 'selected' : '' ?>>
                                <?php echo htmlspecialchars($a->nombre) ?>
                            </option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <!-- Unidades -->
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-semibold" for="Unidades">
                            Unidades <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" id="Unidades" name="Unidades"
                               min="1" value="<?php echo (int)$r->Unidades ?>" required>
                    </div>

                    <!-- Tipo -->
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-semibold" for="Individual">Tipo</label>
                        <select class="form-select" id="Individual" name="Individual">
                            <option value="B" <?php echo $r->Individual !== 'I' ? 'selected' : '' ?>>Bloque</option>
                            <option value="I" <?php echo $r->Individual === 'I' ? 'selected' : '' ?>>Individual</option>
                        </select>
                    </div>

                    <!-- Destino responsable -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold" for="Dep_Responsable">Destino responsable</label>
                        <select class="form-select" id="Dep_Responsable" name="Dep_Responsable"
                                <?php echo !$esED ? 'disabled' : '' ?>>
                            <option value="">— Sin asignar —</option>
                            <?php foreach ($datos['destinos'] as $d): ?>
                            <option value="<?php echo $d->Destino_Id ?>"
                                <?php echo $d->Destino_Id == $r->Dep_Responsable ? 'selected' : '' ?>>
                                <?php echo htmlspecialchars($d->Depart_Servicio) ?>
                            </option>
                            <?php endforeach ?>
                        </select>
                        <?php if (!$esED): ?>
                        <input type="hidden" name="Dep_Responsable" value="<?php echo $r->Dep_Responsable ?>">
                        <?php endif ?>
                    </div>

                    <!-- Ubicación -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold" for="Local_Ini">Ubicación</label>
                        <select class="form-select" id="Local_Ini" name="Local_Ini">
                            <option value="">— Sin asignar —</option>
                            <?php foreach ($datos['ubicaciones'] as $ub): ?>
                            <option value="<?php echo $ub->id_destino ?>"
                                <?php echo $ub->id_destino == $r->Local_Ini ? 'selected' : '' ?>>
                                <?php echo htmlspecialchars($ub->NomLocal) ?>
                            </option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <!-- Descripción -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold" for="Descripcion">Descripción / Nº serie</label>
                        <input type="text" class="form-control" id="Descripcion" name="Descripcion"
                               placeholder="Modelo, nº serie, características…"
                               value="<?php echo htmlspecialchars($r->Descripcion ?? '') ?>">
                    </div>

                    <?php if ($esED): ?>
                    <!-- Observaciones (solo ED) -->
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="Observaciones">Observaciones de la entrada</label>
                        <textarea class="form-control" id="Observaciones" name="Observaciones"
                                  rows="2"><?php echo htmlspecialchars($r->Observaciones ?? '') ?></textarea>
                    </div>
                    <?php endif ?>

                </div>
            </div>
        </div>

        <div class="d-flex gap-2 justify-content-end mb-5">
            <a href="<?php echo RUTA_URL ?>/Inventario/modificar" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-1"></i>Cancelar
            </a>
            <button type="submit" class="btn btn-warning px-4">
                <i class="bi bi-floppy me-1"></i>Guardar cambios
            </button>
        </div>

    </form>

</div><!-- /container -->

<script>
const RUTA_URL = '<?php echo RUTA_URL ?>';

document.getElementById('CodCat').addEventListener('change', function() {
    const cat = this.value;
    const sel = document.getElementById('CodArt');
    sel.innerHTML = '<option>Cargando…</option>';
    fetch(RUTA_URL + '/Inventario/ajaxArticulos?cat=' + cat)
        .then(r => r.json())
        .then(arts => {
            sel.innerHTML = '<option value="">— Selecciona artículo —</option>';
            arts.forEach(a => {
                const o = document.createElement('option');
                o.value = a.CodArt; o.textContent = a.nombre;
                sel.appendChild(o);
            });
        });
});
</script>

<?php include RUTA_APP . '/vistas/inc/footer.php' ?>
