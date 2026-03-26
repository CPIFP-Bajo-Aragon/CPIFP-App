<?php require_once RUTA_APP . '/vistas/inc/header_no_login.php' ?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-12 col-sm-8 col-md-5 col-lg-4">

            <div class="p-4 border rounded" style="background:#fff; box-shadow:0 2px 8px rgba(0,0,0,.1)">

                <h5 class="mb-4 fw-bold" style="color:#0583c3; text-align:center">
                    <i class="fas fa-user-graduate me-2"></i>Orientación IOPE
                </h5>

                <?php if (isset($datos['error']) && $datos['error'] == 'error_1'): ?>
                <div class="alert alert-danger mb-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>Usuario o contraseña incorrectos.
                </div>
                <?php endif ?>

                <form method="post">
                    <div class="input-group mb-3">
                        <label class="input-group-text"><i class="fas fa-user"></i></label>
                        <input type="text" name="usuario" class="form-control"
                               placeholder="Usuario" required autocomplete="username">
                    </div>
                    <div class="input-group mb-4">
                        <label class="input-group-text"><i class="fas fa-lock"></i></label>
                        <input type="password" name="pass" class="form-control"
                               placeholder="Contraseña" required autocomplete="current-password">
                    </div>
                    <button type="submit" class="btn w-100"
                            style="background:#0583c3; color:#fff; font-weight:600">
                        <i class="fas fa-sign-in-alt me-2"></i>Entrar
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
