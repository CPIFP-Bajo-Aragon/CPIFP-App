<?php
// ── Parámetros propios de este MVC ───────────────────────────
define('RUTA_APP',    dirname(dirname(__FILE__)));
define('NOMBRE_SITIO', 'Seguimiento — CPIFP Bajo Aragón');
define('RUTA_URL',    'http://192.168.1.197/seguimiento');

// Rutas internas de navegación propias del seguimiento
define('RUTA_SEGUIMIENTO', '/seguimiento/profeSegui');
define('RUTA_REPARTO',     '/seguimiento/jefeDep');
define('RUTA_CURSO',       '/seguimiento/direccion');
define('RUTA_PDF',         'http://192.168.1.197/seguimiento/public/tcpdf/');

// ── Configuración global compartida ──────────────────────────
require_once '/var/www/html/shared/config/config_global.php';
