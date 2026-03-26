<?php
/**
 * Controlador de tareas programadas (cron).
 * URL: /mensajeria/Cron/limpiarAdjuntos
 *
 * Crontab (cada noche a las 3:00):
 *   0 3 * * * curl -s http://192.168.1.197/mensajeria/Cron/limpiarAdjuntos >> /var/log/mensajeria_cron.log 2>&1
 */
class Cron extends Controlador {

    private $modelo;

    public function __construct() {
        $this->modelo = $this->modelo('MensajeriaModelo');
    }

    public function limpiarAdjuntos() {
        $dias = (int)($this->modelo->getConfig('dias_borrar_adjuntos') ?? 30);
        if ($dias < 1) $dias = 30;

        $adjuntosViejos = $this->modelo->getAdjuntosAntiguos($dias);
        $borrados = $errores = $no_existe = 0;

        foreach ($adjuntosViejos as $adj) {
            $ruta = DIR_ADJUNTOS . $adj->nombre_disco;
            if (file_exists($ruta)) {
                if (unlink($ruta)) { $borrados++; }
                else { $errores++; echo '[ERROR] No se pudo borrar: ' . $ruta . PHP_EOL; continue; }
            } else { $no_existe++; }
            $this->modelo->borrarAdjunto($adj->id_adjunto);
        }

        $fecha = date('Y-m-d H:i:s');
        echo "[{$fecha}] Limpieza completada." . PHP_EOL;
        echo "  Dias de retencion : {$dias}" . PHP_EOL;
        echo "  Procesados        : " . count($adjuntosViejos) . PHP_EOL;
        echo "  Borrados          : {$borrados}" . PHP_EOL;
        echo "  No existian       : {$no_existe}" . PHP_EOL;
        echo "  Errores           : {$errores}" . PHP_EOL;
        exit();
    }

    public function estado() {
        $dias = $this->modelo->getConfig('dias_borrar_adjuntos') ?? 30;
        echo "Dias de retencion configurados: {$dias}" . PHP_EOL;
        exit();
    }
}
