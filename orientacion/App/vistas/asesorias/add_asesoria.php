<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container-fluid px-4 py-4">

    <div class="row mb-4 align-items-center">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <span><i class="fas fa-plus-circle me-2"></i>Añadir Asesoría</span>
            </span>
        </div>
        <div class="col-auto">
            <a href="<?php echo RUTA_URL ?>/" class="btn-volver">
                <i class="fas fa-arrow-left"></i>Volver
            </a>
        </div>
    </div>

    <?php if (isset($datos['error']) && $datos['error'] == 1): ?>
    <div class="alert alert-danger mb-4">
        <i class="fas fa-exclamation-triangle me-2"></i>Debes rellenar todos los campos obligatorios.
    </div>
    <?php endif ?>

    <form method="post" class="mb-5">
        <div class="row g-3">

            <div class="col-12 col-md-4">
                <div class="input-group">
                    <label class="input-group-text">Nombre *</label>
                    <input type="text" class="form-control" name="nombre_as" autofocus required
                           placeholder="Nombre del solicitante">
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="input-group">
                    <label class="input-group-text">DNI</label>
                    <input type="text" class="form-control" name="dni_as" placeholder="DNI">
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="input-group">
                    <label class="input-group-text">Título</label>
                    <input type="text" class="form-control" name="titulo_as" placeholder="Título de la asesoría">
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="input-group">
                    <label class="input-group-text">Teléfono</label>
                    <input type="text" class="form-control" name="telefono_as" placeholder="Teléfono de contacto">
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="input-group">
                    <label class="input-group-text">Email *</label>
                    <input type="email" class="form-control" name="email_as" required
                           placeholder="email@ejemplo.com">
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="input-group">
                    <label class="input-group-text">Domicilio</label>
                    <input type="text" class="form-control" name="domicilio_as" placeholder="Domicilio">
                </div>
            </div>

            <div class="col-12">
                <div class="input-group">
                    <label class="input-group-text">Descripción</label>
                    <textarea class="form-control" name="descripcion_as" rows="4"
                              placeholder="Descripción de la consulta..."></textarea>
                </div>
            </div>

            <div class="col-12 col-md-10">
                <button type="submit" class="btn w-100"
                        style="background:#27ae60; color:#fff; font-weight:600; font-size:1.1rem">
                    <i class="fas fa-save me-2"></i>Guardar asesoría
                </button>
            </div>
            <div class="col-12 col-md-2">
                <a class="btn w-100" href="<?php echo RUTA_URL ?>/"
                   style="background:#e74c3c; color:#fff; font-weight:600; font-size:1.1rem">
                    <i class="fas fa-times me-2"></i>Cancelar
                </a>
            </div>

        </div>
    </form>

</div>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
