
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo NOMBRE_SITIO ?></title>

    <!-- Bootstrap CSS  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/css/estilos_seguimiento.css">
    <link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/css/estilos_tablas_modales.css">

    <!-- Fuentes de Google -->
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@100;200;300&display=swap" rel="stylesheet">

</head>





<body>



            <header style="display: flex; align-items: center; justify-content: space-between; padding: 10px 20px; background-color: #fff;">
            
                <!-- Logo a la izquierda -->
                <div style="flex: 0 0 auto;">
                    <img src="<?php echo RUTA_LOGOS?>logo_cpifp.png" style="width: 280px;">
                </div>

                <!-- Nombre usuario centrado -->
                <div style="flex: 1 1 auto; text-align: center; font-weight: bold; font-size: 1.5em; color:#0583c3;">
                    <?php echo $datos['usuarioSesion']->nombre_completo; ?>
                </div>

                <!-- Botón logout a la derecha -->
                <a type="button" id="boton_logout" class="btn btn-logout" href="<?php echo RUTA_LOGOUT ?>" style="flex: 0 0 auto; display: flex; align-items: center; gap: 5px;">
                    <span>Logout</span>
                    <img src="<?php echo RUTA_Icon ?>logout.png" style="height: 20px;">
                </a>

            </header>



    
<?php $current_url = $_SERVER['REQUEST_URI'];?>
<nav class="navbar navbar-expand-lg">

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav1" aria-controls="navbarNav1" 
        aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>


        <div class="collapse navbar-collapse" id="navbarNav1">
        <ul class="navbar-nav">

                <!-- INICIO -->
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($current_url, 'inicio') !== false ? 'active' : ''); ?>" id="menu_principal" href="<?php echo RUTA_CPIFP?>/inicio">INICIO</a>
                </li>

                <!-- CONFIGURACION -->
                <li class="nav-item">
                    <?php
                        $configuracion_link = "";
                        foreach ($datos['usuarioSesion']->roles as $rol) {
                            if ($rol->id_rol == 50) {
                                $configuracion_link = RUTA_URL . "/curso";
                                break; 
                            } 
                        }
                    ?>
                    <?php if ($configuracion_link != ""): ?>
                       <a class="nav-link <?php echo (
                            strpos($current_url, 'curso') !== false 
                            || strpos($current_url, 'festivos') !== false 
                            || strpos($current_url, 'evaluaciones') !== false 
                            || strpos($current_url, 'indicadores') !== false 
                            || (
                                strpos($current_url, 'programaciones') !== false 
                                && strpos($current_url, 'JDProgramaciones/programaciones_modulos') === false 
                                && strpos($current_url, 'JDProgramaciones/programaciones_fol_leo') === false
                            )
                            || strpos($current_url, 'acciones') !== false 
                        ) ? 'active' : ''; ?>"
                        id="menu_principal" href="<?php echo $configuracion_link; ?>">CONFIGURACION</a>
                    <?php endif; ?>
                </li>



                <!-- INFORMES -->
                <?php 
                    $mostrarInformes = false;
                    foreach ($datos['usuarioSesion']->roles as $rol): 
                        if($rol->id_rol == 50):
                            $mostrarInformes = true; 
                        endif;
                    endforeach;

                    if($mostrarInformes): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($current_url, 'DInformes') !== false ? 'active' : '');?>" 
                            id="menu_principal" href="<?php echo RUTA_URL?>/DInformes">INFORMES GENERALES</a>
                        </li>
                <?php endif; ?>



                <!-- MI DEPARTAMENTO -->
                <?php if (in_array(30, array_column($datos['usuarioSesion']->roles, 'id_rol'))): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($current_url, 'JDReparto') !== false
                        || strpos($current_url, 'JDActas') !== false 
                        || strpos($current_url, 'JDActas/resumen_memoria') !== false 
                        || strpos($current_url, 'JDProgramaciones/programaciones_modulos') !== false 
                        || strpos($current_url, 'JDProgramaciones/programaciones_fol_leo') !== false 
                         ? 'active' : ''); ?>" id="menu_principal" href="<?php echo RUTA_URL?>/JDReparto">MI DEPARTAMENTO</a>
                    </li>
                <?php endif; ?>


                <!-- SEGUIMIENTO POR MODULOS -->
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($current_url, 'PSeguimiento') !== false 
                    || strpos($current_url, 'PDatos') !== false 
                    || strpos($current_url, 'PDiario') !== false 
                    || strpos($current_url, 'PHorasImpartidas') !== false 
                    || strpos($current_url, 'PEnsenanza') !== false 
                    || strpos($current_url, 'PCumplimiento') !== false 
                    || strpos($current_url, 'PAnalisis') !== false 
                    ? 'active' : '');?>" id="menu_principal" href="<?php echo RUTA_URL?>/PSeguimiento">SEGUIMIENTO POR MODULOS</a>
                </li>

                <!-- MI PERFIL -->
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($current_url, 'usuario') !== false ? 'active' : '');?>" id="menu_principal" href="<?php echo RUTA_URL?>/usuario">MI PERFIL</a>
                </li>

        </ul>
        </div>

</nav>




</body>
</html>

