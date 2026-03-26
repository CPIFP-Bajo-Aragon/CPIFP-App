<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container-fluid px-4 py-4">

    <div class="row mb-3 align-items-center">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <span><i class="fas fa-paper-plane me-2"></i>Mensajes enviados</span>
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
        <i class="fas fa-info-circle me-2"></i>No has enviado ningun mensaje todavia.
    </div>
    <?php else: ?>

    <div class="table-responsive">
    <table class="table tabla-formato">
        <thead>
            <tr>
                <th>Destinatarios</th>
                <th>Asunto</th>
                <th style="width:15%" class="text-center">Fecha</th>
                <th style="width:6%"  class="text-center">Adj.</th>
                <th style="width:7%"  class="text-center">Ver</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($datos['mensajes'] as $m): ?>
        <tr onclick="location.href='<?php echo RUTA_URL ?>/Mensajes/ver/<?php echo $m->id ?>'"
            style="cursor:pointer">
            <td style="font-size:.88rem">
                <?php echo htmlspecialchars(mb_strimwidth($m->destinatarios, 0, 80, '...')) ?>
            </td>
            <td><?php echo htmlspecialchars($m->asunto ?: '(sin asunto)') ?></td>
            <td class="text-center text-muted" style="font-size:.88rem">
                <?php echo date('d/m/Y H:i', strtotime($m->fecha_envio)) ?>
            </td>
            <td class="text-center">
                <?php if ($m->tiene_adjunto): ?>
                <i class="fas fa-paperclip" style="color:#0583c3"></i>
                <?php endif ?>
            </td>
            <td class="text-center" onclick="event.stopPropagation()">
                <a href="<?php echo RUTA_URL ?>/Mensajes/ver/<?php echo $m->id ?>"
                   class="btn btn-custom btn-sm" title="Ver mensaje">
                    <i class="fas fa-eye"></i>
                </a>
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
