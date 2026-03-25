
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Bootstrap CSS (solo la versión más reciente) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/css/estilos_principal.css">
    <title>Login</title>
    <!-- Fuentes de Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@100;200;300&family=Doppio+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>


<body>


        <header>
            <!-- Logo a la izquierda -->
            <div style="flex: 0 0 auto;">
                <img id="logo" src="<?php echo RUTA_LOGOS?>logo_cpifp.png">
            </div>
            <!-- Nombre usuario centrado -->
            <div id="info_usuario">
                <?php echo $datos['usuarioSesion']->nombre_completo;?>
            </div>
            <!-- Botón logout a la derecha -->
            <a type="button" id="boton_logout" class="btn btn-logout" href="<?php echo RUTA_LOGOUT?>">
                <span>Logout</span>
                <img src="<?php echo RUTA_Icon?>logout.png">
            </a>
        </header>





