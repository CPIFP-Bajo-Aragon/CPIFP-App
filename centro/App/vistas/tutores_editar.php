<?php require_once RUTA_APP . '/vistas/inc/header_general.php' ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<?php
$curso     = $datos['curso'];
$asignados = $datos['asignados'];
// Construir set de IDs ya asignados para marcar los checkboxes
$ids_asignados = array_map(fn($t) => $t->id_profesor, $asignados);
?>

<div class="p-4 shadow border mt-5 tarjeta">
<div class="container">

    <!-- Encabezado -->
    <div class="row mb-4 align-items-center">
        <div class="col">
            <strong style="font-size:1.05rem">
                <i class="fas fa-user-edit me-2" style="color:#0583c3"></i>
                Asignar tutores al grupo
                <span class="badge ms-2" style="background:#0583c3; font-size:.85rem;">
                    <?php echo htmlspecialchars($curso->curso) ?>
                </span>
            </strong>
            <div class="text-muted mt-1" style="font-size:.88rem">
                <i class="fas fa-graduation-cap me-1"></i>
                <?php echo htmlspecialchars($curso->ciclo) ?>
                &nbsp;·&nbsp;
                <i class="fas fa-building me-1"></i>
                <?php echo htmlspecialchars($curso->departamento) ?>
            </div>
        </div>
        <div class="col-auto">
            <a href="<?php echo RUTA_URL ?>/tutores" class="btn btn-volver">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    <!-- Tutores actualmente asignados -->
    <div class="mb-3">
        <span class="fw-semibold" style="font-size:.9rem;">Tutores actuales:</span>
        <?php if (empty($asignados)): ?>
            <span class="text-muted fst-italic ms-2">Ninguno asignado</span>
        <?php else: ?>
            <?php foreach ($asignados as $t): ?>
            <span class="badge ms-1" style="background:#27ae60; font-size:.82rem; padding:.35em .6em;">
                <i class="fas fa-user-tie me-1"></i>
                <?php echo htmlspecialchars($t->nombre_completo) ?>
            </span>
            <?php endforeach ?>
        <?php endif ?>
    </div>

    <hr>

    <!-- Formulario de asignación -->
    <form action="<?php echo RUTA_URL ?>/tutores/guardar" method="post" id="formTutores">
        <input type="hidden" name="id_curso" value="<?php echo $curso->id_curso ?>">

        <!-- Buscador rápido -->
        <div class="row mb-3">
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="buscadorProfesor" class="form-control"
                           placeholder="Filtrar por nombre…"
                           oninput="filtrarProfesores(this.value)">
                </div>
            </div>
            <div class="col-12 col-md-6 d-flex align-items-center gap-2 mt-2 mt-md-0">
                <button type="button" class="btn btn-sm btn-outline-secondary"
                        onclick="marcarTodos(true)">
                    <i class="fas fa-check-square me-1"></i>Marcar todos
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary"
                        onclick="marcarTodos(false)">
                    <i class="fas fa-square me-1"></i>Desmarcar todos
                </button>
            </div>
        </div>

        <!-- Lista de profesores con checkboxes -->
        <div class="table-responsive mb-4">
        <table class="table table-bordered tabla-formato" id="tablaProf">
            <thead>
                <tr>
                    <th style="width:50px" class="text-center">Tutor</th>
                    <th>Nombre</th>
                    <th class="d-none d-md-table-cell">Login</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($datos['profesores'] as $p): ?>
            <?php $checked = in_array($p->id_profesor, $ids_asignados); ?>
            <tr class="fila-profesor <?php echo $checked ? 'table-success' : '' ?>"
                id="fila-prof-<?php echo $p->id_profesor ?>">

                <td class="text-center">
                    <div class="form-check d-flex justify-content-center">
                        <input class="form-check-input check-tutor"
                               type="checkbox"
                               name="tutores[]"
                               value="<?php echo $p->id_profesor ?>"
                               id="prof-<?php echo $p->id_profesor ?>"
                               <?php echo $checked ? 'checked' : '' ?>
                               onchange="resaltarFila(this)">
                    </div>
                </td>

                <td>
                    <label for="prof-<?php echo $p->id_profesor ?>" class="mb-0 w-100"
                           style="cursor:pointer">
                        <?php if ($checked): ?>
                        <i class="fas fa-user-tie me-1" style="color:#27ae60"></i>
                        <?php else: ?>
                        <i class="fas fa-user me-1 text-muted"></i>
                        <?php endif ?>
                        <span class="nombre-prof"><?php echo htmlspecialchars($p->nombre_completo) ?></span>
                    </label>
                </td>

                <td class="d-none d-md-table-cell text-muted">
                    <small><?php echo htmlspecialchars($p->login) ?></small>
                </td>
            </tr>
            <?php endforeach ?>
            </tbody>
        </table>
        </div>

        <!-- Botones -->
        <div class="d-flex gap-2 justify-content-end">
            <a href="<?php echo RUTA_URL ?>/tutores" class="btn btn-volver">
                <i class="fas fa-times me-1"></i>Cancelar
            </a>
            <input type="submit" class="btn" id="boton-modal"
                   value="Guardar asignación">
        </div>

    </form>

</div>
</div>

<script>
function resaltarFila(checkbox) {
    const fila = checkbox.closest('tr');
    const icono = fila.querySelector('i');
    if (checkbox.checked) {
        fila.classList.add('table-success');
        if (icono) { icono.className = 'fas fa-user-tie me-1'; icono.style.color = '#27ae60'; }
    } else {
        fila.classList.remove('table-success');
        if (icono) { icono.className = 'fas fa-user me-1 text-muted'; icono.style.color = ''; }
    }
}

function filtrarProfesores(texto) {
    texto = texto.toLowerCase();
    document.querySelectorAll('.fila-profesor').forEach(fila => {
        const nombre = fila.querySelector('.nombre-prof').textContent.toLowerCase();
        fila.style.display = nombre.includes(texto) ? '' : 'none';
    });
}

function marcarTodos(marcar) {
    document.querySelectorAll('.check-tutor').forEach(cb => {
        // Solo los visibles
        if (cb.closest('tr').style.display !== 'none') {
            cb.checked = marcar;
            resaltarFila(cb);
        }
    });
}
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer_general.php' ?>
