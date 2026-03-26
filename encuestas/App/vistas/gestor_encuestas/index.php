<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<style>
.tipo-item { cursor:pointer; border-left:3px solid transparent; transition:all .12s; }
.tipo-item:hover  { background:#f0f7ff; border-left-color:#97cff5; }
.tipo-item.activo { background:#deeeff; border-left-color:#0583c3; font-weight:600; }
.preg-input { border:1px solid transparent; background:transparent; width:100%; }
.preg-input:focus { border-color:#0583c3; background:#fff; outline:none; border-radius:4px; padding:2px 6px; }
tr:hover .preg-input { border-color:#dee2e6; background:#fff; }
.btn-xxs { padding:.1rem .35rem; font-size:.7rem; }
</style>

<div class="container-fluid px-4 py-4">

  <div class="row mb-3 align-items-center">
    <div class="col">
      <span class="nombre_modulo_seguimiento">
        <i class="fas fa-layer-group me-2"></i>Gestión de tipos de encuesta y plantillas
      </span>
    </div>
  </div>

  <div class="alert alert-info small py-2 mb-3">
    <i class="fas fa-info-circle me-1"></i>
    Define aquí los <strong>tipos de encuesta</strong> (alumnos, profesores, padres, empresas…)
    y sus <strong>preguntas de valoración</strong>. Al crear una encuesta se copian automáticamente.
    Los campos <strong>Lo mejor · Lo peor · Observaciones</strong> están siempre presentes en todas
    las encuestas — no es necesario añadirlos aquí.
  </div>

  <!-- ═══════════════════════════════════════════════════════
       BLOQUE SUPERIOR — formulario nuevo tipo
  ════════════════════════════════════════════════════════════ -->
  <?php if($datos['usuarioSesion']->id_rol >= 200): ?>
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-header fw-bold" style="background:#0583c3;color:#fff;">
      <i class="fas fa-plus-circle me-2"></i>Nuevo tipo de encuesta
    </div>
    <div class="card-body py-3">
      <div class="row g-2 align-items-end">
        <div class="col-12 col-md-4">
          <label class="form-label form-label-sm fw-semibold mb-1">
            Nombre <span class="text-danger">*</span>
          </label>
          <input type="text" id="nt-nombre" class="form-control form-control-sm"
                 placeholder="Ej: Encuesta de padres" maxlength="100">
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label form-label-sm mb-1">Descripción <span class="text-muted">(opcional)</span></label>
          <input type="text" id="nt-desc" class="form-control form-control-sm"
                 placeholder="Breve descripción del tipo de encuesta">
        </div>
        <div class="col-12 col-md-2">
          <button class="btn btn-custom btn-sm w-100" onclick="Tipos.add()">
            <i class="fas fa-save me-1"></i>Crear tipo
          </button>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <div class="row g-3">

    <!-- ═══════════════════════════════════════════════════════
         COLUMNA IZQUIERDA — lista de tipos
    ════════════════════════════════════════════════════════════ -->
    <div class="col-12 col-lg-4 col-xl-3">
      <div class="card border-0 shadow-sm h-100">

        <div class="card-header d-flex justify-content-between align-items-center"
             style="background:#0583c3;color:#fff;font-weight:600;">
          <span><i class="fas fa-tags me-1"></i>Tipos de encuesta</span>
        </div>

        <!-- Lista -->
        <div class="list-group list-group-flush" id="lista-tipos">
          <?php foreach($datos['tipos'] as $t):
            $sistema = ((int)$t->id_tipo_encuesta === 1);
            $activo  = ((int)$t->id_tipo_encuesta === (int)$datos['tipo_sel']);
          ?>
          <div class="list-group-item tipo-item px-3 py-2 <?php echo $activo ? 'activo' : '' ?>"
               id="tipo-item-<?php echo $t->id_tipo_encuesta ?>"
               onclick="Tipos.seleccionar(<?php echo $t->id_tipo_encuesta ?>)">
            <div class="d-flex justify-content-between align-items-start">
              <div style="min-width:0;">
                <div class="text-truncate" id="tn-<?php echo $t->id_tipo_encuesta ?>">
                  <?php echo htmlspecialchars($t->tipo_encuesta) ?>
                </div>
                <div class="text-muted mt-1" style="font-size:.72rem;">
                  <span id="tc-<?php echo $t->id_tipo_encuesta ?>">
                    <?php echo (int)$t->total_preguntas ?> preg.
                  </span>
                  <?php if($sistema): ?>
                  <span class="badge bg-secondary ms-1" style="font-size:.6rem;">Sistema</span>
                  <?php endif; ?>
                </div>
              </div>
              <?php if(!$sistema && $datos['usuarioSesion']->id_rol >= 200): ?>
              <div class="ms-2 d-flex gap-1 flex-shrink-0" onclick="event.stopPropagation()">
                <button class="btn btn-xxs btn-outline-secondary"
                        onclick="Tipos.abrirEditar(<?php echo $t->id_tipo_encuesta ?>,
                                 '<?php echo addslashes($t->tipo_encuesta) ?>',
                                 '<?php echo addslashes($t->descripcion ?? '') ?>')"
                        title="Editar">
                  <i class="fas fa-pen" style="pointer-events:none;"></i>
                </button>
                <button class="btn btn-xxs btn-outline-danger"
                        onclick="Tipos.eliminar(<?php echo $t->id_tipo_encuesta ?>,
                                 '<?php echo addslashes($t->tipo_encuesta) ?>')"
                        title="Eliminar">
                  <i class="fas fa-trash" style="pointer-events:none;"></i>
                </button>
              </div>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════
         COLUMNA DERECHA — preguntas del tipo seleccionado
    ════════════════════════════════════════════════════════════ -->
    <div class="col-12 col-lg-8 col-xl-9">
      <div class="card border-0 shadow-sm">

        <!-- Cabecera dinámica -->
        <div class="card-header d-flex justify-content-between align-items-center"
             style="background:#0583c3;color:#fff;font-weight:600;">
          <div class="d-flex flex-column" style="min-width:0;">
            <span id="pd-titulo" class="text-truncate">
              <?php echo htmlspecialchars($datos['tipo_info']->tipo_encuesta ?? '') ?>
            </span>
            <span id="pd-desc" class="fw-normal mt-1" style="font-size:.78rem;opacity:.85;">
              <?php echo htmlspecialchars($datos['tipo_info']->descripcion ?? '') ?>
            </span>
          </div>
          <button class="btn btn-sm btn-light ms-3 text-nowrap flex-shrink-0"
                  onclick="UI.mostrarFormPregunta()">
            <i class="fas fa-plus me-1"></i>Añadir pregunta
          </button>
        </div>

        <!-- Aviso campos fijos -->
        <div class="px-3 py-2 border-bottom"
             style="background:#f0fff4;font-size:.78rem;color:#2d6a4f;">
          <i class="fas fa-lock me-1"></i>
          <strong>Siempre incluidos en todas las encuestas:</strong>
          <span class="badge bg-success ms-1">Lo mejor</span>
          <span class="badge bg-danger ms-1">Lo peor</span>
          <span class="badge bg-secondary ms-1">Observaciones</span>
        </div>

        <!-- Form nueva pregunta -->
        <div id="form-nueva-preg" class="px-3 py-3 border-bottom bg-light" style="display:none;">
          <div class="row g-2">

            <div class="col-auto">
              <label class="form-label form-label-sm mb-1">Orden</label>
              <input type="number" id="np-orden" class="form-control form-control-sm"
                     style="width:70px;" min="1"
                     value="<?php echo count($datos['preguntas']) + 1 ?>">
            </div>

            <div class="col-12 col-md">
              <label class="form-label form-label-sm mb-1">Texto de la pregunta</label>
              <input type="text" id="np-texto" class="form-control form-control-sm"
                     placeholder="Ej: ¿Con qué frecuencia habla con su hijo de sus estudios?">
            </div>

            <div class="col-12 col-md-auto">
              <label class="form-label form-label-sm mb-1">Tipo de respuesta</label>
              <select id="np-tipo" class="form-select form-select-sm"
                      onchange="Preguntas.toggleOpciones('np')">
                <option value="puntuacion">⭐ Valoración 1–10</option>
                <option value="opciones">☑ Opciones múltiples</option>
                <option value="numerica">🔢 Numérica abierta</option>
              </select>
            </div>

            <div class="col-12" id="np-opciones-wrap" style="display:none;">
              <label class="form-label form-label-sm mb-1">
                Opciones <small class="text-muted">(una por línea)</small>
              </label>
              <textarea id="np-opciones" class="form-control form-control-sm" rows="4"
                        placeholder="No uso internet&#10;Nunca&#10;Una vez&#10;Entre 1 y 5&#10;Entre 5 y 10&#10;Más de 10"></textarea>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2">
              <button class="btn btn-outline-secondary btn-sm"
                      onclick="UI.ocultarFormPregunta()">Cancelar</button>
              <button class="btn btn-custom btn-sm" onclick="Preguntas.add()">
                <i class="fas fa-save me-1"></i>Guardar pregunta
              </button>
            </div>

          </div>
        </div>

        <!-- Tabla de preguntas -->
        <div class="table-responsive">
          <table class="table table-hover mb-0" style="font-size:.86rem;">
            <thead style="background:#e9f4fb;">
              <tr>
                <th style="width:60px;" class="text-center">Orden</th>
                <th>Pregunta</th>
                <th style="width:120px;" class="text-center">Tipo respuesta</th>
                <th style="width:70px;" class="text-center">Activa</th>
                <th style="width:52px;"></th>
              </tr>
            </thead>
            <tbody id="tbody-pregs">
            <?php if(empty($datos['preguntas'])): ?>
              <tr id="tr-vacio">
                <td colspan="5" class="text-center text-muted py-5">
                  <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                  Sin preguntas. Pulsa "Añadir pregunta" para empezar.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach($datos['preguntas'] as $p):
                $tipo   = $p->tipo_respuesta ?? 'puntuacion';
                $opts   = $p->opciones_json  ?? null;
                $optsJs = $opts ? htmlspecialchars($opts, ENT_QUOTES) : '';
              ?>
              <tr id="trp-<?php echo $p->id_plantilla_pregunta ?>">
                <td class="text-center align-middle py-1">
                  <input type="number" min="1"
                         class="form-control form-control-sm text-center preg-input"
                         style="width:52px;margin:auto;"
                         value="<?php echo $p->orden ?>"
                         onchange="Preguntas.edit(<?php echo $p->id_plantilla_pregunta ?>)">
                </td>
                <td class="align-middle py-1">
                  <input type="text" class="preg-input"
                         value="<?php echo htmlspecialchars($p->pregunta) ?>"
                         onblur="Preguntas.edit(<?php echo $p->id_plantilla_pregunta ?>)">
                  <?php if($tipo === 'opciones' && $opts): ?>
                  <div class="mt-1 d-flex flex-wrap gap-1">
                    <?php foreach(json_decode($opts) as $i => $op): ?>
                    <span class="badge bg-light text-dark border" style="font-size:.7rem;">
                      <?php echo chr(97+$i) ?>) <?php echo htmlspecialchars($op) ?>
                    </span>
                    <?php endforeach; ?>
                  </div>
                  <?php elseif($tipo === 'numerica'): ?>
                  <div class="text-muted mt-1" style="font-size:.72rem;">
                    <i class="fas fa-hashtag me-1"></i>Valor numérico libre
                  </div>
                  <?php endif; ?>
                </td>
                <td class="text-center align-middle py-1">
                  <?php
                    $badgeClases = ['puntuacion'=>'bg-primary','opciones'=>'bg-info text-dark','numerica'=>'bg-warning text-dark'];
                    $badgeIcons  = ['puntuacion'=>'fa-star','opciones'=>'fa-list-ul','numerica'=>'fa-hashtag'];
                    $badgeLabels = ['puntuacion'=>'1–10','opciones'=>'Opciones','numerica'=>'Numérica'];
                  ?>
                  <span class="badge <?php echo $badgeClases[$tipo] ?? 'bg-secondary' ?>"
                        style="cursor:pointer;font-size:.72rem;"
                        title="Clic para cambiar el tipo"
                        onclick="Preguntas.abrirModalTipo(<?php echo $p->id_plantilla_pregunta ?>,
                                 '<?php echo $tipo ?>',
                                 '<?php echo $optsJs ?>')">
                    <i class="fas <?php echo $badgeIcons[$tipo] ?? 'fa-star' ?> me-1"></i>
                    <?php echo $badgeLabels[$tipo] ?? '1–10' ?>
                  </span>
                </td>
                <td class="text-center align-middle py-1">
                  <div class="form-check form-switch d-flex justify-content-center m-0">
                    <input class="form-check-input" type="checkbox"
                           <?php echo $p->activo ? 'checked' : '' ?>
                           onchange="Preguntas.edit(<?php echo $p->id_plantilla_pregunta ?>)">
                  </div>
                </td>
                <td class="text-center align-middle py-1">
                  <button class="btn btn-sm btn-outline-danger"
                          onclick="Preguntas.del(<?php echo $p->id_plantilla_pregunta ?>)">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center
                    text-muted small">
          <span id="pd-count"><?php echo count($datos['preguntas']) ?> pregunta(s)</span>
          <span><i class="fas fa-info-circle me-1 text-info"></i>
            Los cambios solo afectan a las encuestas creadas a partir de ahora</span>
        </div>

      </div>
    </div>
  </div><!-- /row -->
