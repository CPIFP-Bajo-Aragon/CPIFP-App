<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container-fluid px-4 py-4">

    <div class="row mb-3 align-items-center">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <i class="fas fa-user-graduate me-2"></i>Encuestas de alumnos
            </span>
        </div>
        <?php if($datos['usuarioSesion']->id_rol >= 200): ?>
        <div class="col-auto d-flex gap-2 flex-wrap">
            <a class="btn btn-outline-success" href="<?php echo RUTA_URL ?>/encuestas/generar_masiva">
                <i class="fas fa-magic me-1"></i>Generar masiva
            </a>
            <a class="btn btn-custom" href="<?php echo RUTA_URL ?>/encuestas/nueva">
                <i class="fas fa-plus me-1"></i>Nueva encuesta
            </a>
        </div>
        <?php endif; ?>
    </div>

    <?php
    if(session_status() === PHP_SESSION_NONE) session_start();
    if(!empty($_SESSION['msg_masiva'])):
        $msg = $_SESSION['msg_masiva'];
        unset($_SESSION['msg_masiva']);
    ?>
    <div class="alert alert-success alert-dismissible fade show">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <i class="fas fa-check-circle me-2"></i>
                <strong>Generación masiva completada</strong>
                — Evaluación: <strong><?php echo htmlspecialchars($msg['eval']) ?></strong>
                · Curso: <strong><?php echo htmlspecialchars($msg['curso']) ?></strong><br>
                Creadas: <strong><?php echo $msg['creadas'] ?></strong>
                · Omitidas: <strong><?php echo $msg['omitidas'] ?></strong>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php if(!empty($msg['codigos_grupo'])): ?>
        <hr class="my-2">
        <div class="fw-bold small mb-1"><i class="fas fa-key me-1"></i>Códigos de acceso por grupo:</div>
        <div class="row g-2">
            <?php foreach($msg['codigos_grupo'] as $info): ?>
            <div class="col-12 col-md-4 col-lg-3">
                <div class="border rounded bg-white px-3 py-2 small">
                    <div class="text-muted" style="font-size:.78rem;"><?php echo htmlspecialchars($info['ciclo']) ?></div>
                    <div class="fw-bold"><?php echo htmlspecialchars($info['curso']) ?></div>
                    <div style="font-family:monospace;font-size:1.4rem;font-weight:900;letter-spacing:.25em;color:#b8860b;">
                        <?php echo htmlspecialchars($info['codigo']) ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999;">
        <div id="toast-accion" class="toast align-items-center text-white border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body" id="toast-msg-txt"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <!-- ─── FILTROS ──────────────────────────────────────────────────── -->
    <?php if($datos['usuarioSesion']->id_rol >= 200): ?>
    <form method="get" action="<?php echo RUTA_URL ?>/encuestas/filtro" class="mb-3" id="form-filtro">
        <div class="row g-2 align-items-end">

            <!-- 1. Curso académico -->
            <div class="col-6 col-md-2">
                <label class="form-label form-label-sm mb-1 fw-bold">Curso académico</label>
                <select name="curso_academico" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <?php foreach($datos['cursos'] as $c): ?>
                    <option value="<?php echo $c->curso_academico ?>"
                        <?php echo (($datos['filtro']['curso_academico'] ?? '') == $c->curso_academico) ? 'selected' : '' ?>>
                        <?php echo htmlspecialchars($c->curso_academico) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 2. Departamento -->
            <div class="col-6 col-md-2">
                <label class="form-label form-label-sm mb-1">Departamento</label>
                <select name="id_departamento" id="filtro_dept" class="form-select form-select-sm"
                        onchange="cargarCiclosFiltro(this.value)">
                    <option value="">Todos</option>
                    <?php foreach($datos['departamentos'] as $d): ?>
                    <option value="<?php echo $d->id_departamento ?>"
                        <?php echo (($datos['filtro']['id_departamento'] ?? '') == $d->id_departamento) ? 'selected' : '' ?>>
                        <?php echo htmlspecialchars($d->departamento) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 3. Ciclo -->
            <div class="col-6 col-md-2">
                <label class="form-label form-label-sm mb-1">Ciclo</label>
                <select name="id_ciclo" id="filtro_ciclo" class="form-select form-select-sm"
                        onchange="cargarGruposFiltro(this.value)"
                        <?php echo empty($datos['filtro']['id_departamento']) ? 'disabled' : '' ?>>
                    <option value="">Todos</option>
                    <?php foreach($datos['ciclos_filtro'] ?? [] as $ci): ?>
                    <option value="<?php echo $ci->id_ciclo ?>"
                        <?php echo (($datos['filtro']['id_ciclo'] ?? '') == $ci->id_ciclo) ? 'selected' : '' ?>>
                        <?php echo htmlspecialchars($ci->ciclo) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 4. Grupo -->
            <div class="col-6 col-md-1">
                <label class="form-label form-label-sm mb-1">Grupo</label>
                <select name="id_curso" id="filtro_curso" class="form-select form-select-sm"
                        <?php echo empty($datos['filtro']['id_ciclo']) ? 'disabled' : '' ?>>
                    <option value="">Todos</option>
                    <?php foreach($datos['cursos_filtro'] ?? [] as $cu): ?>
                    <option value="<?php echo $cu->id_curso ?>"
                        <?php echo (($datos['filtro']['id_curso'] ?? '') == $cu->id_curso) ? 'selected' : '' ?>>
                        <?php echo htmlspecialchars($cu->curso) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 5. Estado -->
            <div class="col-6 col-md-1">
                <label class="form-label form-label-sm mb-1">Estado</label>
                <select name="activa" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <option value="1" <?php echo (($datos['filtro']['activa'] ?? '') === '1') ? 'selected' : '' ?>>Abiertas</option>
                    <option value="0" <?php echo (($datos['filtro']['activa'] ?? '') === '0') ? 'selected' : '' ?>>Cerradas</option>
                </select>
            </div>

            <!-- 6. Buscar -->
            <div class="col-12 col-md-2">
                <label class="form-label form-label-sm mb-1">Buscar</label>
                <div class="input-group input-group-sm">
                    <input type="text" name="buscar" class="form-control" placeholder="Título / profesor..."
                           value="<?php echo htmlspecialchars($datos['filtro']['buscar'] ?? '') ?>">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <div class="col-auto align-self-end">
                <a href="<?php echo RUTA_URL ?>/encuestas" class="btn btn-sm btn-outline-secondary" title="Limpiar">
                    <i class="fas fa-times"></i>
                </a>
            </div>

            <!-- Por página -->
            <div class="col-auto align-self-end ms-auto">
                <div class="d-flex align-items-center gap-2">
                    <label class="form-label form-label-sm mb-0 text-muted text-nowrap">Por página:</label>
                    <select name="tam_pagina" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                        <?php foreach([20=>'20', 50=>'50', 100=>'100', 0=>'Todas'] as $val=>$lbl): ?>
                        <option value="<?php echo $val ?>"
                            <?php echo (($datos['tamPagina'] ?? TAM_PAGINA) == $val) ? 'selected' : '' ?>>
                            <?php echo $lbl ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

        </div>
    </form>
    <?php endif; ?>

    <!-- Barra masiva -->
    <?php if($datos['usuarioSesion']->id_rol >= 200): ?>
    <div id="barra-masiva" class="alert alert-warning py-2 mb-2 d-none d-flex align-items-center gap-3">
        <span><i class="fas fa-check-square me-1"></i><strong id="cuenta-sel">0</strong> seleccionada(s)</span>
        <button class="btn btn-sm btn-danger" onclick="eliminarSeleccionadas()">
            <i class="fas fa-trash-alt me-1"></i>Eliminar seleccionadas
        </button>
        <button class="btn btn-sm btn-outline-secondary ms-auto" onclick="deseleccionarTodas()">Cancelar</button>
    </div>
    <?php endif; ?>

    <!-- ─── TABLA ─────────────────────────────────────────────────────── -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0" style="font-size:.87rem;">
                <thead style="background:#0583c3;color:#fff;">
                    <tr>
                        <?php if($datos['usuarioSesion']->id_rol >= 200): ?>
                        <th style="width:36px;">
                            <input type="checkbox" id="check-todos" class="form-check-input"
                                   onchange="toggleTodos(this)">
                        </th>
                        <?php endif; ?>
                        <th>Profesor</th>
                        <th>Módulo</th>
                        <th>Grupo / Ciclo</th>
                        <th>Dpto.</th>
                        <th>Evaluación</th>
                        <th>Código</th>
                        <th>Estado</th>
                        <th class="text-center">Resp.</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($datos['lista']->registros)): ?>
                    <tr>
                        <td colspan="<?php echo $datos['usuarioSesion']->id_rol >= 200 ? 10 : 9 ?>"
                            class="text-center text-muted py-4">
                            No se han encontrado encuestas.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($datos['lista']->registros as $enc): ?>
                    <tr id="fila-<?php echo $enc->id_encuesta ?>">
                        <?php if($datos['usuarioSesion']->id_rol >= 200): ?>
                        <td>
                            <?php if((int)$enc->total_respuestas === 0): ?>
                            <input type="checkbox" class="form-check-input check-enc"
                                   value="<?php echo $enc->id_encuesta ?>"
                                   onchange="actualizarBarraMasiva()">
                            <?php else: ?>
                            <i class="fas fa-lock text-muted" style="font-size:.7rem;"
                               title="Tiene respuestas"></i>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                        <td><?php echo htmlspecialchars($enc->nombre_profesor ?? '—') ?></td>
                        <td><?php echo htmlspecialchars($enc->nombre_modulo ?? '—') ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($enc->nombre_curso ?? '—') ?></strong>
                            <?php if($enc->nombre_ciclo): ?>
                            <br><small class="text-muted"><?php echo htmlspecialchars($enc->nombre_ciclo) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small class="text-muted"><?php echo htmlspecialchars($enc->departamento_corto ?? $enc->nombre_departamento ?? '—') ?></small>
                        </td>
                        <td>
                            <?php if(!empty($enc->nombre_evaluacion)): ?>
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($enc->nombre_evaluacion) ?></span>
                            <?php else: ?>—<?php endif; ?>
                        </td>
                        <td>
                            <?php if(!empty($enc->codigo_acceso)): ?>
                            <span style="font-family:monospace;font-weight:700;font-size:.9rem;
                                         letter-spacing:.15em;color:#b8860b;">
                                <?php echo htmlspecialchars($enc->codigo_acceso) ?>
                            </span>
                            <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                        </td>
                        <td>
                            <?php if($enc->activa): ?>
                            <span class="badge bg-success">Abierta</span>
                            <?php else: ?>
                            <span class="badge bg-danger">Cerrada</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?php echo (int)$enc->total_respuestas > 0 ? '' : 'bg-secondary' ?>"
                                  style="<?php echo (int)$enc->total_respuestas > 0 ? 'background:#0583c3;' : '' ?>">
                                <?php echo (int)$enc->total_respuestas ?>
                            </span>
                        </td>
                        <td class="text-nowrap">
                            <a href="<?php echo RUTA_URL ?>/encuestas/ver/<?php echo $enc->id_encuesta ?>"
                               class="btn btn-sm btn-outline-primary" title="Ver resultados">
                                <i class="fas fa-chart-bar"></i>
                            </a>
                            <?php if($datos['usuarioSesion']->id_rol >= 200): ?>
                            <a href="<?php echo RUTA_URL ?>/encuestas/editar/<?php echo $enc->id_encuesta ?>"
                               class="btn btn-sm btn-outline-secondary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if((int)$enc->total_respuestas === 0): ?>
                            <button class="btn btn-sm btn-outline-danger"
                                    onclick="confirmarEliminar(<?php echo $enc->id_encuesta ?>,'<?php echo addslashes($enc->titulo) ?>')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <?php else: ?>
                            <button class="btn btn-sm btn-outline-danger" disabled title="Con respuestas">
                                <i class="fas fa-lock"></i>
                            </button>
                            <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($datos['lista']->totalPaginas > 1): ?>
        <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
            <small class="text-muted">
                <?php
                $tp  = $datos['tamPagina'] ?? TAM_PAGINA;
                $tr  = $tp === 0 ? 999999 : $tp;
                $ini = $datos['paginaActual'] * $tr + 1;
                $fin = min(($datos['paginaActual'] + 1) * $tr, $datos['lista']->total);
                echo "Mostrando {$ini}–{$fin} de {$datos['lista']->total}";
                ?>
            </small>
            <nav><ul class="pagination pagination-sm mb-0">
                <?php for($i=0; $i < $datos['lista']->totalPaginas; $i++): ?>
                <li class="page-item <?php echo $i==$datos['paginaActual']?'active':'' ?>">
                    <a class="page-link"
                       href="<?php echo RUTA_URL ?>/encuestas/filtro/<?php echo $i ?>?<?php echo http_build_query($datos['filtro']??[]) ?>">
                        <?php echo $i+1 ?>
                    </a>
                </li>
                <?php endfor; ?>
            </ul></nav>
        </div>
        <?php else: ?>
        <div class="card-footer text-muted small"><?php echo $datos['lista']->total ?> encuesta(s)</div>
        <?php endif; ?>
    </div>

