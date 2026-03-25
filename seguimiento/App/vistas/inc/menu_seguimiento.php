


<style>
.enlace-desactivado {
    pointer-events: none;
    opacity: 0.5;
    cursor: not-allowed;
    text-decoration: none;
    color: inherit;
}
</style>



<!-- INFO DEL MODULO -->
<nav class="navbar navbar-expand-lg nombre_modulo_seguimiento" style="background-color: #d6eaf8">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <p><span><?php echo $datos['datos_modulo'][0]->ciclo.' ('.$datos['datos_modulo'][0]->numero.'º '.$datos['datos_modulo'][0]->grado.' - '.$datos['datos_modulo'][0]->turno.')';?></span></p>
        <p><span><?php echo $datos['datos_modulo'][0]->modulo;?></span></p>
      </div>
    </div>
  </div>
</nav>



<?php
$resultadoModulo = null;
foreach ($datos['resultado'] as $res) {
    if ($res['id_modulo'] == $datos['datos_modulo'][0]->id_modulo) {
        $resultadoModulo = $res;
        break;
    }
}

$enlaceDesactivado = ($resultadoModulo['hay_temas'] == 0 || $resultadoModulo['hay_horas'] == 0 
                    || $resultadoModulo['hay_seguimiento'] == 0 || $resultadoModulo['hay_suma'] == 0
                    || ($resultadoModulo['hay_suma'] != $resultadoModulo['horas_totales_modulo'])
                    );
?>




