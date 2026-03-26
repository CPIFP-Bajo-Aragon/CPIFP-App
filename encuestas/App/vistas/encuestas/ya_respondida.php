<?php require_once RUTA_APP . '/vistas/inc/header_publico.php' ?>

<div class="container py-4" style="max-width:680px;">

    <div class="card border-0 shadow mb-4" style="border-top:4px solid #f39c12 !important;">
        <div class="card-body py-4 text-center">
            <div style="font-size:4rem; color:#f39c12;">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h3 class="mt-2 mb-1">Esta encuesta ya fue respondida</h3>
            <p class="text-muted mb-0">
                Parece que ya has respondido la encuesta de
                <strong><?php echo htmlspecialchars($datos['encuesta']->nombre_modulo ?? $datos['encuesta']->titulo) ?></strong>
                desde este dispositivo.
            </p>
            <p class="text-muted small mt-2 mb-0">
                <i class="fas fa-shield-alt me-1"></i>
                Las encuestas son anónimas. Solo se permite una respuesta por dispositivo para garantizar la fiabilidad de los resultados.
            </p>
        </div>
    </div>

    <!-- Encuestas aún pendientes -->
    <?php if(!empty($datos['pendientes'])): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-header fw-bold" style="background:#e8f4fd; color:#0583c3;">
            <i class="fas fa-list-check me-2"></i>
            Encuestas pendientes en tu grupo (<?php echo count($datos['pendientes']) ?>)
        </div>
        <div class="card-body p-0">
            <?php foreach($datos['pendientes'] as $enc): ?>
            <a href="<?php echo RUTA_URL ?>/responder/<?php echo $enc->token_publico ?>"
               class="d-flex align-items-center gap-3 px-3 py-3 text-decoration-none text-dark
                      border-bottom enc-pendiente">
                <div class="flex-shrink-0 text-primary" style="font-size:1.4rem;">
                    <i class="fas fa-poll-h"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold"><?php echo htmlspecialchars($enc->nombre_corto ?: $enc->nombre_modulo) ?></div>
                    <div class="text-muted small"><?php echo htmlspecialchars($enc->nombre_profesor) ?></div>
                </div>
                <div class="flex-shrink-0">
                    <span class="btn btn-sm btn-custom">
                        Responder <i class="fas fa-arrow-right ms-1"></i>
                    </span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="card border-0 shadow-sm text-center py-4">
        <div style="font-size:2.5rem; color:#27ae60;"><i class="fas fa-star"></i></div>
        <h5 class="mt-2 mb-1">¡Has completado todas las encuestas de tu grupo!</h5>
        <p class="text-muted small mb-0">Gracias por tu participación.</p>
    </div>
    <?php endif; ?>

    <div class="text-center mt-3">
        <a href="<?php echo RUTA_URL ?>/portal" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-home me-1"></i>Volver al portal
        </a>
    </div>

</div>

<style>
.enc-pendiente { transition: background .15s; }
.enc-pendiente:hover { background: #f0f8ff; }
.enc-pendiente:last-child { border-bottom: none !important; }
</style>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
