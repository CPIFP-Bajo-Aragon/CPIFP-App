<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<style>
.tabla-asesorias {
    table-layout: auto;
    width: 100% !important;
    margin-left: 0 !important;
    margin-top: 10px;
    border-collapse: collapse;
    border: 1px solid #cde;
}
.tabla-asesorias th, .tabla-asesorias td {
    border: 1px solid #dde;
    padding: 7px 10px;
    vertical-align: middle;
}
.tabla-asesorias thead th { background-color: #0583c3; color: white; border-color: #0471a6; }
.tabla-asesorias tbody tr.fila-datos { border-top: 3px solid #0583c3 !important; }
.tabla-asesorias tbody tr.grupo-par td   { background-color: #f4f9fd; }
.tabla-asesorias tbody tr.grupo-impar td { background-color: #ffffff; }
.tabla-asesorias tbody tr.fila-detalle td { background-color: #e8f3fa !important; border-top: none; }
</style>

<div class="container-fluid px-4 py-4">

    <div class="row mb-3 align-items-center">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <span><i class="fas fa-list me-2"></i>Asesorías</span>
            </span>
        </div>
        <div class="col-auto">
            <a class="btn btn-custom" href="<?php echo RUTA_URL ?>/asesorias/add_asesoria">
                <i class="fas fa-plus me-1"></i>Nueva asesoría
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <form method="get" action="<?php echo RUTA_URL ?>/asesorias/filtro" class="mb-3">
        <div class="row g-2 align-items-end">

            <div class="col-12 col-md-2">
                <div class="input-group input-group-sm">
                    <label class="input-group-text">Desde</label>
                    <input type="date" name="fecha_ini" class="form-control" onchange="submit()"
                           value="<?php echo $this->datos['filtro']['fecha_ini'] ?>">
                </div>
            </div>

            <div class="col-12 col-md-2">
                <div class="input-group input-group-sm">
                    <label class="input-group-text">Hasta</label>
                    <input type="date" name="fecha_fin" class="form-control" onchange="submit()"
                           value="<?php echo $this->datos['filtro']['fecha_fin'] ?>">
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="input-group input-group-sm">
                    <label class="input-group-text"><i class="fas fa-search"></i></label>
                    <input type="text" name="buscar" id="buscar" class="form-control"
                           onchange="submit()" autocomplete="off" placeholder="Buscar...">
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
                <a href="<?php echo RUTA_URL ?>/asesorias/filtro" class="btn btn-custom btn-sm w-100">
                    <i class="fas fa-times me-1"></i>Limpiar filtro
                </a>
            </div>

        </div>
    </form>

    <div class="mb-2">
        <strong style="color:#27ae60">
            <i class="fas fa-database me-1"></i>
            Nº de registros: <?php echo $datos['asesorias']->numTotalRegistros ?>
        </strong>
    </div>

    <script>
        window.addEventListener('load', function() {
            document.getElementById('buscar').value = "<?php echo $datos['filtro']['buscar'] ?>";
            document.getElementById('buscar').focus();
        });
    </script>

    <div class="table-responsive">
    <table class="table tabla-asesorias">
        <thead>
            <tr>
                <th class="d-none d-md-table-cell">#</th>
                <th>Título</th>
                <th>Nombre</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Acciones</th>
                <th style="width:40px"></th>
            </tr>
        </thead>
        <tbody>
        <?php $gi = 0; foreach ($datos['asesorias']->registros as $asesoria): $gi++; $gc = ($gi % 2 === 0) ? 'grupo-par' : 'grupo-impar'; ?>

            <tr id="asesoria_<?php echo $asesoria->id_asesoria ?>" class="fila-datos <?php echo $gc ?>">
                <td class="d-none d-md-table-cell"><?php echo $asesoria->id_asesoria ?></td>
                <td><?php echo htmlspecialchars($asesoria->titulo_as) ?></td>
                <td><?php echo htmlspecialchars($asesoria->nombre_as) ?></td>
                <td class="text-center" style="width:110px">
                    <?php if ($asesoria->id_estado == 1): ?>
                        <span style="color:#27ae60; font-weight:600"><?php echo $asesoria->estado ?></span>
                    <?php elseif ($asesoria->id_estado == 2): ?>
                        <span style="color:#f39c12; font-weight:600"><?php echo $asesoria->estado ?></span>
                    <?php elseif ($asesoria->id_estado == 3): ?>
                        <span style="color:#e74c3c; font-weight:600"><?php echo $asesoria->estado ?></span>
                    <?php endif ?>
                </td>
                <td class="text-center text-nowrap" style="width:90px">
                    <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol, [100,200,300]) || $asesoria->acciones[0]->id_profesor == $datos['usuarioSesion']->id_profesor): ?>
                    <a class="btn btn-custom btn-sm"
                       href="<?php echo RUTA_URL ?>/asesorias/ver_asesoria/<?php echo $asesoria->id_asesoria ?>" title="Editar">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <?php endif ?>
                    <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol, [300])): ?>
                    <a onclick="del_asesoria_modal(<?php echo $asesoria->id_asesoria ?>)"
                       data-bs-toggle="modal" data-bs-target="#modalDelAsesoria"
                       class="btn btn-sm" style="color:#e74c3c; border:1px solid #e74c3c" title="Eliminar">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                    <?php endif ?>
                </td>
                <td>
                    <button class="accordion-button collapsed btn-custom btn-sm"
                            type="button" data-bs-toggle="collapse"
                            data-bs-target="#det_<?php echo $asesoria->id_asesoria ?>"
                            style="padding:4px 8px; font-size:.8rem">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </td>
            </tr>

            <tr id="det_<?php echo $asesoria->id_asesoria ?>" class="accordion-collapse collapse fila-detalle">
                <td class="d-none d-md-table-cell"></td>
                <td colspan="5" style="font-size:.88rem; line-height:1.8; padding:8px 12px">
                    <?php echo $asesoria->dni_as      ? '<strong>DNI:</strong> '      . htmlspecialchars($asesoria->dni_as)      . '&nbsp;&nbsp;&nbsp;' : '' ?>
                    <?php echo $asesoria->telefono_as ? '<strong>Teléfono:</strong> ' . htmlspecialchars($asesoria->telefono_as) . '&nbsp;&nbsp;&nbsp;' : '' ?>
                    <?php echo $asesoria->email_as    ? '<strong>Email:</strong> '    . htmlspecialchars($asesoria->email_as)                          : '' ?>
                    <?php echo $asesoria->domicilio_as ? '<br><strong>Domicilio:</strong> ' . htmlspecialchars($asesoria->domicilio_as) : '' ?>
                    <?php echo $asesoria->descripcion_as ? '<br><strong>Descripción:</strong> ' . htmlspecialchars($asesoria->descripcion_as) : '' ?>
                    <ul class="mb-0 mt-1">
                        <?php foreach ($asesoria->acciones as $accion): ?>
                        <li>
                            <strong>Fecha:</strong> <?php echo formatoFecha($accion->fecha_reg) ?>
                            &nbsp;&nbsp;<strong>Por:</strong> <?php echo htmlspecialchars($accion->nombre_completo) ?>
                            &nbsp;&nbsp;<strong>Acción:</strong> <?php echo htmlspecialchars($accion->accion) ?>
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
            <?php echo $datos['asesorias']->paginaActual ?>,
            <?php echo $datos['asesorias']->numPaginas ?>,
            '<?php echo RUTA_URL ?>/asesorias/filtro',
            '<?php echo $datos['cadenaGet'] ?>'
        );
    </script>

