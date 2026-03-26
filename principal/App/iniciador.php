<?php
//Cargamos librerias
require_once 'config/configurar.php';
require_once 'helpers/funciones.php';


 //require_once 'librerias/Base.php';
 //require_once 'librerias/Controlador.php';
 //require_once 'librerias/Core.php';

 //require_once 'librerias/Sesion.php';

// libreria PHPMailer
require_once 'librerias/PHPMailer/src/Exception.php';
require_once 'librerias/PHPMailer/src/PHPMailer.php';
require_once 'librerias/PHPMailer/src/SMTP.php';

// Autoload php
spl_autoload_register(function ($nombreClase) {
    require_once 'librerias/' . $nombreClase . '.php';
});
