<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container-fluid px-4 py-4">

    <div class="row mb-4 align-items-center">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <span><i class="fas fa-plus-circle me-2"></i>Añadir Incidencia</span>
            </span>
        </div>
        <div class="col-auto">
            <a href="<?php echo RUTA_URL ?>" class="btn-volver">
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

            <div class="col-12 col-md-6">
                <div class="input-group">
                    <label class="input-group-text">Título *</label>
                    <input type="text" class="form-control" name="titulo_in" required
                           placeholder="Título de la incidencia">
                </div>
            </div>

            <div class="col-12 col-md-2">
                <div class="input-group">
                    <label class="input-group-text">Urgencia</label>
                    <select class="form-control" name="id_urgencia">
                        <?php foreach ($datos['estadosUrgencia'] as $u): ?>
                        <option value="<?php echo $u->id_urgencia ?>"
                            <?php echo $u->id_urgencia == 1 ? 'selected' : '' ?>>
                            <?php echo htmlspecialchars($u->urgencia) ?>
                        </option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>

            <div class="col-12 col-md-2">
                <div class="input-group">
                    <label class="input-group-text">Edificio *</label>
                    <select class="form-control" id="id_edificio" name="id_edificio" required>
                        <option value="" disabled selected>-- Selecciona --</option>
                        <?php foreach ($datos['edificios'] as $e): ?>
                        <option value="<?php echo $e->id_edificio ?>"><?php echo htmlspecialchars($e->edificio) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>

            <div class="col-12 col-md-2">
                <div class="input-group">
                    <label class="input-group-text">Ubicación *</label>
                    <select class="form-control" id="id_ubicacion" name="id_ubicacion" required>
                        <option value="" disabled selected>-- Selecciona --</option>
                    </select>
                </div>
            </div>

            <div class="col-12">
                <div class="input-group">
                    <label class="input-group-text">Descripción</label>
                    <textarea class="form-control" name="descripcion_in" rows="4"
                              placeholder="Descripción detallada de la incidencia..."></textarea>
                </div>
            </div>

            <div class="col-12 col-md-10">
                <button type="submit" class="btn w-100"
                        style="background:#27ae60; color:#fff; font-weight:600; font-size:1.1rem">
                    <i class="fas fa-save me-2"></i>Guardar incidencia
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

<script>
    let edificios = <?php echo json_encode($datos['edificios']) ?>;

    function cargarUbicaciones() {
        const edificioSelect  = document.getElementById('id_edificio');
        const ubicacionSelect = document.getElementById('id_ubicacion');
        ubicacionSelect.innerHTML = '<option value="" disabled selected>-- Selecciona --</option>';
        const edificio = edificios.find(e => e.id_edificio == edificioSelect.value);
        if (edificio) {
            edificio.ubicaciones.forEach(function(u) {
                const opt = document.createElement('option');
                opt.value = u.id_ubicacion;
                opt.textContent = u.ubicacion;
                ubicacionSelect.appendChild(opt);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('id_edificio').addEventListener('change', cargarUbicaciones);
    });
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
