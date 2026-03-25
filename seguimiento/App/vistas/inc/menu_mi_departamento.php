

<?php $currentUrl = $_SERVER['REQUEST_URI'];?>



<div class="navbar navbar-expand-lg navbar-light menu-configuracion">
    <div class="container-fluid">

        <!-- Botón para dispositivos móviles -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav3" 
            aria-controls="navbarNav3" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>


        <!-- Contenido del menú -->
        <div class="collapse navbar-collapse" id="navbarNav3">
            <div class="d-flex justify-content-start w-100">
                <ul class="navbar-nav">

                    <!-- Enlace "Inicio" -->
                    <li class="nav-item">
                        <a class="nav-link enlace-menu-configuracion
                            <?php if ((strpos($_SERVER['REQUEST_URI'], 'JDReparto') !== false && strpos($_SERVER['REQUEST_URI'], 'actas') === false)
                                || (strpos($_SERVER['REQUEST_URI'], 'JDProgramaciones/programaciones_modulos') !== false)
                                || (strpos($_SERVER['REQUEST_URI'], 'JDProgramaciones/programaciones_fol_leo') !== false)) {
                                echo 'active';
                            } ?>" 
                            id="menu_principal" href="<?php echo RUTA_URL ?>/JDReparto">
                            <i class="fas fa-home"></i> Inicio
                        </a>
                    </li>

                    <!-- Enlace "Actas de seguimiento" -->
                    <?php $isActasActive = strpos($currentUrl, 'JDActas') !== false && strpos($currentUrl, 'resumen_memoria') === false; ?>
                    <li class="nav-item">
                        <a class="nav-link enlace-menu-configuracion <?php echo $isActasActive ? 'active' : ''; ?>"
                            href="<?php echo RUTA_URL ?>/JDActas">
                            <i class="fa fa-file-alt"></i> Actas de seguimiento
                        </a>
                    </li>

                    <!-- Enlace "Resumen para memoria" -->
                    <?php $isResumenActive = strpos($currentUrl, 'JDActas/resumen_memoria') !== false; ?>
                    <li class="nav-item">
                        <a class="nav-link enlace-menu-configuracion <?php echo $isResumenActive ? 'active' : ''; ?>"
                            href="<?php echo RUTA_URL ?>/JDActas/resumen_memoria">
                            <i class="fa fa-chart-bar"></i> Resumen para memoria
                        </a>
                    </li>

                    <!-- Enlace "Ayuda" -->
                    <li class="nav-item">
                        <a class="nav-link enlace-menu-configuracion" id="menu_principal" 
                            href="<?php 
                                if (strpos($_SERVER['REQUEST_URI'], 'JDReparto') !== false) {
                                    echo 'https://difficult-cow-020.notion.site/Reparto-de-horas-196b2e2f06da81b39095f517d6965c6d';
                                } elseif (strpos($_SERVER['REQUEST_URI'], 'JDActas') !== false) {
                                    echo 'https://difficult-cow-020.notion.site/Actas-e-Informes-196b2e2f06da811694abc6d2e44e85c1';
                                };
                            ?>" 
                            target="_blank">
                            <i class="fas fa-question-circle"></i> AYUDA
                        </a>
                    </li>
                
                </ul>
            </div>
        </div>   
                      
    </div>
</div>



<!-- Scripts adicionales -->
<script>
    function curso_cerrado_indicadores(event) {
        event.preventDefault(); 
        alert('No hay un curso lectivo abierto actualmente. No se pueden configurar los indicadores hasta que no haya un curso abierto.');
    }

    function curso_cerrado_reparto_horas(event) {
        event.preventDefault(); 
        alert('No hay un curso lectivo abierto actualmente. No se pueden repartir horas hasta que no haya un curso abierto.');
    }
</script>
