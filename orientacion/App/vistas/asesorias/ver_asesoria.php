<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<?php
    $estadoFormulario = '';
    if ($datos['asesoria']->id_estado == 3) $estadoFormulario = 'disabled';
    if (!tienePrivilegios($datos['usuarioSesion']->id_rol, [200,300])
        && $datos['asesoria']->acciones[0]->id_profesor != $datos['usuarioSesion']->id_profesor)
        $estadoFormulario = 'disabled';
?>

<div class="container-fluid px-4 py-4">

    <div class="row mb-4 align-items-center">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <span>
                    <i class="fas fa-user-graduate me-2"></i>
                    Asesoría: <?php echo htmlspecialchars($datos['asesoria']->nombre_as) ?>
                </span>
            </span>
        </div>
        <div class="col-auto">
            <a href="<?php echo RUTA_URL ?>/" class="btn-volver">
                <i class="fas fa-arrow-left"></i>Volver
            </a>
        </div>
    </div>

    <div class="row g-4">

        <!-- COLUMNA IZQUIERDA -->
        <div class="col-12 col-md-7">

            <!-- Nueva acción -->
            <?php if ($datos['asesoria']->id_estado != 3): ?>
            <div class="mb-3 p-3 border rounded" style="background:#f4f9fd">
                <strong style="color:#0583c3"><i class="fas fa-plus-circle me-1"></i>Añadir acción</strong>
                <form method="post" action="<?php echo RUTA_URL ?>/asesorias/add_accion" class="mt-2">
                    <input type="hidden" name="id_asesoria" value="<?php echo $datos['asesoria']->id_asesoria ?>">
                    <div class="row g-2">
                        <div class="col-10">
                            <textarea class="form-control form-control-sm" name="accion" rows="2"
                                      placeholder="Descripción de la acción realizada..."></textarea>
                        </div>
                        <div class="col-2">
                            <button type="submit" class="btn w-100 h-100"
                                    style="background:#27ae60; color:#fff; font-weight:600">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <?php endif ?>

            <!-- Datos de la asesoría -->
            <div class="p-3 border rounded" style="background:#fff">
                <strong style="color:#0583c3"><i class="fas fa-info-circle me-1"></i>Datos de la asesoría</strong>
                <form method="post" class="mt-3">
                    <div class="row g-3">

                        <div class="col-12 col-md-4">
                            <div class="input-group">
                                <label class="input-group-text">Nombre *</label>
                                <input <?php echo $estadoFormulario ?> type="text" class="form-control"
                                       name="nombre_as" required
                                       value="<?php echo htmlspecialchars($datos['asesoria']->nombre_as) ?>">
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="input-group">
                                <label class="input-group-text">DNI</label>
                                <input <?php echo $estadoFormulario ?> type="text" class="form-control"
                                       name="dni_as"
                                       value="<?php echo htmlspecialchars($datos['asesoria']->dni_as) ?>">
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="input-group">
                                <label class="input-group-text">Título</label>
                                <input <?php echo $estadoFormulario ?> type="text" class="form-control"
                                       name="titulo_as"
                                       value="<?php echo htmlspecialchars($datos['asesoria']->titulo_as) ?>">
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="input-group">
                                <label class="input-group-text">Teléfono</label>
                                <input <?php echo $estadoFormulario ?> type="text" class="form-control"
                                       name="telefono_as"
                                       value="<?php echo htmlspecialchars($datos['asesoria']->telefono_as) ?>">
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="input-group">
                                <label class="input-group-text">Email *</label>
                                <input <?php echo $estadoFormulario ?> type="email" class="form-control"
                                       name="email_as" required
                                       value="<?php echo htmlspecialchars($datos['asesoria']->email_as) ?>">
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="input-group">
                                <label class="input-group-text">Domicilio</label>
                                <input <?php echo $estadoFormulario ?> type="text" class="form-control"
                                       name="domicilio_as"
                                       value="<?php echo htmlspecialchars($datos['asesoria']->domicilio_as) ?>">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="input-group">
                                <label class="input-group-text">Descripción</label>
                                <textarea class="form-control" name="descripcion_as" rows="3"
                                          <?php echo $estadoFormulario ?>><?php echo htmlspecialchars($datos['asesoria']->descripcion_as) ?></textarea>
                            </div>
                        </div>

                        <div class="col-12 col-md-9">
                            <button type="submit" class="btn w-100"
                                    style="background:#0583c3; color:#fff; font-weight:600"
                                    <?php echo $estadoFormulario ?>>
                                <i class="fas fa-save me-2"></i>Modificar
                            </button>
                        </div>
                        <div class="col-12 col-md-3">
                            <a class="btn w-100" href="<?php echo RUTA_URL ?>/"
                               style="background:#e74c3c; color:#fff; font-weight:600">
                                <i class="fas fa-arrow-left me-1"></i>Atrás
                            </a>
                        </div>

                    </div>
                </form>
            </div>

            <!-- Cerrar / Abrir asesoría -->
            <?php if ($datos['asesoria']->id_estado != 3 && tienePrivilegios($datos['usuarioSesion']->id_rol, [200,300])): ?>
            <div class="mt-3">
                <button type="button"
                        onclick="valida_cerrar(<?php echo $datos['asesoria']->id_asesoria ?>)"
                        data-bs-toggle="modal" data-bs-target="#modalCerrarAccion"
                        class="btn w-100" style="background:#f39c12; color:#fff; font-weight:600">
                    <i class="fas fa-lock me-2"></i>Cerrar Asesoría
                </button>
            </div>
            <?php endif ?>

            <?php if ($datos['asesoria']->id_estado == 3 && tienePrivilegios($datos['usuarioSesion']->id_rol, [300])): ?>
            <div class="mt-3">
                <form method="post" action="<?php echo RUTA_URL ?>/asesorias/abrir_asesoria">
                    <input type="hidden" name="id_asesoria" value="<?php echo $datos['asesoria']->id_asesoria ?>">
                    <button type="submit" class="btn w-100"
                            style="background:#27ae60; color:#fff; font-weight:600">
                        <i class="fas fa-lock-open me-2"></i>Abrir Asesoría
                    </button>
                </form>
            </div>
            <?php endif ?>

        </div>

        <!-- COLUMNA DERECHA: historial -->
        <div class="col-12 col-md-5">
            <div class="p-3 border rounded" style="background:#fff">
                <strong style="color:#0583c3; display:block; margin-bottom:12px">
                    <i class="fas fa-history me-1"></i>Historial de acciones
                </strong>

                <?php foreach ($datos['asesoria']->acciones as $accion): ?>
                <div class="mb-2 p-2 border rounded" id="accion_<?php echo $accion->id_reg_acciones ?>"
                     style="background:#f9f9f9; font-size:.92rem">
                    <?php if ($accion->automatica): ?>
                        <span class="text-muted"><?php echo htmlspecialchars($accion->accion) ?>:</span>
                        <span><?php echo htmlspecialchars($accion->nombre_completo) ?></span>
                    <?php else: ?>
                        <div class="d-flex justify-content-between align-items-start">
                            <strong><?php echo htmlspecialchars($accion->nombre_completo) ?></strong>
                            <?php if ($datos['asesoria']->id_estado != 3): ?>
                            <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol, [300]) || $accion->id_profesor == $datos['usuarioSesion']->id_profesor): ?>
                            <div class="d-flex gap-1">
                                <a class="btn btn-custom btn-sm"
                                   onclick="rellenarModal(<?php echo $accion->id_reg_acciones ?>)"
                                   data-bs-toggle="modal" data-bs-target="#modalEditarAccion">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <a onclick="del_asesoria_modal(<?php echo $accion->id_reg_acciones ?>)"
                                   data-bs-toggle="modal" data-bs-target="#modalDelAccion"
                                   class="btn btn-sm" style="color:#e74c3c; border:1px solid #e74c3c">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                            <?php endif ?>
                            <?php endif ?>
                        </div>
                        <p class="mb-0 mt-1 ms-1" id="textoAccion_<?php echo $accion->id_reg_acciones ?>">
                            <?php echo htmlspecialchars($accion->accion) ?>
                        </p>
                    <?php endif ?>
                    <div class="text-end text-muted mt-1" style="font-size:.8rem">
                        <?php echo formatoFecha($accion->fecha_reg) ?>
                    </div>
                </div>
                <?php endforeach ?>
            </div>
        </div>

    </div>
