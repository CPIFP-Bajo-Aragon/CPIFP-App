<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container-fluid px-4 py-4">
    <div class="row mb-3">
        <div class="col">
            <span class="nombre_modulo_seguimiento"><i class="fas fa-edit me-2"></i>Editar Empresa</span>
        </div>
        <div class="col-auto">
            <a href="<?php echo RUTA_URL ?>/empresas" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="max-width:600px;">
        <div class="card-body">
            <form method="post" action="<?php echo RUTA_URL ?>/empresas/editar/<?php echo $datos['empresa']->id_empresa ?>">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre de la empresa <span class="text-danger">*</span></label>
                    <input type="text" name="empresa" class="form-control" required
                           value="<?php echo htmlspecialchars($datos['empresa']->empresa) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Persona de contacto</label>
                    <input type="text" name="contacto" class="form-control"
                           value="<?php echo htmlspecialchars($datos['empresa']->contacto ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                           value="<?php echo htmlspecialchars($datos['empresa']->email ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control"
                           value="<?php echo htmlspecialchars($datos['empresa']->telefono ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Estado</label>
                    <select name="activa" class="form-select">
                        <option value="1" <?php echo $datos['empresa']->activa ? 'selected' : '' ?>>Activa</option>
                        <option value="0" <?php echo !$datos['empresa']->activa ? 'selected' : '' ?>>Inactiva</option>
                    </select>
                </div>
                <div class="d-flex gap-2 justify-content-end">
                    <a href="<?php echo RUTA_URL ?>/empresas" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-custom">
                        <i class="fas fa-save me-1"></i>Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
