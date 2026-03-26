<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container-fluid px-4 py-4">

    <div class="row mb-3 align-items-center">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <i class="fas fa-magic me-2"></i>Generación masiva de encuestas
            </span>
        </div>
        <div class="col-auto">
            <a href="<?php echo RUTA_URL ?>/encuestas" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    <div class="row g-4">

        <!-- Formulario -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header" style="background:#0583c3; color:#fff;">
                    <i class="fas fa-cog me-1"></i> Configuración
                </div>
                <div class="card-body">

                    <div id="zona-formulario">

                        <!-- Evaluación -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Evaluación (trimestre) <span class="text-danger">*</span>
                            </label>
                            <select name="id_evaluacion" id="sel_evaluacion" class="form-select" required>
                                <option value="">— Selecciona —</option>
                                <?php foreach($datos['evaluaciones'] as $ev): ?>
                                <option value="<?php echo $ev->id_evaluacion ?>"
                                    <?php if($datos['evaluacion_actual'] &&
                                             $datos['evaluacion_actual']->id_evaluacion == $ev->id_evaluacion)
                                              echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($ev->evaluacion) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if($datos['evaluacion_actual']): ?>
                            <div class="form-text text-success">
                                <i class="fas fa-check-circle me-1"></i>
                                Autodetectada: <strong><?php echo htmlspecialchars($datos['evaluacion_actual']->evaluacion) ?></strong>
                                (corte <?php echo htmlspecialchars($datos['evaluacion_actual']->fecha_corte) ?>)
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Curso académico -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Curso académico <span class="text-danger">*</span></label>
                            <input type="text" id="campo_curso" class="form-control" required
                                   value="<?php echo $datos['curso_actual'] ?>" placeholder="2024-2025">
                        </div>

                        <!-- Fecha inicio -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Fecha de apertura <span class="text-danger">*</span></label>
                            <input type="date" id="campo_fecha_ini" class="form-control" required
                                   value="<?php echo date('Y-m-d') ?>">
                        </div>

                        <!-- Fecha fin -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Fecha de cierre</label>
                            <input type="date" id="campo_fecha_fin" class="form-control">
                            <div class="form-text">Opcional.</div>
                        </div>

                        <!-- Campos cualitativos -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-comment-alt me-1"></i>Campos de comentarios
                            </label>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox"
                                       id="chk_mejor_peor" checked>
                                <label class="form-check-label" for="chk_mejor_peor">
                                    Mostrar <strong>Lo mejor</strong> y <strong>Lo peor</strong>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       id="chk_observaciones" checked>
                                <label class="form-check-label" for="chk_observaciones">
                                    Mostrar campo de <strong>Observaciones</strong>
                                </label>
                            </div>
                        </div>

                        <div class="alert alert-warning py-2 mb-4 small">
                            <i class="fas fa-key me-1"></i>
                            Se generará <strong>un código de acceso por grupo de alumnos</strong>.
                            Las encuestas que ya existan para esa evaluación y curso serán omitidas.
                        </div>

                        <div class="d-grid">
                            <button type="button" id="btn-generar" class="btn btn-success btn-lg"
                                    onclick="iniciarGeneracion()">
                                <i class="fas fa-magic me-2"></i>Generar encuestas
                            </button>
                        </div>

                    </div><!-- /zona-formulario -->

                    <!-- ── Zona de progreso (oculta hasta que empieza) ── -->
                    <div id="zona-progreso" style="display:none;">
                        <div class="text-center mb-3">
                            <i class="fas fa-cog fa-spin fa-2x text-primary"></i>
                            <div class="fw-bold mt-2">Generando encuestas...</div>
                            <div class="text-muted small" id="prog-detalle">Preparando...</div>
                        </div>
                        <div class="progress mb-2" style="height:22px; border-radius:8px;">
                            <div id="barra-progreso"
                                 class="progress-bar progress-bar-striped progress-bar-animated"
                                 style="width:0%; background:#0583c3; transition:width .4s ease;">
                                0%
                            </div>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span id="prog-cuenta">0 / <?php echo count($datos['pares']) ?></span>
                            <span id="prog-omitidas">0 omitidas</span>
                        </div>
                    </div>

                    <!-- ── Zona de resultado final ── -->
                    <div id="zona-resultado" style="display:none;"></div>

                </div>
            </div>
        </div>

        <!-- Tabla previsualización -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center"
                     style="background:#f8f9fa;">
                    <span class="fw-bold">
                        <i class="fas fa-list me-1 text-primary"></i>
                        <?php echo count($datos['pares']) ?> pares profesor-módulo
                    </span>
                    <span class="badge bg-primary"><?php echo count($datos['pares']) ?></span>
                </div>
                <div class="card-body p-0" id="tabla-pares-wrap" style="max-height:520px; overflow-y:auto;">
                    <?php if(empty($datos['pares'])): ?>
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-exclamation-circle fa-2x mb-2"></i><br>
                            No hay profesores activos con módulos asignados.
                        </div>
                    <?php else: ?>
                    <table class="table table-sm table-hover mb-0" style="font-size:.85rem;" id="tabla-pares">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width:28px;"></th>
                                <th>Ciclo</th>
                                <th>Grupo</th>
                                <th>Módulo</th>
                                <th>Profesor</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($datos['pares'] as $par): ?>
                        <tr id="par-<?php echo $par->id_profesor_modulo ?>">
                            <td class="text-center estado-icono">
                                <i class="fas fa-circle text-muted" style="font-size:.5rem; opacity:.3;"></i>
                            </td>
                            <td><?php echo htmlspecialchars($par->nombre_ciclo) ?></td>
                            <td><strong><?php echo htmlspecialchars($par->nombre_curso) ?></strong></td>
                            <td><?php echo htmlspecialchars($par->nombre_modulo) ?></td>
                            <td><?php echo htmlspecialchars($par->nombre_profesor) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
