<?php require_once RUTA_APP . '/vistas/inc/header.php'; ?>


<div class="p-4 shadow border mt-4 mx-3 tarjeta">
<div class="container-fluid">

    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-12">
            <strong id="ciclo_encabezado">
                <i class="fas fa-search me-2"></i>
                Confirmar datos de la factura
                <span class="ms-2 navbar-destino" style="font-size:1rem;">
                    <?php echo $datos['persistencia']['nombreDestinoSeleccionado'] ?>
                </span>
            </strong>
        </div>
    </div>


    <?php
    // ---- ESTADO: factura YA guardada ----
    $guardada = isset($datos['confirmarFactura']['facturaGuardada']) && $datos['confirmarFactura']['facturaGuardada'] === true;
    ?>


    <?php if ($guardada): ?>
    <!-- ===== FACTURA GUARDADA: solo botón imprimir ===== -->
    <div class="alert alert-success d-flex align-items-center mb-4">
        <i class="fas fa-check-circle fa-2x me-3"></i>
        <div>
            <strong>¡Factura guardada correctamente!</strong><br>
            Nº de asiento: <strong><?php echo $datos['confirmarFactura']['nAsiento'] ?></strong>
        </div>
    </div>
    <form action="<?php echo RUTA_URL ?>/GestionFacturas/imprimirFactura"
          name="justificanteFactura" id="justificanteFactura" method="POST" target="_blank">
        <input type="hidden" name="N_Asiento" value="<?php echo $datos['confirmarFactura']['nAsiento'] ?>">
        <button type="submit" name="imprimirFactura" class="btn btn-success w-100">
            <i class="fas fa-print me-2"></i>Imprimir justificante de factura
        </button>
    </form>


    <?php else: ?>
    <!-- ===== DATOS A CONFIRMAR ===== -->

    <!-- Bloque resumen -->
    <div class="table-responsive mb-4">
    <table class="table table-bordered tabla-formato">
        <tbody>
            <tr>
                <th class="tabla-formato" style="width:30%">Proveedor</th>
                <td><?php echo $datos['confirmarFactura']['NomProveedor'] ?></td>
                <th style="width:20%">Destino</th>
                <td><?php echo $datos['confirmarFactura']['NomDestnino'] ?></td>
            </tr>
            <tr>
                <th>Responsable</th>
                <td><?php echo $datos['confirmarFactura']['responsable'] ?></td>
                <th>Nº Factura</th>
                <td><?php echo $datos['confirmarFactura']['NFactura'] ?></td>
            </tr>
            <tr>
                <th>Fecha factura</th>
                <td><?php echo $datos['confirmarFactura']['Ffactura'] ?></td>
                <th>Fecha conformidad</th>
                <td><?php echo $datos['confirmarFactura']['Fconformidad'] ?></td>
            </tr>
            <tr>
                <th>Importe</th>
                <td><strong><?php echo number_format((float)$datos['confirmarFactura']['importe'], 2, ',', '.') ?> €</strong></td>
                <th>Inventariable</th>
                <td><?php echo ($datos['confirmarFactura']['inventariable'] == 'S') ? 'Sí' : 'No' ?></td>
            </tr>
            <?php if (!empty($datos['confirmarFactura']['descripcion'])): ?>
            <tr>
                <th>Descripción</th>
                <td colspan="3"><?php echo $datos['confirmarFactura']['descripcion'] ?></td>
            </tr>
            <?php endif ?>
        </tbody>
    </table>
    </div>

    <!-- Evaluación del proveedor -->
    <?php
    $media = ($datos['confirmarFactura']['Item1'] + $datos['confirmarFactura']['Item2']
            + $datos['confirmarFactura']['Item3'] + $datos['confirmarFactura']['Item4']) / 4;
    $penalizado = ($datos['confirmarFactura']['Item1'] == 1 || $datos['confirmarFactura']['Item2'] == 1
                || $datos['confirmarFactura']['Item3'] == 1 || $datos['confirmarFactura']['Item4'] == 1
                || $media < 4);
    ?>
    <div class="p-3 border rounded mb-4 <?php echo $penalizado ? 'border-danger bg-light' : 'border-success bg-light' ?>">
        <p class="fw-bold mb-2">
            <i class="fas fa-star me-2 <?php echo $penalizado ? 'text-danger' : 'text-success' ?>"></i>
            Evaluación del proveedor
        </p>
        <div class="row">
            <div class="col-6 col-md-3 text-center">
                <div class="fw-bold text-primary">Item 1</div>
                <div style="font-size:1.8rem; font-weight:bold"><?php echo $datos['confirmarFactura']['Item1'] ?></div>
                <small class="text-muted">Atención e información</small>
            </div>
            <div class="col-6 col-md-3 text-center">
                <div class="fw-bold text-primary">Item 2</div>
                <div style="font-size:1.8rem; font-weight:bold"><?php echo $datos['confirmarFactura']['Item2'] ?></div>
                <small class="text-muted">Plazos de entrega</small>
            </div>
            <div class="col-6 col-md-3 text-center">
                <div class="fw-bold text-primary">Item 3</div>
                <div style="font-size:1.8rem; font-weight:bold"><?php echo $datos['confirmarFactura']['Item3'] ?></div>
                <small class="text-muted">Transporte y embalaje</small>
            </div>
            <div class="col-6 col-md-3 text-center">
                <div class="fw-bold text-primary">Item 4</div>
                <div style="font-size:1.8rem; font-weight:bold"><?php echo $datos['confirmarFactura']['Item4'] ?></div>
                <small class="text-muted">Precio / Calidad</small>
            </div>
        </div>
        <div class="text-center mt-3">
            <?php if ($penalizado): ?>
                <span class="badge bg-danger fs-6">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    PROVEEDOR PENALIZADO — Media: <?php echo number_format($media, 2) ?>
                </span>
                <p class="text-danger mt-1 mb-0 small">Revisar con el Dep. de Calidad o Secretaría</p>
            <?php else: ?>
                <span class="badge bg-success fs-6">
                    <i class="fas fa-check me-1"></i>
                    Media del proveedor: <?php echo number_format($media, 2) ?>
                </span>
            <?php endif ?>
        </div>
    </div>

    <!-- Formulario oculto con todos los datos para guardarFactura -->
    <form action="<?php echo RUTA_URL ?>/GestionFacturas/guardarFactura"
          name="guardarFactura" id="guardarFactura" method="POST">
        <input type="hidden" name="NomProveedor"  value="<?php echo $datos['confirmarFactura']['NomProveedor'] ?>">
        <input type="hidden" name="NomDestnino"   value="<?php echo $datos['confirmarFactura']['NomDestnino'] ?>">
        <input type="hidden" name="responsable"   value="<?php echo $datos['confirmarFactura']['responsable'] ?>">
        <input type="hidden" name="CIF"           value="<?php echo $datos['confirmarFactura']['CIF'] ?>">
        <input type="hidden" name="NFactura"      value="<?php echo $datos['confirmarFactura']['NFactura'] ?>">
        <input type="hidden" name="inventariable" value="<?php echo $datos['confirmarFactura']['inventariable'] ?>">
        <input type="hidden" name="Fconformidad"  value="<?php echo $datos['confirmarFactura']['Fconformidad'] ?>">
        <input type="hidden" name="Ffactura"      value="<?php echo $datos['confirmarFactura']['Ffactura'] ?>">
        <input type="hidden" name="descripcion_as" value="<?php echo $datos['confirmarFactura']['descripcion'] ?>">
        <input type="hidden" name="importe"       value="<?php echo $datos['confirmarFactura']['importe'] ?>">
        <input type="hidden" name="Item1"         value="<?php echo $datos['confirmarFactura']['Item1'] ?>">
        <input type="hidden" name="Item2"         value="<?php echo $datos['confirmarFactura']['Item2'] ?>">
        <input type="hidden" name="Item3"         value="<?php echo $datos['confirmarFactura']['Item3'] ?>">
        <input type="hidden" name="Item4"         value="<?php echo $datos['confirmarFactura']['Item4'] ?>">
        <!-- cancelar se usa por JS para volver atrás sin guardar -->
        <input type="hidden" name="cancelar" value="cancelar" id="campo_cancelar">

        <div class="row g-3">
            <div class="col-12 col-sm-6">
                <button type="submit" name="Aceptar" class="btn btn-success w-100">
                    <i class="fas fa-save me-2"></i>Confirmar y guardar
                </button>
            </div>
            <div class="col-12 col-sm-6">
                <button type="button" class="btn btn-outline-secondary w-100" onclick="cancelarDatos()">
                    <i class="fas fa-arrow-left me-2"></i>Volver y corregir
                </button>
            </div>
        </div>
    </form>

    <?php endif ?>

</div>
</div>


<script>
    // Cancelar: enviamos el formulario con action vacío para que el controlador
    // detecte el campo 'cancelar' y vuelva a mostrar conformidadPago
    function cancelarDatos() {
        document.getElementById('guardarFactura').setAttribute('action',
            '<?php echo RUTA_URL ?>/GestionFacturas/conformidadPago');
        document.getElementById('guardarFactura').submit();
    }
</script>


<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