<!-- MENU PRINCIPAL SEGUIMIENTO -->
<nav class="navbar navbar-expand-lg menu-configuracion">

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
            data-bs-target="#navbarNav4" aria-controls="navbarNav4" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>


        <div class="collapse navbar-collapse" id="navbarNav4">
        <div class="d-flex justify-content-start w-100">
        <ul class="navbar-nav">


              <!-- DATOS -->
              <li class="nav-item">
                <a class="nav-link enlace-menu-configuracion <?php echo (strpos($_SERVER['REQUEST_URI'], '/PDatos'.'/'.$datos['datos_modulo'][0]->id_modulo) !== false) ? 'active' : ''; ?>" 
                  href="<?php echo RUTA_URL?>/PDatos/<?php echo $datos['datos_modulo'][0]->id_modulo?>">
                  <i class="fas fa-info-circle"></i> Datos
                </a>
              </li>


              <!-- PROGRAMACION -->
              <li class="nav-item">
                <?php if ($enlaceDesactivado): ?>
                  <a class="nav-link enlace-menu-configuracion enlace-desactivado" href="javascript:void(0);" onclick="return false;" aria-disabled="true">
                <?php else: ?>
                  <a class="nav-link enlace-menu-configuracion <?php echo (strpos($_SERVER['REQUEST_URI'], '/PProgramacion'.'/'.$datos['datos_modulo'][0]->id_modulo) !== false) ? 'active' : ''; ?>" 
                    href="<?php echo RUTA_URL?>/PProgramacion/<?php echo $datos['datos_modulo'][0]->id_modulo?>">
                    <?php endif; ?>
                    <i class="fas fa-clipboard-list"></i> Programacion
                    <!-- <?php if(empty($datos['obtener_programacion'])): ?>
                          <span class="badge bg-warnning ms-1">!</span>
                    <?php endif; ?> -->
                  </a>
              </li>



              <!-- DIARIO -->
              <li class="nav-item">
                <?php if ($enlaceDesactivado): ?>
                  <a class="nav-link enlace-menu-configuracion enlace-desactivado" href="javascript:void(0);" onclick="return false;" aria-disabled="true">
                    <i class="fas fa-calendar-day"></i> Diario
                  </a>
                <?php else: ?>
                  <?php 
                    $ruta_actual = $_SERVER['REQUEST_URI'];
                    $id_modulo = $datos['datos_modulo'][0]->id_modulo;
                    $activo = (strpos($ruta_actual, '/PDiario/'.$id_modulo) !== false 
                              || strpos($ruta_actual, '/PDiario_visualizar/'.$id_modulo) !== false) ? 'active' : '';
                  ?>
                  <a class="nav-link enlace-menu-configuracion <?php echo $activo; ?>" href="<?php echo RUTA_URL ?>/PDiario/<?php echo $id_modulo ?>">
                    <i class="fas fa-calendar-day"></i> Diario
                  </a>
                <?php endif; ?>
              </li>




              <!-- HORAS IMPARTIDAS -->
              <li class="nav-item">
                <?php if ($enlaceDesactivado): ?>
                  <a class="nav-link enlace-menu-configuracion enlace-desactivado" href="javascript:void(0);" onclick="return false;" aria-disabled="true">
                <?php else: ?>
                  <a class="nav-link enlace-menu-configuracion <?php echo (strpos($_SERVER['REQUEST_URI'], '/PHorasImpartidas'.'/'.$datos['datos_modulo'][0]->id_modulo) !== false) ? 'active' : ''; ?>" 
                    href="<?php echo RUTA_URL?>/PHorasImpartidas/<?php echo $datos['datos_modulo'][0]->id_modulo?>">
                <?php endif; ?>
                  <i class="fas fa-clock"></i> Horas Impartidas
                </a>
              </li>


              <!-- PROCESO DE ENSEÑANZA -->
              <li class="nav-item">
                <?php if ($enlaceDesactivado): ?>
                  <a class="nav-link enlace-menu-configuracion enlace-desactivado" href="javascript:void(0);" onclick="return false;" aria-disabled="true">
                <?php else: ?>
                  <a class="nav-link enlace-menu-configuracion <?php echo (strpos($_SERVER['REQUEST_URI'], '/PEnsenanza'.'/'.$datos['datos_modulo'][0]->id_modulo) !== false) ? 'active' : ''; ?>" 
                    href="<?php echo RUTA_URL?>/PEnsenanza/<?php echo $datos['datos_modulo'][0]->id_modulo?>">
                <?php endif; ?>
                  <i class="fas fa-chalkboard-teacher"></i> Proceso de Enseñanza
                </a>
              </li>


              <!-- CUMPLIMIENTO -->
              <li class="nav-item">
                <?php if ($enlaceDesactivado): ?>
                  <a class="nav-link enlace-menu-configuracion enlace-desactivado" href="javascript:void(0);" onclick="return false;" aria-disabled="true">
                <?php else: ?>
                  <a class="nav-link enlace-menu-configuracion <?php echo (strpos($_SERVER['REQUEST_URI'], '/PCumplimiento'.'/'.$datos['datos_modulo'][0]->id_modulo) !== false) ? 'active' : ''; ?>" 
                    href="<?php echo RUTA_URL?>/PCumplimiento/<?php echo $datos['datos_modulo'][0]->id_modulo?>">
                <?php endif; ?>
                  <i class="fas fa-check-circle"></i> Cumplimiento de la Programación
                </a>
              </li>


              <!-- ANÁLISIS -->
              <li class="nav-item">
                <?php if ($enlaceDesactivado): ?>
                  <a class="nav-link enlace-menu-configuracion enlace-desactivado" href="javascript:void(0);" onclick="return false;" aria-disabled="true">
                <?php else: ?>
                  <a class="nav-link enlace-menu-configuracion <?php echo (strpos($_SERVER['REQUEST_URI'], '/PAnalisis'.'/'.$datos['datos_modulo'][0]->id_modulo) !== false) ? 'active' : ''; ?>" 
                    href="<?php echo RUTA_URL?>/PAnalisis/<?php echo $datos['datos_modulo'][0]->id_modulo?>">
                <?php endif; ?>
                  <i class="fas fa-chart-line"></i> Análisis
                </a>
              </li>

              <!-- CALENDARIO -->
              <li class="nav-item">
                <?php if ($enlaceDesactivado): ?>
                  <a class="nav-link enlace-menu-configuracion enlace-desactivado" href="javascript:void(0);" onclick="return false;" aria-disabled="true">
                <?php else: ?>
                  <a class="nav-link enlace-menu-configuracion <?php echo (strpos($_SERVER['REQUEST_URI'], '/PCalendario'.'/'.$datos['datos_modulo'][0]->id_modulo) !== false) ? 'active' : ''; ?>" 
                    href="<?php echo RUTA_URL?>/PCalendario/<?php echo $datos['datos_modulo'][0]->id_modulo?>">
                <?php endif; ?>
                  <i class="fas fa-calendar-alt"></i> Calendario
                </a>
              </li>

              
              <!-- ENLACE VOLVER -->
              <li class="nav-item">
                <a class="nav-link enlace-menu-configuracion  <?php echo (basename($_SERVER['REQUEST_URI']) == '') ? 'active' : ''; ?>" 
                    href="<?php echo RUTA_URL?>/PSeguimiento">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
              </li>


              <!-- ENLACES PARA AYUDA -->
              <li class="nav-item"> 
                <a class="nav-link enlace-menu-configuracion"  id="menu_principal" 
                    href="<?php 
                        // Comprobamos qué sección está activa y asignamos la URL de ayuda correspondiente
                        if (strpos($_SERVER['REQUEST_URI'], '/PDatos') !== false) {
                            echo 'https://difficult-cow-020.notion.site/Datos-196b2e2f06da8177af19ea7065140f36';
                        } elseif (strpos($_SERVER['REQUEST_URI'], '/PDiario') !== false) {
                            echo 'https://difficult-cow-020.notion.site/Diario-196b2e2f06da81deb4cec8141124d3f7';
                        } elseif (strpos($_SERVER['REQUEST_URI'], '/PHorasImpartidas') !== false) {
                           echo 'https://difficult-cow-020.notion.site/Horas-Impartidas-196b2e2f06da812eb003da6ee964b692';
                        } elseif (strpos($_SERVER['REQUEST_URI'], '/PEnsenanza') !== false) {
                            echo 'https://difficult-cow-020.notion.site/Proceso-de-Ense-anza-196b2e2f06da81b39eacf9700b818a1f';
                        } elseif (strpos($_SERVER['REQUEST_URI'], '/PCumplimiento') !== false) {
                            echo 'https://difficult-cow-020.notion.site/Cumplimiento-de-la-Programaci-n-196b2e2f06da8170a31aceea5c7482ad';
                        } elseif (strpos($_SERVER['REQUEST_URI'], '/PAnalisis') !== false) {
                            echo 'https://difficult-cow-020.notion.site/An-lisis-196b2e2f06da81999953e8f4cedfdc02';
                        }
                      ?>" 
                      target="_blank">
                      <i class="fas fa-question-circle"></i> AYUDA
                </a>
              </li>

        </ul>
        </div>
        </div>

</nav>