</div>

<!-- Modal cambiar tipo de respuesta -->
<div class="modal fade" id="modal-tipo-resp" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">
          <i class="fas fa-sliders-h me-2 text-primary"></i>Tipo de respuesta
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="mt-id">
        <div class="mb-3">
          <label class="form-label fw-semibold">Tipo de respuesta</label>
          <select id="mt-tipo" class="form-select"
                  onchange="Preguntas.toggleOpciones('mt')">
            <option value="puntuacion">⭐ Valoración 1–10</option>
            <option value="opciones">☑ Opciones múltiples</option>
            <option value="numerica">🔢 Numérica abierta</option>
          </select>
        </div>
        <div id="mt-opciones-wrap" style="display:none;">
          <label class="form-label fw-semibold">
            Opciones <small class="text-muted fw-normal">(una por línea)</small>
          </label>
          <textarea id="mt-opciones" class="form-control" rows="6"
                    placeholder="No uso internet&#10;Nunca&#10;Una vez&#10;Entre 1 y 5&#10;Entre 5 y 10&#10;Más de 10"></textarea>
          <div class="form-text">Las letras a), b), c)… se asignan automáticamente.</div>
        </div>
        <div id="mt-numerica-info" class="alert alert-light border small mb-0" style="display:none;">
          <i class="fas fa-hashtag me-1 text-warning"></i>
          El encuestado introducirá un número entero libremente.
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary btn-sm"
                data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-custom btn-sm" onclick="Preguntas.guardarTipo()">
          <i class="fas fa-save me-1"></i>Guardar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal editar tipo -->
