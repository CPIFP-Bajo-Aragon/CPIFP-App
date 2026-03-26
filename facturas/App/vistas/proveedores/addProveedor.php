<?php require_once RUTA_APP . '/vistas/inc/header.php'; ?>

<div class="p-4 shadow border mt-4 mx-3 tarjeta">
<div class="container-fluid">

    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-12">
            <strong id="ciclo_encabezado">
                <i class="fas fa-plus-circle me-2"></i>Añadir proveedor
            </strong>
        </div>
    </div>

    <?php if ($datos['usuarioSesion']->id_rol === 500): ?>

    <!-- ============================================================
         FORMULARIO — solo visible para Equipo Directivo (rol 500)
    ============================================================ -->

    <?php if (isset($datos['errorCif'])): ?>
    <div class="alert alert-danger mb-4">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <?php echo $datos['errorCif'] ?>
    </div>
    <?php endif ?>

    <form method="POST" action="<?php echo RUTA_URL ?>/Proveedores/addProveedor">

        <!-- Fila 1: CIF, Nombre, Alias -->
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-3">
                <div class="input-group">
                    <label class="input-group-text">CIF<sup class="text-danger">*</sup></label>
                    <input type="text" class="form-control" name="CIF"
                           placeholder="Ej: B12345678"
                           value="<?php echo htmlspecialchars($_POST['CIF'] ?? '') ?>"
                           maxlength="11" required autofocus>
                </div>
            </div>
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <label class="input-group-text">Nombre<sup class="text-danger">*</sup></label>
                    <input type="text" class="form-control" name="Nombre"
                           placeholder="Razón social..."
                           value="<?php echo htmlspecialchars($_POST['Nombre'] ?? '') ?>"
                           maxlength="60" required>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <label class="input-group-text">Alias</label>
                    <input type="text" class="form-control" name="Alias"
                           placeholder="Nombre corto..."
                           value="<?php echo htmlspecialchars($_POST['Alias'] ?? '') ?>"
                           maxlength="50">
                </div>
            </div>
        </div>

        <!-- Fila 2: Dirección, CP, Localidad -->
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <label class="input-group-text">Dirección</label>
                    <input type="text" class="form-control" name="Direccion"
                           placeholder="Calle, número..."
                           value="<?php echo htmlspecialchars($_POST['Direccion'] ?? '') ?>"
                           maxlength="50">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="input-group">
                    <label class="input-group-text">C.P.</label>
                    <input type="text" class="form-control" name="CP"
                           placeholder="00000"
                           value="<?php echo htmlspecialchars($_POST['CP'] ?? '') ?>"
                           maxlength="5">
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="input-group">
                    <label class="input-group-text">Localidad</label>
                    <input type="text" class="form-control" name="Localidad"
                           value="<?php echo htmlspecialchars($_POST['Localidad'] ?? '') ?>"
                           maxlength="50">
                </div>
            </div>
        </div>

        <!-- Fila 3: Provincia, País, Teléfono, Externo -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-3">
                <div class="input-group">
                    <label class="input-group-text">Provincia</label>
                    <input type="text" class="form-control" name="Provincia"
                           value="<?php echo htmlspecialchars($_POST['Provincia'] ?? '') ?>"
                           maxlength="50">
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="input-group">
                    <label class="input-group-text">País</label>
                    <input type="text" class="form-control" name="Pais"
                           value="<?php echo htmlspecialchars($_POST['Pais'] ?? '') ?>"
                           maxlength="50">
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="input-group">
                    <label class="input-group-text">Teléfono</label>
                    <input type="text" class="form-control" name="Telefono"
                           placeholder="000000000"
                           value="<?php echo htmlspecialchars($_POST['Telefono'] ?? '') ?>"
                           maxlength="9">
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="input-group">
                    <label class="input-group-text">Externo</label>
                    <select class="form-control" name="Externo">
                        <option value="S" <?php echo (($_POST['Externo'] ?? 'S') === 'S') ? 'selected' : '' ?>>
                            Sí (externo)
                        </option>
                        <option value="N" <?php echo (($_POST['Externo'] ?? '') === 'N') ? 'selected' : '' ?>>
                            No (interno)
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="row g-3">
            <div class="col-12 col-sm-9">
                <button type="submit" name="Enviar" class="btn btn-success w-100">
                    <i class="fas fa-save me-2"></i>Guardar proveedor
                </button>
            </div>
            <div class="col-12 col-sm-3">
                <a href="<?php echo RUTA_URL ?>/Proveedores/listaProveedores"
                   class="btn btn-danger w-100">
                    <i class="fas fa-times me-2"></i>Cancelar
                </a>
            </div>
        </div>

    </form>

    <?php else: ?>

    <!-- ============================================================
         MENSAJE — para usuarios sin permiso (rol 300)
    ============================================================ -->
    <div class="row justify-content-center mt-4">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="text-center p-5 border rounded-3 shadow-sm bg-light">
                <i class="fas fa-lock fa-3x mb-3" style="color:#0583c3"></i>
                <h5 class="fw-bold mb-3">Acción restringida</h5>
                <p class="text-muted mb-4">
                    La creación de nuevos proveedores es una operación reservada al
                    <strong>Equipo Directivo</strong>.<br>
                    Si necesitas dar de alta un proveedor, ponte en contacto con
                    <strong>Secretaría</strong>.
                </p>
                <a href="<?php echo RUTA_URL ?>/Proveedores/listaProveedores"
                   class="btn btn-custom">
                    <i class="fas fa-arrow-left me-2"></i>Volver al listado
                </a>
            </div>
        </div>
    </div>

    <?php endif ?>

</div>
</div>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
