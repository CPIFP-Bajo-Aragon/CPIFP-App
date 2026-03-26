<?php

//Cargamos librerias
require_once 'config/configurar.php';
require_once 'helpers/funciones.php';


// require_once 'librerias/Base.php';
// require_once 'librerias/Controlador.php';
// require_once 'librerias/Core.php';

require_once 'librerias/externas/PHPMailer.php';
require_once 'librerias/externas/SMTP.php';

// Autoload php			IMPORTANTE: El nombre del archivo ha de coincidir con el nobre de la clase
spl_autoload_register(function($nombreClase){
    require_once 'librerias/'.$nombreClase.'.php';
});

// Cargamos librerias externas
// spl_autoload_register(function($nombreClase){
//     require_once 'librerias/externas/'.$nombreClase.'.php';
// });
