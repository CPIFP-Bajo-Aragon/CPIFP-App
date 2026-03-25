<?php


// Ruta de la aplicacion
define('RUTA_APP', dirname(dirname(__FILE__)));
define('NOMBRE_SITIO', 'seguimiento');



//define('RUTA_CPIFP', "https://cpifpbajoaragon.calidapp.es");
define('RUTA_CPIFP', "http://192.168.1.197");
define('RUTA_URL', RUTA_CPIFP.'/seguimiento'); 
define('RUTA_LOGOUT', RUTA_CPIFP.'/login/logout');


define('RUTA_SEGUIMIENTO', "/seguimiento/profeSegui");
define('RUTA_REPARTO', "/seguimiento/jefeDep");
define('RUTA_CURSO', "/seguimiento/direccion");



// REFERENTE A LA BBDD
define('DB_NOMBRE', 'interno_calidapp');
define('DB_HOST', 'localhost');
define('DB_USUARIO', 'interno_ubdcalidapp');
define('DB_PASSWORD', 'k41c-?55K5UcyJ8^');



define('RUTA_Icon', RUTA_URL . '/public/img/icons/');
define('RUTA_LOGOS', RUTA_URL . '/public/img/logos/');
define('RUTA_PDF', RUTA_URL. '/public/tcpdf/');