const RUTA  = '<?php echo RUTA_URL ?>';
// Lista de pares PHP → JS
const PARES = <?php echo json_encode(array_values(array_map(function($p){
    return ['id' => $p->id_profesor_modulo, 'ciclo' => $p->nombre_ciclo, 'curso' => $p->nombre_curso];
}, $datos['pares']))) ?>;

async function iniciarGeneracion(){
    const eval_id  = document.getElementById('sel_evaluacion').value;
    const curso    = document.getElementById('campo_curso').value.trim();
    const fecha_ini= document.getElementById('campo_fecha_ini').value;
    const fecha_fin= document.getElementById('campo_fecha_fin').value;
    const mejor_peor    = document.getElementById('chk_mejor_peor').checked    ? '1' : '0';
    const observaciones = document.getElementById('chk_observaciones').checked ? '1' : '0';

    if(!eval_id || !curso || !fecha_ini){
        alert('Rellena todos los campos obligatorios.');
        return;
    }

    if(!confirm(`¿Generar encuestas para ${PARES.length} pares profesor-módulo?\n\nEvaluación: ${document.getElementById('sel_evaluacion').selectedOptions[0].text}\nCurso: ${curso}\n\nLas que ya existan serán omitidas automáticamente.`))
        return;

    // Mostrar zona de progreso
    document.getElementById('zona-formulario').style.display = 'none';
    document.getElementById('zona-progreso').style.display   = 'block';

    const total    = PARES.length;
    let creadas    = 0;
    let omitidas   = 0;
    let errores    = 0;
    const codigosGrupo = {}; // id_curso → codigo

    for(let i = 0; i < total; i++){
        const par = PARES[i];

        // Actualizar barra
        const pct = Math.round((i / total) * 100);
        actualizarBarra(pct, i, total, omitidas);
        actualizarFilaTrabajando(par.id);

        try {
            const resp = await fetch(RUTA + '/encuestas/generar_uno', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({
                    id_profesor_modulo:    par.id,
                    id_evaluacion:         eval_id,
                    curso_academico:       curso,
                    fecha_inicio:          fecha_ini,
                    fecha_fin:             fecha_fin,
                    mostrar_mejor_peor:    mejor_peor,
                    mostrar_observaciones: observaciones
                })
            });
            const data = await resp.json();

            if(data.estado === 'creada'){
                creadas++;
                codigosGrupo[data.id_curso] = {
                    curso:  data.nombre_curso,
                    ciclo:  data.nombre_ciclo,
                    codigo: data.codigo_acceso
                };
                marcarFila(par.id, 'creada');
            } else if(data.estado === 'omitida'){
                omitidas++;
                marcarFila(par.id, 'omitida');
            } else {
                errores++;
                marcarFila(par.id, 'error');
            }
        } catch(e){
            errores++;
            marcarFila(par.id, 'error');
        }
    }

    // 100%
    actualizarBarra(100, total, total, omitidas);
    document.getElementById('zona-progreso').style.display = 'none';
    mostrarResultado(creadas, omitidas, errores, codigosGrupo, curso,
                     document.getElementById('sel_evaluacion').selectedOptions[0].text);
}

