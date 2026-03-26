<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo NOMBRE_SITIO ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- CSS del seguimiento (compartido) -->
    <link rel="stylesheet" href="<?php echo RUTA_SEGUIMIENTO_CSS ?>">

    <!-- Fuentes Google -->
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@100;200;300&display=swap" rel="stylesheet">

    <!-- JS propio (paginación, etc.) -->
    <script src="<?php echo RUTA_URL ?>/js/main.js"></script>
</head>
<body>

<header style="display:flex; align-items:center; justify-content:space-between; padding:10px 20px; background-color:#fff;">

    <!-- Logo -->
    <div style="flex:0 0 auto;">
        <img src="<?php echo RUTA_LOGOS ?>logo.png" style="width:280px;">
    </div>

    <!-- Nombre usuario centrado -->
    <div style="flex:1 1 auto; text-align:center; font-weight:bold; font-size:1.5em; color:#0583c3;">
        <?php echo htmlspecialchars($datos['usuarioSesion']->nombre_completo ?? $datos['usuarioSesion']->login ?? '') ?>
    </div>

    <!-- Logout -->
    <a id="boton_logout" class="btn btn-logout" href="<?php echo RUTA_LOGOUT ?>"
       style="flex:0 0 auto; display:flex; align-items:center; gap:5px;">
        <span>Logout</span>
        <img src="<?php echo RUTA_Icon ?>logout.png" style="height:20px;">
    </a>

</header>

<?php $current_url = $_SERVER['REQUEST_URI']; ?>
<nav class="navbar navbar-expand-lg">
    <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse" data-bs-target="#navOrient">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navOrient">
    <ul class="navbar-nav">

        <!-- INICIO CPIFP -->
        <li class="nav-item">
            <a class="nav-link <?php echo strpos($current_url, 'inicio') !== false ? 'active' : '' ?>"
               href="<?php echo RUTA_CPIFP ?>/inicio">INICIO</a>
        </li>

        <!-- INCIDENCIAS ACTIVAS (home) -->
        <li class="nav-item">
            <a class="nav-link <?php echo
                (strpos($current_url, RUTA_URL) !== false
                && strpos($current_url, 'filtro') === false
                && strpos($current_url, 'add_asesoria') === false
                && strpos($current_url, 'ver_asesoria') === false)
                ? 'active' : '' ?>"
               href="<?php echo RUTA_URL ?>">ACTIVAS</a>
        </li>

        <!-- TODAS LAS INCIDENCIAS (filtro) -->
        <li class="nav-item">
            <a class="nav-link <?php echo strpos($current_url, 'filtro') !== false ? 'active' : '' ?>"
               href="<?php echo RUTA_URL ?>/asesorias/filtro">TODAS</a>
        </li>

        <!-- NUEVA INCIDENCIA -->
        <li class="nav-item">
            <a class="nav-link <?php echo strpos($current_url, 'add_asesoria') !== false ? 'active' : '' ?>"
               href="<?php echo RUTA_URL ?>/asesorias/add_asesoria">NUEVA ASESORÍA</a>
        </li>

    </ul>
    </div>
</nav>
