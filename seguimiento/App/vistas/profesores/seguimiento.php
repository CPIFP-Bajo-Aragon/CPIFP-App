

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">



<style>
    .enlace-desactivado {
    pointer-events: none;
    opacity: 0.5;
    cursor: not-allowed;
    text-decoration: none;
    color: inherit;
}
</style>




<div class="table-responsive">
<table class="table tabla-formato">

        <!-- CABECERA TABLA -->
        <thead>
            <tr>
                <th colspan="11" style="text-align:center">MODULOS PARA EL SEGUIMIENTO</th>
            </tr>
            <tr>
                <th>Ciclo</th>
                <th>Módulo <i class="fas fa-book-open"></i></th>
                <th>Curso <i class="fas fa-graduation-cap"></i></th>
                <th>Datos <i class="fas fa-info-circle"></i></th>
                <th>Programacion <i class="fas fa-clipboard-list"></i></th>
                <th>Diario <i class="fas fa-calendar-day"></i></th>
                <th>Horas Impartidas <i class="fas fa-clock"></i></th>
                <th>Proceso <i class="fas fa-chalkboard-teacher"></i></th>
                <th>Cumplimiento <i class="fas fa-check-circle"></i></th>
                <th>Análisis <i class="fas fa-chart-line"></i></th>
                <th>Calendario <i class="fas fa-calendar-alt"></i></th>
            </tr>
        </thead>


        <!-- BODY TABLA -->
        <tbody>
        <?php if(!empty($datos['modulo'])):
        foreach($datos['modulo'] as $modulo): ?>
            
            <tr>
                <!-- CICLO -->
                <td style="text-align:left"><?php echo $modulo->ciclo?></td>
                <!-- MODULO -->
                <td style="text-align:left"><?php echo $modulo->modulo.' ('.$modulo->curso.')'?></td>
                <!-- CURSO -->
                <td style="text-align:center"><?php echo $modulo->numero?>º</td>
                <!-- DATOS -->
                <td><a style="text-decoration:none" href="<?php echo RUTA_URL?>/PDatos/<?php echo $modulo->id_modulo?>"><i class="fas fa-info-circle"></i> Datos </a></td>
                
                <!-- PROGRAMACION -->
                <td>
                    <?php foreach ($datos['resultado'] as $resultado):
                    if ($resultado['id_modulo'] == $modulo->id_modulo) :
                        if($resultado['hay_temas'] == 0 || $resultado['hay_horas'] == 0 || $resultado['hay_seguimiento'] == 0 || $resultado['hay_suma'] == 0 
                            || $resultado['hay_suma'] != $resultado['horas_totales_modulo']
                            ){?>
                            
                            <a class="enlace-desactivado" href="javascript:void(0);" onclick="return false;" aria-disabled="true">
                                <i class="fas fa-clipboard-list"></i> Programacion
                            </a>
                               <?php if($datos['tiene_programacion'][0]->id_modulo == $modulo->id_modulo && $datos['tiene_programacion'][0]->tiene_programacion == 0): ?>
                                    <span class="badge bg-warnning ms-1">!</span>
                                <?php endif;  ?>

                        <?php } else { ?>
                                
                                <a style="text-decoration:none" href="<?php echo RUTA_URL?>/PProgramacion/<?php echo $modulo->id_modulo?>"><i class="fas fa-clipboard-list"></i> Programacion</a>
                               <?php foreach($datos['codigo_verificacion'] as $verificacion):
                                if( $verificacion->id_modulo==$modulo->id_modulo && $verificacion->codigo_verificacion!='' && $verificacion->verificada_profesor==0): ?>
                                    <span class="badge bg-warnning ms-1">!</span>
                                <?php endif; 
                                endforeach;

                                if($datos['tiene_programacion'][0]->id_modulo == $modulo->id_modulo && $datos['tiene_programacion'][0]->tiene_programacion == 0): ?>
                                    <span class="badge bg-warnning ms-1">!</span>
                                <?php endif; 
                             };
                   endif; endforeach; ?>
                </td>


                <!-- DIARIO -->
                <td>
                    <?php foreach ($datos['resultado'] as $resultado):
                    if ($resultado['id_modulo'] == $modulo->id_modulo) :
                        if($resultado['hay_temas'] == 0 || $resultado['hay_horas'] == 0 || $resultado['hay_seguimiento'] == 0 || $resultado['hay_suma'] == 0 
                        || $resultado['hay_suma'] != $resultado['horas_totales_modulo']
                        ){?>
                            <a class="enlace-desactivado" href="javascript:void(0);" onclick="return false;" aria-disabled="true">
                                <i class="fas fa-calendar-day"></i> Diario
                            </a>
                        <?php } else { ?>
                            <a style="text-decoration:none" href="<?php echo RUTA_URL?>/PDiario/<?php echo $modulo->id_modulo?>"><i class="fas fa-calendar-day"></i> Diario</a>
                       <?php }
                   endif; endforeach; ?>
                </td>
                
                
                <!-- HORAS IMPARTIDAS -->
                <td>
                    <?php foreach ($datos['resultado'] as $resultado):
                    if ($resultado['id_modulo'] == $modulo->id_modulo) :
                        if($resultado['hay_temas'] == 0 || $resultado['hay_horas'] == 0 || $resultado['hay_seguimiento'] == 0 || $resultado['hay_suma'] == 0 
                            || $resultado['hay_suma'] != $resultado['horas_totales_modulo']
                        ){?>
                            <a class="enlace-desactivado" href="javascript:void(0);" onclick="return false;" aria-disabled="true">
                                <i class="fas fa-clock"></i> Horas Impartidas
                            </a>
                        <?php } else { ?>
                            <a style="text-decoration:none" href="<?php echo RUTA_URL?>/PHorasImpartidas/<?php echo $modulo->id_modulo?>"><i class="fas fa-clock"></i> Horas Impartidas</a>
                       <?php }
                   endif; endforeach; ?>
                </td>
                


                <!-- PROCESO -->
                <td>
                    <?php foreach ($datos['resultado'] as $resultado):
                    if ($resultado['id_modulo'] == $modulo->id_modulo) :
                        if($resultado['hay_temas'] == 0 || $resultado['hay_horas'] == 0 || $resultado['hay_seguimiento'] == 0 || $resultado['hay_suma'] == 0 
                            || $resultado['hay_suma'] != $resultado['horas_totales_modulo']
                        ){?>
                            <a class="enlace-desactivado" href="javascript:void(0);" onclick="return false;" aria-disabled="true">
                                <i class="fas fa-chalkboard-teacher"></i> Proceso
                            </a>
                        <?php } else { ?>
                            <a style="text-decoration:none" href="<?php echo RUTA_URL?>/PEnsenanza/<?php echo $modulo->id_modulo?>"><i class="fas fa-chalkboard-teacher"></i> Proceso </a>
                       <?php }
                   endif; endforeach; ?>
                </td>
                
                
                <!-- CUMPLIMIENTO -->
                <td>
                    <?php foreach ($datos['resultado'] as $resultado):
                    if ($resultado['id_modulo'] == $modulo->id_modulo) :
                        if($resultado['hay_temas'] == 0 || $resultado['hay_horas'] == 0 || $resultado['hay_seguimiento'] == 0 || $resultado['hay_suma'] == 0 
                            || $resultado['hay_suma'] != $resultado['horas_totales_modulo']
                        ){?>
                            <a class="enlace-desactivado" href="javascript:void(0);" onclick="return false;" aria-disabled="true">
                                <i class="fas fa-check-circle"></i> Cumplimiento
                            </a>
                        <?php } else { ?>
                            <a style="text-decoration:none" href="<?php echo RUTA_URL?>/PCumplimiento/<?php echo $modulo->id_modulo?>"><i class="fas fa-check-circle"></i> Cumplimiento </a>
                       <?php }
                   endif; endforeach; ?>
                </td>
                
                <!-- ANALISIS -->
                <td>
                    <?php foreach ($datos['resultado'] as $resultado):
                    if ($resultado['id_modulo'] == $modulo->id_modulo) :
                        if($resultado['hay_temas'] == 0 || $resultado['hay_horas'] == 0 || $resultado['hay_seguimiento'] == 0 || $resultado['hay_suma'] == 0 
                            || $resultado['hay_suma'] != $resultado['horas_totales_modulo']
                        ){?>
                            <a class="enlace-desactivado" href="javascript:void(0);" onclick="return false;" aria-disabled="true">
                                <i class="fas fa-chart-line"></i> Analisis
                            </a>
                        <?php } else { ?>
                            <a style="text-decoration:none" href="<?php echo RUTA_URL?>/PAnalisis/<?php echo $modulo->id_modulo?>"><i class="fas fa-chart-line"></i> Análisis</a>
                       <?php }
                   endif; endforeach; ?>
                </td>

                <!-- CALENDARIO -->
                <td>
                    <?php foreach ($datos['resultado'] as $resultado):
                    if ($resultado['id_modulo'] == $modulo->id_modulo) :
                        if($resultado['hay_temas'] == 0 || $resultado['hay_horas'] == 0 || $resultado['hay_seguimiento'] == 0 || $resultado['hay_suma'] == 0 
                            || $resultado['hay_suma'] != $resultado['horas_totales_modulo'] 
                        ){?>
                            <a class="enlace-desactivado" href="javascript:void(0);" onclick="return false;" aria-disabled="true">
                                <i class="fas fa-calendar-alt"></i> Calendario
                            </a>
                        <?php } else { ?>
                            <a style="text-decoration:none" href="<?php echo RUTA_URL?>/PCalendario/<?php echo $modulo->id_modulo?>"><i class="fas fa-calendar-alt"></i> Calendario</a>
                       <?php }
                   endif; endforeach; ?>
                </td>


            </tr>

        <?php endforeach; endif; ?>
        </tbody>

</table>
</div>

        

<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>   

