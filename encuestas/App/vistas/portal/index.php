<?php require_once RUTA_APP . '/vistas/inc/header_publico.php' ?>

<style>
.portal-card {
    cursor: pointer;
    border: 2px solid transparent;
    transition: all .18s;
}
.portal-card:hover  { border-color: #0583c3; background: #f0f8ff; transform: translateY(-2px); box-shadow: 0 4px 16px rgba(5,131,195,.15); }
.portal-card.activa { border-color: #0583c3; background: #e8f4fd; }
.portal-card.activa .card-icon { color: #0583c3; }
.card-icon { font-size: 2rem; color: #adb5bd; transition: color .18s; }
.step-num  { width:28px; height:28px; border-radius:50%; background:#0583c3;
             color:#fff; font-weight:700; font-size:.85rem;
             display:inline-flex; align-items:center; justify-content:center; }
.enc-card  { border-left: 4px solid #0583c3; transition: box-shadow .15s; }
.enc-card:hover { box-shadow: 0 3px 12px rgba(0,0,0,.12); }
.spinner-overlay { display:none; text-align:center; padding:2rem; color:#6c757d; }
</style>

<div class="container py-4" style="max-width:960px;">

    <!-- Cabecera -->
    <div class="text-center mb-4">
        <div style="font-size:3rem; color:#0583c3;"><i class="fas fa-poll"></i></div>
        <h2 class="fw-bold mt-2 mb-1">Encuestas de satisfacción</h2>
        <p class="text-muted">CPIFP Bajo Aragón · Selecciona tu grupo para acceder a las encuestas activas</p>

        <!-- Selector de curso académico -->
        <div class="d-inline-flex align-items-center gap-2 mt-1">
            <label class="text-muted small">Curso académico:</label>
            <select id="sel-curso-acad" class="form-select form-select-sm w-auto"
                    onchange="cambiarCursoAcad(this.value)">
                <?php foreach($datos['cursos_acad'] as $ca): ?>
                <option value="<?php echo htmlspecialchars($ca->curso_academico) ?>"
                    <?php echo $ca->curso_academico == $datos['curso_actual'] ? 'selected' : '' ?>>
                    <?php echo htmlspecialchars($ca->curso_academico) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- PASO 1: Ciclo formativo -->
    <div class="mb-4">
        <div class="d-flex align-items-center mb-3 gap-2">
            <span class="step-num">1</span>
            <span class="fw-bold fs-5">Selecciona tu ciclo formativo</span>
        </div>

        <?php if(empty($datos['ciclos'])): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            No hay encuestas activas para el curso <strong><?php echo htmlspecialchars($datos['curso_actual']) ?></strong>.
        </div>
        <?php else: ?>
        <div class="row g-3" id="grid-ciclos">
            <?php foreach($datos['ciclos'] as $ci): ?>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card portal-card h-100 p-3"
                     onclick="seleccionarCiclo(<?php echo $ci->id_ciclo ?>, this)"
                     data-id="<?php echo $ci->id_ciclo ?>">
                    <div class="d-flex align-items-center gap-3">
                        <div class="card-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div>
                            <div class="fw-bold lh-sm"><?php echo htmlspecialchars($ci->ciclo) ?></div>
                            <?php if($ci->ciclo_corto): ?>
                            <div class="text-muted small"><?php echo htmlspecialchars($ci->ciclo_corto) ?></div>
                            <?php endif; ?>
                        </div>
                        <i class="fas fa-chevron-right text-muted ms-auto"></i>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- PASO 2: Grupo (oculto hasta seleccionar ciclo) -->
    <div id="bloque-grupos" style="display:none;" class="mb-4">
        <div class="d-flex align-items-center mb-3 gap-2">
            <span class="step-num">2</span>
            <span class="fw-bold fs-5">Selecciona tu grupo</span>
            <button class="btn btn-sm btn-outline-secondary ms-auto" onclick="resetGrupos()">
                <i class="fas fa-arrow-left me-1"></i>Cambiar ciclo
            </button>
        </div>
        <div class="spinner-overlay" id="spin-grupos">
            <i class="fas fa-spinner fa-spin fa-2x mb-2"></i><br>Cargando grupos...
        </div>
        <div class="row g-3" id="grid-grupos"></div>
    </div>

    <!-- PASO 3: Encuestas (oculto hasta seleccionar grupo) -->
    <div id="bloque-encuestas" style="display:none;" class="mb-4">
        <div class="d-flex align-items-center mb-3 gap-2">
            <span class="step-num">3</span>
            <span class="fw-bold fs-5">Encuestas disponibles para tu grupo</span>
            <button class="btn btn-sm btn-outline-secondary ms-auto" onclick="resetEncuestas()">
                <i class="fas fa-arrow-left me-1"></i>Cambiar grupo
            </button>
        </div>
        <div class="spinner-overlay" id="spin-encuestas">
            <i class="fas fa-spinner fa-spin fa-2x mb-2"></i><br>Cargando encuestas...
        </div>
        <div id="lista-encuestas"></div>
    </div>

</div>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>

<script>
const RUTA  = '<?php echo RUTA_URL ?>';
let cursoAcad = '<?php echo htmlspecialchars($datos['curso_actual']) ?>';

function cambiarCursoAcad(val){
    cursoAcad = val;
    // Recargar la página con el nuevo curso
    window.location.href = RUTA + '/portal?curso=' + encodeURIComponent(val);
}

function seleccionarCiclo(id_ciclo, card){
    // Marcar seleccionado
    document.querySelectorAll('#grid-ciclos .portal-card').forEach(c => c.classList.remove('activa'));
    card.classList.add('activa');

    // Mostrar paso 2
    resetEncuestas();
    document.getElementById('bloque-grupos').style.display = 'block';
    document.getElementById('grid-grupos').innerHTML = '';
    document.getElementById('spin-grupos').style.display = 'block';

    fetch(RUTA + '/portal/cursos/' + id_ciclo + '?curso=' + encodeURIComponent(cursoAcad))
        .then(r => r.json())
        .then(grupos => {
            document.getElementById('spin-grupos').style.display = 'none';
            const grid = document.getElementById('grid-grupos');
            if(!grupos.length){
                grid.innerHTML = '<div class="col"><div class="alert alert-info">No hay grupos con encuestas activas en este ciclo.</div></div>';
                return;
            }
            grupos.forEach(g => {
                grid.innerHTML += `
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card portal-card h-100 p-3"
                         onclick="seleccionarGrupo(${g.id_curso}, this)"
                         data-id="${g.id_curso}">
                        <div class="d-flex align-items-center gap-3">
                            <div class="card-icon"><i class="fas fa-users"></i></div>
                            <div class="fw-bold">${g.curso}</div>
                            <i class="fas fa-chevron-right text-muted ms-auto"></i>
                        </div>
                    </div>
                </div>`;
            });
        })
        .catch(() => {
            document.getElementById('spin-grupos').style.display = 'none';
            document.getElementById('grid-grupos').innerHTML =
                '<div class="col"><div class="alert alert-danger">Error al cargar los grupos.</div></div>';
        });
}

function seleccionarGrupo(id_curso, card){
    document.querySelectorAll('#grid-grupos .portal-card').forEach(c => c.classList.remove('activa'));
    card.classList.add('activa');

    document.getElementById('bloque-encuestas').style.display = 'block';
    document.getElementById('lista-encuestas').innerHTML = '';
    document.getElementById('spin-encuestas').style.display = 'block';

    fetch(RUTA + '/portal/encuestas/' + id_curso + '?curso=' + encodeURIComponent(cursoAcad))
        .then(r => r.json())
        .then(encuestas => {
            document.getElementById('spin-encuestas').style.display = 'none';
            const lista = document.getElementById('lista-encuestas');
            if(!encuestas.length){
                lista.innerHTML = '<div class="alert alert-info">No hay encuestas activas para este grupo.</div>';
                return;
            }

            // Contadores para el resumen
            const total      = encuestas.length;
            const respondidas = encuestas.filter(e => e.ya_respondida).length;
            const pendientes  = total - respondidas;

            // Resumen de progreso
            const pct = Math.round((respondidas / total) * 100);
            let html = `
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small fw-bold text-muted">Progreso del grupo</span>
                    <span class="small text-muted">${respondidas} de ${total} respondidas</span>
                </div>
                <div class="progress" style="height:10px; border-radius:6px;">
                    <div class="progress-bar ${pct===100 ? 'bg-success' : ''}"
                         style="width:${pct}%; background:${pct===100 ? '' : '#0583c3'}; border-radius:6px;">
                    </div>
                </div>
                ${pct===100
                    ? '<div class="text-success small mt-1 fw-bold"><i class="fas fa-star me-1"></i>¡Has completado todas las encuestas!</div>'
                    : `<div class="text-muted small mt-1">${pendientes} encuesta(s) pendiente(s)</div>`
                }
            </div>`;

            // Agrupar por evaluación
            const porEval = {};
            encuestas.forEach(e => {
                const ev = e.nombre_evaluacion || 'Sin evaluación';
                if(!porEval[ev]) porEval[ev] = [];
                porEval[ev].push(e);
            });

            for(const [eval_nombre, encs] of Object.entries(porEval)){
                html += `<div class="fw-bold text-muted small text-uppercase mb-2 mt-3">
                            <i class="fas fa-calendar-check me-1"></i>${eval_nombre}
                          </div>`;
                encs.forEach(e => {
                    const modulo = e.nombre_corto || e.nombre_modulo;

                    if(e.ya_respondida){
                        // ── Encuesta ya respondida ──────────────────────────
                        html += `
                        <div class="card mb-2 p-0" style="border-left:4px solid #27ae60; opacity:.85;">
                            <div class="card-body py-3 px-3">
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <div class="flex-shrink-0 text-success" style="font-size:1.5rem;">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">${modulo}</div>
                                        <div class="text-muted small">${e.nombre_profesor}</div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-success px-3 py-2" style="font-size:.85rem;">
                                            <i class="fas fa-check me-1"></i>Respondida
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    } else {
                        // ── Encuesta pendiente ──────────────────────────────
                        const codigoBadge = e.codigo_acceso
                            ? `<span class="badge" style="background:#fff8e1; color:#b8860b;
                                                          border:1px solid #ffe082; font-size:.8rem;">
                                   <i class="fas fa-key me-1"></i>Requiere código
                               </span>`
                            : '';
                        html += `
                        <div class="card enc-card mb-2 p-0">
                            <div class="card-body py-3 px-3">
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <div class="flex-shrink-0 text-muted" style="font-size:1.5rem;">
                                        <i class="fas fa-circle-notch"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">${modulo}</div>
                                        <div class="text-muted small">${e.nombre_profesor}</div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end gap-2">
                                        ${codigoBadge}
                                        <a href="${RUTA}/responder/${e.token_publico}"
                                           class="btn btn-sm btn-custom">
                                            <i class="fas fa-poll me-1"></i>Responder
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    }
                });
            }
            lista.innerHTML = html;
        })
        .catch(() => {
            document.getElementById('spin-encuestas').style.display = 'none';
            document.getElementById('lista-encuestas').innerHTML =
                '<div class="alert alert-danger">Error al cargar las encuestas.</div>';
        });
}

function resetGrupos(){
    document.querySelectorAll('#grid-ciclos .portal-card').forEach(c => c.classList.remove('activa'));
    document.getElementById('bloque-grupos').style.display = 'none';
    resetEncuestas();
}

function resetEncuestas(){
    document.querySelectorAll('#grid-grupos .portal-card').forEach(c => c.classList.remove('activa'));
    document.getElementById('bloque-encuestas').style.display = 'none';
    document.getElementById('lista-encuestas').innerHTML = '';
}
</script>
