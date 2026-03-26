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

    <!-- Chart.js para gráficos de resultados -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- CSS compartido del seguimiento -->
    <link rel="stylesheet" href="<?php echo RUTA_SEGUIMIENTO_CSS ?>">

    <!-- CSS propio de encuestas -->
    <link rel="stylesheet" href="<?php echo RUTA_URL ?>/css/encuestas.css">

    <!-- Fuentes Google -->
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@100;200;300&display=swap" rel="stylesheet">

    <!-- JS propio -->
    <script src="<?php echo RUTA_URL ?>/js/encuestas.js" defer></script>
</head>
<body>

<header style="display:flex; align-items:center; justify-content:space-between; padding:10px 20px; background-color:#fff;">
    <div style="flex:0 0 auto;">
        <img src="<?php echo RUTA_LOGOS ?>logo.png" style="width:280px;">
    </div>
    <div style="flex:1 1 auto; text-align:center; font-weight:bold; font-size:1.5em; color:#0583c3;">
        <?php echo htmlspecialchars($datos['usuarioSesion']->nombre_completo ?? $datos['usuarioSesion']->login ?? '') ?>
    </div>
    <a id="boton_logout" class="btn btn-logout" href="<?php echo RUTA_LOGOUT ?>"
       style="flex:0 0 auto; display:flex; align-items:center; gap:5px;">
        <span>Logout</span>
        <img src="<?php echo RUTA_Icon ?>logout.png" style="height:20px;">
    </a>
</header>

<?php $current_url = $_SERVER['REQUEST_URI']; ?>
<nav class="navbar navbar-expand-lg">
    <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse" data-bs-target="#navEncuestas">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navEncuestas">
    <ul class="navbar-nav">

        <li class="nav-item">
            <a class="nav-link <?php echo strpos($current_url, '/inicio') !== false ? 'active' : '' ?>"
               href="<?php echo RUTA_CPIFP ?>/inicio">INICIO</a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo ($datos['menuActivo'] == 'encuestas') ? 'active' : '' ?>"
               href="<?php echo RUTA_URL ?>/encuestas">ENC. ALUMNOS</a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo ($datos['menuActivo'] == 'otras_encuestas') ? 'active' : '' ?>"
               href="<?php echo RUTA_URL ?>/otras_encuestas">OTRAS ENCUESTAS</a>
        </li>

        <?php if($datos['usuarioSesion']->id_rol >= 200): ?>
        <li class="nav-item">
            <a class="nav-link <?php echo ($datos['menuActivo'] == 'gestor') ? 'active' : '' ?>"
               href="<?php echo RUTA_URL ?>/gestor_encuestas">GESTIÓN</a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo ($datos['menuActivo'] == 'estadisticas') ? 'active' : '' ?>"
               href="<?php echo RUTA_URL ?>/encuestas/estadisticas">ESTADÍSTICAS</a>
        </li>
        <?php endif; ?>



    </ul>
    </div>
</nav>
