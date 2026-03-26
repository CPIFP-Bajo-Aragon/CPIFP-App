<?php
// ── Parámetros propios de este MVC ───────────────────────────
define('RUTA_APP',    dirname(dirname(__FILE__)));
define('NOMBRE_SITIO', 'Inicio — CPIFP Bajo Aragón');

// El MVC principal usa su propia BD pública (distinta de interno_calidapp)
define('DB_HOST',     'localhost');
define('DB_USUARIO',  'root');
define('DB_PASSWORD', 'root');
define('DB_NOMBRE',   'calidapp');

// RUTA_URL del principal es la raíz
define('RUTA_URL', 'http://192.168.1.197');

// ── Configuración global compartida ──────────────────────────
// (sobreescribe DB_* con los del principal — se carga DESPUÉS
//  de las defines propias para que RUTA_CPIFP y recursos queden
//  disponibles sin redefinir la BD)
require_once '/var/www/html/shared/config/config_global.php';
