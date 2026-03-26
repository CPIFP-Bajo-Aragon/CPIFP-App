<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container-fluid px-4 py-4">
    <div class="row mb-3">
        <div class="col">
            <span class="nombre_modulo_seguimiento"><i class="fas fa-plus me-2"></i>Nueva Empresa</span>
        </div>
        <div class="col-auto">
            <a href="<?php echo RUTA_URL ?>/empresas" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <?php if($datos['error']): ?>
    <div class="alert alert-danger">Rellena los campos obligatorios.</div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm" style="max-width:600px;">
        <div class="card-body">
            <form method="post" action="<?php echo RUTA_URL ?>/empresas/nueva">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre de la empresa <span class="text-danger">*</span></label>
                    <input type="text" name="empresa" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Persona de contacto</label>
                    <input type="text" name="contacto" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email de contacto</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control">
                </div>
                <div class="alert alert-info small">
                    <i class="fas fa-info-circle me-1"></i>
                    Se generará automáticamente un token único para que la empresa acceda a sus encuestas sin necesidad de login.
                </div>
                <div class="d-flex gap-2 justify-content-end">
                    <a href="<?php echo RUTA_URL ?>/empresas" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-custom">
                        <i class="fas fa-save me-1"></i>Guardar empresa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
