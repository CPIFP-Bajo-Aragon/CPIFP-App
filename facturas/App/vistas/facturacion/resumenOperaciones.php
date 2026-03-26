<?php require_once RUTA_APP . '/vistas/inc/header.php'; ?>

<style>
#tblResumen { font-size: 1rem; border-collapse: separate; border-spacing: 0; }
#tblResumen thead th {
    background-color: #0583c3; color: #fff;
    font-weight: 600; padding: 12px 14px; vertical-align: middle; border: none;
}
#tblResumen tbody td {
    padding: 10px 14px; vertical-align: middle;
    border-bottom: 1px solid #dee2e6; font-size: .95rem;
}
#tblResumen tbody tr:nth-child(odd)  { background-color: #f4f9fd; }
#tblResumen tbody tr:nth-child(even) { background-color: #ffffff; }
#tblResumen tbody tr:hover           { background-color: #dbeef9; }

.importe-cell { font-family: monospace; font-weight: 600; text-align: right; }
.inv-badge    { font-size:.78rem; padding:2px 8px; border-radius:10px; font-weight:600; }
.inv-s        { background:#d4edda; color:#155724; }
.inv-n        { background:#e9ecef; color:#6c757d; }

/* Total en pie de tabla */
tfoot td { background:#eaf4fb; font-weight:700; border-top:2px solid #0583c3; padding:10px 14px; }
</style>

<div class="p-4 shadow border mt-4 mx-3 tarjeta">
<div class="container-fluid">

    <!-- Encabezado -->
    <div class="row mb-3">
        <div class="col-12">
            <strong id="ciclo_encabezado">
                <i class="fas fa-list-alt me-2"></i>Resumen de operaciones
                <span class="ms-2 navbar-destino" style="font-size:1rem;">
                    <?php echo $datos['filtroTodosDestinos']
                        ? 'Todos los departamentos'
                        : htmlspecialchars($datos['persistencia']['nombreDestinoSeleccionado']) ?>
                </span>
            </strong>
        </div>
    </div>

    <!-- Filtros -->
    <form id="fmFiltros"
          action="<?php echo RUTA_URL ?>/GestionFacturas/resumen"
          method="POST" class="mb-4">

        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <label class="input-group-text">Proveedor</label>
                    <input type="text" name="proveedor" class="form-control"
                           placeholder="Buscar por proveedor..."
                           value="<?php echo htmlspecialchars($datos['filtroProveedor']) ?>">
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="input-group">
                    <label class="input-group-text">Desde</label>
                    <input type="date" name="fechaIni" class="form-control"
                           value="<?php echo $datos['filtroFechaIni'] ?>">
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="input-group">
                    <label class="input-group-text">Hasta</label>
                    <input type="date" name="fechaFin" class="form-control"
                           value="<?php echo $datos['filtroFechaFin'] ?>">
                </div>
            </div>
            <div class="col-6 col-md-1">
                <button type="submit" class="btn btn-custom w-100" title="Buscar">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <div class="col-6 col-md-1">
                <a href="<?php echo RUTA_URL ?>/GestionFacturas/resumen"
                   class="btn btn-outline-secondary w-100" title="Limpiar filtros">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </div>

        <?php if ($datos['usuarioSesion']->id_rol == 500): ?>
        <!-- Opción "Todos los departamentos" solo para Equipo Directivo -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox"
                           name="todosDestinos" value="1"
                           id="chkTodos"
                           <?php echo $datos['filtroTodosDestinos'] ? 'checked' : '' ?>
                           onchange="this.form.submit()">
                    <label class="form-check-label fw-semibold" for="chkTodos">
                        <i class="fas fa-globe me-1" style="color:#0583c3"></i>
                        Ver facturas de <strong>todos los departamentos</strong>
                    </label>
                </div>
            </div>
        </div>
        <?php endif ?>

    </form>

    <!-- Botón exportar Excel -->
    <?php
    $qExport = http_build_query([
        'proveedor'      => $datos['filtroProveedor'],
        'fechaIni'       => $datos['filtroFechaIni'],
        'fechaFin'       => $datos['filtroFechaFin'],
        'todosDestinos'  => $datos['filtroTodosDestinos'],
    ]);
    ?>
    <div class="d-flex justify-content-end mb-3">
        <a href="<?php echo RUTA_URL ?>/GestionFacturas/exportarResumen?<?php echo $qExport ?>"
           class="btn btn-outline-success btn-sm">
            <i class="fas fa-file-excel me-2"></i>Exportar a Excel
        </a>
    </div>

    <!-- Paginación superior -->
    <?php dibujarBotonesPaginacion($datos['totalPaginas'], $datos['paginaActual']) ?>

    <!-- Tabla -->
    <?php if (empty($datos['resumenFacturas'])): ?>
    <div class="alert alert-info mt-3">
        <i class="fas fa-info-circle me-2"></i>No hay facturas que coincidan con los filtros aplicados.
    </div>
    <?php else: ?>

    <?php
    $totalImporte = array_sum(array_map(fn($f) => (float)$f->Importe, $datos['resumenFacturas']));
    ?>

    <div class="table-responsive mt-3">
    <table class="table table-bordered" id="tblResumen">
        <thead>
            <tr>
                <th style="width:6%">Asiento</th>
                <th style="width:10%">Nº Factura</th>
                <th>Proveedor</th>
                <?php if ($datos['filtroTodosDestinos']): ?>
                <th style="width:14%">Departamento</th>
                <?php endif ?>
                <th style="width:10%" class="text-center">F. Factura</th>
                <th style="width:10%" class="text-center">F. Aprobación</th>
                <th style="width:9%" class="text-center">Inv.</th>
                <th style="width:10%" class="text-end">Importe</th>
                <th style="width:5%" class="text-center">Ver</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($datos['resumenFacturas'] as $f): ?>
        <tr>
            <td class="text-muted" style="font-size:.83rem"><?php echo $f->N_Asiento ?></td>
            <td><?php echo htmlspecialchars($f->NFactura ?? '—') ?></td>
            <td>
                <span class="fw-semibold"><?php echo htmlspecialchars($f->Nombre ?? $f->CIF) ?></span>
                <br><small class="text-muted" style="font-size:.78rem"><?php echo $f->CIF ?></small>
            </td>
            <?php if ($datos['filtroTodosDestinos']): ?>
            <td><small><?php echo htmlspecialchars($f->Depart_Servicio) ?></small></td>
            <?php endif ?>
            <td class="text-center"><?php echo transformarFecha($f->Ffactura) ?></td>
            <td class="text-center text-muted">
                <?php echo $f->Faprobacion ? transformarFecha($f->Faprobacion) : '—' ?>
            </td>
            <td class="text-center">
                <span class="inv-badge <?php echo ($f->Inventariable === 'S') ? 'inv-s' : 'inv-n' ?>">
                    <?php echo ($f->Inventariable === 'S') ? 'Sí' : 'No' ?>
                </span>
            </td>
            <td class="importe-cell">
                <?php echo number_format((float)$f->Importe, 2, ',', '.') ?> €
            </td>
            <td class="text-center">
                <form action="<?php echo RUTA_URL ?>/GestionFacturas/verFactura" method="POST">
                    <input type="hidden" name="nAsiento" value="<?php echo $f->N_Asiento ?>">
                    <button type="submit" class="btn btn-sm btn-custom" title="Ver factura">
                        <i class="fas fa-eye"></i>
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="<?php echo $datos['filtroTodosDestinos'] ? 7 : 6 ?>"
                    class="text-end">
                    Total página:
                </td>
                <td class="importe-cell">
                    <?php echo number_format($totalImporte, 2, ',', '.') ?> €
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    </div>

    <?php endif ?>

    <!-- Paginación inferior -->
    <?php dibujarBotonesPaginacion($datos['totalPaginas'], $datos['paginaActual']) ?>

</div>
</div>

<script>
// La paginación del helper usa ?pagina=N — necesitamos preservar los filtros del POST.
// Interceptamos los botones de paginación y añadimos los filtros como campos ocultos.
document.addEventListener('DOMContentLoaded', function () {

    // Recoger valores actuales de filtros
    const filtros = {
        proveedor:     '<?php echo addslashes($datos['filtroProveedor']) ?>',
        fechaIni:      '<?php echo $datos['filtroFechaIni'] ?>',
        fechaFin:      '<?php echo $datos['filtroFechaFin'] ?>',
        todosDestinos: '<?php echo $datos['filtroTodosDestinos'] ? '1' : '0' ?>',
    };

    // Sobrescribir todos los onclick de paginación para usar POST con filtros
    document.querySelectorAll('nav .pagination button:not([disabled])').forEach(function(btn) {
        const onclick = btn.getAttribute('onclick') || '';
        const match   = onclick.match(/pagina=(\d+)/);
        if (!match) return;
        const pagina = match[1];

        btn.removeAttribute('onclick');
        btn.addEventListener('click', function () {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo RUTA_URL ?>/GestionFacturas/resumen?pagina=' + pagina;
            for (const [k, v] of Object.entries(filtros)) {
                const input = document.createElement('input');
                input.type  = 'hidden';
                input.name  = k;
                input.value = v;
                form.appendChild(input);
            }
            document.body.appendChild(form);
            form.submit();
        });
    });
});
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
