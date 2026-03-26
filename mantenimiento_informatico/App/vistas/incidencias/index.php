<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container-fluid px-4 py-4">

    <div class="row mb-3 align-items-center">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <span><i class="fas fa-list me-2"></i>Incidencias</span>
            </span>
        </div>
        <div class="col-auto">
            <a class="btn btn-custom" href="<?php echo RUTA_URL ?>/incidencias/add_incidencia">
                <i class="fas fa-plus me-1"></i>Nueva incidencia
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <form method="get" action="<?php echo RUTA_URL ?>/incidencias/filtro" class="mb-3">
        <div class="row g-2 align-items-end">

            <div class="col-12 col-md-2">
                <div class="input-group input-group-sm">
                    <label class="input-group-text">Desde</label>
                    <input type="date" name="fecha_ini" class="form-control"
                           onchange="submit()"
                           value="<?php echo $this->datos['filtro']['fecha_ini'] ?>">
                </div>
            </div>

            <div class="col-12 col-md-2">
                <div class="input-group input-group-sm">
                    <label class="input-group-text">Hasta</label>
                    <input type="date" name="fecha_fin" class="form-control"
                           onchange="submit()"
                           value="<?php echo $this->datos['filtro']['fecha_fin'] ?>">
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="input-group input-group-sm">
                    <label class="input-group-text"><i class="fas fa-search"></i></label>
                    <input type="text" name="buscar" id="buscar" class="form-control"
                           onchange="submit()" autocomplete="off"
                           placeholder="Buscar...">
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="d-flex gap-3 flex-wrap">
                    <?php foreach ($datos['estados'] as $estado): ?>
                    <?php $checked = in_array($estado->id_estado, $datos['filtro']['estado']) ? 'checked' : '' ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="estado[]"
                               value="<?php echo $estado->id_estado ?>"
                               id="estado_<?php echo $estado->id_estado ?>"
                               onchange="submit()" <?php echo $checked ?>>
                        <label class="form-check-label" for="estado_<?php echo $estado->id_estado ?>">
                            <?php if ($estado->id_estado == 1): ?>
                                <strong style="color:#27ae60"><?php echo $estado->estado ?></strong>
                            <?php elseif ($estado->id_estado == 2): ?>
                                <strong style="color:#f39c12"><?php echo $estado->estado ?></strong>
                            <?php elseif ($estado->id_estado == 3): ?>
                                <strong style="color:#e74c3c"><?php echo $estado->estado ?></strong>
                            <?php endif ?>
                        </label>
                    </div>
                    <?php endforeach ?>
                </div>
            </div>

            <div class="col-12 col-md-2">
                <a href="<?php echo RUTA_URL ?>/incidencias/filtro" class="btn btn-custom btn-sm w-100">
                    <i class="fas fa-times me-1"></i>Limpiar filtro
                </a>
            </div>

        </div>
    </form>

    <div class="mb-2">
        <strong style="color:#27ae60">
            <i class="fas fa-database me-1"></i>
            Nº de registros: <?php echo $datos['incidencias']->numTotalRegistros ?>
        </strong>
    </div>

    <script>
        window.addEventListener('load', function() {
            document.getElementById('buscar').value = "<?php echo $datos['filtro']['buscar'] ?>";
            document.getElementById('buscar').focus();
        });
    </script>


