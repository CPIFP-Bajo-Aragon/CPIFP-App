<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container-fluid px-4 py-4">

    <div class="row mb-3 align-items-center">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <span>
                    <i class="fas fa-envelope-open me-2"></i>
                    <?php echo htmlspecialchars($datos['mensaje']->asunto ?: '(sin asunto)') ?>
                </span>
            </span>
        </div>
        <div class="col-auto">
            <a href="<?php echo RUTA_URL ?>/Mensajes/<?php echo $datos['esRemitente'] ? 'enviados' : 'bandeja' ?>"
               class="btn-volver">
                <i class="fas fa-arrow-left"></i>Volver
            </a>
        </div>
    </div>

    <!-- Metadatos del mensaje -->
    <div class="table-responsive mb-4">
    <table class="table tabla-formato">
        <tbody>
            <tr>
                <th style="width:10%">De</th>
                <td><?php echo htmlspecialchars($datos['mensaje']->remitente) ?></td>
                <th style="width:12%">Fecha</th>
                <td><?php echo date('d/m/Y H:i', strtotime($datos['mensaje']->fecha_envio)) ?></td>
            </tr>
            <tr>
                <th>Para</th>
                <td colspan="3">
                    <?php foreach ($datos['destinatarios'] as $d): ?>
                    <span class="badge me-1"
                          style="background:<?php echo $d->leido ? '#27ae60' : '#6c757d' ?>"
                          title="<?php echo $d->leido
                                        ? 'Leido ' . date('d/m/Y H:i', strtotime($d->fecha_lectura))
                                        : 'No leido' ?>">
                        <?php echo htmlspecialchars($d->nombre_completo) ?>
                    </span>
                    <?php endforeach ?>
                </td>
            </tr>
        </tbody>
    </table>
    </div>

    <!-- Cuerpo del mensaje -->
    <div class="mb-4 p-3 border rounded" style="background:#fff; white-space:pre-wrap; font-size:.97rem; line-height:1.7">
        <?php echo nl2br(htmlspecialchars($datos['mensaje']->cuerpo)) ?>
    </div>

    <!-- Adjuntos -->
    <?php if (!empty($datos['adjuntos'])): ?>
    <div class="mb-4">
        <strong><i class="fas fa-paperclip me-2"></i>Adjuntos:</strong>
        <div class="d-flex flex-wrap gap-2 mt-2">
            <?php foreach ($datos['adjuntos'] as $adj): ?>
            <a href="<?php echo RUTA_URL ?>/Mensajes/descargar/<?php echo $adj->id ?>"
               class="btn btn-custom btn-sm">
                <i class="fas fa-download me-1"></i>
                <?php echo htmlspecialchars($adj->nombre_orig) ?>
                <small class="ms-1 text-muted">(<?php echo formatearTamanio($adj->tamanio) ?>)</small>
            </a>
            <?php endforeach ?>
        </div>
    </div>
    <?php endif ?>

    <!-- Boton responder -->
    <?php if (!$datos['esRemitente']): ?>
    <div class="mt-2">
        <a href="<?php echo RUTA_URL ?>/Mensajes/nuevo?responder_a=<?php echo $datos['mensaje']->id ?>&asunto=<?php echo urlencode('RE: ' . $datos['mensaje']->asunto) ?>&dest=<?php echo $datos['mensaje']->id_remitente ?>"
           class="btn btn-custom btn-sm">
            <i class="fas fa-reply me-1"></i>Responder
        </a>
    </div>
    <?php endif ?>

</div>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
