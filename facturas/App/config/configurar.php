<?php

// Desarrollo - comentar en producción
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ruta de la aplicación
define('RUTA_APP', dirname(dirname(__FILE__)));
define('NOMBRE_SITIO', 'Gestión de Facturas - CPIFP Bajo Aragón');

// URLs
define('RUTA_CPIFP', 'http://192.168.1.197');
define('RUTA_URL',   RUTA_CPIFP . '/facturas');
define('RUTA_LOGOUT', RUTA_CPIFP . '/login/logout');

// Recursos públicos
define('RUTA_Icon',  RUTA_CPIFP . '/public/img/icons/');
define('RUTA_LOGOS', RUTA_CPIFP . '/public/img/logos/');

// Base de datos
define('DB_NOMBRE', 'interno_calidapp');
define('DB_HOST', 'localhost');
define('DB_USUARIO', 'interno_ubdcalidapp');
define('DB_PASSWORD', 'k41c-?55K5UcyJ8^');

// Sesión
define('TMP_SESION', 2 * 60 * 60);   // 2 horas (igual que el resto de MVCs)

// Correo
define('EmailEmisor', 'noreply-calidapp@cpifpbajoaragon.com');
define('EmailPass',   '5Vti9D0U78Bio7pXfy4P');
define('Emisor',      'CPIFP Bajo Aragón');
define('Host',        'smtp.ionos.es');
define('SMTPSecure',  'TLS');
define('Puerto',      587);

// Paginación
define('NUM_ITEMS_BY_PAGE', 20);
