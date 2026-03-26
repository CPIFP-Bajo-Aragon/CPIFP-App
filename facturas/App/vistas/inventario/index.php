<?php include RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container mt-4 pt-4">

    <div class="row mb-3 align-items-center">
        <div class="col">
            <h4 class="fw-bold" style="color:#2C3E50">
                <i class="bi bi-archive me-2" style="color:#0583c3"></i>Inventario
            </h4>
            <p class="text-muted mb-0">Gestión del inventario del centro</p>
        </div>
    </div>

    <div class="row g-3 justify-content-center mt-1">

        <!-- Consultar -->
        <div class="col-12 col-sm-6 col-lg-3">
            <a href="<?php echo RUTA_URL ?>/Inventario/consulta" class="text-decoration-none">
                <div class="card h-100 text-center p-3 shadow-sm"
                     style="border-top: 4px solid #0583c3 !important; border-radius:8px;">
                    <div class="card-body">
                        <i class="bi bi-search fs-1 mb-3 d-block" style="color:#0583c3"></i>
                        <h5 class="card-title fw-bold">Consultar</h5>
                        <p class="card-text text-muted small">
                            Busca y filtra todo el inventario por departamento, categoría o texto libre.
                        </p>
                    </div>
                </div>
            </a>
        </div>

        <?php if ($datos['puedeGest']): ?>
        <!-- Modificar -->
        <div class="col-12 col-sm-6 col-lg-3">
            <a href="<?php echo RUTA_URL ?>/Inventario/modificar" class="text-decoration-none">
                <div class="card h-100 text-center p-3 shadow-sm"
                     style="border-top: 4px solid #f39c12 !important; border-radius:8px;">
                    <div class="card-body">
                        <i class="bi bi-pencil-square fs-1 mb-3 d-block" style="color:#f39c12"></i>
                        <h5 class="card-title fw-bold">Modificar</h5>
                        <p class="card-text text-muted small">
                            Actualiza los datos de artículos de
                            <?php echo $datos['esED'] ? 'cualquier departamento' : 'tu departamento' ?>.
                        </p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Bajas -->
        <div class="col-12 col-sm-6 col-lg-3">
            <a href="<?php echo RUTA_URL ?>/Inventario/bajas" class="text-decoration-none">
                <div class="card h-100 text-center p-3 shadow-sm"
                     style="border-top: 4px solid #e74c3c !important; border-radius:8px;">
                    <div class="card-body">
                        <i class="bi bi-trash3 fs-1 mb-3 d-block" style="color:#e74c3c"></i>
                        <h5 class="card-title fw-bold">Dar de baja</h5>
                        <p class="card-text text-muted small">
                            Registra la baja de artículos obsoletos, rotos o extraviados.
                        </p>
                    </div>
                </div>
            </a>
        </div>
        <?php endif ?>

        <?php if ($datos['esED']): ?>
        <!-- Alta -->
        <div class="col-12 col-sm-6 col-lg-3">
            <a href="<?php echo RUTA_URL ?>/Inventario/alta" class="text-decoration-none">
                <div class="card h-100 text-center p-3 shadow-sm"
                     style="border-top: 4px solid #27ae60 !important; border-radius:8px;">
                    <div class="card-body">
                        <i class="bi bi-plus-circle fs-1 mb-3 d-block" style="color:#27ae60"></i>
                        <h5 class="card-title fw-bold">Nueva Alta</h5>
                        <p class="card-text text-muted small">
                            Registra nuevos bienes procedentes de facturas inventariables o donaciones.
                        </p>
                    </div>
                </div>
            </a>
        </div>
        <?php endif ?>

    </div>

</div>

<?php include RUTA_APP . '/vistas/inc/footer.php' ?>
