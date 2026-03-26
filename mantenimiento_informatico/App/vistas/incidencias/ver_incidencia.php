<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<?php
    $estadoFormulario = '';
    if ($datos['incidencia']->id_estado == 3) {
        $estadoFormulario = 'disabled';
    }
    if (!tienePrivilegios($datos['usuarioSesion']->id_rol, [200, 300])
        && $datos['incidencia']->acciones[0]->id_profesor != $datos['usuarioSesion']->id_profesor) {
        $estadoFormulario = 'disabled';
    }
?>

<div class="container-fluid px-4 py-4">

    <div class="row mb-4 align-items-center">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <span>
                    <i class="fas fa-tools me-2"></i>
                    Incidencia #<?php echo $datos['incidencia']->id_incidencia ?>:
                    <?php echo htmlspecialchars($datos['incidencia']->titulo_in) ?>
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

        <!-- COLUMNA IZQUIERDA: formulario + nueva acción -->
        <div class="col-12 col-md-7">

            <!-- Nueva acción -->
            <?php if ($datos['incidencia']->id_estado != 3): ?>
            <div class="mb-3 p-3 border rounded" style="background:#f4f9fd">
                <strong style="color:#0583c3"><i class="fas fa-plus-circle me-1"></i>Añadir acción</strong>
                <form method="post" action="<?php echo RUTA_URL ?>/incidencias/add_accion" class="mt-2">
                    <input type="hidden" name="id_incidencia" value="<?php echo $datos['incidencia']->id_incidencia ?>">
                    <div class="row g-2">
                        <div class="col-12 col-sm-7">
                            <textarea class="form-control form-control-sm" name="accion" rows="2"
                                      placeholder="Descripción de la acción realizada..." required></textarea>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="input-group input-group-sm">
                                <label class="input-group-text">hh:mm</label>
                                <input type="time" class="form-control" name="horas"
                                       value="00:00" step="300" required>
                            </div>
                        </div>
                        <div class="col-6 col-sm-2">
                            <button type="submit" class="btn w-100"
                                    style="background:#27ae60; color:#fff; font-weight:600">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <?php endif ?>

            <!-- Datos de la incidencia -->
            <div class="p-3 border rounded" style="background:#fff">
                <strong style="color:#0583c3"><i class="fas fa-info-circle me-1"></i>Datos de la incidencia</strong>
                <form method="post" class="mt-3">
                    <div class="row g-3">

                        <div class="col-12">
                            <div class="input-group">
                                <label class="input-group-text">Título *</label>
                                <input <?php echo $estadoFormulario ?> type="text" class="form-control"
                                       name="titulo_in" value="<?php echo htmlspecialchars($datos['incidencia']->titulo_in) ?>"
                                       required>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="input-group">
                                <label class="input-group-text">Urgencia</label>
                                <select <?php echo $estadoFormulario ?> class="form-control" name="id_urgencia">
                                    <?php foreach ($datos['estadosUrgencia'] as $u): ?>
                                    <option value="<?php echo $u->id_urgencia ?>"
                                        <?php echo $u->id_urgencia == $datos['incidencia']->id_urgencia ? 'selected' : '' ?>>
                                        <?php echo htmlspecialchars($u->urgencia) ?>
                                    </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="input-group">
                                <label class="input-group-text">Edificio *</label>
                                <select <?php echo $estadoFormulario ?> class="form-control"
                                        id="id_edificio" name="id_edificio" required>
                                    <?php foreach ($datos['edificios'] as $e): ?>
                                    <option value="<?php echo $e->id_edificio ?>"
                                        <?php echo $e->id_edificio == $datos['incidencia']->id_edificio ? 'selected' : '' ?>>
                                        <?php echo htmlspecialchars($e->edificio) ?>
                                    </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="input-group">
                                <label class="input-group-text">Ubicación *</label>
                                <select <?php echo $estadoFormulario ?> class="form-control"
                                        id="id_ubicacion" name="id_ubicacion" required>
                                    <option value="" disabled>-- Selecciona --</option>
                                    <?php foreach ($datos['ubicacionesActivas'] as $ub): ?>
                                    <option value="<?php echo $ub->id_ubicacion ?>"
                                        <?php echo $ub->id_ubicacion == $datos['incidencia']->id_ubicacion ? 'selected' : '' ?>>
                                        <?php echo htmlspecialchars($ub->ubicacion) ?>
                                    </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="input-group">
                                <label class="input-group-text">Descripción</label>
                                <textarea class="form-control" name="descripcion_in" rows="3"
                                          <?php echo $estadoFormulario ?>><?php echo htmlspecialchars($datos['incidencia']->descripcion_in) ?></textarea>
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

            <!-- Botones cerrar / abrir incidencia -->
            <?php if ($datos['incidencia']->id_estado != 3
                       && tienePrivilegios($datos['usuarioSesion']->id_rol, [200, 300])): ?>
            <div class="mt-3">
                <button type="button"
                        onclick="valida_cerrar(<?php echo $datos['incidencia']->id_incidencia ?>)"
                        data-bs-toggle="modal" data-bs-target="#modalCerrarAccion"
                        class="btn w-100" style="background:#f39c12; color:#fff; font-weight:600">
                    <i class="fas fa-lock me-2"></i>Cerrar Incidencia
                </button>
            </div>
            <?php endif ?>

            <?php if ($datos['incidencia']->id_estado == 3
                       && tienePrivilegios($datos['usuarioSesion']->id_rol, [200, 300])): ?>
            <div class="mt-3">
                <form method="post" action="<?php echo RUTA_URL ?>/incidencias/abrir_incidencia">
                    <input type="hidden" name="id_incidencia" value="<?php echo $datos['incidencia']->id_incidencia ?>">
                    <button type="submit" class="btn w-100"
                            style="background:#27ae60; color:#fff; font-weight:600">
                        <i class="fas fa-lock-open me-2"></i>Abrir Incidencia
                    </button>
                </form>
            </div>
            <?php endif ?>

        </div>

        <!-- COLUMNA DERECHA: historial de acciones -->
        <div class="col-12 col-md-5">

            <div class="p-3 border rounded" style="background:#fff">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <strong style="color:#0583c3">
                        <i class="fas fa-history me-1"></i>Historial de acciones
                    </strong>
                    <span style="color:#0583c3; font-weight:600">
                        <i class="fas fa-clock me-1"></i>Total:
                        <span id="horasTotales"><?php echo formatoMinutosAHoras($datos['incidencia']->minTotales) ?></span>
                    </span>
                </div>

                <?php foreach ($datos['incidencia']->acciones as $accion): ?>
                <div class="mb-2 p-2 border rounded" id="accion_<?php echo $accion->id_reg_acciones ?>"
                     style="background:#f9f9f9; font-size:.92rem">
                    <?php if ($accion->automatica): ?>
                        <span class="text-muted"><?php echo htmlspecialchars($accion->accion) ?>:</span>
                        <span><?php echo htmlspecialchars($accion->nombre_completo) ?></span>
                    <?php else: ?>
                        <div class="d-flex justify-content-between align-items-start">
                            <strong><?php echo htmlspecialchars($accion->nombre_completo) ?></strong>
                            <div class="d-flex align-items-center gap-1">
                                <strong id="horasAccion_<?php echo $accion->id_reg_acciones ?>">
                                    <?php echo formatoMinutosAHoras($accion->minutos) ?>
                                </strong>
                                <?php if ($datos['incidencia']->id_estado != 3): ?>
                                <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol, [300])
                                           || $accion->id_profesor == $datos['usuarioSesion']->id_profesor): ?>
                                <a class="btn btn-custom btn-sm"
                                   onclick="rellenarModal(<?php echo $accion->id_reg_acciones ?>)"
                                   data-bs-toggle="modal" data-bs-target="#modalEditarAccion">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <a onclick="del_incidencia_modal(<?php echo $accion->id_reg_acciones ?>)"
                                   data-bs-toggle="modal" data-bs-target="#modalDelAccion"
                                   class="btn btn-sm" style="color:#e74c3c; border:1px solid #e74c3c">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                                <?php endif ?>
                                <?php endif ?>
                            </div>
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


