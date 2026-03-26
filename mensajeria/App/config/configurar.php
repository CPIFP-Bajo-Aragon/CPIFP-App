<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('RUTA_APP',    dirname(dirname(__FILE__)));
define('RUTA_URL',    '/mensajeria');
define('NOMBRE_SITIO','Mensajeria Interna CPIFP Bajo Aragon');

define('DB_NOMBRE', 'interno_calidapp');
define('DB_HOST', 'localhost');
define('DB_USUARIO', 'interno_ubdcalidapp');
define('DB_PASSWORD', 'k41c-?55K5UcyJ8^');

define('TMP_SESION',        2 * 60 * 60);
define('NUM_ITEMS_BY_PAGE', 20);

define('EmailEmisor', 'informatica@cpifpbajoaragon.com');
define('EmailPass',   'Naranjito.82');
define('Emisor',      'CPIFP Bajo Aragon');
define('Host',        'smtp.ionos.es');
define('SMTPSecure',  'TLS');
define('Puerto',       587);

if (!defined('RUTA_CPIFP'))  define('RUTA_CPIFP',  'http://192.168.1.197');
if (!defined('RUTA_LOGOUT')) define('RUTA_LOGOUT',  RUTA_CPIFP . '/login/logout');
if (!defined('RUTA_Icon'))   define('RUTA_Icon',    RUTA_CPIFP . '/public/img/icons/');
if (!defined('RUTA_LOGOS'))  define('RUTA_LOGOS',   RUTA_CPIFP . '/public/img/logos/');

// CSS compartido del MVC seguimiento
define('RUTA_SEGUIMIENTO_CSS', RUTA_CPIFP . '/seguimiento/public/css/estilos_seguimiento.css');

// Carpeta de adjuntos (ruta absoluta en servidor)
define('RUTA_ADJUNTOS', RUTA_APP . '/../public/uploads/adjuntos/');
