<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container-fluid px-4 py-4">

    <div class="row mb-4">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <span><i class="fas fa-cog me-2"></i>Configuracion del sistema de mensajeria</span>
            </span>
        </div>
    </div>

    <?php if (isset($datos['okConfig'])): ?>
    <div class="alert alert-success mb-4">
        <i class="fas fa-check-circle me-2"></i>Configuracion guardada correctamente.
    </div>
    <?php endif ?>

    <form method="POST" action="<?php echo RUTA_URL ?>/Mensajes/configuracion">
    <div class="row g-4">

        <div class="col-12 col-md-4">
            <div class="input-group">
                <label class="input-group-text">Dias borrado adjuntos</label>
                <input type="number" name="dias_borrado_adjuntos" class="form-control"
                       min="1" max="365"
                       value="<?php echo htmlspecialchars($datos['config']['dias_borrado_adjuntos']->valor ?? '30') ?>">
            </div>
            <small class="text-muted ms-1">
                <?php echo $datos['config']['dias_borrado_adjuntos']->descripcion ?? 'Dias hasta borrar adjuntos del servidor' ?>
            </small>
        </div>

        <div class="col-12 col-md-4">
            <div class="input-group">
                <label class="input-group-text">Tamano max. adjunto (MB)</label>
                <input type="number" name="max_tam_adjunto_mb" class="form-control"
                       min="1" max="100"
                       value="<?php echo htmlspecialchars($datos['config']['max_tam_adjunto_mb']->valor ?? '10') ?>">
            </div>
            <small class="text-muted ms-1">
                <?php echo $datos['config']['max_tam_adjunto_mb']->descripcion ?? 'Tamano maximo por archivo adjunto' ?>
            </small>
        </div>

        <div class="col-12">
            <div class="input-group">
                <label class="input-group-text">Extensiones permitidas</label>
                <input type="text" name="extensiones_permitidas" class="form-control"
                       value="<?php echo htmlspecialchars($datos['config']['extensiones_permitidas']->valor ?? '') ?>">
            </div>
            <small class="text-muted ms-1">Separadas por comas, sin puntos: pdf,docx,xlsx,png...</small>
        </div>

        <div class="col-12">
            <button type="submit" class="btn"
                    style="background:#27ae60; color:#fff; font-weight:600">
                <i class="fas fa-save me-2"></i>Guardar configuracion
            </button>
        </div>

    </div>
    </form>

</div>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
