<?php

// Cargamos librerías y configuración
require_once 'config/configurar.php';
require_once 'helpers/funciones.php';

// PHPMailer: soporta tanto la estructura /externas/ como /PHPMailer/src/
if (file_exists(RUTA_APP . '/librerias/externas/PHPMailer.php')) {
    // Estructura usada en orientacion, mantenimiento, etc.
    require_once RUTA_APP . '/librerias/externas/PHPMailer.php';
    require_once RUTA_APP . '/librerias/externas/SMTP.php';
} elseif (file_exists(RUTA_APP . '/librerias/PHPMailer/src/PHPMailer.php')) {
    // Estructura usada en principal
    require_once RUTA_APP . '/librerias/PHPMailer/src/Exception.php';
    require_once RUTA_APP . '/librerias/PHPMailer/src/PHPMailer.php';
    require_once RUTA_APP . '/librerias/PHPMailer/src/SMTP.php';
}
// Si no existe ninguna, el módulo funciona igualmente salvo el envío de emails

// Autoload: el nombre del archivo debe coincidir con el nombre de la clase
spl_autoload_register(function($nombreClase){
    require_once RUTA_APP . '/librerias/' . $nombreClase . '.php';
});
