<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container-fluid px-4 py-4">

    <div class="row justify-content-center mt-4">
        <div class="col-12 col-md-7">
            <div class="text-center p-5 border rounded"
                 style="background:#fff; box-shadow:0 2px 6px rgba(0,0,0,.07)">
                <i class="fas fa-check-circle fa-3x mb-3" style="color:#27ae60"></i>
                <h5 class="fw-bold mb-3">Mensaje enviado correctamente</h5>
                <p class="text-muted mb-4">
                    El mensaje se ha enviado a
                    <strong><?php echo $datos['okEnvio'] ?></strong> destinatario(s)
                    y se les ha notificado por correo electronico.
                </p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="<?php echo RUTA_URL ?>/Mensajes/nuevo" class="btn btn-custom">
                        <i class="fas fa-pen me-2"></i>Nuevo mensaje
                    </a>
                    <a href="<?php echo RUTA_URL ?>/Mensajes/enviados" class="btn btn-custom">
                        <i class="fas fa-paper-plane me-2"></i>Ver enviados
                    </a>
                    <a href="<?php echo RUTA_URL ?>/Mensajes/bandeja" class="btn btn-custom">
                        <i class="fas fa-inbox me-2"></i>Ir a bandeja
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
