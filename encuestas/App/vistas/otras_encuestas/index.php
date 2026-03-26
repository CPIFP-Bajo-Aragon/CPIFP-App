<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<?php
$filtro = $datos['filtro'] ?? [];
$f_curso  = $filtro['curso_academico']  ?? '';
$f_tipo   = $filtro['id_tipo_encuesta'] ?? '';
$f_eval   = $filtro['trimestre']        ?? '';
$f_estado = $filtro['activa']           ?? '';
$total_filtrado = $datos['lista']->total ?? 0;
$hay_filtro = ($f_tipo !== '' || $f_eval !== '' || $f_estado !== '' ||
               $f_curso !== cursoAcademicoActual());
?>

<style>
.tipo-card {
    cursor: pointer; border: 2px solid #dee2e6; border-radius: 10px;
    transition: all .15s; background: #fff;
}
.tipo-card:hover {
    border-color: #0583c3; background: #f0f8ff;
    transform: translateY(-2px); box-shadow: 0 4px 12px rgba(5,131,195,.15);
}
.tipo-card.seleccionado {
    border-color: #0583c3; background: #e3f2fd;
    box-shadow: 0 4px 16px rgba(5,131,195,.25);
}
.tipo-card.seleccionado .tipo-icono { background:#0583c3; color:#fff; }
.tipo-icono {
    width:48px; height:48px; border-radius:50%;
    background:#e9f4fb; color:#0583c3;
    display:flex; align-items:center; justify-content:center;
    font-size:1.25rem; transition:all .15s; flex-shrink:0;
}
#panel-formulario { animation: fadeIn .2s ease; }
@keyframes fadeIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:none; } }
.filtro-activo { border-color: #0583c3 !important; background: #f0f8ff !important; }
</style>

<div class="container-fluid px-4 py-4">

    <!-- Cabecera -->
    <div class="row mb-3 align-items-center">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <i class="fas fa-poll me-2"></i>Otras encuestas
            </span>
        </div>
        <?php if($datos['usuarioSesion']->id_rol >= 200 && !empty($datos['tipos'])): ?>
        <div class="col-auto">
            <button class="btn btn-custom" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapse-nueva"
                    aria-expanded="false">
                <i class="fas fa-plus me-2"></i>Nueva encuesta
            </button>
        </div>
        <?php endif; ?>
    </div>

    <!-- ═══════════════════════════════════════════════
         PANEL COLAPSABLE: crear nueva encuesta
    ════════════════════════════════════════════════════ -->
    <?php if($datos['usuarioSesion']->id_rol >= 200): ?>
    <div class="collapse mb-4" id="collapse-nueva">
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-bold d-flex justify-content-between align-items-center"
                 style="background:#0583c3;color:#fff;">
                <span><i class="fas fa-plus-circle me-2"></i>Nueva encuesta — selecciona el tipo</span>
                <button type="button" class="btn-close btn-close-white btn-sm"
                        data-bs-toggle="collapse" data-bs-target="#collapse-nueva"></button>
            </div>
            <div class="card-body">

                <?php if(empty($datos['tipos'])): ?>
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    No hay tipos de encuesta configurados.
                    <a href="<?php echo RUTA_URL ?>/gestor_encuestas" class="alert-link ms-1">Crea uno en Gestión →</a>
                </div>
                <?php else: ?>

                <!-- Tarjetas de tipo -->
                <div class="row g-3" id="grid-tipos">
                    <?php foreach($datos['tipos'] as $t): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="tipo-card p-3 h-100 d-flex align-items-center gap-3"
                             id="card-tipo-<?php echo $t->id_tipo_encuesta ?>"
                             onclick="seleccionarTipo(<?php echo $t->id_tipo_encuesta ?>,
                                      '<?php echo addslashes($t->tipo_encuesta) ?>')">
                            <div class="tipo-icono"><i class="fas fa-poll-h"></i></div>
                            <div style="min-width:0;">
                                <div class="fw-semibold text-truncate">
                                    <?php echo htmlspecialchars($t->tipo_encuesta) ?>
                                </div>
                                <?php if($t->descripcion): ?>
                                <div class="text-muted" style="font-size:.75rem;line-height:1.3;">
                                    <?php echo htmlspecialchars($t->descripcion) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Formulario (aparece al seleccionar tipo) -->
                <div id="panel-formulario" style="display:none;" class="mt-4 pt-3 border-top">

                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="tipo-icono" style="background:#0583c3;color:#fff;">
                            <i class="fas fa-poll-h"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Tipo seleccionado</div>
                            <div class="fw-bold fs-6" id="form-tipo-nombre">—</div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary ms-auto"
                                onclick="deseleccionar()">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                    </div>

                    <form method="post" action="<?php echo RUTA_URL ?>/otras_encuestas/nueva" id="form-nueva">
                        <input type="hidden" name="id_tipo_encuesta" id="input-tipo">
                        <div class="row g-3">

                            <div class="col-12">
                                <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
                                <input type="text" name="titulo" id="input-titulo" class="form-control" required
                                       placeholder="Título de la encuesta">
                                <div class="form-text"><i class="fas fa-magic me-1 text-primary"></i>Rellenado automáticamente. Puedes editarlo.</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Descripción / instrucciones <span class="text-muted small">(opcional)</span></label>
                                <textarea name="descripcion" class="form-control" rows="2"
                                          placeholder="Texto que verán los encuestados al abrir el formulario..."></textarea>
                            </div>

                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold">Curso académico <span class="text-danger">*</span></label>
                                <input type="text" name="curso_academico" class="form-control"
                                       value="<?php echo htmlspecialchars($datos['curso_actual']) ?>"
                                       placeholder="2024-2025" required pattern="\d{4}-\d{4}">
                            </div>

                            <div class="col-6 col-md-3">
                                <label class="form-label">Evaluación <span class="text-muted small">(opcional)</span></label>
                                <select name="trimestre" class="form-select">
                                    <option value="">— Sin evaluación —</option>
                                    <?php foreach($datos['evaluaciones'] as $ev): ?>
                                    <option value="<?php echo $ev->id_evaluacion ?>"
                                        <?php echo ($datos['evaluacion_actual'] &&
                                                    $datos['evaluacion_actual']->id_evaluacion == $ev->id_evaluacion) ? 'selected' : '' ?>>
                                        <?php echo htmlspecialchars($ev->evaluacion) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-6 col-md-3">
                                <label class="form-label">Fecha de apertura <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_inicio" class="form-control"
                                       value="<?php echo date('Y-m-d') ?>" required>
                            </div>

                            <div class="col-6 col-md-3">
                                <label class="form-label">Fecha de cierre <span class="text-muted small">(opcional)</span></label>
                                <input type="date" name="fecha_fin" class="form-control">
                            </div>

                        </div>

                        <div class="alert alert-light border small mt-3 mb-3">
                            <i class="fas fa-shield-alt me-1 text-primary"></i>
                            <strong>Encuesta anónima.</strong> No se solicitará ningún dato personal.
                            Las preguntas se copiarán automáticamente desde la plantilla del tipo.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small"><i class="fas fa-comment-alt me-1"></i>Campos de comentarios</label>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="mostrar_mejor_peor" id="chk_mejor_peor" checked>
                                <label class="form-check-label small" for="chk_mejor_peor">Mostrar <strong>Lo mejor</strong> y <strong>Lo peor</strong></label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="mostrar_observaciones" id="chk_observaciones" checked>
                                <label class="form-check-label small" for="chk_observaciones">Mostrar campo de <strong>Observaciones</strong></label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-custom px-4">
                                <i class="fas fa-save me-1"></i>Crear encuesta
                            </button>
                        </div>

                    </form>
                </div><!-- /panel-formulario -->
                <?php endif; ?>

            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ═══════════════════════════════════════════
         FILTROS
    ════════════════════════════════════════════════ -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="get" action="<?php echo RUTA_URL ?>/otras_encuestas"
                  class="row g-2 align-items-end">

                <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                    <label class="form-label form-label-sm mb-1 fw-semibold">Tipo</label>
                    <select name="tipo" class="form-select form-select-sm filtro-sel" onchange="this.form.submit()">
                        <option value="">Todos los tipos</option>
                        <?php foreach($datos['tipos'] as $t): ?>
                        <option value="<?php echo $t->id_tipo_encuesta ?>"
                                <?php echo ($f_tipo == $t->id_tipo_encuesta) ? 'selected' : '' ?>>
                            <?php echo htmlspecialchars($t->tipo_encuesta) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                    <label class="form-label form-label-sm mb-1 fw-semibold">Evaluación</label>
                    <select name="eval" class="form-select form-select-sm filtro-sel" onchange="this.form.submit()">
                        <option value="">Todas</option>
                        <option value="0" <?php echo ($f_eval === '0') ? 'selected' : '' ?>>Sin evaluación</option>
                        <?php foreach($datos['evaluaciones'] as $ev): ?>
                        <option value="<?php echo $ev->id_evaluacion ?>"
                                <?php echo ($f_eval == $ev->id_evaluacion) ? 'selected' : '' ?>>
                            <?php echo htmlspecialchars($ev->evaluacion) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-2 col-lg-2">
                    <label class="form-label form-label-sm mb-1 fw-semibold">Curso</label>
                    <input type="text" name="curso" class="form-control form-control-sm <?php echo ($f_curso !== cursoAcademicoActual()) ? 'filtro-activo' : '' ?>"
                           value="<?php echo htmlspecialchars($f_curso) ?>"
                           placeholder="2024-2025" pattern="\d{4}-\d{4}"
                           onchange="this.form.submit()">
                </div>

                <div class="col-12 col-sm-6 col-md-2 col-lg-2">
                    <label class="form-label form-label-sm mb-1 fw-semibold">Estado</label>
                    <select name="estado" class="form-select form-select-sm filtro-sel" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <option value="1" <?php echo ($f_estado === '1') ? 'selected' : '' ?>>Abiertas</option>
                        <option value="0" <?php echo ($f_estado === '0') ? 'selected' : '' ?>>Cerradas</option>
                    </select>
                </div>

                <div class="col-auto ms-auto">
                    <?php if($hay_filtro): ?>
                    <a href="<?php echo RUTA_URL ?>/otras_encuestas"
                       class="btn btn-sm btn-outline-secondary" title="Quitar filtros">
                        <i class="fas fa-times me-1"></i>Limpiar
                    </a>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-sm btn-outline-primary ms-1">
                        <i class="fas fa-search me-1"></i>Filtrar
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════
         LISTADO
    ════════════════════════════════════════════════ -->
    <div class="card border-0 shadow-sm">
        <div class="card-header fw-bold d-flex justify-content-between align-items-center"
             style="background:#0583c3;color:#fff;">
            <span><i class="fas fa-list me-2"></i>Encuestas</span>
            <span class="badge bg-light text-dark">
                <?php echo $total_filtrado ?>
                <?php echo $hay_filtro ? 'encontradas' : 'en este curso' ?>
            </span>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0" style="font-size:.87rem;">
                <thead style="background:#e9f4fb;">
                    <tr>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Evaluación</th>
                        <th>Curso</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Resp.</th>
                        <th style="width:110px;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-encuestas">
                <?php if(empty($datos['lista']->registros)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fas fa-<?php echo $hay_filtro ? 'filter' : 'inbox' ?> fa-2x mb-2 d-block"></i>
                            <?php echo $hay_filtro
                                ? 'No hay encuestas que coincidan con los filtros aplicados.'
                                : 'No hay encuestas creadas en este curso académico.' ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($datos['lista']->registros as $enc): ?>
                    <tr id="fila-<?php echo $enc->id_encuesta ?>">
                        <td class="fw-semibold"><?php echo htmlspecialchars($enc->titulo) ?></td>
                        <td>
                            <span class="badge bg-secondary">
                                <?php echo htmlspecialchars($enc->tipo_encuesta ?? '—') ?>
                            </span>
                        </td>
                        <td>
                            <?php echo $enc->nombre_evaluacion
                                ? htmlspecialchars($enc->nombre_evaluacion)
                                : '<span class="text-muted">—</span>' ?>
                        </td>
                        <td class="text-muted small"><?php echo htmlspecialchars($enc->curso_academico) ?></td>
                        <td class="text-center">
                            <?php if($enc->activa): ?>
                            <span class="badge bg-success" style="cursor:pointer;" title="Clic para cerrar"
                                  onclick="toggleEstado(<?php echo $enc->id_encuesta ?>, 0, this)">Abierta</span>
                            <?php else: ?>
                            <span class="badge bg-danger" style="cursor:pointer;" title="Clic para abrir"
                                  onclick="toggleEstado(<?php echo $enc->id_encuesta ?>, 1, this)">Cerrada</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?php echo (int)$enc->total_respuestas > 0 ? '' : 'bg-secondary' ?>"
                                  style="<?php echo (int)$enc->total_respuestas > 0 ? 'background:#0583c3;' : '' ?>">
                                <?php echo (int)$enc->total_respuestas ?>
                            </span>
                        </td>
                        <td class="text-nowrap">
                            <a href="<?php echo RUTA_URL ?>/otras_encuestas/ver/<?php echo $enc->id_encuesta ?>"
                               class="btn btn-sm btn-outline-primary" title="Ver resultados">
                                <i class="fas fa-chart-bar"></i>
                            </a>
                            <?php if($datos['usuarioSesion']->id_rol >= 200): ?>
                            <button class="btn btn-sm btn-outline-danger"
                                    title="<?php echo (int)$enc->total_respuestas > 0 ? 'No se puede eliminar: tiene respuestas' : 'Eliminar' ?>"
                                    <?php echo (int)$enc->total_respuestas > 0 ? 'disabled' : '' ?>
                                    onclick="confirmarEliminar(<?php echo $enc->id_encuesta ?>, '<?php echo addslashes($enc->titulo) ?>')">
                                <i class="fas fa-<?php echo (int)$enc->total_respuestas > 0 ? 'lock' : 'trash' ?>"></i>
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal eliminar -->
<div class="modal fade" id="modal-del" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h6 class="modal-title text-danger"><i class="fas fa-trash me-2"></i>Eliminar encuesta</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">¿Eliminar <strong id="del-titulo"></strong>?</p>
                <p class="text-muted small mb-0">Solo es posible si no tiene respuestas.</p>
            </div>
            <div class="modal-footer border-0">
                <button class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger btn-sm" id="btn-del-confirm">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999;">
    <div id="oe-toast" class="toast text-white border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="oe-toast-msg"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>
const RUTA = '<?php echo RUTA_URL ?>';
let delId  = null;

// Resaltar filtros activos
document.querySelectorAll('.filtro-sel').forEach(function(el){
    if(el.value !== '') el.classList.add('filtro-activo');
    el.addEventListener('change', function(){ this.classList.toggle('filtro-activo', this.value !== ''); });
});

// Cerrar panel nueva al colapsar
document.getElementById('collapse-nueva')?.addEventListener('hide.bs.collapse', function(){ deseleccionar(); });

function seleccionarTipo(id, nombre){
    document.querySelectorAll('.tipo-card').forEach(c => c.classList.remove('seleccionado'));
    document.getElementById('card-tipo-'+id)?.classList.add('seleccionado');
    document.getElementById('input-tipo').value             = id;
    document.getElementById('input-titulo').value           = nombre;
    document.getElementById('form-tipo-nombre').textContent = nombre;
    const panel = document.getElementById('panel-formulario');
    panel.style.display = 'block';
    panel.scrollIntoView({behavior:'smooth', block:'nearest'});
}

function deseleccionar(){
    document.querySelectorAll('.tipo-card').forEach(c => c.classList.remove('seleccionado'));
    document.getElementById('panel-formulario').style.display = 'none';
    document.getElementById('input-tipo').value   = '';
    document.getElementById('input-titulo').value = '';
}

function toggleEstado(id, nuevoEstado, el){
    fetch(RUTA+'/otras_encuestas/'+(nuevoEstado?'abrir':'cerrar'), {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id_encuesta='+id
    }).then(r=>r.json()).then(ok => {
        if(!ok){ toast('Error al cambiar el estado', false); return; }
        if(nuevoEstado){
            el.className='badge bg-success'; el.textContent='Abierta';
            el.title='Clic para cerrar'; el.setAttribute('onclick',`toggleEstado(${id},0,this)`);
        } else {
            el.className='badge bg-danger'; el.textContent='Cerrada';
            el.title='Clic para abrir'; el.setAttribute('onclick',`toggleEstado(${id},1,this)`);
        }
    });
}

function confirmarEliminar(id, titulo){
    delId = id;
    document.getElementById('del-titulo').textContent = titulo;
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-del')).show();
}

document.getElementById('btn-del-confirm').addEventListener('click', function(){
    if(!delId) return;
    bootstrap.Modal.getInstance(document.getElementById('modal-del')).hide();
    fetch(RUTA+'/otras_encuestas/eliminar', {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id_encuesta='+delId
    }).then(r=>r.json()).then(data => {
        toast(data.msg, data.ok);
        if(data.ok) document.getElementById('fila-'+delId)?.remove();
        delId = null;
    });
});

function toast(msg, ok=true){
    const el = document.getElementById('oe-toast');
    document.getElementById('oe-toast-msg').textContent = msg;
    el.classList.remove('bg-success','bg-warning');
    el.classList.add(ok?'bg-success':'bg-warning');
    bootstrap.Toast.getOrCreateInstance(el,{delay:4000}).show();
}
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
