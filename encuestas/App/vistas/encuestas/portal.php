<?php require_once RUTA_APP . '/vistas/inc/header_publico.php' ?>

<style>
.dept-header {
    cursor: pointer;
    background: #0583c3;
    color: #fff;
    border-radius: 10px;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: background .15s;
    user-select: none;
}
.dept-header:hover { background: #0470a8; }
.dept-header .chevron { transition: transform .25s; font-size:.9rem; }
.dept-header[aria-expanded="true"] .chevron { transform: rotate(180deg); }

.ciclo-header {
    cursor: pointer;
    background: #e9f4fb;
    border-left: 4px solid #0583c3;
    border-radius: 6px;
    padding: .65rem 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: background .12s;
    user-select: none;
}
.ciclo-header:hover { background: #d6ecf8; }
.ciclo-header .chevron { transition: transform .25s; font-size:.85rem; color:#0583c3; }
.ciclo-header[aria-expanded="true"] .chevron { transform: rotate(180deg); }

.curso-header {
    cursor: pointer;
    background: #f8f9fa;
    border-left: 3px solid #97cff5;
    border-radius: 5px;
    padding: .55rem .9rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: background .12s;
    user-select: none;
    font-size: .9rem;
}
.curso-header:hover { background: #eef7fd; }
.curso-header .chevron { transition: transform .25s; font-size:.8rem; color:#6c757d; }
.curso-header[aria-expanded="true"] .chevron { transform: rotate(180deg); }

.enc-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    background: #fff;
    transition: box-shadow .12s, border-color .12s;
}
.enc-card:hover { box-shadow: 0 2px 10px rgba(5,131,195,.15); border-color: #97cff5; }
.enc-card.respondida {
    background: #f6fdf6;
    border-color: #a8d5a2;
    opacity: .85;
}
.enc-card .badge-eval {
    font-size: .7rem;
    font-weight: 600;
    letter-spacing: .03em;
}
.pill-pendiente {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffc107;
    border-radius: 20px;
    padding: .15rem .6rem;
    font-size: .72rem;
    font-weight: 600;
    white-space: nowrap;
}
.pill-hecho {
    background: #d1e7dd;
    color: #0f5132;
    border: 1px solid #a3cfbb;
    border-radius: 20px;
    padding: .15rem .6rem;
    font-size: .72rem;
    font-weight: 600;
    white-space: nowrap;
}
.btn-encuesta {
    background: #0583c3;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: .4rem .9rem;
    font-size: .85rem;
    font-weight: 600;
    transition: background .12s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: .4rem;
}
.btn-encuesta:hover { background: #0470a8; color: #fff; }
.btn-encuesta.disabled-link {
    background: #adb5bd;
    pointer-events: none;
    cursor: default;
}
.contador-badge {
    font-size: .7rem;
    padding: .2em .55em;
}
</style>

<div class="container py-4" style="max-width:820px;">

    <!-- Cabecera -->
    <div class="text-center mb-4">
        <div style="font-size:3rem; color:#0583c3;"><i class="fas fa-poll"></i></div>
        <h2 class="mt-2 mb-1 fw-bold">Encuestas de satisfacción</h2>
        <p class="text-muted">
            Selecciona tu departamento, ciclo y curso para ver las encuestas disponibles.
            <br><small>Curso <?php echo htmlspecialchars($datos['curso_academico']) ?></small>
        </p>
    </div>

    <?php if(empty($datos['arbol'])): ?>
    <div class="alert alert-info text-center">
        <i class="fas fa-info-circle me-2"></i>
        No hay encuestas activas en este momento.
    </div>

    <?php else: ?>

    <!-- Leyenda -->
    <div class="d-flex gap-3 justify-content-center mb-4 flex-wrap">
        <span class="pill-pendiente"><i class="fas fa-circle-dot me-1"></i>Pendiente de responder</span>
        <span class="pill-hecho"><i class="fas fa-check-circle me-1"></i>Ya respondida</span>
    </div>

    <!-- Acordeón departamentos -->
    <div class="d-flex flex-column gap-3" id="acord-dept">

    <?php foreach($datos['arbol'] as $did => $dept):
        $total_enc   = 0;
        $total_resp  = 0;
        foreach($dept['ciclos'] as $ciclo)
            foreach($ciclo['cursos'] as $curso)
                foreach($curso['encuestas'] as $e){
                    $total_enc++;
                    if($e['respondida']) $total_resp++;
                }
        $pendientes = $total_enc - $total_resp;
    ?>
    <div class="dept-bloque">

        <!-- Cabecera departamento -->
        <div class="dept-header"
             data-bs-toggle="collapse"
             data-bs-target="#dept-<?php echo $did ?>"
             aria-expanded="false"
             aria-controls="dept-<?php echo $did ?>">
            <div class="d-flex align-items-center gap-3">
                <div style="width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,.2);
                             display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <div class="fw-bold fs-6"><?php echo htmlspecialchars($dept['label']) ?></div>
                    <div style="font-size:.75rem;opacity:.85;">
                        <?php echo count($dept['ciclos']) ?> ciclo(s) ·
                        <?php echo $total_enc ?> encuesta(s)
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <?php if($pendientes > 0): ?>
                <span class="badge bg-warning text-dark contador-badge">
                    <?php echo $pendientes ?> pendiente<?php echo $pendientes > 1 ? 's' : '' ?>
                </span>
                <?php elseif($total_enc > 0): ?>
                <span class="badge bg-success contador-badge">
                    <i class="fas fa-check me-1"></i>Completado
                </span>
                <?php endif; ?>
                <i class="fas fa-chevron-down chevron"></i>
            </div>
        </div>

        <!-- Cuerpo departamento -->
        <div class="collapse" id="dept-<?php echo $did ?>">
            <div class="pt-2 ps-2 d-flex flex-column gap-2">

            <?php foreach($dept['ciclos'] as $cid => $ciclo):
                $enc_ciclo  = 0;
                $resp_ciclo = 0;
                foreach($ciclo['cursos'] as $curso)
                    foreach($curso['encuestas'] as $e){
                        $enc_ciclo++;
                        if($e['respondida']) $resp_ciclo++;
                    }
                $pend_ciclo = $enc_ciclo - $resp_ciclo;
            ?>
            <div>
                <!-- Cabecera ciclo -->
                <div class="ciclo-header"
                     data-bs-toggle="collapse"
                     data-bs-target="#ciclo-<?php echo $cid ?>"
                     aria-expanded="false">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-graduation-cap text-primary" style="font-size:.9rem;"></i>
                        <span class="fw-semibold" style="font-size:.92rem;">
                            <?php echo htmlspecialchars($ciclo['label']) ?>
                        </span>
                        <?php if($ciclo['corto']): ?>
                        <span class="badge bg-secondary" style="font-size:.65rem;">
                            <?php echo htmlspecialchars($ciclo['corto']) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <?php if($pend_ciclo > 0): ?>
                        <span class="badge bg-warning text-dark contador-badge">
                            <?php echo $pend_ciclo ?> pendiente<?php echo $pend_ciclo > 1 ? 's' : '' ?>
                        </span>
                        <?php elseif($enc_ciclo > 0): ?>
                        <span class="badge bg-success contador-badge"><i class="fas fa-check me-1"></i>OK</span>
                        <?php endif; ?>
                        <i class="fas fa-chevron-down chevron"></i>
                    </div>
                </div>

                <!-- Cuerpo ciclo -->
                <div class="collapse" id="ciclo-<?php echo $cid ?>">
                    <div class="pt-2 ps-3 d-flex flex-column gap-2">

                    <?php foreach($ciclo['cursos'] as $uid_curso => $curso):
                        $enc_curso  = count($curso['encuestas']);
                        $resp_curso = count(array_filter($curso['encuestas'], fn($e) => $e['respondida']));
                        $pend_curso = $enc_curso - $resp_curso;
                    ?>
                    <div>
                        <!-- Cabecera curso -->
                        <div class="curso-header"
                             data-bs-toggle="collapse"
                             data-bs-target="#curso-<?php echo $uid_curso ?>"
                             aria-expanded="false">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-users" style="font-size:.8rem;color:#6c757d;"></i>
                                <span style="font-size:.88rem;">
                                    <?php echo htmlspecialchars($curso['label']) ?>
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <?php if($pend_curso > 0): ?>
                                <span class="badge bg-warning text-dark contador-badge">
                                    <?php echo $pend_curso ?>/<?php echo $enc_curso ?>
                                </span>
                                <?php else: ?>
                                <span class="badge bg-success contador-badge">
                                    <i class="fas fa-check me-1"></i><?php echo $enc_curso ?>
                                </span>
                                <?php endif; ?>
                                <i class="fas fa-chevron-down chevron" style="color:#6c757d;"></i>
                            </div>
                        </div>

                        <!-- Cuerpo curso: lista de encuestas/módulos -->
                        <div class="collapse" id="curso-<?php echo $uid_curso ?>">
                            <div class="pt-2 ps-2 d-flex flex-column gap-2">

                            <?php foreach($curso['encuestas'] as $enc): ?>
                            <div class="enc-card p-3 <?php echo $enc['respondida'] ? 'respondida' : '' ?>">
                                <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap">

                                    <!-- Info módulo -->
                                    <div style="min-width:0; flex:1;">
                                        <div class="fw-semibold" style="font-size:.9rem;">
                                            <?php echo htmlspecialchars($enc['nombre_corto'] ?: $enc['nombre_modulo']) ?>
                                        </div>
                                        <div class="text-muted" style="font-size:.78rem; line-height:1.4;">
                                            <?php if($enc['nombre_modulo'] !== ($enc['nombre_corto'] ?: $enc['nombre_modulo'])): ?>
                                            <span><?php echo htmlspecialchars($enc['nombre_modulo']) ?></span>
                                            <br>
                                            <?php endif; ?>
                                            <i class="fas fa-chalkboard-teacher me-1"></i>
                                            <?php echo htmlspecialchars($enc['nombre_profesor']) ?>
                                            <?php if($enc['nombre_evaluacion']): ?>
                                            &nbsp;·&nbsp;
                                            <span class="badge-eval badge bg-light text-secondary border">
                                                <?php echo htmlspecialchars($enc['nombre_evaluacion']) ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Estado + acción -->
                                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                        <?php if($enc['respondida']): ?>
                                        <span class="pill-hecho">
                                            <i class="fas fa-check-circle me-1"></i>Respondida
                                        </span>
                                        <?php else: ?>
                                        <span class="pill-pendiente">
                                            <i class="fas fa-circle-dot me-1"></i>Pendiente
                                        </span>
                                        <a href="<?php echo RUTA_URL ?>/responder/<?php echo $enc['token_publico'] ?>"
                                           class="btn-encuesta">
                                            <i class="fas fa-play"></i>Responder
                                        </a>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            </div>
                            <?php endforeach; ?>

                            </div>
                        </div>

                    </div>
                    <?php endforeach; /* cursos */ ?>

                    </div>
                </div>

            </div>
            <?php endforeach; /* ciclos */ ?>

            </div>
        </div>

    </div>
    <?php endforeach; /* departamentos */ ?>

    </div><!-- /acord-dept -->

    <!-- Resumen global -->
    <?php
    $g_total = 0; $g_resp = 0;
    foreach($datos['arbol'] as $dept)
        foreach($dept['ciclos'] as $ciclo)
            foreach($ciclo['cursos'] as $curso)
                foreach($curso['encuestas'] as $e){
                    $g_total++;
                    if($e['respondida']) $g_resp++;
                }
    $g_pend = $g_total - $g_resp;
    ?>
    <div class="mt-4 p-3 rounded-3 text-center border" style="background:#f8fbff;">
        <div class="d-flex justify-content-center gap-4 flex-wrap">
            <div>
                <div class="fw-bold fs-5"><?php echo $g_total ?></div>
                <div class="text-muted small">Total encuestas</div>
            </div>
            <div>
                <div class="fw-bold fs-5 text-success"><?php echo $g_resp ?></div>
                <div class="text-muted small">Respondidas</div>
            </div>
            <div>
                <div class="fw-bold fs-5 <?php echo $g_pend > 0 ? 'text-warning' : 'text-success' ?>">
                    <?php echo $g_pend ?>
                </div>
                <div class="text-muted small">Pendientes</div>
            </div>
        </div>
    </div>

    <?php endif; ?>

    <div class="text-center mt-4">
        <small class="text-muted">
            <i class="fas fa-shield-alt me-1"></i>
            Las encuestas son completamente anónimas. Tus respuestas no están vinculadas a tu identidad.
        </small>
    </div>

</div>

<script>
// Al abrir un nivel, propagar el aria-expanded al chevron
// Bootstrap ya lo hace — solo añadimos apertura automática si solo hay 1 dept
(function(){
    const depts = document.querySelectorAll('.dept-bloque');
    if(depts.length === 1){
        const h = depts[0].querySelector('.dept-header');
        if(h) bootstrap.Collapse.getOrCreateInstance(
            document.querySelector(h.getAttribute('data-bs-target'))
        ).show();
    }
})();
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
