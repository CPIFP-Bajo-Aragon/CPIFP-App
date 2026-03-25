

<?php require_once RUTA_APP . '/vistas/inc/header.php'?>


<div class="content">
<div class="container" style="margin-top:50px">
<div class="row justify-content-center">


        <?php 
        $mostrarSeguimiento = false;
        foreach ($datos['usuarioSesion']->roles as $rol) {
            if (in_array($rol->id_rol, [10, 20, 30, 40, 50])) {
                $mostrarSeguimiento = true;
                break;
            }
        }?>


        <!-- Columna 1 (Seguimiento) -->
        <?php if ($mostrarSeguimiento): ?>
        <div class="columnas col-12 col-md-3 col-sm-4 pt-5">
        <ul class="enlaces">
            <!-- TITULO -->
            <li id="header_columna">SEGUIMIENTO DE LA PROGRAMACION</li>
            <!-- AYUDA -->
            <li id="enlaces_menu_estilo">
                <a href="https://difficult-cow-020.notion.site/Seguimiento-Centros-Educativos-196b2e2f06da801a8d0ec0e90262f8a3" target="_blank">
                    <i class="fas fa-question-circle"></i> Ayuda
                </a>
            </li>
            <!-- MI PERFIL -->
            <li id="enlaces_menu_estilo"><a href="<?php echo RUTA_URL?>/seguimiento/usuario"><i class="fas fa-user"></i> Mi perfil</a></li>
            <!-- MENU SEGURN ROL -->
            <?php foreach ($datos['usuarioSesion']->roles as $rol): ?>
                <?php if ($rol->id_rol == 50): ?>
                    <li id="enlaces_menu_estilo"><a href="<?php echo RUTA_URL?>/seguimiento/curso"><i class="fas fa-cog"></i> Configuración</a></li>
                    <li id="enlaces_menu_estilo"><a href="<?php echo RUTA_URL?>/seguimiento/DInformes"><i class="fas fa-chart-line"></i> Informes Generales</a></li>
                <?php endif; ?>
                <?php if ($rol->id_rol == 30): ?>
                    <li id="enlaces_menu_estilo"><a href="<?php echo RUTA_URL?>/seguimiento/JDReparto"><i class="fas fa-building"></i> Mi departamento</a></li>
                <?php endif; ?>
            <?php endforeach; ?>
            <!-- SEGUIMIENTO -->
            <li id="enlaces_menu_estilo"><a href="<?php echo RUTA_URL?>/seguimiento/PSeguimiento"><i class="fas fa-book"></i> Seguimiento por módulos</a></li>
        </ul>
        </div>
        <?php endif; ?>



        <!-- Columna 2 (Gestión del Centro) -->
        <?php foreach ($datos['usuarioSesion']->roles as $rol):
            if($rol->id_rol==50): ?>
                <div class="columnas col-12 col-md-3 col-sm-4 pt-5">
                    <ul class="enlaces">
                        <li id="header_columna">GESTION DEL CENTRO</li>
                        <li id="enlaces_menu_estilo"><a href="<?php echo RUTA_URL?>/centro/departamento"><i class="fas fa-building"></i> Departamentos</a></li>
                        <li id="enlaces_menu_estilo"><a href="<?php echo RUTA_URL?>/centro/ciclo"><i class="fas fa-cubes"></i> Ciclos y modulos</a></li>
                        <li id="enlaces_menu_estilo"><a href="<?php echo RUTA_URL?>/centro/personal"><i class="fas fa-user-tie"></i> Personal del centro</a></li>
                    </ul>
                </div>
        <?php endif; 
        endforeach ;?>


</div>
</div>
</div>



<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>




