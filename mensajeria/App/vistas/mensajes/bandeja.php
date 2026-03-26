<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container-fluid px-4 py-4">

    <div class="row mb-3 align-items-center">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <span><i class="fas fa-inbox me-2"></i>Bandeja de entrada</span>
            </span>
        </div>
        <div class="col-auto">
            <a href="<?php echo RUTA_URL ?>/Mensajes/nuevo" class="btn btn-custom btn-sm">
                <i class="fas fa-pen me-1"></i>Nuevo mensaje
            </a>
        </div>
    </div>

    <?php dibujarBotonesPaginacion($datos['totalPaginas'], $datos['paginaActual']) ?>

    <?php if (empty($datos['mensajes'])): ?>
    <div class="alert alert-info mt-3">
        <i class="fas fa-info-circle me-2"></i>No tienes mensajes recibidos.
    </div>
    <?php else: ?>

    <div class="table-responsive">
    <table class="table tabla-formato">
        <thead>
            <tr>
                <th style="width:3%"></th>
                <th>Remitente</th>
                <th>Asunto</th>
                <th style="width:15%" class="text-center">Fecha</th>
                <th style="width:6%"  class="text-center">Adj.</th>
                <th style="width:7%"  class="text-center">Accion</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($datos['mensajes'] as $m): ?>
        <tr onclick="location.href='<?php echo RUTA_URL ?>/Mensajes/ver/<?php echo $m->id ?>'"
            style="cursor:pointer; <?php echo !$m->leido ? 'font-weight:600' : '' ?>">
            <td class="text-center">
                <?php if (!$m->leido): ?>
                <i class="fas fa-circle" style="color:#0583c3; font-size:.6rem; vertical-align:middle"
                   title="No leido"></i>
                <?php endif ?>
            </td>
            <td><?php echo htmlspecialchars($m->remitente) ?></td>
            <td><?php echo htmlspecialchars($m->asunto ?: '(sin asunto)') ?></td>
            <td class="text-center text-muted" style="font-size:.88rem">
                <?php echo date('d/m/Y H:i', strtotime($m->fecha_envio)) ?>
            </td>
            <td class="text-center">
                <?php if ($m->tiene_adjunto): ?>
                <i class="fas fa-paperclip" style="color:#0583c3" title="Tiene adjunto"></i>
                <?php endif ?>
            </td>
            <td class="text-center" onclick="event.stopPropagation()">
                <form method="POST" action="<?php echo RUTA_URL ?>/Mensajes/eliminar"
                      onsubmit="return confirm('¿Eliminar este mensaje?')">
                    <input type="hidden" name="id_mensaje" value="<?php echo $m->id ?>">
                    <button class="btn btn-sm" style="color:#e74c3c; border:1px solid #e74c3c"
                            title="Eliminar">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach ?>
        </tbody>
    </table>
    </div>

    <?php dibujarBotonesPaginacion($datos['totalPaginas'], $datos['paginaActual']) ?>
    <?php endif ?>

</div>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
