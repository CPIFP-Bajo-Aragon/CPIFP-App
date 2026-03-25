<?php

//Cargamos librerias
require_once 'config/configurar.php';
require_once 'helpers/funciones.php';


// FUNCIONES CALCULOS INDICADORES
require_once 'helpers/indicador_aa.php';
require_once 'helpers/indicador_hi.php';
require_once 'helpers/indicador_ap.php';
require_once 'helpers/indicador_at.php';
require_once 'helpers/indicador_ap2.php';
require_once 'helpers/indicador_ep2.php';
require_once 'helpers/indicador_ep1.php';


require_once 'librerias/Base.php';
require_once 'librerias/Controlador.php';
require_once 'librerias/Core.php';

require_once 'librerias/Sesion.php';

require_once 'librerias/Indicador.php';


// Autoload php
/*spl_autoload_register(function ($nombreClase) {
    require_once 'librerias/' . $nombreClase . '.php';
});*/
