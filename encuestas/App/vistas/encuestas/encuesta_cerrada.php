<?php require_once RUTA_APP . '/vistas/inc/header_publico.php' ?>
<div class="container py-5 text-center" style="max-width:500px;">
    <div class="card border-0 shadow">
        <div class="card-body py-5">
            <div style="font-size:4rem; color:#e67e22;"><i class="fas fa-lock"></i></div>
            <h3 class="mt-3">Encuesta cerrada</h3>
            <p class="text-muted">
                La encuesta <strong><?php echo htmlspecialchars($datos['encuesta']->titulo) ?></strong>
                ya no admite respuestas. Gracias por tu interés.
            </p>
        </div>
    </div>
</div>
<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