</div>

<!-- Modal borrado individual -->
<div class="modal fade" id="modal-eliminar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h6 class="modal-title"><i class="fas fa-trash-alt me-2 text-danger"></i>Eliminar</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Eliminar <strong id="modal-titulo"></strong>?</p>
                <p class="text-muted small mb-0">Solo posible si no tiene respuestas.</p>
            </div>
            <div class="modal-footer border-0">
                <button class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger btn-sm" id="btn-confirmar-eliminar">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal borrado masivo -->
<div class="modal fade" id="modal-masivo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h6 class="modal-title"><i class="fas fa-trash-alt me-2 text-danger"></i>Eliminar seleccionadas</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Eliminar las <strong id="modal-masivo-cuenta"></strong> encuestas seleccionadas?</p>
                <p class="text-muted small mb-0">Solo se eliminarán las que no tengan respuestas.</p>
            </div>
            <div class="modal-footer border-0">
                <button class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger btn-sm" id="btn-confirmar-masivo">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script>
const RUTA = '<?php echo RUTA_URL ?>';
let idEliminar = null;

function confirmarEliminar(id, titulo){
    idEliminar = id;
    document.getElementById('modal-titulo').textContent = titulo;
    new bootstrap.Modal(document.getElementById('modal-eliminar')).show();
}
document.getElementById('btn-confirmar-eliminar')?.addEventListener('click', function(){
    if(!idEliminar) return;
    bootstrap.Modal.getInstance(document.getElementById('modal-eliminar')).hide();
    peticionEliminar([idEliminar], data => {
        if(data.eliminadas > 0) document.getElementById('fila-'+idEliminar)?.remove();
        mostrarToast(data.msg, data.eliminadas > 0);
        idEliminar = null;
    });
});

