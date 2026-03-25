



<nav class="navbar navbar-expand-lg menu-configuracion">

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav2" aria-controls="navbarNav2" 
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav2">
            <ul class="navbar-nav">

                <!-- SI EL ROL ES 50 -->
                <?php foreach ($datos['usuarioSesion']->roles as $rol): 
                if($rol->id_rol == 50): ?>

                    <!-- curso-->
                    <li class="nav-item">
                        <a class="nav-link enlace-menu-configuracion <?php echo (strpos($_SERVER['REQUEST_URI'], 'curso') !== false 
                        || strpos($_SERVER['REQUEST_URI'], 'festivos') !== false 
                        || strpos($_SERVER['REQUEST_URI'], 'evaluaciones') !== false 
                        || strpos($_SERVER['REQUEST_URI'], 'curso_indicadores') !== false? 'active' : ''); ?>" 
                        href="<?php echo RUTA_URL?>/curso">
                            <i class="fas fa-calendar-check"></i> Curso lectivo
                        </a>
                    </li>

                    <!-- indicadores -->
                    <li class="nav-item">
                        <a class="nav-link enlace-menu-configuracion 
                            <?php echo (strpos($_SERVER['REQUEST_URI'], 'indicadores') !== false 
                            && strpos($_SERVER['REQUEST_URI'], 'curso_indicadores') === false ? 'active' : ''); ?>" 
                            href="<?php echo RUTA_URL ?>/indicadores">
                            <i class="fas fa-chart-bar"></i> Indicadores
                        </a>
                    </li>

                    <!-- causas y soluciones -->
                    <li class="nav-item">
                        <?php $currentUrl = $_SERVER['REQUEST_URI'];
                            $isActive = strpos($currentUrl, 'acciones') !== false;?>
                        <a class="nav-link enlace-menu-configuracion <?php echo $isActive ? 'active' : ''; ?>" href="<?php echo RUTA_URL?>/acciones">
                            <i class="fas fa-lightbulb"></i> Causas y soluciones
                        </a>
                    </li>

                    <!-- programaciones -->
                    <li class="nav-item">
                        <?php 
                            $currentUrl = $_SERVER['REQUEST_URI'];
                            $isActive = strpos($currentUrl, 'programaciones') !== false;
                            $hayLectivo = !empty($datos['lectivo']);
                        ?>
                        <?php if ($hayLectivo): ?>
                            <a class="nav-link enlace-menu-configuracion <?php echo $isActive ? 'active' : ''; ?>" 
                                href="<?php echo RUTA_URL ?>/programaciones">
                                <i class="fas fa-book"></i> Programaciones
                            </a>
                        <?php else: ?>
                            <span class="nav-link enlace-menu-configuracion" style="opacity: 0.5; cursor: not-allowed;" title="Debe seleccionar un curso">
                                <i class="fas fa-book"></i> Programaciones
                            </span>
                        <?php endif; ?>
                    </li>


            <?php endif; 
            endforeach; ?>


                <!-- ENLACES PARA AYUDA -->
                <li class="nav-item">
                    <a class="nav-link enlace-menu-configuracion"  id="menu_principal" 
                        href="<?php 
                            if (strpos($_SERVER['REQUEST_URI'], 'curso') !== false) {
                                echo 'https://difficult-cow-020.notion.site/Curso-Lectivo-196b2e2f06da813daaa5e3af6f716088';
                            } elseif (strpos($_SERVER['REQUEST_URI'], 'indicadores') !== false) {
                                echo 'https://difficult-cow-020.notion.site/Umbrales-e-indicadores-196b2e2f06da81dfab95d343fbb4f46f';
                            } elseif (strpos($_SERVER['REQUEST_URI'], 'acciones') !== false) {
                                echo 'https://difficult-cow-020.notion.site/Causas-y-soluciones-196b2e2f06da81dfb5efc86cb0decd74';
                            } elseif (strpos($_SERVER['REQUEST_URI'], 'programaciones') !== false) {
                                echo 'https://difficult-cow-020.notion.site/Programaciones-199b2e2f06da80f29975fa02465a96f6';
                            }
                        ?>" 
                        target="_blank">
                        <i class="fas fa-question-circle"></i> AYUDA
                    </a>
                </li>

        </ul>
        </div>

</nav>