</div>

<!-- Modal Eliminar -->
<div class="modal fade" id="modalDelAsesoria" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">¿Eliminar la asesoría?</h5></div>
            <div class="modal-body"><p>Se borrarán todas las acciones registradas en la asesoría.</p></div>
            <div class="modal-footer">
                <form method="post" id="formDelAsesoria" action="javascript:del_asesoria()">
                    <button type="button" class="btn btn-custom" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn" style="background:#e74c3c;color:#fff" data-bs-dismiss="modal">Borrar</button>
                    <input type="hidden" id="id_asesoria" name="id_asesoria">
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
    function del_asesoria_modal(id) { document.getElementById('id_asesoria').value = id; }
    async function del_asesoria() {
        const f = new FormData(document.getElementById('formDelAsesoria'));
        await fetch(`<?php echo RUTA_URL ?>/asesorias/del_asesoria`, { method:'POST', body:f })
            .then(r => r.json())
            .then(data => {
                if (data) {
                    document.getElementById('asesoria_' + f.get('id_asesoria')).remove();
                    new bootstrap.Toast(document.getElementById('toastOK')).show();
                } else { new bootstrap.Toast(document.getElementById('toastKO')).show(); }
            })
            .catch(() => new bootstrap.Toast(document.getElementById('toastKO')).show());
    }
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
