<?php require_once RUTA_APP . '/vistas/inc/header_publico.php' ?>

<div class="container py-5" style="max-width:480px;">
    <div class="card border-0 shadow" style="border-top:4px solid #0583c3 !important;">
        <div class="card-body py-5 px-4">

            <div class="text-center mb-4">
                <div style="font-size:3.5rem; color:#0583c3;">
                    <i class="fas fa-key"></i>
                </div>
                <h3 class="mt-2 mb-1">Código de acceso</h3>
                <p class="text-muted small">
                    Esta encuesta requiere un código de acceso.<br>
                    Consulta a tu profesor/a el código de 6 dígitos.
                </p>
            </div>

            <!-- Info de la encuesta -->
            <div class="alert alert-light border mb-4 small">
                <strong><i class="fas fa-poll me-1"></i><?php echo htmlspecialchars($datos['encuesta']->titulo) ?></strong>
                <?php if($datos['encuesta']->nombre_profesor): ?>
                <br><?php echo htmlspecialchars($datos['encuesta']->nombre_profesor) ?>
                &nbsp;·&nbsp;<?php echo htmlspecialchars($datos['encuesta']->nombre_modulo ?? '') ?>
                <?php endif; ?>
            </div>

            <?php if($datos['error_codigo']): ?>
            <div class="alert alert-danger">
                <i class="fas fa-times-circle me-1"></i><?php echo htmlspecialchars($datos['error_codigo']) ?>
            </div>
            <?php endif; ?>

            <form method="post"
                  action="<?php echo RUTA_URL ?>/responder/<?php echo $datos['encuesta']->token_publico ?>">

                <div class="mb-3">
                    <label class="form-label fw-bold">Introduce el código de 6 dígitos</label>
                    <input type="text" name="codigo_acceso" class="form-control form-control-lg text-center"
                           maxlength="6" minlength="6" pattern="[0-9]{6}"
                           placeholder="000000" autocomplete="off" autofocus
                           style="font-size:2rem; letter-spacing:0.4em; font-weight:bold;">
                    <div class="form-text text-center">Solo números, 6 dígitos.</div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-lg" style="background:#0583c3; color:#fff;">
                        <i class="fas fa-unlock me-2"></i>Acceder a la encuesta
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
