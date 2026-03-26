<?php require_once RUTA_APP . '/vistas/inc/header.php'; ?>


<div class="p-4 shadow border mt-4 mx-3 tarjeta">
<div class="container-fluid">

    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-12">
            <strong id="ciclo_encabezado">
                <i class="fas fa-file-invoice me-2"></i>
                Conformidad de pago
                <span class="ms-2 navbar-destino" style="font-size:1rem;">
                    <?php echo $datos['persistencia']['nombreDestinoSeleccionado'] ?>
                </span>
            </strong>
        </div>
    </div>

    <?php if (isset($datos['error']) && $datos['error'] == 1): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Hay campos obligatorios sin rellenar.
        </div>
    <?php endif ?>

    <form method="post" action="<?php echo RUTA_URL ?>/GestionFacturas/conformidadPago">

        <!-- Fila 1: Destino y Responsable (solo lectura, se autocompletan) -->
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <label class="input-group-text"><i class="fas fa-building me-1"></i> Destino</label>
                    <input type="text" class="form-control" name="NomDestnino"
                           value="<?php echo $datos['persistencia']['nombreDestinoSeleccionado'] ?>" readonly>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <label class="input-group-text"><i class="fas fa-user me-1"></i> Realizado por</label>
                    <input type="text" class="form-control" name="responsable"
                           value="<?php echo $datos['usuarioSesion']->nombre_completo ?>" readonly>
                </div>
            </div>
        </div>

        <!-- Fila 2: Proveedor, Nº Factura, Fecha factura -->
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <label class="input-group-text"><i class="fas fa-store me-1"></i> Proveedor<sup>*</sup></label>
                    <select class="form-control" name="CIF" required autofocus>
                        <option value="">— Selecciona proveedor —</option>
                        <?php $valorCIF = isset($datos['confirmarFactura']['CIF']) ? $datos['confirmarFactura']['CIF'] : '' ?>
                        <?php foreach ($datos['proveedores'] as $proveedor): ?>
                            <option value="<?php echo $proveedor->CIF ?>"
                                <?php echo ($proveedor->CIF == $valorCIF) ? 'selected' : '' ?>>
                                <?php echo $proveedor->Nombre ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <label class="input-group-text">Nº Factura<sup>*</sup></label>
                    <input type="text" class="form-control" name="NFactura" required
                           value="<?php echo isset($datos['confirmarFactura']['NFactura']) ? $datos['confirmarFactura']['NFactura'] : '' ?>">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <label class="input-group-text">Fecha factura<sup>*</sup></label>
                    <input type="date" class="form-control" name="Ffactura" required
                           value="<?php echo isset($datos['confirmarFactura']['Ffactura']) ? $datos['confirmarFactura']['Ffactura'] : '' ?>">
                </div>
            </div>
        </div>

        <!-- Fila 3: Fecha conformidad, Importe, Inventariable -->
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <label class="input-group-text">Fecha conformidad<sup>*</sup></label>
                    <input type="date" class="form-control" name="Fconformidad" required
                           value="<?php echo isset($datos['confirmarFactura']['Fconformidad']) ? $datos['confirmarFactura']['Fconformidad'] : date('Y-m-d') ?>">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <label class="input-group-text">Importe (€)<sup>*</sup></label>
                    <input type="number" step="0.01" min="0" class="form-control" name="importe" required
                           placeholder="0.00"
                           value="<?php echo isset($datos['confirmarFactura']['importe']) ? $datos['confirmarFactura']['importe'] : '' ?>">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="input-group align-items-center">
                    <label class="input-group-text">Inventariable</label>
                    <div class="form-control d-flex gap-4 align-items-center">
                        <?php $valInv = isset($datos['confirmarFactura']['inventariable']) ? $datos['confirmarFactura']['inventariable'] : 'N' ?>
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="radio" name="inventariable" id="inv_si" value="S"
                                   <?php echo ($valInv == 'S') ? 'checked' : '' ?> required>
                            <label class="form-check-label" for="inv_si">Sí</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="radio" name="inventariable" id="inv_no" value="N"
                                   <?php echo ($valInv == 'N') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="inv_no">No</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fila 4: Descripción -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="input-group">
                    <label class="input-group-text">Descripción</label>
                    <textarea class="form-control" name="descripcion_as" rows="2"
                              placeholder="Descripción del gasto..."><?php echo isset($datos['confirmarFactura']['descripcion']) ? $datos['confirmarFactura']['descripcion'] : '' ?></textarea>
                </div>
            </div>
        </div>

        <!-- ===== EVALUACIÓN DEL PROVEEDOR ===== -->
        <div class="row mb-2">
            <div class="col-12">
                <strong id="ciclo_encabezado" style="font-size:1.1rem;">
                    <i class="fas fa-star me-2"></i>Evaluación del proveedor
                </strong>
                <hr>
            </div>
        </div>

        <!-- Items 1 y 2 -->
        <div class="row g-3 mb-3">

            <!-- Item 1 -->
            <div class="col-12 col-md-6">
                <div class="p-3 border rounded h-100">
                    <p class="fw-bold text-primary mb-2">Item 1 — Información, trato y servicio posventa</p>
                    <?php
                    $i1 = isset($datos['confirmarFactura']['Item1']) ? $datos['confirmarFactura']['Item1'] : '';
                    $opciones1 = [
                        1 => 'Atienden mal y/o no aporta ninguna solución',
                        3 => 'Atiende bien, pero la información y/o soluciones aportadas son deficientes',
                        5 => 'Se esfuerzan en complacernos y/o aporta alguna solución',
                        7 => 'La atención es buena y nos aporta soluciones',
                        9 => 'El trato en todo momento es exquisito, aportando información y soluciones eficaces',
                    ];
                    foreach ($opciones1 as $val => $texto): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Item1"
                                   id="i1_<?php echo $val ?>" value="<?php echo $val ?>" required
                                   <?php echo ($i1 == $val) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="i1_<?php echo $val ?>">
                                <span class="badge-valor me-1"><?php echo $val ?></span> <?php echo $texto ?>
                            </label>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>

            <!-- Item 2 -->
            <div class="col-12 col-md-6">
                <div class="p-3 border rounded h-100">
                    <p class="fw-bold text-primary mb-2">Item 2 — Cumplimiento de plazos de entrega</p>
                    <?php
                    $i2 = isset($datos['confirmarFactura']['Item2']) ? $datos['confirmarFactura']['Item2'] : '';
                    $opciones2 = [
                        1 => 'Tarde y después de alguna reclamación',
                        3 => 'Tarde pero sin reclamación',
                        5 => 'Con breve retraso, pero no afectó al desarrollo de la actividad',
                        7 => 'Se realizó dentro del tiempo acordado',
                        9 => 'Servicio excelente, con entrega inmediata (menos de 24 horas)',
                    ];
                    foreach ($opciones2 as $val => $texto): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Item2"
                                   id="i2_<?php echo $val ?>" value="<?php echo $val ?>" required
                                   <?php echo ($i2 == $val) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="i2_<?php echo $val ?>">
                                <span class="badge-valor me-1"><?php echo $val ?></span> <?php echo $texto ?>
                            </label>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        </div>

        <!-- Items 3 y 4 -->
        <div class="row g-3 mb-4">

            <!-- Item 3 -->
            <div class="col-12 col-md-6">
                <div class="p-3 border rounded h-100">
                    <p class="fw-bold text-primary mb-2">Item 3 — Transporte, embalaje y entrega</p>
                    <?php
                    $i3 = isset($datos['confirmarFactura']['Item3']) ? $datos['confirmarFactura']['Item3'] : '';
                    $opciones3 = [
                        1 => 'Se entregó con deficiencias graves y no se entregó donde correspondía',
                        3 => 'Se entregó con alguna deficiencia, pero no afectó al producto o servicio',
                        5 => 'Se entregó bien / aceptable el servicio prestado',
                        7 => 'Una entrega muy buena (embalajes buenos o servicios bien terminados)',
                        9 => 'Entrega excelente, tanto en la realización como en interés',
                    ];
                    foreach ($opciones3 as $val => $texto): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Item3"
                                   id="i3_<?php echo $val ?>" value="<?php echo $val ?>" required
                                   <?php echo ($i3 == $val) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="i3_<?php echo $val ?>">
                                <span class="badge-valor me-1"><?php echo $val ?></span> <?php echo $texto ?>
                            </label>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>

            <!-- Item 4 -->
            <div class="col-12 col-md-6">
                <div class="p-3 border rounded h-100">
                    <p class="fw-bold text-primary mb-2">Item 4 — Relación precio / calidad</p>
                    <?php
                    $i4 = isset($datos['confirmarFactura']['Item4']) ? $datos['confirmarFactura']['Item4'] : '';
                    $opciones4 = [
                        1 => 'Mal',
                        4 => 'Regular',
                        6 => 'Bien',
                        9 => 'Muy bien',
                    ];
                    foreach ($opciones4 as $val => $texto): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Item4"
                                   id="i4_<?php echo $val ?>" value="<?php echo $val ?>" required
                                   <?php echo ($i4 == $val) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="i4_<?php echo $val ?>">
                                <span class="badge-valor me-1"><?php echo $val ?></span> <?php echo $texto ?>
                            </label>
                        </div>
                    <?php endforeach ?>
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

<style>
.badge-valor {
    display: inline-block;
    background-color: #0583c3;
    color: white;
    border-radius: 50%;
    width: 22px;
    height: 22px;
    text-align: center;
    line-height: 22px;
    font-size: 11px;
    font-weight: bold;
}
</style>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
