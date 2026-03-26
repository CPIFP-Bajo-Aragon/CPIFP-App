<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo NOMBRE_SITIO ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Estilos propios -->
    <link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/css/estilos.css">

    <!-- Fuentes de Google -->
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@100;200;300&display=swap" rel="stylesheet">
</head>

<body>

    <!-- ===== CABECERA ===== -->
    <header>
        <!-- Logo -->
        <div style="flex: 0 0 auto;">
            <img src="<?php echo RUTA_LOGOS ?>logo.png" style="width: 280px;">
        </div>

        <!-- Nombre usuario -->
        <div style="flex: 1 1 auto; text-align: center; font-weight: bold; font-size: 1.5em; color: #0583c3;">
            <?php echo $datos['usuarioSesion']->nombre_completo ?>
        </div>

        <!-- Logout -->
        <a type="button" id="boton_logout" class="btn" href="<?php echo RUTA_LOGOUT ?>">
            <span>Logout</span>
            <img src="<?php echo RUTA_Icon ?>logout.png" style="height: 20px; margin-left: 8px;">
        </a>
    </header>


    <!-- ===== MENÚ PRINCIPAL ===== -->
    <?php $current_url = $_SERVER['REQUEST_URI']; ?>
    <nav class="navbar navbar-expand-lg">

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navFacturas"
                aria-controls="navFacturas" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navFacturas">
        <ul class="navbar-nav">

            <!-- INICIO -->
            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($current_url, 'Inicio') !== false || $current_url === '/facturas/' || $current_url === '/facturas') ? 'active' : '' ?>"
                   href="<?php echo RUTA_URL ?>/">INICIO</a>
            </li>

            <!-- GESTIÓN DE FACTURAS -->
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($current_url, 'GestionFacturas') !== false ? 'active' : '' ?>"
                   href="<?php echo RUTA_URL ?>/GestionFacturas">GESTIÓN DE FACTURAS</a>
            </li>

            <!-- PROVEEDORES -->
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($current_url, 'Proveedores') !== false ? 'active' : '' ?>"
                   href="<?php echo RUTA_URL ?>/Proveedores">PROVEEDORES</a>
            </li>
        
            <!-- INVENTARIO -->
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($current_url, 'Inventario') !== false ? 'active' : '' ?>"
                   href="<?php echo RUTA_URL ?>/Inventario">INVENTARIO</a>
            </li>

        </ul>

        <!-- Destino seleccionado (centrado) -->
        <?php if (isset($datos['persistencia']['nombreDestinoSeleccionado'])): ?>
        <span class="navbar-destino mx-auto">
            <i class="fas fa-building me-2"></i>
            <?php echo $datos['persistencia']['nombreDestinoSeleccionado'] ?>
        </span>
        <?php endif ?>

        </div>
    </nav>
