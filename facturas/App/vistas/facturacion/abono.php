<?php require_once RUTA_APP . '/vistas/inc/header.php'; ?>

<div class="p-4 shadow border mt-4 mx-3 tarjeta">
<div class="container-fluid">

    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-12">
            <strong id="ciclo_encabezado">
                <i class="fas fa-undo-alt me-2"></i>
                Abono
                <span class="ms-2 navbar-destino" style="font-size:1rem;">
                    <?php echo $datos['persistencia']['nombreDestinoSeleccionado'] ?>
                </span>
            </strong>
        </div>
    </div>

    <?php if (isset($datos['error']) && $datos['error'] == 1): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Proveedor e importe son campos obligatorios.
    </div>
    <?php endif ?>

    <form method="POST" action="<?php echo RUTA_URL ?>/GestionFacturas/abono">

        <!-- Fila 1: Destino (solo lectura) y Responsable (solo lectura) -->
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <label class="input-group-text">
                        <i class="fas fa-building me-1"></i>Destino
                    </label>
                    <input type="text" class="form-control"
                           name="NomDestino"
                           value="<?php echo htmlspecialchars($datos['persistencia']['nombreDestinoSeleccionado']) ?>"
                           readonly>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <label class="input-group-text">
                        <i class="fas fa-user me-1"></i>Realizado por
                    </label>
                    <input type="text" class="form-control"
                           name="Responsable"
                           value="<?php echo htmlspecialchars($datos['usuarioSesion']->nombre_completo) ?>"
                           readonly>
                </div>
            </div>
        </div>

        <!-- Fila 2: Proveedor -->
        <div class="row g-3 mb-3">
            <div class="col-12">
                <div class="input-group">
                    <label class="input-group-text">
                        <i class="fas fa-store me-1"></i>Proveedor<sup class="text-danger">*</sup>
                    </label>
                    <?php
                    $cifActual = $datos['confirmarAbono']['CIF'] ?? '';
                    ?>
                    <select class="form-control" name="CIF" required autofocus>
                        <option value="">— Selecciona proveedor —</option>
                        <?php foreach ($datos['proveedores'] as $p): ?>
                        <option value="<?php echo htmlspecialchars($p->CIF ?? '') ?>"
                                <?php echo ($p->CIF == $cifActual) ? 'selected' : '' ?>
                                <?php echo ($p->Penalizacion == 'S') ? 'style="color:red"' : '' ?>>
                            <?php echo htmlspecialchars($p->Nombre ?? '') ?>
                            <?php echo ($p->Penalizacion == 'S') ? ' ⚠ PENALIZADO' : '' ?>
                        </option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Fila 3: Nº Abono, Nº Factura original, Importe -->
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <label class="input-group-text">Nº Abono</label>
                    <input type="text" class="form-control"
                           name="NAbono"
                           placeholder="Nº del abono..."
                           value="<?php echo htmlspecialchars($datos['confirmarAbono']['NAbono'] ?? '') ?>">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <label class="input-group-text">Nº Factura orig.</label>
                    <input type="text" class="form-control"
                           name="NFactura"
                           placeholder="Factura que se abona..."
                           value="<?php echo htmlspecialchars($datos['confirmarAbono']['NFactura'] ?? '') ?>">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <label class="input-group-text">Importe (€)<sup class="text-danger">*</sup></label>
                    <input type="number" step="0.01" min="0" class="form-control"
                           name="Importe" placeholder="0.00" required
                           value="<?php echo htmlspecialchars($datos['confirmarAbono']['Importe'] ?? '') ?>">
                </div>
            </div>
        </div>

        <!-- Fila 4: Fecha de comprobación -->
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <label class="input-group-text">Fecha comprobación</label>
                    <?php
                    $fApro = $datos['confirmarAbono']['Faprobacion'] ?? date('Y-m-d');
                    ?>
                    <input type="date" class="form-control"
                           name="Faprobacion"
                           value="<?php echo $fApro ?>" required>
                </div>
            </div>
        </div>

        <!-- Fila 5: Motivos -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="input-group">
                    <label class="input-group-text align-items-start pt-2">Motivos</label>
                    <textarea class="form-control" name="Motivos" rows="3"
                              placeholder="Describe el motivo del abono..."><?php echo htmlspecialchars($datos['confirmarAbono']['Motivos'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="row g-3">
            <div class="col-12 col-sm-9">
                <button type="submit" name="guardar" class="btn btn-success w-100">
                    <i class="fas fa-check me-2"></i>Revisar y confirmar
                </button>
            </div>
            <div class="col-12 col-sm-3">
                <a href="<?php echo RUTA_URL ?>/" class="btn btn-danger w-100">
                    <i class="fas fa-times me-2"></i>Cancelar
                </a>
            </div>
        </div>

    </form>

</div>
</div>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