<div class="modal fade" id="modal-editar-tipo" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">
          <i class="fas fa-pen me-2 text-primary"></i>Editar tipo de encuesta
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="et-id">
        <div class="mb-3">
          <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
          <input type="text" id="et-nombre" class="form-control" maxlength="100">
        </div>
        <div>
          <label class="form-label fw-semibold">Descripción</label>
          <textarea id="et-desc" class="form-control" rows="3"
                    placeholder="Descripción del tipo de encuesta…"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary btn-sm"
                data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-custom btn-sm" onclick="Tipos.guardarEditar()">
          <i class="fas fa-save me-1"></i>Guardar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999;">
  <div id="gc-toast" class="toast text-white border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body" id="gc-toast-msg"></div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto"
              data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

<script>
const RUTA       = '<?php echo RUTA_URL ?>';
let   tipoActual = <?php echo (int)$datos['tipo_sel'] ?>;

/* ════════════════════════════════════════════════════════════════
   UI helpers
════════════════════════════════════════════════════════════════ */
const UI = {
  mostrarFormPregunta() { document.getElementById('form-nueva-preg').style.display='block';
                          document.getElementById('np-texto').focus(); },
  ocultarFormPregunta() { document.getElementById('form-nueva-preg').style.display='none';
                          document.getElementById('np-texto').value=''; },
};

