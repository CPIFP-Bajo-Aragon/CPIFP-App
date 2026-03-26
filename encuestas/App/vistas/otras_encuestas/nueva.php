<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container py-4" style="max-width:700px;">

    <div class="row mb-3 align-items-center">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <i class="fas fa-plus-circle me-2"></i>Nueva encuesta
            </span>
        </div>
        <div class="col-auto">
            <a href="<?php echo RUTA_URL ?>/otras_encuestas" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    <?php if(!empty($datos['error'])): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-1"></i>
        Error al crear la encuesta. Revisa los campos e inténtalo de nuevo.
    </div>
    <?php endif; ?>

    <div class="alert alert-info small">
        <i class="fas fa-shield-alt me-1"></i>
        <strong>Encuesta anónima.</strong> No se solicitará ningún dato personal a los participantes.
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="<?php echo RUTA_URL ?>/otras_encuestas/nueva">

                <!-- Tipo -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        Tipo de encuesta <span class="text-danger">*</span>
                    </label>
                    <select name="id_tipo_encuesta" id="sel-tipo" class="form-select" required
                            onchange="cargarInfoTipo(this.value)">
                        <option value="">— Selecciona un tipo —</option>
                        <?php foreach($datos['tipos'] as $t): ?>
                        <option value="<?php echo $t->id_tipo_encuesta ?>"
                            <?php echo (($datos['post']['id_tipo_encuesta'] ?? '') == $t->id_tipo_encuesta) ? 'selected' : '' ?>>
                            <?php echo htmlspecialchars($t->tipo_encuesta) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <!-- Info de la plantilla del tipo seleccionado -->
                    <div id="info-tipo" class="mt-2 text-muted small" style="display:none;">
                        <i class="fas fa-question-circle me-1"></i>
                        <span id="info-tipo-txt"></span>
                    </div>
                </div>

                <!-- Título -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        Título <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="titulo" class="form-control" required
                           placeholder="Ej: Encuesta de satisfacción de familias 2024-2025"
                           value="<?php echo htmlspecialchars($datos['post']['titulo'] ?? '') ?>">
                </div>

                <!-- Descripción -->
                <div class="mb-3">
                    <label class="form-label">Descripción / instrucciones <small class="text-muted">(opcional)</small></label>
                    <textarea name="descripcion" class="form-control" rows="2"
                              placeholder="Texto introductorio que verán los participantes..."><?php
                        echo htmlspecialchars($datos['post']['descripcion'] ?? '')
                    ?></textarea>
                </div>

                <div class="row g-3 mb-3">
                    <!-- Curso académico -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Curso académico <span class="text-danger">*</span></label>
                        <input type="text" name="curso_academico" class="form-control"
                               pattern="\d{4}-\d{4}" placeholder="2024-2025" required
                               value="<?php echo htmlspecialchars($datos['post']['curso_academico'] ?? $datos['curso_actual']) ?>">
                    </div>

                    <!-- Evaluación -->
                    <div class="col-md-4">
                        <label class="form-label">Evaluación <small class="text-muted">(opcional)</small></label>
                        <select name="trimestre" class="form-select">
                            <option value="">— Sin evaluación —</option>
                            <?php foreach($datos['evaluaciones'] as $ev): ?>
                            <option value="<?php echo $ev->id_evaluacion ?>"
                                <?php echo (($datos['post']['trimestre'] ?? $datos['evaluacion_actual']?->id_evaluacion ?? '') == $ev->id_evaluacion) ? 'selected' : '' ?>>
                                <?php echo htmlspecialchars($ev->evaluacion) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Fecha fin -->
                    <div class="col-md-4">
                        <label class="form-label">Fecha de cierre <small class="text-muted">(opcional)</small></label>
                        <input type="date" name="fecha_fin" class="form-control"
                               value="<?php echo htmlspecialchars($datos['post']['fecha_fin'] ?? '') ?>">
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end pt-2">
                    <a href="<?php echo RUTA_URL ?>/otras_encuestas" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-custom">
                        <i class="fas fa-save me-1"></i>Crear encuesta
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>

<script>
const RUTA = '<?php echo RUTA_URL ?>';

function cargarInfoTipo(id){
    const infoDiv = document.getElementById('info-tipo');
    const infoTxt = document.getElementById('info-tipo-txt');
    if(!id){ infoDiv.style.display='none'; return; }

    fetch(RUTA+'/preguntas/get_preguntas?id_tipo_encuesta='+id)
        .then(r=>r.json())
        .then(data => {
            const n = data.preguntas.filter(p => p.activo).length;
            infoTxt.textContent = `Esta plantilla tiene ${n} pregunta(s) activa(s). Se copiarán al crear la encuesta.`;
            infoDiv.style.display = 'block';
        });
}

// Autorellenar info si ya hay tipo seleccionado (vuelta con error)
(function(){
    const sel = document.getElementById('sel-tipo');
    if(sel.value) cargarInfoTipo(sel.value);
})();
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
