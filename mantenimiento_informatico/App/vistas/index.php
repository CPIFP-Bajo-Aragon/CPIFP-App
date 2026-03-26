<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container-fluid px-4 py-4">

    <div class="row mb-3 align-items-center">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <span><i class="fas fa-exclamation-circle me-2"></i>Incidencias Nuevas o Activas</span>
            </span>
        </div>
        <div class="col-auto">
            <a class="btn btn-custom" href="<?php echo RUTA_URL ?>/incidencias/add_incidencia">
                <i class="fas fa-plus me-1"></i>Nueva incidencia
            </a>
        </div>
    </div>

    <?php if (empty($datos['incidenciasActivas'])): ?>
    <div class="alert alert-info">
        <i class="fas fa-check-circle me-2"></i>No hay incidencias activas en este momento.
    </div>
    <?php else: ?>


<style>
/* Responsive: sobreescribe tabla-formato para que ocupe el ancho del contenedor */
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
                <th class="d-none d-lg-table-cell">Descripción</th>
                <th class="text-center d-none d-md-table-cell">Prioridad</th>
                <th class="d-none d-md-table-cell">Ubicación</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php $grupoIdx = 0; foreach ($datos['incidenciasActivas'] as $incidencia): $grupoIdx++; $grupoClase = ($grupoIdx % 2 === 0) ? 'grupo-par' : 'grupo-impar'; ?>

            <tr id="incidencia_<?php echo $incidencia->id_incidencia ?>" class="fila-datos <?php echo $grupoClase ?>">
                <td class="d-none d-md-table-cell"><?php echo $incidencia->id_incidencia ?></td>
                <td><strong><?php echo htmlspecialchars($incidencia->titulo_in) ?></strong></td>
                <td class="d-none d-lg-table-cell" style="max-width:250px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis">
                    <?php echo htmlspecialchars($incidencia->descripcion_in) ?>
                </td>
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
                <td class="text-center">
                    <?php if ($incidencia->id_estado == 1): ?>
                        <span style="color:#27ae60; font-weight:600"><?php echo $incidencia->estado ?></span>
                    <?php elseif ($incidencia->id_estado == 2): ?>
                        <span style="color:#f39c12; font-weight:600"><?php echo $incidencia->estado ?></span>
                    <?php endif ?>
                </td>
                <td class="text-center text-nowrap">
                    <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol, [200, 300])
                               || $incidencia->acciones[0]->id_profesor == $datos['usuarioSesion']->id_profesor): ?>
                    <a class="btn btn-custom btn-sm"
                       href="<?php echo RUTA_URL ?>/incidencias/ver_incidencia/<?php echo $incidencia->id_incidencia ?>"
                       title="Editar">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <?php endif ?>

                    <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol, [200, 300])
                               || ($incidencia->acciones[0]->id_profesor == $datos['usuarioSesion']->id_profesor
                                   && count($incidencia->acciones) == 1)): ?>
                    <a onclick="del_incidencia_modal(<?php echo $incidencia->id_incidencia ?>)"
                       data-bs-toggle="modal" data-bs-target="#modalDelIncidencia"
                       class="btn btn-sm" style="color:#e74c3c; border:1px solid #e74c3c" title="Eliminar">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                    <?php endif ?>
                </td>
            </tr>

            <tr id="acciones_<?php echo $incidencia->id_incidencia ?>" class="fila-acciones <?php echo $grupoClase ?>">
                <td></td>
                <td colspan="6">
                    <ul class="mb-0" style="font-size:.9rem">
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
    <?php endif ?>

</div>


<!-- Modal Eliminar Incidencia -->
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
                document.getElementById('acciones_'  + datosForm.get('id_incidencia')).remove();
                new bootstrap.Toast(document.getElementById('toastOK')).show();
            } else {
                new bootstrap.Toast(document.getElementById('toastKO')).show();
            }
        })
        .catch(() => new bootstrap.Toast(document.getElementById('toastKO')).show());
    }
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
