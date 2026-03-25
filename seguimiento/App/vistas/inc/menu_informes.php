

<?php $currentUrl = $_SERVER['REQUEST_URI']; ?>



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

                    <!-- Enlace "Curso actual" -->
                    <li class="nav-item">
                        <a class="nav-link enlace-menu-configuracion
                            <?php
                            if (strpos($_SERVER['REQUEST_URI'], 'DInformes') !== false &&
                                strpos($_SERVER['REQUEST_URI'], 'DInformes/historicos') === false) {
                                    echo 'active';
                            }?>" 
                            id="menu_principal" href="<?php echo RUTA_URL ?>/DInformes">
                            <i class="fas fa-home"></i> Curso actual
                        </a>
                    </li>

                    <!-- Enlace "Históricos" -->
                    <li class="nav-item">
                        <a class="nav-link enlace-menu-configuracion
                            <?php
                            if (strpos($_SERVER['REQUEST_URI'], 'DInformes/historicos') !== false) {
                                echo 'active';
                            }?>" 
                            href="<?php echo RUTA_URL ?>/DInformes/historicos">
                            <i class="fa fa-chart-bar"></i> Históricos
                        </a>
                    </li>


                    <!-- Enlace "Ayuda" -->
                    <li class="nav-item">
                        <a class="nav-link enlace-menu-configuracion" id="menu_principal" href="" target="_blank">
                            <i class="fas fa-question-circle"></i> AYUDA
                        </a>
                    </li>
                
                </ul>

            </div>
        </div>

    </div>
</div>



