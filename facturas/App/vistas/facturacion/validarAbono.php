<?php require_once RUTA_APP . '/vistas/inc/header.php'; ?>

<div class="p-4 shadow border mt-4 mx-3 tarjeta">
<div class="container-fluid">

    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-12">
            <strong id="ciclo_encabezado">
                <i class="fas fa-search me-2"></i>
                Confirmar datos del abono
                <span class="ms-2 navbar-destino" style="font-size:1rem;">
                    <?php echo htmlspecialchars($datos['persistencia']['nombreDestinoSeleccionado']) ?>
                </span>
            </strong>
        </div>
    </div>

    <?php
    $guardado = isset($datos['confirmarAbono']['abonoGuardado']) && $datos['confirmarAbono']['abonoGuardado'] === true;
    ?>

    <?php if ($guardado): ?>
    <!-- ===== ABONO GUARDADO ===== -->
    <div class="alert alert-success d-flex align-items-center mb-4">
        <i class="fas fa-check-circle fa-2x me-3"></i>
        <div>
            <strong>¡Abono registrado correctamente!</strong><br>
            ID de registro: <strong><?php echo $datos['confirmarAbono']['idAbono'] ?></strong>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-6">
            <a href="<?php echo RUTA_URL ?>/GestionFacturas/abono" class="btn btn-custom w-100">
                <i class="fas fa-plus me-2"></i>Registrar otro abono
            </a>
        </div>
        <div class="col-12 col-sm-6 mt-2 mt-sm-0">
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
                    <?php echo htmlspecialchars($datos['confirmarAbono']['NomProveedor']) ?>
                    <span class="text-muted ms-2" style="font-size:.88rem">
                        (<?php echo htmlspecialchars($datos['confirmarAbono']['CIF']) ?>)
                    </span>
                </td>
            </tr>
            <tr>
                <th>Destino</th>
                <td><?php echo htmlspecialchars($datos['confirmarAbono']['NomDestino']) ?></td>
                <th style="width:20%">Responsable</th>
                <td><?php echo htmlspecialchars($datos['confirmarAbono']['Responsable']) ?></td>
            </tr>
            <tr>
                <th>Nº Abono</th>
                <td><?php echo htmlspecialchars($datos['confirmarAbono']['NAbono']) ?: '<span class="text-muted">—</span>' ?></td>
                <th>Nº Factura original</th>
                <td><?php echo htmlspecialchars($datos['confirmarAbono']['NFactura']) ?: '<span class="text-muted">—</span>' ?></td>
            </tr>
            <tr>
                <th>Importe</th>
                <td><strong><?php echo number_format((float)$datos['confirmarAbono']['Importe'], 2, ',', '.') ?> €</strong></td>
                <th>Fecha comprobación</th>
                <td><?php echo $datos['confirmarAbono']['Faprobacion'] ?></td>
            </tr>
            <?php if (!empty($datos['confirmarAbono']['Motivos'])): ?>
            <tr>
                <th>Motivos</th>
                <td colspan="3"><?php echo htmlspecialchars($datos['confirmarAbono']['Motivos']) ?></td>
            </tr>
            <?php endif ?>
        </tbody>
    </table>
    </div>

    <!-- Formulario oculto para guardarAbono -->
    <form id="fmAbono"
          action="<?php echo RUTA_URL ?>/GestionFacturas/guardarAbono"
          method="POST">
        <input type="hidden" name="NomProveedor"  value="<?php echo htmlspecialchars($datos['confirmarAbono']['NomProveedor']) ?>">
        <input type="hidden" name="NomDestino"    value="<?php echo htmlspecialchars($datos['confirmarAbono']['NomDestino']) ?>">
        <input type="hidden" name="Responsable"   value="<?php echo htmlspecialchars($datos['confirmarAbono']['Responsable']) ?>">
        <input type="hidden" name="CIF"           value="<?php echo htmlspecialchars($datos['confirmarAbono']['CIF']) ?>">
        <input type="hidden" name="NAbono"        value="<?php echo htmlspecialchars($datos['confirmarAbono']['NAbono']) ?>">
        <input type="hidden" name="NFactura"      value="<?php echo htmlspecialchars($datos['confirmarAbono']['NFactura']) ?>">
        <input type="hidden" name="Importe"       value="<?php echo htmlspecialchars($datos['confirmarAbono']['Importe']) ?>">
        <input type="hidden" name="Faprobacion"   value="<?php echo htmlspecialchars($datos['confirmarAbono']['Faprobacion']) ?>">
        <input type="hidden" name="Motivos"       value="<?php echo htmlspecialchars($datos['confirmarAbono']['Motivos']) ?>">

        <div class="row g-3">
            <div class="col-12 col-sm-6">
                <button type="submit" name="Aceptar" class="btn btn-success w-100">
                    <i class="fas fa-save me-2"></i>Confirmar y guardar
                </button>
            </div>
            <div class="col-12 col-sm-6">
                <button type="button" class="btn btn-outline-secondary w-100"
                        onclick="cancelarAbono()">
                    <i class="fas fa-arrow-left me-2"></i>Volver y corregir
                </button>
            </div>
        </div>
    </form>

    <?php endif ?>

</div>
</div>

<script>
    function cancelarAbono() {
        // Enviamos con name=cancelar para que el controlador vuelva al formulario
        const btn = document.createElement('input');
        btn.type  = 'hidden';
        btn.name  = 'cancelar';
        btn.value = '1';
        document.getElementById('fmAbono').appendChild(btn);
        document.getElementById('fmAbono').submit();
    }
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
