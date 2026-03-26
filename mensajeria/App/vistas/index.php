<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container-fluid px-4 py-4">

    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-0" style="color:#0583c3; font-weight:bold;">
                <i class="fas fa-comments me-2"></i>
                Bienvenido, <?php echo htmlspecialchars($datos['usuarioSesion']->nombre_completo) ?>
            </h5>
        </div>
    </div>

    <!-- Tarjetas resumen -->
    <div class="row g-3 mb-4">

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="p-4 text-center border rounded" style="background:#fff; box-shadow:0 2px 6px rgba(0,0,0,.07)">
                <i class="fas fa-envelope fa-2x mb-2" style="color:#0583c3"></i>
                <div style="font-size:2.5rem; font-weight:700; color:#e74c3c; line-height:1">
                    <?php echo $datos['totalNoLeidos'] ?>
                </div>
                <div class="text-muted mt-1">Mensajes sin leer</div>
                <a href="<?php echo RUTA_URL ?>/Mensajes/bandeja" class="btn btn-custom btn-sm mt-3 w-100">
                    <i class="fas fa-inbox me-1"></i>Ver bandeja
                </a>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="p-4 text-center border rounded" style="background:#fff; box-shadow:0 2px 6px rgba(0,0,0,.07)">
                <i class="fas fa-paper-plane fa-2x mb-2" style="color:#0583c3"></i>
                <div style="font-size:2.5rem; font-weight:700; color:#0583c3; line-height:1">
                    <?php echo $datos['totalEnviados'] ?>
                </div>
                <div class="text-muted mt-1">Mensajes enviados</div>
                <a href="<?php echo RUTA_URL ?>/Mensajes/enviados" class="btn btn-custom btn-sm mt-3 w-100">
                    <i class="fas fa-paper-plane me-1"></i>Ver enviados
                </a>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="p-4 text-center border rounded" style="background:#fff; box-shadow:0 2px 6px rgba(0,0,0,.07)">
                <i class="fas fa-pen-square fa-2x mb-2" style="color:#0583c3"></i>
                <div style="font-size:1.4rem; font-weight:700; color:#0583c3; padding:8px 0">Nuevo</div>
                <div class="text-muted">Redactar mensaje</div>
                <a href="<?php echo RUTA_URL ?>/Mensajes/nuevo" class="btn mt-3 w-100"
                   style="background:#27ae60; color:#fff;">
                    <i class="fas fa-pen me-1"></i>Redactar
                </a>
            </div>
        </div>

        <?php if (($datos['usuarioSesion']->id_rol ?? 0) >= 50): ?>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="p-4 text-center border rounded" style="background:#fff; box-shadow:0 2px 6px rgba(0,0,0,.07)">
                <i class="fas fa-cog fa-2x mb-2" style="color:#0583c3"></i>
                <div style="font-size:1.4rem; font-weight:700; color:#0583c3; padding:8px 0">Config</div>
                <div class="text-muted">Configuracion del sistema</div>
                <a href="<?php echo RUTA_URL ?>/Mensajes/configuracion" class="btn btn-custom btn-sm mt-3 w-100">
                    <i class="fas fa-cog me-1"></i>Ajustes
                </a>
            </div>
        </div>
        <?php endif ?>

    </div>

    <!-- Mensajes recientes + accesos rapidos -->
    <div class="row g-3">

        <div class="col-12 col-lg-8">
            <div class="border rounded p-4" style="background:#fff; box-shadow:0 2px 6px rgba(0,0,0,.07)">
                <h6 style="color:#0583c3; font-weight:bold; margin-bottom:16px">
                    <i class="fas fa-clock me-2"></i>Mensajes recientes en bandeja
                </h6>

                <?php if (empty($datos['ultimosNoLeidos'])): ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-check-circle me-2" style="color:#27ae60"></i>No tienes mensajes nuevos
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
                            <th style="width:5%" class="text-center">Adj.</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach (array_slice($datos['ultimosNoLeidos'], 0, 8) as $m): ?>
                    <tr onclick="location.href='<?php echo RUTA_URL ?>/Mensajes/ver/<?php echo $m->id ?>'"
                        style="cursor:pointer; <?php echo !$m->leido ? 'font-weight:600' : '' ?>">
                        <td class="text-center">
                            <?php if (!$m->leido): ?>
                            <i class="fas fa-circle" style="color:#0583c3; font-size:.55rem; vertical-align:middle"></i>
                            <?php endif ?>
                        </td>
                        <td><?php echo htmlspecialchars($m->remitente) ?></td>
                        <td><?php echo htmlspecialchars($m->asunto ?: '(sin asunto)') ?></td>
                        <td class="text-center text-muted" style="font-size:.88rem">
                            <?php echo date('d/m/Y H:i', strtotime($m->fecha_envio)) ?>
                        </td>
                        <td class="text-center">
                            <?php if ($m->tiene_adjunto): ?>
                            <i class="fas fa-paperclip" style="color:#0583c3"></i>
                            <?php endif ?>
                        </td>
                    </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
                </div>
                <?php if (count($datos['ultimosNoLeidos']) >= 8): ?>
                <div class="text-center mt-2">
                    <a href="<?php echo RUTA_URL ?>/Mensajes/bandeja" style="color:#0583c3; font-size:.9rem">
                        Ver todos los mensajes &rarr;
                    </a>
                </div>
                <?php endif ?>
                <?php endif ?>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="border rounded p-4" style="background:#fff; box-shadow:0 2px 6px rgba(0,0,0,.07)">
                <h6 style="color:#0583c3; font-weight:bold; margin-bottom:16px">
                    <i class="fas fa-bolt me-2"></i>Accesos rapidos
                </h6>
                <div class="d-grid gap-2">
                    <a href="<?php echo RUTA_URL ?>/Mensajes/nuevo"
                       class="btn btn-sm text-start" style="background:#27ae60; color:#fff;">
                        <i class="fas fa-pen me-2"></i>Nuevo mensaje
                    </a>
                    <a href="<?php echo RUTA_URL ?>/Mensajes/bandeja" class="btn btn-custom btn-sm text-start">
                        <i class="fas fa-inbox me-2"></i>Bandeja de entrada
                        <?php if ($datos['totalNoLeidos'] > 0): ?>
                        <span class="badge float-end"><?php echo $datos['totalNoLeidos'] ?></span>
                        <?php endif ?>
                    </a>
                    <a href="<?php echo RUTA_URL ?>/Mensajes/enviados" class="btn btn-custom btn-sm text-start">
                        <i class="fas fa-paper-plane me-2"></i>Mensajes enviados
                    </a>
                    <hr class="my-1">
                    <a href="<?php echo RUTA_CPIFP ?>/inicio" class="btn btn-custom btn-sm text-start">
                        <i class="fas fa-home me-2"></i>Volver al inicio
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