<style>
.tabla-incidencias {
    table-layout: auto;
    width: 100% !important;
    margin-left: 0 !important;
    margin-top: 10px;
    border-collapse: collapse;
    border: 1px solid #cde;
}
.tabla-incidencias th,
.tabla-incidencias td {
    border: 1px solid #dde;
    padding: 7px 10px;
    vertical-align: middle;
}
.tabla-incidencias thead th {
    background-color: #0583c3;
    color: white;
    border-color: #0471a6;
}
/* Separación visual entre incidencias: borde superior azul grueso en fila de datos */
.tabla-incidencias tbody tr.fila-datos {
    border-top: 3px solid #0583c3 !important;
}
/* Franjas alternas por grupo (par/impar via PHP) */
.tabla-incidencias tbody tr.grupo-par td {
    background-color: #f4f9fd;
}
.tabla-incidencias tbody tr.grupo-impar td {
    background-color: #ffffff;
}
/* Fila de acciones: ligeramente más oscura que su grupo */
.tabla-incidencias tbody tr.fila-acciones.grupo-par td {
    background-color: #e8f3fa;
}
.tabla-incidencias tbody tr.fila-acciones.grupo-impar td {
    background-color: #f7f7f7;
}
/* Fila de detalle expandible (todas las incidencias) */
.tabla-incidencias tbody tr.fila-detalle td {
    background-color: #e8f3fa !important;
    border-top: none;
}
</style>

    <div class="table-responsive">
    <table class="table tabla-incidencias">
        <thead>
            <tr>
                <th class="d-none d-md-table-cell">#</th>
                <th>Título</th>
                <th class="d-none d-lg-table-cell">Abierta por</th>
                <th class="text-center d-none d-md-table-cell">Prioridad</th>
                <th class="d-none d-md-table-cell">Ubicación</th>
                <th class="text-center d-none d-lg-table-cell">Horas</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Acciones</th>
                <th style="width:40px"></th>
            </tr>
        </thead>
        <tbody>
        <?php $grupoIdx = 0; foreach ($datos['incidencias']->registros as $incidencia): $grupoIdx++; $grupoClase = ($grupoIdx % 2 === 0) ? 'grupo-par' : 'grupo-impar'; ?>

            <tr id="incidencia_<?php echo $incidencia->id_incidencia ?>" class="fila-datos <?php echo $grupoClase ?>">
                <td class="d-none d-md-table-cell"><?php echo $incidencia->id_incidencia ?></td>
                <td><?php echo htmlspecialchars($incidencia->titulo_in) ?></td>
                <td class="d-none d-lg-table-cell" style="font-size:.88rem"><?php echo htmlspecialchars($incidencia->acciones[0]->nombre_completo) ?></td>
                <td class="text-center d-none d-md-table-cell">
                    <?php if ($incidencia->id_urgencia == 1): ?>
                        <span style="color:#f39c12; font-weight:600"><?php echo $incidencia->urgencia ?></span>
                    <?php elseif ($incidencia->id_urgencia == 2): ?>
                        <span style="color:#e67e22; font-weight:600"><?php echo $incidencia->urgencia ?></span>
                    <?php elseif ($incidencia->id_urgencia == 3): ?>
                        <span style="color:#e74c3c; font-weight:600"><?php echo $incidencia->urgencia ?></span>
                    <?php endif ?>
                </td>
                <td class="d-none d-md-table-cell"><?php echo htmlspecialchars($incidencia->edificio) ?> / <?php echo htmlspecialchars($incidencia->ubicacion) ?></td>
                <td class="text-center d-none d-lg-table-cell"><?php echo $incidencia->horasTotales ?></td>
                <td class="text-center" style="width:110px">
                    <?php if ($incidencia->id_estado == 1): ?>
                        <span style="color:#27ae60; font-weight:600"><?php echo $incidencia->estado ?></span>
                    <?php elseif ($incidencia->id_estado == 2): ?>
                        <span style="color:#f39c12; font-weight:600"><?php echo $incidencia->estado ?></span>
                    <?php elseif ($incidencia->id_estado == 3): ?>
                        <span style="color:#e74c3c; font-weight:600"><?php echo $incidencia->estado ?></span>
                    <?php endif ?>
                </td>
                <td class="text-center text-nowrap" style="width:90px">
                    <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol, [200, 300])
                               || $incidencia->acciones[0]->id_profesor == $datos['usuarioSesion']->id_profesor): ?>
                    <a class="btn btn-custom btn-sm"
                       href="<?php echo RUTA_URL ?>/incidencias/ver_incidencia/<?php echo $incidencia->id_incidencia ?>"
                       title="Editar">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <?php endif ?>

                    <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol, [200, 300])): ?>
                    <a onclick="del_incidencia_modal(<?php echo $incidencia->id_incidencia ?>)"
                       data-bs-toggle="modal" data-bs-target="#modalDelIncidencia"
                       class="btn btn-sm" style="color:#e74c3c; border:1px solid #e74c3c" title="Eliminar">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                    <?php endif ?>
                </td>
                <td>
                    <button class="accordion-button collapsed btn-custom btn-sm"
                            type="button" data-bs-toggle="collapse"
                            data-bs-target="#det_<?php echo $incidencia->id_incidencia ?>"
                            aria-expanded="false"
                            style="padding:4px 8px; font-size:.8rem">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </td>
            </tr>

            <tr id="det_<?php echo $incidencia->id_incidencia ?>" class="accordion-collapse collapse fila-detalle <?php echo $grupoClase ?>">
                <td class="d-none d-md-table-cell"></td>
                <td colspan="8">
                    <?php if ($incidencia->descripcion_in): ?>
                    <p class="mb-2 ms-2">
                        <strong>Descripción:</strong> <?php echo htmlspecialchars($incidencia->descripcion_in) ?>
                    </p>
                    <?php endif ?>
                    <ul class="mb-0" style="font-size:.88rem">
                        <?php foreach ($incidencia->acciones as $accion): ?>
                        <li>
                            <strong>Fecha:</strong> <?php echo formatoFecha($accion->fecha_reg) ?>
                            &nbsp;&nbsp;
                            <strong>Por:</strong> <?php echo htmlspecialchars($accion->nombre_completo) ?>
                            &nbsp;&nbsp;
                            <strong>Acción:</strong> <?php echo htmlspecialchars($accion->accion) ?>
                            <?php if (!$accion->automatica): ?>
                                <strong style="float:right"><?php echo formatoMinutosAHoras($accion->minutos) ?></strong>
                            <?php endif ?>
                        </li>
                        <?php endforeach ?>
                    </ul>
                </td>
            </tr>

        <?php endforeach ?>
        </tbody>
    </table>
    </div>

    <script>
        paginar(
            <?php echo $datos['incidencias']->paginaActual ?>,
            <?php echo $datos['incidencias']->numPaginas ?>,
            '<?php echo RUTA_URL ?>/incidencias/filtro',
            '<?php echo $datos['cadenaGet'] ?>'
        );
    </script>

