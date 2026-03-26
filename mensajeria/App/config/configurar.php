<?php
// ── Parámetros propios de este MVC ───────────────────────────
define('RUTA_APP',    dirname(dirname(__FILE__)));
define('NOMBRE_SITIO', 'Mensajería Interna — CPIFP Bajo Aragón');
define('RUTA_URL',    'http://192.168.1.197/mensajeria');

// Carpeta de adjuntos (ruta absoluta en servidor)
define('RUTA_ADJUNTOS', RUTA_APP . '/../adjuntos/');

// ── Configuración global compartida ──────────────────────────
require_once '/var/www/html/shared/config/config_global.php';
