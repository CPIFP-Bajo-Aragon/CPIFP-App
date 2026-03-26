<?php require_once RUTA_APP . '/vistas/inc/header_publico.php' ?>

<div class="container py-5" style="max-width:780px;">

    <!-- Cabecera de la encuesta -->
    <div class="card border-0 shadow mb-4" style="border-top:4px solid #0583c3 !important;">
        <div class="card-body">
            <h2 class="mb-1" style="color:#0583c3;">
                <?php echo htmlspecialchars($datos['encuesta']->titulo) ?>
            </h2>

            <?php if($datos['encuesta']->nombre_profesor): ?>
            <div class="text-muted mb-1">
                <i class="fas fa-chalkboard-teacher me-1"></i>
                Profesor: <strong><?php echo htmlspecialchars($datos['encuesta']->nombre_profesor) ?></strong>
                &nbsp;|&nbsp;
                Módulo: <strong><?php echo htmlspecialchars($datos['encuesta']->nombre_modulo) ?></strong>
                &nbsp;|&nbsp;
                <?php echo htmlspecialchars($datos['encuesta']->nombre_ciclo . ' · ' . $datos['encuesta']->nombre_curso) ?>
            </div>
            <?php endif; ?>

            <div class="text-muted small">
                <i class="fas fa-calendar-alt me-1"></i>
                Curso <?php echo htmlspecialchars($datos['encuesta']->curso_academico) ?>
                <?php if($datos['encuesta']->trimestre): ?>
                 · <?php echo etiquetaTrimestre($datos['encuesta']->trimestre) ?>
                <?php endif; ?>
            </div>

            <?php if($datos['encuesta']->descripcion): ?>
            <hr>
            <p class="mb-0"><?php echo nl2br(htmlspecialchars($datos['encuesta']->descripcion)) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Error de validación -->
    <?php if(!empty($datos['error'])): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-1"></i><?php echo $datos['error'] ?>
    </div>
    <?php endif; ?>

    <!-- Formulario de respuesta -->
    <form method="post" action="<?php echo RUTA_URL ?>/responder/<?php echo $datos['encuesta']->token_publico ?>" id="form-encuesta">

        <!-- Escala de referencia — solo si hay preguntas de puntuación -->
        <?php
        $hay_puntuacion = false;
        foreach($datos['preguntas'] as $p){
            if(($p->tipo_respuesta ?? 'puntuacion') === 'puntuacion'){ $hay_puntuacion = true; break; }
        }
        ?>
        <?php if($hay_puntuacion): ?>
        <div class="card border-0 bg-light mb-4 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-danger me-1">1</span>
                    <span class="badge bg-danger me-1">2</span>
                    <span class="badge bg-danger me-1">3</span>
                    <small class="text-muted">Muy insatisfecho</small>
                </div>
                <div>
                    <span class="badge bg-warning text-dark me-1">4</span>
                    <span class="badge bg-warning text-dark me-1">5</span>
                    <span class="badge bg-warning text-dark me-1">6</span>
                    <small class="text-muted">Regular</small>
                </div>
                <div>
                    <span class="badge bg-success me-1">7</span>
                    <span class="badge bg-success me-1">8</span>
                    <span class="badge bg-success me-1">9</span>
                    <span class="badge bg-success me-1">10</span>
                    <small class="text-muted">Muy satisfecho</small>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Preguntas -->
        <?php foreach($datos['preguntas'] as $p):
            $tipo = $p->tipo_respuesta ?? 'puntuacion';
            $opts = [];
            if($tipo === 'opciones' && !empty($p->opciones_json)){
                $opts = json_decode($p->opciones_json, true) ?: [];
            }
            $val_guardado = $datos['post']['preguntas'][$p->id_pregunta] ?? null;
        ?>
        <div class="card border-0 shadow-sm mb-3 pregunta-card" id="preg_<?php echo $p->id_pregunta ?>">
            <div class="card-body">
                <p class="fw-bold mb-3">
                    <span class="badge bg-secondary me-2"><?php echo $p->orden ?></span>
                    <?php echo htmlspecialchars($p->pregunta) ?>
                </p>

                <?php if($tipo === 'puntuacion'): ?>
                <!-- Valoración 1-10 -->
                <div class="d-flex flex-wrap gap-2 justify-content-center radio-group">
                    <?php for($v = 1; $v <= 10; $v++):
                        $color = ($v <= 3) ? 'btn-outline-danger' : (($v <= 6) ? 'btn-outline-warning' : 'btn-outline-success');
                    ?>
                    <div>
                        <input type="radio" class="btn-check"
                               name="preguntas[<?php echo $p->id_pregunta ?>]"
                               id="p<?php echo $p->id_pregunta ?>_<?php echo $v ?>"
                               value="<?php echo $v ?>"
                               <?php echo ($val_guardado == $v) ? 'checked' : '' ?> required>
                        <label class="btn <?php echo $color ?> btn-radio-nota"
                               for="p<?php echo $p->id_pregunta ?>_<?php echo $v ?>">
                            <?php echo $v ?>
                        </label>
                    </div>
                    <?php endfor; ?>
                </div>

                <?php elseif($tipo === 'opciones' && !empty($opts)): ?>
                <!-- Opciones múltiples -->
                <div class="d-flex flex-column gap-2">
                    <?php foreach($opts as $i => $opcion): ?>
                    <div>
                        <input type="radio" class="btn-check"
                               name="preguntas[<?php echo $p->id_pregunta ?>]"
                               id="p<?php echo $p->id_pregunta ?>_<?php echo $i ?>"
                               value="<?php echo $i ?>"
                               <?php echo ($val_guardado !== null && (int)$val_guardado === $i) ? 'checked' : '' ?>
                               required>
                        <label class="btn btn-outline-primary btn-opcion w-100 text-start"
                               for="p<?php echo $p->id_pregunta ?>_<?php echo $i ?>">
                            <span class="badge bg-primary me-2" style="min-width:1.6rem;">
                                <?php echo chr(97 + $i) ?>
                            </span>
                            <?php echo htmlspecialchars($opcion) ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php elseif($tipo === 'numerica'): ?>
                <!-- Numérica abierta -->
                <div class="row justify-content-center">
                    <div class="col-12 col-sm-5 col-md-3">
                        <input type="number" min="0"
                               class="form-control form-control-lg text-center"
                               name="preguntas[<?php echo $p->id_pregunta ?>]"
                               placeholder="—"
                               value="<?php echo htmlspecialchars($val_guardado ?? '') ?>"
                               required>
                        <div class="form-text text-center">Introduce un número</div>
                    </div>
                </div>

                <?php else: ?>
                <!-- Fallback puntuación -->
                <div class="d-flex flex-wrap gap-2 justify-content-center radio-group">
                    <?php for($v = 1; $v <= 10; $v++):
                        $color = ($v <= 3) ? 'btn-outline-danger' : (($v <= 6) ? 'btn-outline-warning' : 'btn-outline-success');
                    ?>
                    <div>
                        <input type="radio" class="btn-check"
                               name="preguntas[<?php echo $p->id_pregunta ?>]"
                               id="p<?php echo $p->id_pregunta ?>_<?php echo $v ?>"
                               value="<?php echo $v ?>"
                               <?php echo ($val_guardado == $v) ? 'checked' : '' ?> required>
                        <label class="btn <?php echo $color ?> btn-radio-nota"
                               for="p<?php echo $p->id_pregunta ?>_<?php echo $v ?>"><?php echo $v ?></label>
                    </div>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>

            </div>
        </div>
        <?php endforeach; ?>

        <!-- Comentarios cualitativos -->
        <?php
        $mostrar_mejor_peor   = (bool)($datos['encuesta']->mostrar_mejor_peor   ?? 1);
        $mostrar_observaciones= (bool)($datos['encuesta']->mostrar_observaciones ?? 1);
        $hay_comentarios = $mostrar_mejor_peor || $mostrar_observaciones;
        ?>
        <?php if($hay_comentarios): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header" style="background:#e9f4fb; font-weight:bold;">
                <i class="fas fa-comment-alt me-1"></i>Comentarios (opcional)
            </div>
            <div class="card-body">
                <?php if($mostrar_mejor_peor): ?>
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-thumbs-up text-success me-1"></i>
                        <strong>Lo mejor</strong> del módulo / servicio
                    </label>
                    <textarea name="comentario_mejor" class="form-control" rows="2"
                              placeholder="¿Qué destacarías positivamente?"><?php echo htmlspecialchars($datos['post']['comentario_mejor'] ?? '') ?></textarea>
                </div>
                <div class="<?php echo $mostrar_observaciones ? 'mb-3' : 'mb-0' ?>">
                    <label class="form-label">
                        <i class="fas fa-thumbs-down text-danger me-1"></i>
                        <strong>Lo peor</strong> o aspectos a mejorar
                    </label>
                    <textarea name="comentario_peor" class="form-control" rows="2"
                              placeholder="¿Qué mejorarías?"><?php echo htmlspecialchars($datos['post']['comentario_peor'] ?? '') ?></textarea>
                </div>
                <?php endif; ?>
                <?php if($mostrar_observaciones): ?>
                <div class="mb-0">
                    <label class="form-label">
                        <i class="fas fa-comment text-secondary me-1"></i>
                        <strong>Observaciones</strong> adicionales
                    </label>
                    <textarea name="comentario_libre" class="form-control" rows="2"
                              placeholder="Cualquier otro comentario..."><?php echo htmlspecialchars($datos['post']['comentario_libre'] ?? '') ?></textarea>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Aviso privacidad -->
        <div class="alert alert-light border mb-4 small text-muted">
            <i class="fas fa-shield-alt me-1"></i>
            Esta encuesta es completamente <strong>anónima</strong>. Tus respuestas no están vinculadas a tu identidad
            y solo se utilizarán de forma agregada para la mejora del centro educativo.
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-lg" style="background:#0583c3; color:#fff; font-size:1.2rem;">
                <i class="fas fa-paper-plane me-2"></i>Enviar respuesta
            </button>
        </div>

    </form>