</div>


<!-- Modal Eliminar -->
<div class="modal fade" id="modalDelIncidencia" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">¿Eliminar la incidencia?</h5>
            </div>
            <div class="modal-body">
                <p>Se borrarán todas las acciones registradas en la incidencia.</p>
            </div>
            <div class="modal-footer">
                <form method="post" id="formDelIncidencia" action="javascript:del_incidencia()">
                    <button type="button" class="btn btn-custom" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn" style="background:#e74c3c;color:#fff" data-bs-dismiss="modal">
                        Borrar
                    </button>
                    <input type="hidden" id="id_incidencia" name="id_incidencia">
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
            <strong class="me-auto">Acción OK</strong>
        </div>
    </div>
</div>
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:11">
    <div id="toastKO" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <svg class="bd-placeholder-img rounded me-2" width="20" height="20"><rect width="100%" height="100%" fill="red"/></svg>
            <strong class="me-auto">Error !!!</strong>
        </div>
    </div>
</div>

<script>
    function del_incidencia_modal(id_incidencia) {
        document.getElementById('id_incidencia').value = id_incidencia;
    }
    async function del_incidencia() {
        const datosForm = new FormData(document.getElementById('formDelIncidencia'));
        await fetch(`<?php echo RUTA_URL ?>/incidencias/del_incidencia`, {
            method: 'POST', body: datosForm
        })
        .then(r => r.json())
        .then(function(data) {
            if (data) {
                document.getElementById('incidencia_' + datosForm.get('id_incidencia')).remove();
                new bootstrap.Toast(document.getElementById('toastOK')).show();
            } else {
                new bootstrap.Toast(document.getElementById('toastKO')).show();
            }
        })
        .catch(() => new bootstrap.Toast(document.getElementById('toastKO')).show());
    }
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