function actualizarBarraMasiva(){
    const n = document.querySelectorAll('.check-enc:checked').length;
    document.getElementById('cuenta-sel').textContent = n;
    const b = document.getElementById('barra-masiva');
    b.classList.toggle('d-none', n===0);
    b.classList.toggle('d-flex', n>0);
}
function toggleTodos(cb){
    document.querySelectorAll('.check-enc').forEach(c => c.checked = cb.checked);
    actualizarBarraMasiva();
}
function deseleccionarTodas(){
    document.querySelectorAll('.check-enc').forEach(c => c.checked = false);
    const ct = document.getElementById('check-todos');
    if(ct) ct.checked = false;
    actualizarBarraMasiva();
}
function eliminarSeleccionadas(){
    const ids = [...document.querySelectorAll('.check-enc:checked')].map(c=>c.value);
    if(!ids.length) return;
    document.getElementById('modal-masivo-cuenta').textContent = ids.length;
    new bootstrap.Modal(document.getElementById('modal-masivo')).show();
}
document.getElementById('btn-confirmar-masivo')?.addEventListener('click', function(){
    const ids = [...document.querySelectorAll('.check-enc:checked')].map(c=>c.value);
    bootstrap.Modal.getInstance(document.getElementById('modal-masivo')).hide();
    peticionEliminar(ids, data => {
        ids.forEach(id => { if(data.eliminadas>0) document.getElementById('fila-'+id)?.remove(); });
        deseleccionarTodas();
        mostrarToast(data.msg, data.bloqueadas===0 && data.errores===0);
    });
});
function peticionEliminar(ids, cb){
    fetch(RUTA+'/encuestas/eliminar_masivo',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body: ids.map(id=>'ids[]='+id).join('&')
    }).then(r=>r.json()).then(cb);
}
function mostrarToast(msg, ok){
    const t = document.getElementById('toast-accion');
    document.getElementById('toast-msg-txt').textContent = msg;
    t.classList.remove('bg-success','bg-warning','bg-danger');
    t.classList.add(ok ? 'bg-success' : 'bg-warning');
    new bootstrap.Toast(t,{delay:5000}).show();
}