<!-- Modal Cerrar Incidencia -->
<div class="modal fade" id="modalCerrarAccion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">¿Cerrar la incidencia?</h5>
            </div>
            <div class="modal-footer">
                <form method="post" id="formCerrarAccion"
                      action="<?php echo RUTA_URL ?>/incidencias/cerrar_incidencia">
                    <button type="button" class="btn btn-custom" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn" style="background:#f39c12; color:#fff">
                        <i class="fas fa-lock me-1"></i>Cerrar
                    </button>
                    <input type="hidden" id="id_incidencia" name="id_incidencia">
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Acción -->
<div class="modal fade" id="modalEditarAccion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modificar acción</h5>
            </div>
            <div class="modal-footer">
                <form method="post" id="formEditarAccion" action="javascript:guardarEditAccion()">
                    <div class="row g-2">
                        <div class="col-9">
                            <textarea class="form-control form-control-sm" id="accion_edit"
                                      name="accion" rows="3" placeholder="Editar acción..."></textarea>
                        </div>
                        <div class="col-3">
                            <div class="input-group input-group-sm">
                                <label class="input-group-text">hh:mm</label>
                                <input type="time" class="form-control" id="horas_modal"
                                       name="horas_minutos" value="00:00" step="300" required>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="id_reg_acciones"  name="id_reg_acciones">
                    <input type="hidden" id="id_incidencia_ed" name="id_incidencia"
                           value="<?php echo $datos['incidencia']->id_incidencia ?>">
                    <div class="mt-2 d-flex gap-2 justify-content-end">
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
            <div class="modal-header">
                <h5 class="modal-title">¿Eliminar la acción?</h5>
            </div>
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
    function valida_cerrar(id_incidencia) {
        document.getElementById('id_incidencia').value = id_incidencia;
    }

    function del_incidencia_modal(id_reg_accion) {
        document.getElementById('del_id_reg_acciones').value = id_reg_accion;
    }

    async function rellenarModal(id_reg_acciones) {
        let btn = document.getElementById('buttonEditar');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Cargando...';
        btn.disabled = true;

        await fetch(`<?php echo RUTA_URL ?>/incidencias/get_accion/${id_reg_acciones}`, {
            headers: { 'Content-Type': 'application/json' }, credentials: 'include'
        })
        .then(r => r.json())
        .then(function(data) {
            document.getElementById('id_reg_acciones').value  = id_reg_acciones;
            document.getElementById('accion_edit').value      = data.accion;
            document.getElementById('horas_modal').value      = deMinutosAHorasMinutos(data.minutos);
            setTimeout(() => { btn.innerHTML = 'Guardar'; btn.disabled = false; }, 800);
        });
    }

    async function guardarEditAccion() {
        const datosForm = new FormData(document.getElementById('formEditarAccion'));
        await fetch(`<?php echo RUTA_URL ?>/incidencias/set_accion`, {
            method: 'POST', body: datosForm
        })
        .then(r => r.json())
        .then(function(data) {
            if (data) {
                document.getElementById('textoAccion_' + datosForm.get('id_reg_acciones')).innerHTML = datosForm.get('accion');
                document.getElementById('horasAccion_' + datosForm.get('id_reg_acciones')).innerHTML = data.formatoHoras;
                document.getElementById('horasTotales').innerHTML = data.horasTotales;
                new bootstrap.Toast(document.getElementById('toastOK')).show();
            } else {
                new bootstrap.Toast(document.getElementById('toastKO')).show();
            }
        })
        .catch(() => new bootstrap.Toast(document.getElementById('toastKO')).show());
    }

    async function delAccion(formulario) {
        const datosForm = new FormData(formulario);
        await fetch(`<?php echo RUTA_URL ?>/incidencias/del_accion`, {
            method: 'POST', body: datosForm
        })
        .then(r => r.json())
        .then(function(data) {
            if (data) {
                document.getElementById('accion_' + datosForm.get('id_reg_acciones')).remove();
                new bootstrap.Toast(document.getElementById('toastOK')).show();
            } else {
                new bootstrap.Toast(document.getElementById('toastKO')).show();
            }
        })
        .catch(() => new bootstrap.Toast(document.getElementById('toastKO')).show());
    }

    function deMinutosAHorasMinutos(totalMinutos) {
        const h = Math.floor(totalMinutos / 60).toString().padStart(2, '0');
        const m = (totalMinutos % 60).toString().padStart(2, '0');
        return `${h}:${m}`;
    }

    // Carga de ubicaciones al cambiar edificio
    let edificios = <?php echo json_encode($datos['edificios']) ?>;

    function cargarUbicaciones() {
        const edificioSelect  = document.getElementById('id_edificio');
        const ubicacionSelect = document.getElementById('id_ubicacion');
        ubicacionSelect.innerHTML = '<option value="" disabled selected>-- Selecciona --</option>';
        const edificio = edificios.find(e => e.id_edificio === edificioSelect.value);
        if (edificio) {
            edificio.ubicaciones.forEach(function(u) {
                const opt = document.createElement('option');
                opt.value = u.id_ubicacion;
                opt.textContent = u.ubicacion;
                ubicacionSelect.appendChild(opt);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('id_edificio').addEventListener('change', cargarUbicaciones);
    });
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
