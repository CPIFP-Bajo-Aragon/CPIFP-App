<?php require_once RUTA_APP . '/vistas/inc/header_general.php' ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="p-4 shadow border mt-5 tarjeta">
<div class="container-fluid">

    <!-- Encabezado -->
    <div class="row mb-3 align-items-center">
        <div class="col">
            <strong style="font-size:1.1rem">
                <i class="fas fa-chalkboard-teacher me-2" style="color:#2C3E50"></i>
                Asignación de Tutores por Grupo
            </strong>
        </div>
    </div>

    <?php if (empty($datos['agrupado'])): ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-2"></i>
        No hay cursos registrados en la base de datos.
    </div>

    <?php else: ?>

    <?php foreach ($datos['agrupado'] as $dep): ?>

    <!-- ══════════════════════════════════════════════════
         BLOQUE DEPARTAMENTO
    ══════════════════════════════════════════════════ -->
    <div class="mb-4">

        <!-- Cabecera departamento -->
        <div class="p-2 mb-2 rounded"
             style="background:#2C3E50; color:#fff; font-weight:600; font-size:.95rem;">
            <i class="fas fa-building me-2"></i>
            <?php echo htmlspecialchars($dep['departamento']) ?>
            <span class="badge ms-2" style="background:#0583c3; font-size:.75rem;">
                <?php echo htmlspecialchars($dep['departamento_corto']) ?>
            </span>
        </div>

        <?php foreach ($dep['ciclos'] as $ciclo): ?>

        <!-- Subcabecera ciclo -->
        <div class="ps-3 py-1 mb-1 rounded"
             style="background:#ecf0f1; border-left:4px solid #0583c3; font-weight:600; font-size:.88rem; color:#2C3E50;">
            <i class="fas fa-graduation-cap me-2" style="color:#0583c3"></i>
            <?php echo htmlspecialchars($ciclo['ciclo']) ?>
            <span class="text-muted fw-normal ms-1">(<?php echo htmlspecialchars($ciclo['ciclo_corto']) ?>)</span>
        </div>

        <!-- Tabla de cursos del ciclo -->
        <div class="table-responsive mb-3 ps-3">
        <table class="table table-bordered tabla-formato mb-0">
            <thead>
                <tr>
                    <th style="width:120px">Grupo</th>
                    <th>Tutores asignados</th>
                    <th class="text-center" style="width:100px">Editar</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($ciclo['cursos'] as $curso): ?>
            <tr id="fila-curso-<?php echo $curso['id_curso'] ?>">

                <!-- Nombre del grupo -->
                <td class="fw-semibold">
                    <?php echo htmlspecialchars($curso['curso']) ?>
                </td>

                <!-- Tutores asignados (chips eliminables) -->
                <td id="tutores-<?php echo $curso['id_curso'] ?>">
                    <?php if (empty($curso['tutores'])): ?>
                        <span class="text-muted fst-italic" id="sin-tutor-<?php echo $curso['id_curso'] ?>">
                            Sin tutor asignado
                        </span>
                    <?php else: ?>
                        <?php foreach ($curso['tutores'] as $t): ?>
                        <span class="badge me-1 mb-1 d-inline-flex align-items-center gap-1"
                              style="background:#0583c3; font-size:.82rem; padding:.35em .6em;"
                              id="badge-<?php echo $curso['id_curso'] ?>-<?php echo $t->id_profesor ?>">
                            <i class="fas fa-user-tie me-1"></i>
                            <?php echo htmlspecialchars($t->nombre_completo) ?>
                            <button type="button"
                                    class="btn-close btn-close-white btn-sm ms-1"
                                    style="font-size:.6rem"
                                    title="Quitar tutor"
                                    onclick="quitarTutor(<?php echo $curso['id_curso'] ?>, <?php echo $t->id_profesor ?>, '<?php echo addslashes(htmlspecialchars($t->nombre_completo)) ?>')">
                            </button>
                        </span>
                        <?php endforeach ?>
                    <?php endif ?>
                </td>

                <!-- Botón editar -->
                <td class="text-center">
                    <a href="<?php echo RUTA_URL ?>/tutores/editar/<?php echo $curso['id_curso'] ?>"
                       title="Asignar / cambiar tutores"
                       class="btn btn-sm"
                       style="background:#f39c12; color:#fff; border:none;">
                        <i class="fas fa-user-edit"></i>
                    </a>
                </td>

            </tr>
            <?php endforeach ?>
            </tbody>
        </table>
        </div>

        <?php endforeach ?>
    </div><!-- /bloque departamento -->

    <?php endforeach ?>
    <?php endif ?>

</div>
</div>

<!-- Toast de confirmación -->
<div id="toastContainer" style="position:fixed;bottom:20px;right:20px;z-index:9999;"></div>

<script>
const RUTA_URL = '<?php echo RUTA_URL ?>';

function toast(msg, ok) {
    const d = document.createElement('div');
    d.className = 'toast align-items-center border-0 mb-2 text-white bg-' + (ok ? 'success' : 'danger');
    d.setAttribute('role', 'alert');
    d.innerHTML = `<div class="d-flex"><div class="toast-body fw-semibold">${msg}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
    document.getElementById('toastContainer').appendChild(d);
    new bootstrap.Toast(d, {delay: 3000}).show();
    d.addEventListener('hidden.bs.toast', () => d.remove());
}

function quitarTutor(id_curso, id_profesor, nombre) {
    if (!confirm('¿Quitar a ' + nombre + ' como tutor de este grupo?')) return;

    fetch(RUTA_URL + '/tutores/ajax_quitar', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id_curso=' + id_curso + '&id_profesor=' + id_profesor
    })
    .then(r => r.json())
    .then(d => {
        if (d.ok) {
            // Eliminar el badge del DOM
            const badge = document.getElementById('badge-' + id_curso + '-' + id_profesor);
            if (badge) badge.remove();

            // Si no quedan badges, mostrar "Sin tutor asignado"
            const celda = document.getElementById('tutores-' + id_curso);
            const badges = celda.querySelectorAll('.badge');
            if (badges.length === 0) {
                celda.innerHTML = '<span class="text-muted fst-italic">Sin tutor asignado</span>';
            }
            toast('Tutor quitado correctamente', true);
        } else {
            toast('Error al quitar el tutor', false);
        }
    })
    .catch(() => toast('Error de conexión', false));
}
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer_general.php' ?>