</div>


<!-- Modal Cerrar Asesoría -->
<div class="modal fade" id="modalCerrarAccion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">¿Cerrar la asesoría?</h5></div>
            <div class="modal-footer">
                <form method="post" id="formCerrarAccion"
                      action="<?php echo RUTA_URL ?>/asesorias/cerrar_asesoria">
                    <button type="button" class="btn btn-custom" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn" style="background:#f39c12; color:#fff">
                        <i class="fas fa-lock me-1"></i>Cerrar
                    </button>
                    <input type="hidden" id="id_asesoria" name="id_asesoria">
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Acción -->
<div class="modal fade" id="modalEditarAccion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Modificar acción</h5></div>
            <div class="modal-footer">
                <form method="post" id="formEditarAccion" action="javascript:guardarEditAccion()">
                    <div class="mb-2">
                        <textarea class="form-control form-control-sm" id="accion_edit"
                                  name="accion" rows="3" placeholder="Editar acción..."></textarea>
                    </div>
                    <input type="hidden" id="id_reg_acciones" name="id_reg_acciones">
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-custom" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn" id="buttonEditar"
                                style="background:#0583c3; color:#fff" data-bs-dismiss="modal">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Eliminar Acción -->
<div class="modal fade" id="modalDelAccion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">¿Eliminar la acción?</h5></div>
            <div class="modal-footer">
                <form method="post" id="formDelAccion"
                      action="javascript:delAccion(document.getElementById('formDelAccion'))">
                    <button type="button" class="btn btn-custom" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn" style="background:#e74c3c; color:#fff"
                            data-bs-dismiss="modal">Borrar</button>
                    <input type="hidden" id="del_id_reg_acciones" name="id_reg_acciones">
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toasts -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:11">
    <div id="toastOK" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <svg class="bd-placeholder-img rounded me-2" width="20" height="20"><rect width="100%" height="100%" fill="green"/></svg>
            <strong class="me-auto">Guardado correctamente</strong>
        </div>
    </div>
