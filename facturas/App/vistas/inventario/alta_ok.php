<?php include RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container mt-4 pt-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6 text-center">
            <div class="card shadow-sm p-4" style="border-top:4px solid #27ae60; border-radius:8px;">
                <i class="bi bi-check-circle-fill text-success mb-3" style="font-size:4rem"></i>
                <h4 class="fw-bold mb-1">¡Alta registrada con éxito!</h4>
                <p class="text-muted">El bien ha sido dado de alta en el inventario.</p>
                <p class="mb-4">
                    <strong>Número de entrada: </strong>
                    <span class="badge bg-primary fs-6">
                        NE-<?php echo str_pad($datos['nEntrada'], 5, '0', STR_PAD_LEFT) ?>
                    </span>
                </p>
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <a href="<?php echo RUTA_URL ?>/Inventario/alta"
                       class="btn btn-success">
                        <i class="bi bi-plus me-1"></i>Nueva alta
                    </a>
                    <a href="<?php echo RUTA_URL ?>/Inventario/consulta"
                       class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Ver inventario
                    </a>
                    <a href="<?php echo RUTA_URL ?>/Inventario/index"
                       class="btn btn-outline-secondary">
                        <i class="bi bi-house me-1"></i>Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include RUTA_APP . '/vistas/inc/footer.php' ?>