function post(url, datos){
  return fetch(url, {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body: Object.entries(datos)
           .map(([k,v]) => encodeURIComponent(k)+'='+encodeURIComponent(v))
           .join('&')
  }).then(r => r.json());
}

function toast(msg, ok=true){
  const el = document.getElementById('gc-toast');
  document.getElementById('gc-toast-msg').textContent = msg;
  el.classList.remove('bg-success','bg-warning','bg-danger');
  el.classList.add(ok ? 'bg-success' : 'bg-warning');
  bootstrap.Toast.getOrCreateInstance(el, {delay:4000}).show();
}

function contadorTipo(delta){
  const el = document.getElementById('tc-'+tipoActual);
  if(!el) return;
  const n = Math.max(0, parseInt(el.textContent) + delta);
  el.textContent = n+' preg.';
  document.getElementById('pd-count').textContent = n+' pregunta(s)';
}

/* ════════════════════════════════════════════════════════════════
   CRUD TIPOS
════════════════════════════════════════════════════════════════ */
const Tipos = {

  seleccionar(id){
    tipoActual = id;
    document.querySelectorAll('.tipo-item').forEach(r => r.classList.remove('activo'));
    document.getElementById('tipo-item-'+id)?.classList.add('activo');

    fetch(RUTA+'/gestor_encuestas/get_preguntas?tipo='+id)
      .then(r => r.json())
      .then(data => {
        document.getElementById('pd-titulo').textContent = data.tipo;
        document.getElementById('pd-desc').textContent   = data.descripcion || '';
        document.getElementById('pd-count').textContent  =
          data.preguntas.length+' pregunta(s)';
        document.getElementById('np-orden').value =
          data.preguntas.length + 1;
        this._renderTabla(data.preguntas);
      });
  },

  _renderTabla(preguntas){
    const tbody = document.getElementById('tbody-pregs');
    if(!preguntas.length){
      tbody.innerHTML = `<tr id="tr-vacio"><td colspan="5"
        class="text-center text-muted py-5">
        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
        Sin preguntas. Pulsa "Añadir pregunta" para empezar.</td></tr>`;
      return;
    }
    tbody.innerHTML = preguntas.map(p => {
      const tipo  = p.tipo_respuesta || 'puntuacion';
      const opts  = p.opciones_json  || '';
      const b     = TIPO_BADGE[tipo] || TIPO_BADGE.puntuacion;

      // Badges de opciones bajo el texto de la pregunta
      let optsHtml = '';
      if(tipo === 'opciones' && opts){
        try {
          const arr = JSON.parse(opts);
          optsHtml = '<div class="mt-1 d-flex flex-wrap gap-1">' +
            arr.map((o,i) => `<span class="badge bg-light text-dark border" style="font-size:.7rem;">${String.fromCharCode(97+i)}) ${esc(o)}</span>`).join('') +
            '</div>';
        } catch(e){}
      } else if(tipo === 'numerica'){
        optsHtml = '<div class="text-muted mt-1" style="font-size:.72rem;"><i class="fas fa-hashtag me-1"></i>Valor numérico libre</div>';
      }

      return `
      <tr id="trp-${p.id_plantilla_pregunta}">
        <td class="text-center align-middle py-1">
          <input type="number" min="1"
                 class="form-control form-control-sm text-center preg-input"
                 style="width:56px;margin:auto;" value="${p.orden}"
                 onchange="Preguntas.edit(${p.id_plantilla_pregunta})">
        </td>
        <td class="align-middle py-1">
          <input type="text" class="preg-input"
                 value="${p.pregunta.replace(/"/g,'&quot;')}"
                 onblur="Preguntas.edit(${p.id_plantilla_pregunta})">
          ${optsHtml}
        </td>
        <td class="text-center align-middle py-1">
          <span class="badge ${b.cls}" style="cursor:pointer;font-size:.72rem;"
                title="Clic para cambiar el tipo"
                onclick="Preguntas.abrirModalTipo(${p.id_plantilla_pregunta},'${tipo}','${esc(opts)}')">
            <i class="fas ${b.icon} me-1"></i>${b.label}
          </span>
        </td>
        <td class="text-center align-middle py-1">
          <div class="form-check form-switch d-flex justify-content-center m-0">
            <input class="form-check-input" type="checkbox"
                   ${p.activo ? 'checked' : ''}
                   onchange="Preguntas.edit(${p.id_plantilla_pregunta})">
          </div>
        </td>
        <td class="text-center align-middle py-1">
          <button class="btn btn-sm btn-outline-danger"
                  onclick="Preguntas.del(${p.id_plantilla_pregunta})">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      </tr>`;
    }).join('');
  },

  add(){
    const nombre = document.getElementById('nt-nombre').value.trim();
    const desc   = document.getElementById('nt-desc').value.trim();
    if(!nombre){ toast('El nombre es obligatorio.', false); return; }

    post(RUTA+'/gestor_encuestas/add_tipo',
         {tipo_encuesta: nombre, descripcion: desc})
    .then(data => {
      if(!data.ok){ toast('Error al crear el tipo.', false); return; }

      // Añadir fila a la lista
      const html = `
      <div class="list-group-item tipo-item px-3 py-2"
           id="tipo-item-${data.id}"
           onclick="Tipos.seleccionar(${data.id})">
        <div class="d-flex justify-content-between align-items-start">
          <div style="min-width:0;">
            <div class="text-truncate" id="tn-${data.id}">${esc(nombre)}</div>
            <div class="text-muted mt-1" style="font-size:.72rem;">
              <span id="tc-${data.id}">0 preg.</span>
            </div>
          </div>
          <div class="ms-2 d-flex gap-1 flex-shrink-0" onclick="event.stopPropagation()">
            <button class="btn btn-xxs btn-outline-secondary"
                    onclick="Tipos.abrirEditar(${data.id},'${nombre.replace(/'/g,"\\'")}','${desc.replace(/'/g,"\\'")}')">
              <i class="fas fa-pen" style="pointer-events:none;"></i>
            </button>
            <button class="btn btn-xxs btn-outline-danger"
                    onclick="Tipos.eliminar(${data.id},'${nombre.replace(/'/g,"\\'")}')">
              <i class="fas fa-trash" style="pointer-events:none;"></i>
            </button>
          </div>
        </div>
      </div>`;
      document.getElementById('lista-tipos').insertAdjacentHTML('beforeend', html);
      UI.ocultarFormTipo && UI.ocultarFormTipo();
      document.getElementById('nt-nombre').value = '';
      document.getElementById('nt-desc').value   = '';
      Tipos.seleccionar(data.id);
      toast('Tipo "'+nombre+'" creado correctamente.');
    });
  },

  abrirEditar(id, nombre, desc){
    document.getElementById('et-id').value     = id;
    document.getElementById('et-nombre').value = nombre;
    document.getElementById('et-desc').value   = desc;
    bootstrap.Modal.getOrCreateInstance(
      document.getElementById('modal-editar-tipo')).show();
  },

  guardarEditar(){
    const id     = document.getElementById('et-id').value;
    const nombre = document.getElementById('et-nombre').value.trim();
    const desc   = document.getElementById('et-desc').value.trim();
    if(!nombre){ toast('El nombre es obligatorio.', false); return; }

    post(RUTA+'/gestor_encuestas/edit_tipo',
         {id_tipo_encuesta: id, tipo_encuesta: nombre, descripcion: desc})
    .then(data => {
      if(!data.ok){ toast('Error al guardar.', false); return; }
      // Actualizar nombre en la lista
      const tn = document.getElementById('tn-'+id);
      if(tn) tn.textContent = nombre;
      // Actualizar cabecera si es el tipo activo
      if(parseInt(id) === tipoActual){
        document.getElementById('pd-titulo').textContent = nombre;
        document.getElementById('pd-desc').textContent   = desc || '';
      }
      bootstrap.Modal.getInstance(
        document.getElementById('modal-editar-tipo')).hide();
      toast('Tipo actualizado correctamente.');
    });
  },

  eliminar(id, nombre){
    if(!confirm(`¿Eliminar el tipo "${nombre}"?\n\nTambién se borrarán sus preguntas de plantilla.\nNo es posible si ya tiene encuestas creadas.`)) return;
    post(RUTA+'/gestor_encuestas/del_tipo', {id_tipo_encuesta: id})
    .then(data => {
      toast(data.msg, data.ok);
      if(data.ok){
        document.getElementById('tipo-item-'+id)?.remove();
        if(parseInt(id) === tipoActual) Tipos.seleccionar(1);
      }
    });
  },
};

