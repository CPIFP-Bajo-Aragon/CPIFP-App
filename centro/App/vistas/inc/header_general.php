
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo NOMBRE_SITIO ?></title>
    <!-- Bootstrap CSS  -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/css/estilos_centro.css">
    <!-- Fuentes de Google -->
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@100;200;300&display=swap" rel="stylesheet">
</head>



<body>
    
        <header>
            <!-- Logo a la izquierda -->
            <div class="logo_header_stylo">
                <img id="logo" src="<?php echo RUTA_LOGOS?>logo_cpifp.png">
            </div>
            <!-- Nombre usuario centrado -->
            <div id="info_usuario">
                <?php echo $datos['usuarioSesion']->nombre_completo; ?>
            </div>
            <!-- Botón logout a la derecha -->
            <a type="button" id="boton_logout" class="btn btn-logout" href="<?php echo RUTA_LOGOUT?>">
                <span>Logout</span>
                <img src="<?php echo RUTA_Icon ?>logout.png">
            </a>
        </header>


    
        <?php $current_url = $_SERVER['REQUEST_URI']; ?>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($current_url, '/inicio') !== false ? 'active' : '');?>" href="<?php echo RUTA_CPIFP?>/inicio">INICIO</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($current_url, '/departamento') !== false ? 'active' : '');?>" href="<?php echo RUTA_URL?>/departamento">DEPARTAMENTOS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($current_url, '/ciclo') !== false || strpos($current_url, '/modulo') !== false? 'active' : '');?>"  href="<?php echo RUTA_URL?>/ciclo">CICLOS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($current_url, '/personal') !== false ? 'active' : '');?>"  href="<?php echo RUTA_URL?>/personal">PERSONAL CENTRO</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($current_url, '/usuario') !== false ? 'active' : '');?>" href="<?php echo RUTA_URL?>/usuario">MI PERFIL</a>
                    </li>
                </ul>
            </div>
        </nav>



