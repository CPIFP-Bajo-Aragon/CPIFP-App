<?php
//** Desarrollo */
    ini_set('display_errors',1);
    ini_set('display_startup_errors',1);
    error_reporting(E_ALL);
//** Desarrollo */

define('RUTA_APP', dirname(dirname(__FILE__)));
define('RUTA_URL', '/orientacion');
define('NOMBRE_SITIO', 'Web de Asesoria de Orientación');

define('DB_NOMBRE', 'interno_calidapp');
define('DB_HOST', 'localhost');
define('DB_USUARIO', 'interno_ubdcalidapp');
define('DB_PASSWORD', 'k41c-?55K5UcyJ8^');

define('EmailEmisor', 'noreply@cpifpbajoaragon.com');
define('EmailPass',   'kvAPuHCKX9NSDZts$$py');
define('Emisor',      'CPIFP Bajo Aragón');
define('Host',        'smtp.ionos.es');
define('SMTPSecure',  'TLS');
define('Puerto',      587);

define('TAM_PAGINA', 20);

if (!defined('RUTA_CPIFP'))  define('RUTA_CPIFP',  'http://192.168.1.197');
if (!defined('RUTA_LOGOUT')) define('RUTA_LOGOUT',  RUTA_CPIFP . '/login/logout');
if (!defined('RUTA_Icon'))   define('RUTA_Icon',    RUTA_CPIFP . '/public/img/icons/');
if (!defined('RUTA_LOGOS'))  define('RUTA_LOGOS',   RUTA_CPIFP . '/public/img/logos/');
define('RUTA_SEGUIMIENTO_CSS', RUTA_CPIFP . '/seguimiento/public/css/estilos_seguimiento.css');
