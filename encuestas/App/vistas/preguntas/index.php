<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<style>
.tipo-card { cursor:pointer; border:2px solid transparent; transition:all .15s; }
.tipo-card:hover  { border-color:#0583c3; background:#f0f8ff; }
.tipo-card.activo { border-color:#0583c3; background:#e8f4fd; }
.tipo-card.bloqueado { opacity:.7; cursor:default; }
</style>

<div class="container-fluid px-4 py-4">

    <div class="row mb-3 align-items-center">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <i class="fas fa-cog me-2"></i>Tipos de encuesta y plantillas de preguntas
            </span>
        </div>
    </div>

    <div class="alert alert-info small mb-3">
        <i class="fas fa-info-circle me-1"></i>
        Desde aquí puedes crear nuevos tipos de encuesta (profesores, padres, empresas…) y editar
        las preguntas de su plantilla. Las preguntas se copian automáticamente cuando se crea una
        encuesta de ese tipo. Los cambios en la plantilla <strong>no afectan</strong> a encuestas ya creadas.
    </div>

    <div class="row g-4">

        <!-- ── Columna izquierda: lista de tipos ─────────────────────── -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center"
                     style="background:#0583c3; color:#fff; font-weight:bold;">
                    <span><i class="fas fa-tags me-1"></i>Tipos de encuesta</span>
                    <button class="btn btn-sm btn-light fw-bold" onclick="mostrarFormNuevoTipo()">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>

                <!-- Formulario nuevo tipo (oculto) -->
                <div id="form-nuevo-tipo" class="card-body border-bottom bg-light" style="display:none;">
                    <p class="fw-bold small mb-2"><i class="fas fa-plus me-1"></i>Nuevo tipo de encuesta</p>
                    <div class="mb-2">
                        <input type="text" id="nuevo_tipo_nombre" class="form-control form-control-sm"
                               placeholder="Nombre del tipo (ej: Encuesta de padres)">
                    </div>
                    <div class="mb-2">
                        <textarea id="nuevo_tipo_desc" class="form-control form-control-sm" rows="2"
                                  placeholder="Descripción (opcional)"></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-custom btn-sm" onclick="addTipo()">
                            <i class="fas fa-save me-1"></i>Crear
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="ocultarFormNuevoTipo()">
                            Cancelar
                        </button>
                    </div>
                </div>

                <div class="list-group list-group-flush" id="lista-tipos">
                    <?php foreach($datos['tipos'] as $tipo): ?>
                    <div class="list-group-item tipo-card px-3 py-2
                                <?php echo $tipo->id_tipo_encuesta == $datos['tipo_sel'] ? 'activo' : '' ?>
                                <?php echo $tipo->id_tipo_encuesta == 1 ? 'bloqueado' : '' ?>"
                         id="tipo-item-<?php echo $tipo->id_tipo_encuesta ?>"
                         onclick="seleccionarTipo(<?php echo $tipo->id_tipo_encuesta ?>)">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="fw-bold" id="tipo-nombre-<?php echo $tipo->id_tipo_encuesta ?>">
                                    <?php echo htmlspecialchars($tipo->tipo_encuesta) ?>
                                </div>
                                <div class="text-muted small">
                                    <?php echo $tipo->total_preguntas ?> pregunta(s)
                                    <?php if($tipo->id_tipo_encuesta == 1): ?>
                                    <span class="badge bg-secondary ms-1">Sistema</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if($tipo->id_tipo_encuesta != 1): ?>
                            <div class="d-flex gap-1" onclick="event.stopPropagation()">
                                <button class="btn btn-xs btn-outline-secondary"
                                        title="Editar nombre"
                                        onclick="editarTipo(<?php echo $tipo->id_tipo_encuesta ?>, '<?php echo addslashes($tipo->tipo_encuesta) ?>', '<?php echo addslashes($tipo->descripcion ?? '') ?>')">
                                    <i class="fas fa-edit" style="font-size:.7rem;"></i>
                                </button>
                                <button class="btn btn-xs btn-outline-danger"
                                        title="Eliminar tipo"
                                        onclick="delTipo(<?php echo $tipo->id_tipo_encuesta ?>, '<?php echo addslashes($tipo->tipo_encuesta) ?>')">
                                    <i class="fas fa-trash" style="font-size:.7rem;"></i>
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- ── Columna derecha: plantilla de preguntas ───────────────── -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center"
                     style="background:#0583c3; color:#fff; font-weight:bold;">
                    <span>
                        <i class="fas fa-question-circle me-1"></i>
                        Plantilla: <span id="titulo-tipo"><?php echo htmlspecialchars($datos['tipo_info']->tipo_encuesta ?? '') ?></span>
                    </span>
                    <button class="btn btn-sm btn-light" onclick="mostrarFormNuevaPregunta()">
                        <i class="fas fa-plus me-1"></i>Añadir pregunta
                    </button>
                </div>

                <!-- Formulario nueva pregunta (oculto) -->
                <div id="form-nueva-preg" class="card-body border-bottom bg-light" style="display:none;">
                    <div class="row g-2 align-items-end">
                        <div class="col-auto">
                            <label class="form-label small mb-1">Orden</label>
                            <input type="number" id="nueva_orden" class="form-control form-control-sm"
                                   style="width:70px;" value="<?php echo count($datos['preguntas']) + 1 ?>" min="1">
                        </div>
                        <div class="col">
                            <label class="form-label small mb-1">Texto de la pregunta</label>
                            <input type="text" id="nueva_pregunta" class="form-control form-control-sm"
                                   placeholder="Introduce el texto de la pregunta...">
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-custom btn-sm" onclick="addPregunta()">
                                <i class="fas fa-save me-1"></i>Guardar
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="ocultarFormNuevaPregunta()">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <table class="table table-hover mb-0" id="tabla-preguntas">
                        <thead style="background:#e9f4fb;">
                            <tr>
                                <th style="width:70px;" class="text-center">Orden</th>
                                <th>Pregunta</th>
                                <th style="width:80px;" class="text-center">Activa</th>
                                <th style="width:60px;"></th>
                            </tr>
                        </thead>
                        <tbody id="tbody-preguntas">
                        <?php if(empty($datos['preguntas'])): ?>
                            <tr id="fila-vacia">
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox me-2"></i>
                                    Este tipo no tiene preguntas en su plantilla aún.
                                    <br><small>Pulsa "Añadir pregunta" para empezar.</small>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($datos['preguntas'] as $p): ?>
                            <?php include __DIR__ . '/fila_pregunta.php'; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div><!-- /row -->
</div>

<!-- Modal editar tipo -->
<div class="modal fade" id="modal-editar-tipo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h6 class="modal-title"><i class="fas fa-edit me-2"></i>Editar tipo de encuesta</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_tipo_id">
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" id="edit_tipo_nombre" class="form-control">
                </div>
                <div class="mb-0">
                    <label class="form-label">Descripción</label>
                    <textarea id="edit_tipo_desc" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-custom btn-sm" onclick="guardarEditTipo()">
                    <i class="fas fa-save me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.btn-xs { padding:.15rem .4rem; font-size:.7rem; }
</style>

<script>
const RUTA     = '<?php echo RUTA_URL ?>';
let tipoActual = <?php echo $datos['tipo_sel'] ?>;

// ── Seleccionar tipo: recargar plantilla por AJAX ─────────────────────────
function seleccionarTipo(id){
    tipoActual = id;
    document.querySelectorAll('.tipo-card').forEach(c => c.classList.remove('activo'));
    const card = document.getElementById('tipo-item-'+id);
    if(card) card.classList.add('activo');

    fetch(RUTA + '/preguntas/get_preguntas?id_tipo_encuesta=' + id)
        .then(r => r.json())
        .then(data => {
            document.getElementById('titulo-tipo').textContent = data.tipo;
            document.getElementById('nueva_orden').value = data.preguntas.length + 1;

            const tbody = document.getElementById('tbody-preguntas');
            if(!data.preguntas.length){
                tbody.innerHTML = `<tr id="fila-vacia"><td colspan="4" class="text-center text-muted py-4">
                    <i class="fas fa-inbox me-2"></i>Sin preguntas aún. Pulsa "Añadir pregunta" para empezar.
                </td></tr>`;
                return;
            }
            tbody.innerHTML = data.preguntas.map(p => `
            <tr id="fila_${p.id_plantilla_pregunta}">
                <td class="text-center">
                    <input type="number" class="form-control form-control-sm text-center" style="width:65px;"
                           value="${p.orden}" min="1"
                           onchange="editPregunta(${p.id_plantilla_pregunta}, 'orden', this.value)">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm"
                           value="${p.pregunta.replace(/"/g,'&quot;')}"
                           onblur="editPregunta(${p.id_plantilla_pregunta}, 'pregunta', this.value)">
                </td>
                <td class="text-center">
                    <div class="form-check form-switch d-flex justify-content-center">
                        <input class="form-check-input" type="checkbox" ${p.activo ? 'checked' : ''}
                               onchange="editPregunta(${p.id_plantilla_pregunta}, 'activo', this.checked ? 1 : 0)">
                    </div>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-danger"
                            onclick="delPregunta(${p.id_plantilla_pregunta})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>`).join('');
        });
}

// ── CRUD de tipos ─────────────────────────────────────────────────────────
function mostrarFormNuevoTipo(){ document.getElementById('form-nuevo-tipo').style.display='block'; }
function ocultarFormNuevoTipo(){ document.getElementById('form-nuevo-tipo').style.display='none'; }

function addTipo(){
    const nombre = document.getElementById('nuevo_tipo_nombre').value.trim();
    const desc   = document.getElementById('nuevo_tipo_desc').value.trim();
    if(!nombre){ alert('Introduce el nombre del tipo.'); return; }

    fetch(RUTA+'/preguntas/add_tipo', {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'tipo_encuesta='+encodeURIComponent(nombre)+'&descripcion='+encodeURIComponent(desc)
    }).then(r=>r.json()).then(data => {
        if(data.ok){
            // Añadir a la lista sin recargar
            const li = document.createElement('div');
            li.className = 'list-group-item tipo-card px-3 py-2';
            li.id = 'tipo-item-'+data.id;
            li.onclick = () => seleccionarTipo(data.id);
            li.innerHTML = `
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="fw-bold" id="tipo-nombre-${data.id}">${nombre}</div>
                    <div class="text-muted small">0 pregunta(s)</div>
                </div>
                <div class="d-flex gap-1" onclick="event.stopPropagation()">
                    <button class="btn btn-xs btn-outline-secondary"
                            onclick="editarTipo(${data.id}, '${nombre.replace(/'/g,"\\'")}', '${desc.replace(/'/g,"\\'")}')">
                        <i class="fas fa-edit" style="font-size:.7rem;"></i>
                    </button>
                    <button class="btn btn-xs btn-outline-danger"
                            onclick="delTipo(${data.id}, '${nombre.replace(/'/g,"\\'")}')">
                        <i class="fas fa-trash" style="font-size:.7rem;"></i>
                    </button>
                </div>
            </div>`;
            document.getElementById('lista-tipos').appendChild(li);
            document.getElementById('nuevo_tipo_nombre').value = '';
            document.getElementById('nuevo_tipo_desc').value   = '';
            ocultarFormNuevoTipo();
            seleccionarTipo(data.id);
        } else {
            alert('Error al crear el tipo.');
        }
    });
}

function editarTipo(id, nombre, desc){
    document.getElementById('edit_tipo_id').value    = id;
    document.getElementById('edit_tipo_nombre').value = nombre;
    document.getElementById('edit_tipo_desc').value   = desc;
    new bootstrap.Modal(document.getElementById('modal-editar-tipo')).show();
}

function guardarEditTipo(){
    const id     = document.getElementById('edit_tipo_id').value;
    const nombre = document.getElementById('edit_tipo_nombre').value.trim();
    const desc   = document.getElementById('edit_tipo_desc').value.trim();
    if(!nombre){ alert('El nombre no puede estar vacío.'); return; }

    fetch(RUTA+'/preguntas/edit_tipo', {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id_tipo_encuesta='+id+'&tipo_encuesta='+encodeURIComponent(nombre)+'&descripcion='+encodeURIComponent(desc)
    }).then(r=>r.json()).then(data => {
        if(data.ok){
            const el = document.getElementById('tipo-nombre-'+id);
            if(el) el.textContent = nombre;
            if(parseInt(id) === tipoActual)
                document.getElementById('titulo-tipo').textContent = nombre;
            bootstrap.Modal.getInstance(document.getElementById('modal-editar-tipo')).hide();
        } else {
            alert('Error al guardar.');
        }
    });
}

function delTipo(id, nombre){
    if(!confirm(`¿Eliminar el tipo "${nombre}"? También se borrarán sus preguntas de plantilla.`)) return;
    fetch(RUTA+'/preguntas/del_tipo', {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id_tipo_encuesta='+id
    }).then(r=>r.json()).then(data => {
        if(data.ok){
            document.getElementById('tipo-item-'+id)?.remove();
            if(parseInt(id) === tipoActual) seleccionarTipo(1);
        } else {
            alert(data.msg);
        }
    });
}

// ── CRUD de preguntas ─────────────────────────────────────────────────────
function mostrarFormNuevaPregunta(){ document.getElementById('form-nueva-preg').style.display='block'; }
function ocultarFormNuevaPregunta(){ document.getElementById('form-nueva-preg').style.display='none'; }

function editPregunta(id, campo, valor){
    const fila     = document.getElementById('fila_'+id);
    const ordenVal = fila.querySelector('input[type="number"]').value;
    const pregVal  = fila.querySelector('input[type="text"]').value;
    const activoVal= fila.querySelector('input[type="checkbox"]').checked ? 1 : 0;

    fetch(RUTA+'/preguntas/edit', {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id_plantilla_pregunta='+id+'&orden='+ordenVal
             +'&pregunta='+encodeURIComponent(pregVal)+'&activo='+activoVal
    }).then(r=>r.json()).then(ok => { if(!ok) alert('Error al guardar'); });
}

function delPregunta(id){
    if(!confirm('¿Eliminar esta pregunta de la plantilla?')) return;
    fetch(RUTA+'/preguntas/del', {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id_plantilla_pregunta='+id
    }).then(r=>r.json()).then(ok => {
        if(ok){ document.getElementById('fila_'+id)?.remove(); }
        else alert('Error al eliminar');
    });
}

function addPregunta(){
    const orden    = document.getElementById('nueva_orden').value;
    const pregunta = document.getElementById('nueva_pregunta').value.trim();
    if(!pregunta){ alert('Introduce el texto de la pregunta.'); return; }

    fetch(RUTA+'/preguntas/add', {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id_tipo_encuesta='+tipoActual+'&orden='+orden+'&pregunta='+encodeURIComponent(pregunta)
    }).then(r=>r.json()).then(ok => {
        if(ok){
            // Recargar el tipo para que aparezca la nueva fila con su id real
            seleccionarTipo(tipoActual);
            document.getElementById('nueva_pregunta').value = '';
            ocultarFormNuevaPregunta();
        } else {
            alert('Error al añadir la pregunta');
        }
    });
}
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