</div>

<style>
.btn-radio-nota {
    width: 46px;
    height: 46px;
    font-size: 1.1rem;
    font-weight: bold;
    border-radius: 8px !important;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.1s;
}
.btn-check:checked + .btn-radio-nota {
    transform: scale(1.15);
    box-shadow: 0 0 0 3px rgba(5,131,195,0.35);
}
.btn-opcion {
    font-size: .95rem;
    padding: .55rem 1rem;
    border-radius: 8px !important;
    transition: transform 0.1s;
}
.btn-check:checked + .btn-opcion {
    transform: scale(1.01);
    box-shadow: 0 0 0 3px rgba(5,131,195,0.3);
}
.pregunta-card.respondida {
    border-left: 4px solid #27ae60 !important;
}
</style>

<script>
// Marcar visualmente las preguntas ya respondidas
document.querySelectorAll('.btn-check').forEach(function(radio){
    radio.addEventListener('change', function(){
        const preg = this.name.match(/\[(\d+)\]/)[1];
        const card = document.getElementById('preg_' + preg);
        if(card) card.classList.add('respondida');
    });
    if(radio.checked){
        const preg = radio.name.match(/\[(\d+)\]/)[1];
        const card = document.getElementById('preg_' + preg);
        if(card) card.classList.add('respondida');
    }
});
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
