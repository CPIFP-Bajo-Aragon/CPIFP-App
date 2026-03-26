<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container-fluid px-4 py-4">

    <div class="row mb-3">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <i class="fas fa-plus-circle me-2"></i>Nueva Encuesta
            </span>
        </div>
        <div class="col-auto">
            <a href="<?php echo RUTA_URL ?>/encuestas" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    <?php if(!empty($datos['error'])): ?>
    <div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-1"></i>Rellena todos los campos obligatorios.</div>
    <?php endif; ?>

    <?php if(!empty($datos['error_duplicado'])): ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-1"></i>
        Ya existe una encuesta para este profesor, módulo y evaluación en el curso seleccionado.
        <a href="<?php echo RUTA_URL ?>/encuestas/ver/<?php echo $datos['error_duplicado']->id_encuesta ?>" class="alert-link ms-2">
            Ver encuesta existente →
        </a>
    </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="<?php echo RUTA_URL ?>/encuestas/nueva" id="form-nueva-encuesta">

                <!-- ── Tipo de encuesta ────────────────────────────────── -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Tipo de encuesta <span class="text-danger">*</span></label>
                    <div class="d-flex gap-3 flex-wrap">
                        <?php foreach($datos['tipos'] as $tipo): ?>
                        <div class="border rounded p-3 tipo-card" style="cursor:pointer; min-width:200px;
                             transition:border-color .15s;">
                            <div class="form-check">
                                <input class="form-check-input" type="radio"
                                       name="id_tipo_encuesta"
                                       id="tipo_<?php echo $tipo->id_tipo_encuesta ?>"
                                       value="<?php echo $tipo->id_tipo_encuesta ?>"
                                       onchange="cambiarTipo(this.value)" required
                                       <?php echo (($datos['post']['id_tipo_encuesta'] ?? '') == $tipo->id_tipo_encuesta) ? 'checked' : '' ?>>
                                <label class="form-check-label ms-1 fw-bold"
                                       for="tipo_<?php echo $tipo->id_tipo_encuesta ?>"
                                       style="cursor:pointer;">
                                    <?php if($tipo->id_tipo_encuesta == 1): ?>
                                    <i class="fas fa-user-graduate me-1 text-primary"></i>
                                    <?php else: ?>
                                    <i class="fas fa-poll me-1 text-secondary"></i>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($tipo->tipo_encuesta) ?>
                                    <?php if($tipo->id_tipo_encuesta != 1): ?>
                                    <span class="badge bg-light text-muted border ms-1" style="font-size:.7rem;">Anónima</span>
                                    <?php endif; ?>
                                    <?php if($tipo->descripcion): ?>
                                    <br><small class="text-muted fw-normal"><?php echo htmlspecialchars($tipo->descripcion) ?></small>
                                    <?php endif; ?>
                                </label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- ── Bloque ALUMNOS (solo tipo 1) ───────────────────── -->
                <div id="bloque_alumnos" style="display:<?php echo (($datos['post']['id_tipo_encuesta'] ?? '') == '1') ? 'block' : 'none' ?>;">

                    <div class="card bg-light border-0 mb-4 p-3">
                        <div class="fw-bold mb-3 text-primary">
                            <i class="fas fa-sitemap me-1"></i>Selección de ciclo, curso, módulo y profesor
                        </div>
                        <div class="row g-3">

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Ciclo <span class="text-danger">*</span></label>
                                <select name="id_ciclo" id="sel_ciclo" class="form-select"
                                        onchange="cargarCursos(this.value)">
                                    <option value="">— Selecciona ciclo —</option>
                                    <?php foreach($datos['ciclos'] as $ci): ?>
                                    <option value="<?php echo $ci->id_ciclo ?>">
                                        <?php echo htmlspecialchars($ci->ciclo) ?>
                                        <?php if($ci->ciclo_corto) echo ' (' . htmlspecialchars($ci->ciclo_corto) . ')' ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Curso <span class="text-danger">*</span></label>
                                <select name="id_curso" id="sel_curso" class="form-select"
                                        onchange="cargarModulos(this.value)" disabled>
                                    <option value="">— Selecciona primero el ciclo —</option>
                                </select>
                                <div id="spinner_curso" class="text-muted small mt-1" style="display:none;">
                                    <i class="fas fa-spinner fa-spin me-1"></i>Cargando...
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Módulo <span class="text-danger">*</span></label>
                                <select name="id_modulo" id="sel_modulo" class="form-select"
                                        onchange="cargarProfesores(this.value)" disabled>
                                    <option value="">— Selecciona primero el curso —</option>
                                </select>
                                <div id="spinner_modulo" class="text-muted small mt-1" style="display:none;">
                                    <i class="fas fa-spinner fa-spin me-1"></i>Cargando...
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Profesor <span class="text-danger">*</span></label>
                                <select name="id_profesor_modulo" id="sel_profesor" class="form-select"
                                        onchange="actualizarTitulo()" disabled>
                                    <option value="">— Selecciona primero el módulo —</option>
                                </select>
                                <div id="spinner_profesor" class="text-muted small mt-1" style="display:none;">
                                    <i class="fas fa-spinner fa-spin me-1"></i>Cargando...
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card bg-light border-0 mb-4 p-3">
                        <div class="fw-bold mb-3 text-primary">
                            <i class="fas fa-calendar-check me-1"></i>Período de la encuesta
                        </div>
                        <div class="row g-3 align-items-start">
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold">Evaluación <span class="text-danger">*</span></label>
                                <select name="trimestre" id="sel_trimestre" class="form-select" required
                                        onchange="actualizarTitulo()">
                                    <option value="">— Selecciona —</option>
                                    <?php foreach($datos['evaluaciones'] as $ev): ?>
                                    <option value="<?php echo $ev->id_evaluacion ?>"
                                        <?php echo ($datos['evaluacion_actual'] && $datos['evaluacion_actual']->id_evaluacion == $ev->id_evaluacion) ? 'selected' : '' ?>>
                                        <?php echo htmlspecialchars($ev->evaluacion) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if($datos['evaluacion_actual']): ?>
                                <div class="form-text text-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Detectada: <strong><?php echo htmlspecialchars($datos['evaluacion_actual']->evaluacion) ?></strong>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold">Curso académico <span class="text-danger">*</span></label>
                                <input type="text" name="curso_academico" class="form-control" required
                                       value="<?php echo htmlspecialchars($datos['post']['curso_academico'] ?? $datos['curso_actual']) ?>"
                                       placeholder="2024-2025">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 p-3 rounded" style="background:#fffbf0; border:1px solid #ffe082;">
                        <div class="fw-bold mb-1" style="color:#b8860b;">
                            <i class="fas fa-key me-1"></i>Código de acceso
                        </div>
                        <p class="mb-0 small text-muted">
                            Se generará automáticamente un código de 6 dígitos. Comunícalo a los alumnos del grupo.
                        </p>
                    </div>

                </div>

                <!-- ── Bloque OTROS TIPOS (anónimo, sin datos personales) ─ -->
                <div id="bloque_anonimo" style="display:<?php echo (!empty($datos['post']['id_tipo_encuesta']) && $datos['post']['id_tipo_encuesta'] != '1') ? 'block' : 'none' ?>;">
                    <div class="alert alert-info small mb-4">
                        <i class="fas fa-shield-alt me-1"></i>
                        <strong>Encuesta completamente anónima.</strong>
                        No se solicitará ningún dato personal a los participantes.
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Curso académico <span class="text-danger">*</span></label>
                            <input type="text" name="curso_academico_otro" id="curso_academico_otro"
                                   class="form-control"
                                   value="<?php echo htmlspecialchars($datos['post']['curso_academico'] ?? $datos['curso_actual']) ?>"
                                   placeholder="2024-2025">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Evaluación <small class="text-muted">(opcional)</small></label>
                            <select name="trimestre_otro" id="sel_trimestre_otro" class="form-select">
                                <option value="">— Sin evaluación —</option>
                                <?php foreach($datos['evaluaciones'] as $ev): ?>
                                <option value="<?php echo $ev->id_evaluacion ?>"
                                    <?php echo ($datos['evaluacion_actual'] && $datos['evaluacion_actual']->id_evaluacion == $ev->id_evaluacion) ? 'selected' : '' ?>>
                                    <?php echo htmlspecialchars($ev->evaluacion) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ── Campos comunes (título, descripción, fechas) ───── -->
                <div id="bloque_comun" style="display:<?php echo !empty($datos['post']['id_tipo_encuesta']) ? 'block' : 'none' ?>;">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Título de la encuesta <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" id="campo_titulo" class="form-control" required
                               placeholder="Se sugiere automáticamente o escríbelo aquí"
                               value="<?php echo htmlspecialchars($datos['post']['titulo'] ?? '') ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Descripción / instrucciones <small class="text-muted">(opcional)</small></label>
                        <textarea name="descripcion" class="form-control" rows="2"
                                  placeholder="Texto que verán los encuestados antes de responder..."><?php
                            echo htmlspecialchars($datos['post']['descripcion'] ?? '')
                        ?></textarea>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Fecha de inicio <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_inicio" class="form-control" required
                                   value="<?php echo $datos['post']['fecha_inicio'] ?? date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fecha de cierre <small class="text-muted">(opcional)</small></label>
                            <input type="date" name="fecha_fin" class="form-control"
                                   value="<?php echo $datos['post']['fecha_fin'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="alert alert-light border small mb-3">
                        <i class="fas fa-info-circle me-1 text-primary"></i>
                        Las preguntas se copiarán automáticamente desde la <strong>plantilla activa</strong>
                        del tipo seleccionado.
                    </div>

                    <!-- Campos cualitativos opcionales -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-comment-alt me-1"></i>Campos de comentarios
                        </label>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox"
                                   name="mostrar_mejor_peor" id="chk_mejor_peor"
                                   <?php echo !empty($datos['post']['mostrar_mejor_peor']) || empty($datos['post']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="chk_mejor_peor">
                                Mostrar <strong>Lo mejor</strong> y <strong>Lo peor</strong>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="mostrar_observaciones" id="chk_observaciones"
                                   <?php echo !empty($datos['post']['mostrar_observaciones']) || empty($datos['post']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="chk_observaciones">
                                Mostrar campo de <strong>Observaciones</strong>
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="<?php echo RUTA_URL ?>/encuestas" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-custom">
                            <i class="fas fa-save me-1"></i>Crear encuesta
                        </button>
                    </div>

                </div>

            </form>
        </div>
    </div>
</div>

<script>
const RUTA = '<?php echo RUTA_URL ?>';

function cambiarTipo(tipo){
    document.getElementById('bloque_alumnos').style.display = (tipo == '1') ? 'block' : 'none';
    document.getElementById('bloque_anonimo').style.display = (tipo && tipo != '1') ? 'block' : 'none';
    document.getElementById('bloque_comun').style.display   =  tipo ? 'block' : 'none';

    // Sincronizar curso_academico al enviar: usar hidden input
    sincronizarCurso(tipo);
    if(tipo == '1') actualizarTitulo();
}

function sincronizarCurso(tipo){
    // Eliminar hidden anterior si existe
    document.getElementById('hidden_curso')?.remove();
    document.getElementById('hidden_trimestre')?.remove();
    if(tipo && tipo != '1'){
        // Para tipos anónimos, al enviar usamos los campos _otro
        // Renombramos dinámicamente en submit para que el POST llegue bien
    }
}

// Al enviar el formulario: unificar curso_academico y trimestre según tipo
document.getElementById('form-nueva-encuesta').addEventListener('submit', function(e){
    const tipo = document.querySelector('input[name="id_tipo_encuesta"]:checked')?.value;
    if(tipo && tipo != '1'){
        // Copiar valores de bloque anónimo a campos que espera el modelo
        const ca = document.getElementById('curso_academico_otro').value;
        const tr = document.getElementById('sel_trimestre_otro').value;

        let hca = document.createElement('input');
        hca.type = 'hidden'; hca.name = 'curso_academico'; hca.value = ca; hca.id = 'hidden_curso';
        this.appendChild(hca);

        let htr = document.createElement('input');
        htr.type = 'hidden'; htr.name = 'trimestre'; htr.value = tr; htr.id = 'hidden_trimestre';
        this.appendChild(htr);
    }
});

function cargarCursos(id_ciclo){
    resetear('sel_curso',    '— Selecciona curso —');
    resetear('sel_modulo',   '— Selecciona primero el curso —');
    resetear('sel_profesor', '— Selecciona primero el módulo —');
    if(!id_ciclo) return;
    mostrarSpinner('spinner_curso');
    fetch(RUTA + '/encuestas/get_cursos/' + id_ciclo)
        .then(r => r.json())
        .then(data => {
            ocultarSpinner('spinner_curso');
            poblarSelect('sel_curso', data, 'id_curso', 'curso', '— Selecciona curso —');
        }).catch(() => ocultarSpinner('spinner_curso'));
}

function cargarModulos(id_curso){
    resetear('sel_modulo',   '— Selecciona módulo —');
    resetear('sel_profesor', '— Selecciona primero el módulo —');
    if(!id_curso) return;
    mostrarSpinner('spinner_modulo');
    fetch(RUTA + '/encuestas/get_modulos/' + id_curso)
        .then(r => r.json())
        .then(data => {
            ocultarSpinner('spinner_modulo');
            const opts = data.map(m => ({
                id: m.id_modulo,
                label: m.nombre_corto ? `${m.modulo} (${m.nombre_corto})` : m.modulo
            }));
            poblarSelectCustom('sel_modulo', opts, '— Selecciona módulo —');
            actualizarTitulo();
        }).catch(() => ocultarSpinner('spinner_modulo'));
}

function cargarProfesores(id_modulo){
    resetear('sel_profesor', '— Selecciona profesor —');
    if(!id_modulo) return;
    mostrarSpinner('spinner_profesor');
    fetch(RUTA + '/encuestas/get_profesores/' + id_modulo)
        .then(r => r.json())
        .then(data => {
            ocultarSpinner('spinner_profesor');
            poblarSelect('sel_profesor', data, 'id_profesor_modulo', 'nombre_completo', '— Selecciona profesor —');
            if(data.length === 1) document.getElementById('sel_profesor').value = data[0].id_profesor_modulo;
            actualizarTitulo();
        }).catch(() => ocultarSpinner('spinner_profesor'));
}

function actualizarTitulo(){
    const tipo = document.querySelector('input[name="id_tipo_encuesta"]:checked')?.value;
    if(tipo != '1') return;
    const mod   = textSeleccionado('sel_modulo')?.split('(')[0].trim();
    const prof  = textSeleccionado('sel_profesor');
    const trim  = textSeleccionado('sel_trimestre');
    const curso = document.querySelector('input[name="curso_academico"]')?.value || '';
    if(mod && mod !== '— Selecciona módulo —' && prof && prof !== '— Selecciona primero el módulo —'){
        const t = (trim && trim !== '— Selecciona —') ? trim + ' – ' : '';
        document.getElementById('campo_titulo').value = `Encuesta ${t}${mod} – ${prof} (${curso})`;
    }
}

function resetear(id, placeholder){
    const el = document.getElementById(id);
    if(el){ el.innerHTML = `<option value="">${placeholder}</option>`; el.disabled = true; }
}
function mostrarSpinner(id){ document.getElementById(id).style.display = 'block'; }
function ocultarSpinner(id){ document.getElementById(id).style.display = 'none'; }
function poblarSelect(id, data, valKey, labelKey, placeholder){
    const el = document.getElementById(id);
    el.innerHTML = `<option value="">${placeholder}</option>`;
    data.forEach(d => el.innerHTML += `<option value="${d[valKey]}">${d[labelKey]}</option>`);
    el.disabled = false;
}
function poblarSelectCustom(id, opts, placeholder){
    const el = document.getElementById(id);
    el.innerHTML = `<option value="">${placeholder}</option>`;
    opts.forEach(o => el.innerHTML += `<option value="${o.id}">${o.label}</option>`);
    el.disabled = false;
}
function textSeleccionado(id){
    const el = document.getElementById(id);
    return el?.selectedOptions[0]?.text;
}

document.addEventListener('DOMContentLoaded', function(){
    document.getElementById('sel_trimestre')?.addEventListener('change', actualizarTitulo);
    document.querySelector('input[name="curso_academico"]')?.addEventListener('input', actualizarTitulo);
    // Restaurar estado si hay post (error/duplicado)
    const checked = document.querySelector('input[name="id_tipo_encuesta"]:checked');
    if(checked) cambiarTipo(checked.value);
});
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
