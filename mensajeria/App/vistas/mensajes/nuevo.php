<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container-fluid px-4 py-4">

    <div class="row mb-4">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <span><i class="fas fa-pen me-2"></i>Nuevo mensaje</span>
            </span>
        </div>
    </div>

    <?php if (isset($datos['error'])): ?>
    <div class="alert alert-danger mb-4">
        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $datos['error'] ?>
    </div>
    <?php endif ?>

    <form method="POST" action="<?php echo RUTA_URL ?>/Mensajes/enviar"
          enctype="multipart/form-data">

        <!-- Tipo de envio -->
        <div class="row g-3 mb-3">
            <div class="col-12">
                <div class="input-group">
                    <label class="input-group-text">Enviar a</label>
                    <div class="form-control d-flex gap-4 flex-wrap align-items-center"
                         style="height:auto; padding:10px 14px">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="radio" name="tipo_destino"
                                   value="individual" id="tipo_ind" checked
                                   onchange="cambiarTipo('individual')">
                            <label class="form-check-label" for="tipo_ind">Usuarios seleccionados</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="radio" name="tipo_destino"
                                   value="departamento" id="tipo_dep"
                                   onchange="cambiarTipo('departamento')">
                            <label class="form-check-label" for="tipo_dep">Todo un departamento</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="radio" name="tipo_destino"
                                   value="todos" id="tipo_todos"
                                   onchange="cambiarTipo('todos')">
                            <label class="form-check-label" for="tipo_todos">
                                <strong>Todos los usuarios activos</strong>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Destinatarios individuales -->
        <div id="bloque-individual" class="row g-3 mb-3">
            <div class="col-12">
                <div class="input-group mb-2">
                    <label class="input-group-text">
                        <i class="fas fa-search"></i>
                    </label>
                    <input type="text" id="buscador-dest" class="form-control"
                           placeholder="Filtrar por nombre..."
                           oninput="filtrarDestinatarios(this.value)">
                    <button type="button" class="btn btn-custom btn-sm"
                            onclick="selTodos(true)">Todos</button>
                    <button type="button" class="btn btn-custom btn-sm"
                            onclick="selTodos(false)">Ninguno</button>
                </div>
                <div id="lista-destinatarios"
                     style="max-height:200px; overflow-y:auto; border:1px solid #dee2e6;
                            border-radius:4px; padding:10px; background:#fff;">
                    <?php foreach ($datos['profesores'] as $p): ?>
                    <?php if ($p->id_profesor == $datos['usuarioSesion']->id_profesor) continue ?>
                    <label style="display:block; padding:3px 0; cursor:pointer">
                        <input type="checkbox" name="destinatarios[]"
                               value="<?php echo $p->id_profesor ?>"
                               <?php
                                 $destPost = isset($datos['post']['destinatarios'])
                                             ? (array)$datos['post']['destinatarios'] : [];
                                 $respDest = $_GET['dest'] ?? '';
                                 if (in_array($p->id_profesor, $destPost)
                                     || $p->id_profesor == $respDest) echo 'checked';
                               ?>>
                        <?php echo htmlspecialchars($p->nombre_completo) ?>
                    </label>
                    <?php endforeach ?>
                </div>
            </div>
        </div>

        <!-- Destinatarios por departamento -->
        <div id="bloque-departamento" class="row g-3 mb-3" style="display:none">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <label class="input-group-text">Departamento</label>
                    <select name="id_departamento" id="sel-dep" class="form-control"
                            onchange="cargarMiembrosDep(this.value)">
                        <option value="">-- Selecciona --</option>
                        <?php foreach ($datos['departamentos'] as $d): ?>
                        <option value="<?php echo $d->id_departamento ?>"
                            <?php if (isset($datos['post']['id_departamento'])
                                && $datos['post']['id_departamento'] == $d->id_departamento) echo 'selected' ?>>
                            <?php echo htmlspecialchars($d->departamento) ?>
                        </option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="col-12 col-md-7">
                <div id="lista-dep-miembros" style="display:none">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Desmarca los miembros que quieras <strong>excluir</strong>:
                        </small>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-custom btn-sm"
                                    onclick="selDep(true)">Todos</button>
                            <button type="button" class="btn btn-custom btn-sm"
                                    onclick="selDep(false)">Ninguno</button>
                        </div>
                    </div>
                    <div id="checks-dep"
                         style="max-height:180px; overflow-y:auto; border:1px solid #dee2e6;
                                border-radius:4px; padding:10px; background:#fff;">
                    </div>
                    <small id="conteo-dep" class="text-muted mt-1 d-block"></small>
                </div>
                <div id="dep-cargando" style="display:none; color:#0583c3; font-size:.88rem; padding-top:6px">
                    <i class="fas fa-spinner fa-spin me-1"></i>Cargando miembros...
                </div>
            </div>
        </div>

        <!-- Bloque todos -->
        <div id="bloque-todos" class="row g-3 mb-3" style="display:none">
            <div class="col-12">
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-bullhorn me-2"></i>
                    El mensaje se enviara a <strong>todos los usuarios activos</strong>
                    del sistema y a sus correos electronicos.
                </div>
            </div>
        </div>

        <!-- Asunto -->
        <div class="row g-3 mb-3">
            <div class="col-12">
                <div class="input-group">
                    <label class="input-group-text">Asunto</label>
                    <input type="text" name="asunto" class="form-control" required maxlength="200"
                           placeholder="Asunto del mensaje..."
                           value="<?php echo htmlspecialchars($_GET['asunto'] ?? $datos['post']['asunto'] ?? '') ?>">
                </div>
            </div>
        </div>

        <!-- Cuerpo -->
        <div class="row g-3 mb-3">
            <div class="col-12">
                <textarea name="cuerpo" class="form-control" rows="7" required
                          placeholder="Escribe tu mensaje aqui..."><?php echo htmlspecialchars($datos['post']['cuerpo'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Adjuntos -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="input-group">
                    <label class="input-group-text">
                        <i class="fas fa-paperclip me-1"></i>Adjuntos
                    </label>
                    <input type="file" name="adjuntos[]" multiple class="form-control">
                </div>
                <small class="text-muted ms-1">
                    Max. <?php echo $datos['config']['max_tam_adjunto_mb']->valor ?? 10 ?> MB por archivo.
                    Extensiones: <?php echo htmlspecialchars($datos['config']['extensiones_permitidas']->valor ?? '') ?>
                </small>
            </div>
        </div>

        <!-- Botones -->
        <div class="row g-3">
            <div class="col-12 col-sm-9">
                <button type="submit" class="btn w-100"
                        style="background:#27ae60; color:#fff; font-weight:600">
                    <i class="fas fa-paper-plane me-2"></i>Enviar mensaje
                </button>
            </div>
            <div class="col-12 col-sm-3">
                <a href="<?php echo RUTA_URL ?>/Mensajes/bandeja"
                   class="btn w-100" style="background:#e74c3c; color:#fff">
                    <i class="fas fa-times me-2"></i>Cancelar
                </a>
            </div>
        </div>

    </form>

</div>

<script>
function cambiarTipo(tipo) {
    document.getElementById('bloque-individual').style.display   = tipo === 'individual'   ? '' : 'none';
    document.getElementById('bloque-departamento').style.display = tipo === 'departamento' ? '' : 'none';
    document.getElementById('bloque-todos').style.display        = tipo === 'todos'        ? '' : 'none';
}
function filtrarDestinatarios(q) {
    const q2 = q.toLowerCase();
    document.querySelectorAll('#lista-destinatarios label').forEach(function(lbl) {
        lbl.style.display = lbl.textContent.toLowerCase().includes(q2) ? '' : 'none';
    });
}
function selTodos(sel) {
    document.querySelectorAll('#lista-destinatarios input[type=checkbox]').forEach(function(cb) {
        if (cb.closest('label').style.display !== 'none') cb.checked = sel;
    });
}
function cargarMiembrosDep(id_dep) {
    const lista  = document.getElementById('lista-dep-miembros');
    const checks = document.getElementById('checks-dep');
    const conteo = document.getElementById('conteo-dep');
    const spin   = document.getElementById('dep-cargando');

    lista.style.display = 'none';
    checks.innerHTML    = '';
    conteo.textContent  = '';

    if (!id_dep) return;

    spin.style.display = 'block';

    fetch('<?php echo RUTA_URL ?>/Mensajes/profesoresDep?id_dep=' + id_dep)
        .then(r => r.json())
        .then(function(profs) {
            spin.style.display = 'none';
            if (!profs.length) {
                checks.innerHTML = '<span class="text-muted">Sin usuarios activos en este departamento.</span>';
                lista.style.display = 'block';
                return;
            }
            const yo = <?php echo $datos['usuarioSesion']->id_profesor ?>;
            let html = '';
            profs.forEach(function(p) {
                if (p.id_profesor == yo) return;  // no incluirse a uno mismo
                html += '<label style="display:block; padding:3px 0; cursor:pointer">'
                      + '<input type="checkbox" class="cb-dep" name="destinatarios[]" '
                      + 'value="' + p.id_profesor + '" checked> '
                      + p.nombre_completo
                      + '</label>';
            });
            checks.innerHTML = html || '<span class="text-muted">No hay otros usuarios en este departamento.</span>';
            actualizarConteoDep();
            // Actualizar conteo al cambiar checks
            checks.querySelectorAll('.cb-dep').forEach(function(cb) {
                cb.addEventListener('change', actualizarConteoDep);
            });
            lista.style.display = 'block';
        })
        .catch(function() {
            spin.style.display = 'none';
            checks.innerHTML = '<span class="text-danger">Error al cargar los miembros.</span>';
            lista.style.display = 'block';
        });
}

function actualizarConteoDep() {
    const total    = document.querySelectorAll('#checks-dep .cb-dep').length;
    const marcados = document.querySelectorAll('#checks-dep .cb-dep:checked').length;
    document.getElementById('conteo-dep').textContent =
        marcados + ' de ' + total + ' miembro(s) incluido(s)';
}

function selDep(sel) {
    document.querySelectorAll('#checks-dep .cb-dep').forEach(function(cb) {
        cb.checked = sel;
    });
    actualizarConteoDep();
}
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
