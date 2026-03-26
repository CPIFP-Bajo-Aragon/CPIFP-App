<?php
require_once 'config/configurar.php';
require_once 'helpers/funciones.php';
require_once RUTA_APP . '/librerias/externas/PHPMailer.php';
require_once RUTA_APP . '/librerias/externas/SMTP.php';

spl_autoload_register(function($clase) {
    $rutas = [
        RUTA_APP . '/librerias/' . $clase . '.php',
        RUTA_APP . '/modelos/'   . $clase . '.php',
        RUTA_APP . '/controladores/' . $clase . '.php',
    ];
    foreach ($rutas as $ruta) {
        if (file_exists($ruta)) { require_once $ruta; return; }
    }
});