function actualizarBarra(pct, hecho, total, omitidas){
    const barra = document.getElementById('barra-progreso');
    barra.style.width  = pct + '%';
    barra.textContent  = pct + '%';
    document.getElementById('prog-cuenta').textContent    = `${hecho} / ${total}`;
    document.getElementById('prog-omitidas').textContent  = `${omitidas} omitidas`;
    document.getElementById('prog-detalle').textContent   = hecho < total
        ? `Procesando ${hecho + 1} de ${total}...`
        : 'Finalizando...';
}

function marcarFila(id, estado){
    const td = document.querySelector(`#par-${id} .estado-icono`);
    if(!td) return;
    const iconos = {
        creada:  '<i class="fas fa-check-circle text-success"></i>',
        omitida: '<i class="fas fa-minus-circle text-warning" title="Ya existía"></i>',
        error:   '<i class="fas fa-times-circle text-danger" title="Error"></i>',
    };
    td.innerHTML = iconos[estado] || '';
    // Scroll automático a la fila activa
    const fila = document.getElementById(`par-${id}`);
    if(fila) fila.scrollIntoView({block:'nearest', behavior:'smooth'});
}

function actualizarFilaTrabajando(id){
    const td = document.querySelector(`#par-${id} .estado-icono`);
    if(td) td.innerHTML = '<i class="fas fa-spinner fa-spin text-primary"></i>';
}

function mostrarResultado(creadas, omitidas, errores, codigosGrupo, curso, evalNombre){
    const grupos = Object.values(codigosGrupo);
    let html = `
        <div class="alert alert-success mb-3">
            <i class="fas fa-check-circle me-2"></i>
            <strong>¡Generación completada!</strong><br>
            Evaluación: <strong>${evalNombre}</strong> · Curso: <strong>${curso}</strong><br>
            <span class="badge bg-success">${creadas} creadas</span>
            <span class="badge bg-warning text-dark ms-1">${omitidas} omitidas</span>
            ${errores ? `<span class="badge bg-danger ms-1">${errores} errores</span>` : ''}
        </div>`;

    if(grupos.length){
        html += `<div class="fw-bold small mb-2"><i class="fas fa-key me-1"></i>Códigos por grupo:</div>
                 <div class="row g-2 mb-3">`;
        grupos.forEach(g => {
            html += `
                <div class="col-6">
                    <div class="border rounded px-2 py-1 bg-light small">
                        <div class="text-muted" style="font-size:.75rem;">${g.ciclo}</div>
                        <div class="fw-bold">${g.curso}</div>
                        <div style="font-family:monospace; font-size:1.3rem; font-weight:900;
                                    letter-spacing:0.2em; color:#b8860b;">${g.codigo}</div>
                    </div>
                </div>`;
        });
        html += `</div>`;
    }

    html += `<a href="${RUTA}/encuestas" class="btn btn-custom w-100">
                <i class="fas fa-list me-1"></i>Ver listado de encuestas
             </a>`;

    document.getElementById('zona-resultado').innerHTML = html;
    document.getElementById('zona-resultado').style.display = 'block';
}
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
