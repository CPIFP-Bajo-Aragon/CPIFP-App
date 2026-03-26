<?php require_once RUTA_APP . '/vistas/inc/header.php'; ?>

<div class="p-4 shadow border mt-4 mx-3 tarjeta">
<div class="container-fluid">

    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-12">
            <strong id="ciclo_encabezado">
                <i class="fas fa-search me-2"></i>
                Confirmar retención de factura
                <span class="ms-2 navbar-destino" style="font-size:1rem;">
                    <?php echo htmlspecialchars($datos['persistencia']['nombreDestinoSeleccionado']) ?>
                </span>
            </strong>
        </div>
    </div>

    <?php
    $guardada = isset($datos['confirmarRetencion']['retencionGuardada'])
             && $datos['confirmarRetencion']['retencionGuardada'] === true;
    ?>

    <?php if ($guardada): ?>
    <!-- ===== RETENCIÓN GUARDADA ===== -->
    <div class="alert alert-success d-flex align-items-center mb-4">
        <i class="fas fa-check-circle fa-2x me-3"></i>
        <div>
            <strong>¡Retención registrada correctamente!</strong><br>
            ID de registro: <strong><?php echo $datos['confirmarRetencion']['idRetencion'] ?></strong>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-12 col-sm-6">
            <a href="<?php echo RUTA_URL ?>/GestionFacturas/retencion" class="btn btn-custom w-100">
                <i class="fas fa-plus me-2"></i>Registrar otra retención
            </a>
        </div>
        <div class="col-12 col-sm-6">
            <a href="<?php echo RUTA_URL ?>/" class="btn btn-outline-secondary w-100">
                <i class="fas fa-home me-2"></i>Volver al inicio
            </a>
        </div>
    </div>

    <?php else: ?>
    <!-- ===== DATOS A CONFIRMAR ===== -->

    <div class="table-responsive mb-4">
    <table class="table table-bordered tabla-formato">
        <tbody>
            <tr>
                <th style="width:30%">Proveedor</th>
                <td colspan="3">
                    <?php echo htmlspecialchars($datos['confirmarRetencion']['NomProveedor']) ?>
                    <span class="text-muted ms-2" style="font-size:.88rem">
                        (<?php echo htmlspecialchars($datos['confirmarRetencion']['CIF']) ?>)
                    </span>
                </td>
            </tr>
            <tr>
                <th>Destino</th>
                <td><?php echo htmlspecialchars($datos['confirmarRetencion']['NomDestino']) ?></td>
                <th style="width:20%">Responsable</th>
                <td><?php echo htmlspecialchars($datos['confirmarRetencion']['Responsable']) ?></td>
            </tr>
            <tr>
                <th>Nº Factura</th>
                <td><?php echo htmlspecialchars($datos['confirmarRetencion']['NFactura']) ?: '<span class="text-muted">—</span>' ?></td>
                <th>Fecha comprobación</th>
                <td><?php echo htmlspecialchars($datos['confirmarRetencion']['Faprobacion']) ?></td>
            </tr>
            <tr>
                <th>Importe</th>
                <td colspan="3">
                    <strong><?php echo number_format((float)$datos['confirmarRetencion']['Importe'], 2, ',', '.') ?> €</strong>
                </td>
            </tr>
            <?php if (!empty($datos['confirmarRetencion']['Motivos'])): ?>
            <tr>
                <th>Motivos de no conformidad</th>
                <td colspan="3"><?php echo htmlspecialchars($datos['confirmarRetencion']['Motivos']) ?></td>
            </tr>
            <?php endif ?>
        </tbody>
    </table>
    </div>

    <!-- Formulario oculto para guardarRetencion -->
    <form id="fmRetencion"
          action="<?php echo RUTA_URL ?>/GestionFacturas/guardarRetencion"
          method="POST">
        <input type="hidden" name="NomProveedor"  value="<?php echo htmlspecialchars($datos['confirmarRetencion']['NomProveedor']) ?>">
        <input type="hidden" name="NomDestino"    value="<?php echo htmlspecialchars($datos['confirmarRetencion']['NomDestino']) ?>">
        <input type="hidden" name="Responsable"   value="<?php echo htmlspecialchars($datos['confirmarRetencion']['Responsable']) ?>">
        <input type="hidden" name="CIF"           value="<?php echo htmlspecialchars($datos['confirmarRetencion']['CIF']) ?>">
        <input type="hidden" name="NFactura"      value="<?php echo htmlspecialchars($datos['confirmarRetencion']['NFactura']) ?>">
        <input type="hidden" name="Importe"       value="<?php echo htmlspecialchars($datos['confirmarRetencion']['Importe']) ?>">
        <input type="hidden" name="Faprobacion"   value="<?php echo htmlspecialchars($datos['confirmarRetencion']['Faprobacion']) ?>">
        <input type="hidden" name="Motivos"       value="<?php echo htmlspecialchars($datos['confirmarRetencion']['Motivos']) ?>">

        <div class="row g-3">
            <div class="col-12 col-sm-6">
                <button type="submit" name="Aceptar" class="btn btn-success w-100">
                    <i class="fas fa-save me-2"></i>Confirmar y guardar
                </button>
            </div>
            <div class="col-12 col-sm-6">
                <button type="button" class="btn btn-outline-secondary w-100"
                        onclick="cancelarRetencion()">
                    <i class="fas fa-arrow-left me-2"></i>Volver y corregir
                </button>
            </div>
        </div>
    </form>

    <?php endif ?>

</div>
</div>

<script>
    function cancelarRetencion() {
        const btn = document.createElement('input');
        btn.type  = 'hidden';
        btn.name  = 'cancelar';
        btn.value = '1';
        document.getElementById('fmRetencion').appendChild(btn);
        document.getElementById('fmRetencion').submit();
    }
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
