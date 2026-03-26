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

    <!-- Estilos del seguimiento (compartidos) -->
    <link rel="stylesheet" href="<?php echo RUTA_SEGUIMIENTO_CSS ?>">

    <!-- Fuentes Google -->
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@100;200;300&display=swap" rel="stylesheet">
</head>
<body>

<header style="display:flex; align-items:center; justify-content:space-between; padding:10px 20px; background-color:#fff;">

    <!-- Logo -->
    <div style="flex:0 0 auto;">
        <img src="<?php echo RUTA_LOGOS ?>logo.png" style="width:280px;">
    </div>

    <!-- Nombre usuario centrado -->
    <div style="flex:1 1 auto; text-align:center; font-weight:bold; font-size:1.5em; color:#0583c3;">
        <?php echo htmlspecialchars($datos['usuarioSesion']->nombre_completo) ?>
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
            data-bs-toggle="collapse" data-bs-target="#navMsg">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMsg">
    <ul class="navbar-nav">

        <!-- INICIO (volver al principal) -->
        <li class="nav-item">
            <a class="nav-link <?php echo strpos($current_url,'inicio') !== false ? 'active' : '' ?>"
               href="<?php echo RUTA_CPIFP ?>/inicio">INICIO</a>
        </li>

        <!-- BANDEJA -->
        <li class="nav-item">
            <a class="nav-link <?php echo strpos($current_url,'/Mensajes/bandeja') !== false
                                        || strpos($current_url,'/Mensajes/ver') !== false
                                        || (strpos($current_url,'/mensajeria') !== false
                                            && strpos($current_url,'enviados') === false
                                            && strpos($current_url,'nuevo') === false
                                            && strpos($current_url,'config') === false) ? 'active' : '' ?>"
               href="<?php echo RUTA_URL ?>/Mensajes/bandeja">
                BANDEJA
                <?php if (!empty($datos['noLeidos'])): ?>
                <span class="badge"><?php echo $datos['noLeidos'] ?></span>
                <?php endif ?>
            </a>
        </li>

        <!-- ENVIADOS -->
        <li class="nav-item">
            <a class="nav-link <?php echo strpos($current_url,'enviados') !== false ? 'active' : '' ?>"
               href="<?php echo RUTA_URL ?>/Mensajes/enviados">ENVIADOS</a>
        </li>

        <!-- NUEVO MENSAJE -->
        <li class="nav-item">
            <a class="nav-link <?php echo strpos($current_url,'nuevo') !== false ? 'active' : '' ?>"
               href="<?php echo RUTA_URL ?>/Mensajes/nuevo">NUEVO MENSAJE</a>
        </li>

        <!-- CONFIGURACION (solo equipo directivo, rol >= 50) -->
        <?php if (($datos['usuarioSesion']->id_rol ?? 0) >= 50): ?>
        <li class="nav-item">
            <a class="nav-link <?php echo strpos($current_url,'configuracion') !== false ? 'active' : '' ?>"
               href="<?php echo RUTA_URL ?>/Mensajes/configuracion">CONFIGURACION</a>
        </li>
        <?php endif ?>

    </ul>
    </div>
</nav>
