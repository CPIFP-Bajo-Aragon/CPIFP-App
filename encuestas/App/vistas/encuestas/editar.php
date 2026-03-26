<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container-fluid px-4 py-4">
    <div class="row mb-3">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <i class="fas fa-edit me-2"></i>Editar Encuesta
            </span>
        </div>
        <div class="col-auto">
            <a href="<?php echo RUTA_URL ?>/encuestas/ver/<?php echo $datos['encuesta']->id_encuesta ?>"
               class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="<?php echo RUTA_URL ?>/encuestas/editar/<?php echo $datos['encuesta']->id_encuesta ?>">

                <div class="mb-3">
                    <label class="form-label fw-bold">Título <span class="text-danger">*</span></label>
                    <input type="text" name="titulo" class="form-control" required
                           value="<?php echo htmlspecialchars($datos['encuesta']->titulo) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="2"><?php echo htmlspecialchars($datos['encuesta']->descripcion ?? '') ?></textarea>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Fecha inicio <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_inicio" class="form-control" required
                               value="<?php echo $datos['encuesta']->fecha_inicio ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fecha fin</label>
                        <input type="date" name="fecha_fin" class="form-control"
                               value="<?php echo $datos['encuesta']->fecha_fin ?? '' ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Estado</label>
                        <select name="activa" class="form-select">
                            <option value="1" <?php echo $datos['encuesta']->activa ? 'selected' : '' ?>>Abierta</option>
                            <option value="0" <?php echo !$datos['encuesta']->activa ? 'selected' : '' ?>>Cerrada</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="<?php echo RUTA_URL ?>/encuestas/ver/<?php echo $datos['encuesta']->id_encuesta ?>"
                       class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-custom">
                        <i class="fas fa-save me-1"></i>Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