// AJAX: cargar ciclos del departamento seleccionado
function cargarCiclosFiltro(id_dept){
    const selCiclo  = document.getElementById('filtro_ciclo');
    const selGrupo  = document.getElementById('filtro_curso');
    selCiclo.innerHTML = '<option value="">Todos</option>';
    selGrupo.innerHTML = '<option value="">Todos</option>';
    selCiclo.disabled = true;
    selGrupo.disabled = true;
    if(!id_dept){ document.getElementById('form-filtro').submit(); return; }
    fetch(RUTA+'/encuestas/get_ciclos_filtro/'+id_dept)
        .then(r=>r.json())
        .then(data => {
            data.forEach(ci => selCiclo.innerHTML += `<option value="${ci.id_ciclo}">${ci.ciclo}</option>`);
            selCiclo.disabled = false;
            document.getElementById('form-filtro').submit();
        });
}

// AJAX: cargar grupos del ciclo seleccionado
function cargarGruposFiltro(id_ciclo){
    const selGrupo = document.getElementById('filtro_curso');
    selGrupo.innerHTML = '<option value="">Todos</option>';
    selGrupo.disabled = true;
    if(!id_ciclo){ document.getElementById('form-filtro').submit(); return; }
    fetch(RUTA+'/encuestas/get_cursos_filtro/'+id_ciclo)
        .then(r=>r.json())
        .then(data => {
            data.forEach(g => selGrupo.innerHTML += `<option value="${g.id_curso}">${g.curso}</option>`);
            selGrupo.disabled = false;
            document.getElementById('form-filtro').submit();
        });
}
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
