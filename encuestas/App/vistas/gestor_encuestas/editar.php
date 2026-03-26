<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<style>
.preg-fila { transition: background .15s; }
.preg-fila:hover { background: #f8fbff; }
.preg-drag { cursor: grab; color: #aaa; }
.bloque-fijo {
    background: linear-gradient(135deg, #f0f7ff 0%, #e8f4fd 100%);
    border: 1px solid #c8e0f5;
    border-radius: 8px;
}
</style>

<div class="container-fluid px-4 py-4">

    <!-- Cabecera -->
    <div class="row mb-3 align-items-center">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <i class="fas fa-edit me-2"></i>Editar encuesta
            </span>
        </div>
        <div class="col-auto d-flex gap-2">
            <a href="<?php echo RUTA_URL ?>/encuestas/ver/<?php echo $datos['encuesta']->id_encuesta ?>"
               class="btn btn-sm btn-outline-primary" title="Ver resultados">
                <i class="fas fa-chart-bar me-1"></i>Resultados
            </a>
            <a href="<?php echo RUTA_URL ?>/gestor_encuestas"
               class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    <div class="row g-4">

        <!-- ── Columna izquierda: ficha de la encuesta ─────────────── -->
        <div class="col-12 col-xl-5">

            <!-- Info de contexto (solo lectura) -->
            <?php if($datos['encuesta']->nombre_profesor || $datos['encuesta']->tipo_encuesta): ?>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body py-2 px-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="text-muted" style="font-size:.72rem;">TIPO</div>
                            <span class="badge"
                                  style="background:<?php echo $datos['encuesta']->id_tipo_encuesta == 1 ? '#0583c3' : '#6c757d' ?>">
                                <?php echo htmlspecialchars($datos['encuesta']->tipo_encuesta ?? '—') ?>
                            </span>
                        </div>
                        <?php if($datos['encuesta']->nombre_profesor): ?>
                        <div class="col-6">
                            <div class="text-muted" style="font-size:.72rem;">PROFESOR</div>
                            <div class="small fw-semibold"><?php echo htmlspecialchars($datos['encuesta']->nombre_profesor) ?></div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted" style="font-size:.72rem;">MÓDULO</div>
                            <div class="small"><?php echo htmlspecialchars($datos['encuesta']->nombre_modulo ?? '—') ?></div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted" style="font-size:.72rem;">GRUPO · CICLO</div>
                            <div class="small"><?php echo htmlspecialchars(($datos['encuesta']->nombre_curso ?? '—') . ' · ' . ($datos['encuesta']->nombre_ciclo ?? '')) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Formulario editable -->
            <div class="card border-0 shadow-sm">
                <div class="card-header fw-bold" style="background:#0583c3;color:#fff;">
                    <i class="fas fa-info-circle me-1"></i>Datos de la encuesta
                </div>
                <div class="card-body">
                    <form method="post"
                          action="<?php echo RUTA_URL ?>/gestor_encuestas/editar/<?php echo $datos['encuesta']->id_encuesta ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Título <span class="text-danger">*</span></label>
                            <input type="text" name="titulo" class="form-control" required
                                   value="<?php echo htmlspecialchars($datos['encuesta']->titulo) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción / instrucciones</label>
                            <textarea name="descripcion" class="form-control" rows="2"><?php
                                echo htmlspecialchars($datos['encuesta']->descripcion ?? '')
                            ?></textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold">Fecha inicio <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_inicio" class="form-control" required
                                       value="<?php echo $datos['encuesta']->fecha_inicio ?>">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Fecha cierre</label>
                                <input type="date" name="fecha_fin" class="form-control"
                                       value="<?php echo $datos['encuesta']->fecha_fin ?? '' ?>">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Evaluación</label>
                                <select name="trimestre" class="form-select">
                                    <option value="">— Sin evaluación —</option>
                                    <?php foreach($datos['evaluaciones'] as $ev): ?>
                                    <option value="<?php echo $ev->id_evaluacion ?>"
                                        <?php echo ($datos['encuesta']->trimestre == $ev->id_evaluacion) ? 'selected' : '' ?>>
                                        <?php echo htmlspecialchars($ev->evaluacion) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold">Estado</label>
                                <select name="activa" class="form-select">
                                    <option value="1" <?php echo $datos['encuesta']->activa ? 'selected' : '' ?>>Abierta</option>
                                    <option value="0" <?php echo !$datos['encuesta']->activa ? 'selected' : '' ?>>Cerrada</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-custom">
                                <i class="fas fa-save me-1"></i>Guardar datos
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bloque fijo: lo mejor / lo peor / observaciones -->
            <div class="bloque-fijo mt-3 p-3">
                <div class="fw-bold mb-2" style="color:#0583c3;">
                    <i class="fas fa-lock me-1"></i>Campos fijos incluidos en todas las encuestas
                </div>
                <p class="small text-muted mb-3">
                    Estos tres campos aparecen siempre al final del formulario de respuesta,
                    independientemente del tipo de encuesta. No es necesario añadirlos como preguntas.
                </p>
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success"><i class="fas fa-thumbs-up"></i></span>
                        <div>
                            <div class="fw-semibold small">Lo mejor</div>
                            <div class="text-muted" style="font-size:.78rem;">¿Qué es lo que más te ha gustado?</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger"><i class="fas fa-thumbs-down"></i></span>
                        <div>
                            <div class="fw-semibold small">Lo peor</div>
                            <div class="text-muted" style="font-size:.78rem;">¿Qué es lo que menos te ha gustado?</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-secondary"><i class="fas fa-comment"></i></span>
                        <div>
                            <div class="fw-semibold small">Observaciones</div>
                            <div class="text-muted" style="font-size:.78rem;">Comentarios libres y sugerencias.</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ── Columna derecha: CRUD de preguntas ──────────────────── -->
        <div class="col-12 col-xl-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center"
                     style="background:#0583c3; color:#fff; font-weight:bold;">
                    <span><i class="fas fa-question-circle me-1"></i>Preguntas de valoración</span>
                    <button class="btn btn-sm btn-light" onclick="mostrarFormNueva()">
                        <i class="fas fa-plus me-1"></i>Añadir pregunta
                    </button>
                </div>

                <!-- Aviso respuestas existentes -->
                <?php if((int)$datos['encuesta']->total_respuestas > 0): ?>
                <div class="alert alert-warning mb-0 rounded-0 small py-2 px-3">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Esta encuesta tiene <strong><?php echo $datos['encuesta']->total_respuestas ?> respuesta(s)</strong>.
                    No se pueden eliminar preguntas que ya tengan respuestas registradas.
                </div>
                <?php endif; ?>

                <!-- Formulario nueva pregunta (oculto) -->
                <div id="form-nueva" class="card-body border-bottom bg-light" style="display:none;">
                    <div class="row g-2 align-items-end">
                        <div class="col-auto">
                            <label class="form-label small mb-1">Orden</label>
                            <input type="number" id="nueva_orden" class="form-control form-control-sm"
                                   style="width:70px;"
                                   value="<?php echo count($datos['preguntas']) + 1 ?>" min="1">
                        </div>
                        <div class="col">
                            <label class="form-label small mb-1">Texto de la pregunta</label>
                            <input type="text" id="nueva_preg_txt" class="form-control form-control-sm"
                                   placeholder="Introduce la pregunta de valoración (puntuación 1-10)...">
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-custom btn-sm" onclick="addPregunta()">
                                <i class="fas fa-save me-1"></i>Guardar
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="ocultarFormNueva()">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabla de preguntas -->
                <div class="card-body p-0">
                    <table class="table table-hover mb-0" style="font-size:.88rem;">
                        <thead style="background:#e9f4fb;">
                            <tr>
                                <th style="width:65px;" class="text-center">Orden</th>
                                <th>Pregunta de valoración (1–10)</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="tbody-pregs">
                        <?php if(empty($datos['preguntas'])): ?>
                            <tr id="tr-vacio">
                                <td colspan="3" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox me-2"></i>No hay preguntas. Pulsa "Añadir pregunta" para empezar.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($datos['preguntas'] as $p): ?>
                            <tr class="preg-fila" id="tr-<?php echo $p->id_pregunta ?>">
                                <td class="text-center">
                                    <input type="number" class="form-control form-control-sm text-center"
                                           style="width:58px; margin:auto;"
                                           value="<?php echo $p->orden ?>" min="1"
                                           onchange="editPregunta(<?php echo $p->id_pregunta ?>, this.value, null)">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm border-0 bg-transparent"
                                           value="<?php echo htmlspecialchars($p->pregunta) ?>"
                                           onblur="editPregunta(<?php echo $p->id_pregunta ?>, null, this.value)"
                                           onfocus="this.classList.add('border')"
                                           onblur2="this.classList.remove('border')">
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-danger"
                                            onclick="delPregunta(<?php echo $p->id_pregunta ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Separador: campos fijos (no editables) -->
                <div class="card-body py-2 px-3" style="background:#f8fbff; border-top:2px dashed #c8e0f5;">
                    <div class="text-muted small fw-semibold mb-1">
                        <i class="fas fa-lock me-1"></i>Campos de texto libre (siempre presentes, no editables)
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-success py-2 px-3">
                            <i class="fas fa-thumbs-up me-1"></i>Lo mejor
                        </span>
                        <span class="badge bg-danger py-2 px-3">
                            <i class="fas fa-thumbs-down me-1"></i>Lo peor
                        </span>
                        <span class="badge bg-secondary py-2 px-3">
                            <i class="fas fa-comment me-1"></i>Observaciones
                        </span>
                    </div>
                </div>

            </div><!-- /card preguntas -->
        </div>

    </div><!-- /row -->
</div>

<!-- Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999;">
    <div id="toast-acc" class="toast align-items-center text-white border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="toast-txt"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>
const RUTA  = '<?php echo RUTA_URL ?>';
const ID_ENC = <?php echo $datos['encuesta']->id_encuesta ?>;

function mostrarFormNueva(){ document.getElementById('form-nueva').style.display='block'; }
function ocultarFormNueva(){ document.getElementById('form-nueva').style.display='none'; }

// ── Añadir pregunta ───────────────────────────────────────────────────────
function addPregunta(){
    const orden = document.getElementById('nueva_orden').value;
    const txt   = document.getElementById('nueva_preg_txt').value.trim();
    if(!txt){ toast('Introduce el texto de la pregunta.', false); return; }

    fetch(RUTA+'/gestor_encuestas/add_pregunta', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id_encuesta='+ID_ENC+'&orden='+orden+'&pregunta='+encodeURIComponent(txt)
    }).then(r=>r.json()).then(data => {
        if(data.ok){
            document.getElementById('tr-vacio')?.remove();
            const tbody = document.getElementById('tbody-pregs');
            tbody.insertAdjacentHTML('beforeend', `
            <tr class="preg-fila" id="tr-${data.id_pregunta}">
                <td class="text-center">
                    <input type="number" class="form-control form-control-sm text-center"
                           style="width:58px;margin:auto;" value="${orden}" min="1"
                           onchange="editPregunta(${data.id_pregunta}, this.value, null)">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm border-0 bg-transparent"
                           value="${txt.replace(/"/g,'&quot;')}"
                           onblur="editPregunta(${data.id_pregunta}, null, this.value)"
                           onfocus="this.classList.add('border')">
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-danger"
                            onclick="delPregunta(${data.id_pregunta})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>`);
            document.getElementById('nueva_preg_txt').value = '';
            document.getElementById('nueva_orden').value = parseInt(orden) + 1;
            ocultarFormNueva();
            toast('Pregunta añadida.', true);
        } else {
            toast('Error al añadir la pregunta.', false);
        }
    });
}

// ── Editar pregunta (orden o texto) ──────────────────────────────────────
function editPregunta(id, nuevoOrden, nuevoTxt){
    const fila  = document.getElementById('tr-'+id);
    const orden = nuevoOrden ?? fila.querySelector('input[type="number"]').value;
    const txt   = nuevoTxt   ?? fila.querySelector('input[type="text"]').value;

    fetch(RUTA+'/gestor_encuestas/edit_pregunta', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id_pregunta='+id+'&orden='+orden+'&pregunta='+encodeURIComponent(txt)
    }).then(r=>r.json()).then(data => {
        if(!data.ok) toast('Error al guardar.', false);
    });
}

// ── Eliminar pregunta ─────────────────────────────────────────────────────
function delPregunta(id){
    if(!confirm('¿Eliminar esta pregunta?')) return;
    fetch(RUTA+'/gestor_encuestas/del_pregunta', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id_pregunta='+id
    }).then(r=>r.json()).then(data => {
        if(data.ok){
            document.getElementById('tr-'+id)?.remove();
            if(!document.querySelector('#tbody-pregs tr.preg-fila')){
                document.getElementById('tbody-pregs').innerHTML = `
                <tr id="tr-vacio"><td colspan="3" class="text-center text-muted py-4">
                    <i class="fas fa-inbox me-2"></i>No hay preguntas.
                </td></tr>`;
            }
            toast('Pregunta eliminada.', true);
        } else {
            toast(data.msg || 'Error al eliminar.', false);
        }
    });
}

function toast(msg, ok){
    const t = document.getElementById('toast-acc');
    document.getElementById('toast-txt').textContent = msg;
    t.classList.remove('bg-success','bg-warning','bg-danger');
    t.classList.add(ok ? 'bg-success' : 'bg-warning');
    new bootstrap.Toast(t, {delay:3500}).show();
}
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