</div>
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:11">
    <div id="toastKO" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <svg class="bd-placeholder-img rounded me-2" width="20" height="20"><rect width="100%" height="100%" fill="red"/></svg>
            <strong class="me-auto">Error al guardar</strong>
        </div>
    </div>
</div>

<script>
    function valida_cerrar(id) { document.getElementById('id_asesoria').value = id; }
    function del_asesoria_modal(id) { document.getElementById('del_id_reg_acciones').value = id; }

    async function rellenarModal(id_reg_acciones) {
        let btn = document.getElementById('buttonEditar');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Cargando...';
        btn.disabled = true;
        await fetch(`<?php echo RUTA_URL ?>/asesorias/get_accion/${id_reg_acciones}`, {
            headers: {'Content-Type':'application/json'}, credentials:'include'
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('id_reg_acciones').value = id_reg_acciones;
            document.getElementById('accion_edit').value     = data.accion;
            setTimeout(() => { btn.innerHTML='Guardar'; btn.disabled=false; }, 800);
        });
    }

    async function guardarEditAccion() {
        const f = new FormData(document.getElementById('formEditarAccion'));
        await fetch(`<?php echo RUTA_URL ?>/asesorias/set_accion`, { method:'POST', body:f })
            .then(r => r.json())
            .then(data => {
                if (data) {
                    document.getElementById('textoAccion_' + f.get('id_reg_acciones')).innerHTML = f.get('accion');
                    new bootstrap.Toast(document.getElementById('toastOK')).show();
                } else { new bootstrap.Toast(document.getElementById('toastKO')).show(); }
            })
            .catch(() => new bootstrap.Toast(document.getElementById('toastKO')).show());
    }

    async function delAccion(formulario) {
        const f = new FormData(formulario);
        await fetch(`<?php echo RUTA_URL ?>/asesorias/del_accion`, { method:'POST', body:f })
            .then(r => r.json())
            .then(data => {
                if (data) {
                    document.getElementById('accion_' + f.get('id_reg_acciones')).remove();
                    new bootstrap.Toast(document.getElementById('toastOK')).show();
                } else { new bootstrap.Toast(document.getElementById('toastKO')).show(); }
            })
            .catch(() => new bootstrap.Toast(document.getElementById('toastKO')).show());
    }
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