/* ════════════════════════════════════════════════════════════════
   CRUD PREGUNTAS DE PLANTILLA
════════════════════════════════════════════════════════════════ */
const TIPO_BADGE = {
  puntuacion: {cls:'bg-primary',          icon:'fa-star',    label:'1–10'},
  opciones:   {cls:'bg-info text-dark',   icon:'fa-list-ul', label:'Opciones'},
  numerica:   {cls:'bg-warning text-dark',icon:'fa-hashtag', label:'Numérica'},
};

const Preguntas = {

  // Mostrar/ocultar el textarea de opciones según el selector
  toggleOpciones(prefix){
    const tipo    = document.getElementById(prefix+'-tipo').value;
    const wrap    = document.getElementById(prefix+'-opciones-wrap');
    const numInfo = document.getElementById(prefix+'-numerica-info');
    if(wrap)    wrap.style.display    = (tipo === 'opciones')  ? 'block' : 'none';
    if(numInfo) numInfo.style.display = (tipo === 'numerica')  ? 'block' : 'none';
  },

  // Convierte textarea (una opción por línea) en JSON string
  _opcionesJson(prefix){
    const tipo = document.getElementById(prefix+'-tipo').value;
    if(tipo !== 'opciones') return '';
    const lines = document.getElementById(prefix+'-opciones').value
                  .split('\n').map(l => l.trim()).filter(l => l.length > 0);
    return lines.length > 0 ? JSON.stringify(lines) : '';
  },

  add(){
    const orden = document.getElementById('np-orden').value;
    const txt   = document.getElementById('np-texto').value.trim();
    const tipo  = document.getElementById('np-tipo').value;
    const opts  = this._opcionesJson('np');
    if(!txt){ toast('Introduce el texto de la pregunta.', false); return; }
    if(tipo === 'opciones' && !opts){ toast('Añade al menos una opción.', false); return; }

    post(RUTA+'/gestor_encuestas/add_pregunta',
         {id_tipo_encuesta: tipoActual, orden, pregunta: txt,
          tipo_respuesta: tipo, opciones_json: opts})
    .then(data => {
      if(!data.ok){ toast('Error al añadir la pregunta.', false); return; }

      document.getElementById('tr-vacio')?.remove();
      const id = data.id_plantilla_pregunta;
      const b  = TIPO_BADGE[tipo] || TIPO_BADGE.puntuacion;

      // Construir badges de opciones si aplica
      let optsHtml = '';
      if(tipo === 'opciones' && opts){
        const arr = JSON.parse(opts);
        optsHtml = '<div class="mt-1 d-flex flex-wrap gap-1">' +
          arr.map((o,i) => `<span class="badge bg-light text-dark border" style="font-size:.7rem;">${String.fromCharCode(97+i)}) ${esc(o)}</span>`).join('') +
          '</div>';
      } else if(tipo === 'numerica'){
        optsHtml = '<div class="text-muted mt-1" style="font-size:.72rem;"><i class="fas fa-hashtag me-1"></i>Valor numérico libre</div>';
      }

      document.getElementById('tbody-pregs').insertAdjacentHTML('beforeend', `
      <tr id="trp-${id}">
        <td class="text-center align-middle py-1">
          <input type="number" min="1"
                 class="form-control form-control-sm text-center preg-input"
                 style="width:52px;margin:auto;" value="${orden}"
                 onchange="Preguntas.edit(${id})">
        </td>
        <td class="align-middle py-1">
          <input type="text" class="preg-input" value="${esc(txt)}"
                 onblur="Preguntas.edit(${id})">
          ${optsHtml}
        </td>
        <td class="text-center align-middle py-1">
          <span class="badge ${b.cls}" style="cursor:pointer;font-size:.72rem;"
                title="Clic para cambiar el tipo"
                onclick="Preguntas.abrirModalTipo(${id},'${tipo}','${esc(opts)}')">
            <i class="fas ${b.icon} me-1"></i>${b.label}
          </span>
        </td>
        <td class="text-center align-middle py-1">
          <div class="form-check form-switch d-flex justify-content-center m-0">
            <input class="form-check-input" type="checkbox" checked
                   onchange="Preguntas.edit(${id})">
          </div>
        </td>
        <td class="text-center align-middle py-1">
          <button class="btn btn-sm btn-outline-danger" onclick="Preguntas.del(${id})">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      </tr>`);

      contadorTipo(+1);
      document.getElementById('np-orden').value = parseInt(orden)+1;
      document.getElementById('np-texto').value = '';
      document.getElementById('np-tipo').value  = 'puntuacion';
      document.getElementById('np-opciones-wrap').style.display = 'none';
      UI.ocultarFormPregunta();
      toast('Pregunta añadida.');
    });
  },

  edit(id){
    const fila   = document.getElementById('trp-'+id);
    if(!fila) return;
    const orden  = fila.querySelector('input[type="number"]').value;
    const txt    = fila.querySelector('input[type="text"]').value;
    const activo = fila.querySelector('input[type="checkbox"]').checked ? 1 : 0;
    // tipo y opciones se guardan solo desde el modal — aquí solo guardamos orden/texto/activo
    post(RUTA+'/gestor_encuestas/edit_pregunta',
         {id_plantilla_pregunta: id, orden, pregunta: txt, activo})
    .then(data => { if(!data.ok) toast('Error al guardar.', false); });
  },

  del(id){
    if(!confirm('¿Eliminar esta pregunta de la plantilla?')) return;
    post(RUTA+'/gestor_encuestas/del_pregunta', {id_plantilla_pregunta: id})
    .then(data => {
      if(!data.ok){ toast(data.msg||'Error al eliminar.', false); return; }
      document.getElementById('trp-'+id)?.remove();
      contadorTipo(-1);
      if(!document.querySelector('#tbody-pregs tr[id^="trp-"]')){
        document.getElementById('tbody-pregs').innerHTML = `
        <tr id="tr-vacio"><td colspan="5" class="text-center text-muted py-5">
          <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
          Sin preguntas. Pulsa "Añadir pregunta" para empezar.</td></tr>`;
      }
      toast('Pregunta eliminada.');
    });
  },

  // Abrir modal para cambiar tipo de respuesta
  abrirModalTipo(id, tipo, optsJson){
    document.getElementById('mt-id').value = id;
    document.getElementById('mt-tipo').value = tipo || 'puntuacion';
    // Rellenar textarea con opciones existentes (una por línea)
    try {
      const arr = optsJson ? JSON.parse(optsJson) : [];
      document.getElementById('mt-opciones').value = arr.join('\n');
    } catch(e){
      document.getElementById('mt-opciones').value = '';
    }
    this.toggleOpciones('mt');
    bootstrap.Modal.getOrCreateInstance(
      document.getElementById('modal-tipo-resp')).show();
  },

  guardarTipo(){
    const id   = document.getElementById('mt-id').value;
    const tipo = document.getElementById('mt-tipo').value;
    const opts = this._opcionesJson('mt');
    if(tipo === 'opciones' && !opts){ toast('Añade al menos una opción.', false); return; }

    // Necesitamos también el texto y orden actuales para el edit completo
    const fila   = document.getElementById('trp-'+id);
    const orden  = fila?.querySelector('input[type="number"]')?.value || 1;
    const txt    = fila?.querySelector('input[type="text"]')?.value   || '';
    const activo = fila?.querySelector('input[type="checkbox"]')?.checked ? 1 : 0;

    post(RUTA+'/gestor_encuestas/edit_pregunta',
         {id_plantilla_pregunta: id, orden, pregunta: txt, activo,
          tipo_respuesta: tipo, opciones_json: opts})
    .then(data => {
      if(!data.ok){ toast('Error al guardar.', false); return; }

      bootstrap.Modal.getInstance(
        document.getElementById('modal-tipo-resp')).hide();

      // Actualizar badge en la tabla
      const b = TIPO_BADGE[tipo] || TIPO_BADGE.puntuacion;
      const badge = fila?.querySelector('.badge[onclick^="Preguntas.abrirModalTipo"]');
      if(badge){
        badge.className   = `badge ${b.cls}`;
        badge.style.cssText = 'cursor:pointer;font-size:.72rem;';
        badge.setAttribute('onclick', `Preguntas.abrirModalTipo(${id},'${tipo}','${esc(opts)}')`);
        badge.innerHTML   = `<i class="fas ${b.icon} me-1"></i>${b.label}`;
      }

      // Actualizar las opciones visibles bajo el texto
      const td = fila?.querySelectorAll('td')[1];
      const existingInfo = td?.querySelector('div.mt-1');
      if(existingInfo) existingInfo.remove();
      if(td){
        if(tipo === 'opciones' && opts){
          const arr = JSON.parse(opts);
          const div = document.createElement('div');
          div.className = 'mt-1 d-flex flex-wrap gap-1';
          div.innerHTML = arr.map((o,i) =>
            `<span class="badge bg-light text-dark border" style="font-size:.7rem;">${String.fromCharCode(97+i)}) ${esc(o)}</span>`
          ).join('');
          td.appendChild(div);
        } else if(tipo === 'numerica'){
          const div = document.createElement('div');
          div.className = 'text-muted mt-1';
          div.style.fontSize = '.72rem';
          div.innerHTML = '<i class="fas fa-hashtag me-1"></i>Valor numérico libre';
          td.appendChild(div);
        }
      }
      toast('Tipo de respuesta actualizado.');
    });
  },
};

function esc(s){
  return String(s)
    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
    .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
