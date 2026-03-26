#!/usr/bin/env php
<?php
/**
 * CRON: limpiar adjuntos caducados
 * Uso:  php /var/www/html/mensajeria/cron/limpiar_adjuntos.php
 * Crontab (cada noche a las 02:00):
 *   0 2 * * * /usr/bin/php /var/www/html/mensajeria/cron/limpiar_adjuntos.php >> /var/log/mensajeria_cron.log 2>&1
 */

define('EJECUTADO_DESDE_CRON', true);
chdir(dirname(__FILE__) . '/../App');
require_once 'config/configurar.php';
require_once 'helpers/funciones.php';

// Autoload
spl_autoload_register(function($clase) {
    $rutas = ['librerias/' . $clase . '.php', 'modelos/' . $clase . '.php'];
    foreach ($rutas as $ruta) {
        if (file_exists($ruta)) { require_once $ruta; return; }
    }
});

$modelo = new MensajeriaModelo();
$cfg    = $modelo->getConfig();
$dias   = isset($cfg['dias_borrado_adjuntos']) ? (int)$cfg['dias_borrado_adjuntos']->valor : 30;

$caducados = $modelo->getAdjuntosCaducados($dias);
$ok = 0; $err = 0;

foreach ($caducados as $adj) {
    $ruta = RUTA_ADJUNTOS . $adj->nombre_disco;
    if (file_exists($ruta)) {
        if (unlink($ruta)) {
            $modelo->borrarAdjuntoBD($adj->id);
            $ok++;
        } else {
            $err++;
            echo "[ERROR] No se pudo borrar: $ruta\n";
        }
    } else {
        // El fichero ya no existe en disco pero si en BD -> borrar registro
        $modelo->borrarAdjuntoBD($adj->id);
        $ok++;
    }
}

echo "[" . date('Y-m-d H:i:s') . "] Adjuntos borrados: $ok | Errores: $err | Dias configurados: $dias\n";
