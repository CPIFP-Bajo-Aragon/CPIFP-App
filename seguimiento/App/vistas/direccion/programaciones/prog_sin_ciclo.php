

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_configuracion.php'?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">



<div class="table-responsive">
    <table class="table tabla-formato">

        <thead>
            <tr>
                <th colspan="12" style="vertical-align: middle;">
                    <div style="color:white; display: flex; justify-content: center;  height: 100%;">
                        <h5 style="margin-right: 20px;"><b><?php echo $datos['modulos'][0]->ciclo;?></b></h5>
                        <a class="nav-link" href="<?php echo RUTA_URL.'/programaciones/departamento/'.$datos['modulos'][0]->id_departamento;?>">
                            <i class="fas fa-arrow-circle-left"></i> Volver
                        </a>
                    </div>
                </th>
            </tr>
            <tr>
                <th>Modulos</th>
                <th>Codigo prog.</th>
                <th class="text-center">Curso</th>
                <th class="text-center">Edicion anterior</th>
                <th class="text-center">Profesor curso anterior</th>
                <th class="text-center">Profesor curso actual</th>
                <th class="text-center">Es nueva?</th>
                <th class="text-center">Edicion vigente</th>
                <th class="text-center">Revisada por jefe dto.</th>
                <th class="text-center">Codigo verificacion</th>
                <th class="text-center">Verficada profesor</th>
                <th class="text-center" colspan="2">Opciones<i class="fas fa-cogs ms-2" ></i></th>
            </tr> 
        </thead>

        <tbody>
            <?php foreach ($datos['modulos'] as $dep) : ?>
                <tr>

                    <!-- modulo -->
                    <td><?php echo $dep->modulo;?></td>

                    <!-- codigo programacion -->
                    <td><?php echo $dep->codigo_programacion;?></td>

                    <!-- curso -->
                    <td class="text-center"><?php echo $dep->curso . ' (' . $dep->numero . 'º)'; ?></td>

                    <!-- edicion anterior -->
                    <td class="text-center">
                        <?php
                        // Buscar si el módulo actual tiene programaciones
                        $tiene_programacion = false;
                        foreach ($datos['numero_programaciones_ciclo'] as $numero) {
                            if ($dep->id_modulo == $numero->id_modulo && $numero->total_programaciones > 0) {
                                $tiene_programacion = true;
                                break;
                            }
                        }
                        if ($tiene_programacion) {
                            // Mostrar la edición anterior si existe
                            $mostrado = false;
                            foreach ($datos['programaciones_ediciones_anteriores'] as $programaciones_anteriores) {
                                if ($programaciones_anteriores->id_modulo == $dep->id_modulo) {
                                    echo "<span>{$programaciones_anteriores->num_version}</span>";
                                    $mostrado = true;
                                    break;
                                }
                            }
                            if (!$mostrado) {
                                echo "<p>Sin edición anterior</p>";
                            }
                        } else {
                            echo "<p style='color:orangered; font-weight:bold'>Módulo sin programación</p>";
                        }
                        ?>
                    </td>


                    <!-- porfesor edicion anterior -->
                    <td> <?php foreach($datos['programaciones_ediciones_anteriores'] as $programaciones_anteriores):
                    if($programaciones_anteriores->id_modulo == $dep->id_modulo):?>
                        <span><?php echo $programaciones_anteriores->nombre_completo;?></span>
                    <?php endif; 
                    endforeach ?>
                    </td>


                    <!-- porfesor actual -->
                    <td> 
                    <?php foreach($datos['profesor_modulo_ciclo'] as $profesor_modulo):
                    if($profesor_modulo->id_modulo == $dep->id_modulo):?>
                        <span><?php echo $profesor_modulo->nombre_completo;?></span>
                    <?php endif; 
                    endforeach ?>
                    </td>


                    <!-- es nueva -->
                    <td class="text-center" >
                    <?php foreach($datos['programaciones_modulos_activas'] as $programaciones_activas):
                    if($programaciones_activas->id_modulo == $dep->id_modulo):?>
                        <span style=" color: orangered; font-weight:bold"><?php echo $programaciones_activas->nueva==1 ?  'Si' : 'No' ?></span>
                    <?php endif; endforeach ?>
                    </td>


                    <!-- edicion programacion actual -->
                    <td class="text-center"> 
                    <?php foreach($datos['programaciones_modulos_activas'] as $programaciones_activas):
                    if($programaciones_activas->id_modulo == $dep->id_modulo):?>
                        <span><?php echo $programaciones_activas->num_version;?></span>
                    <?php endif; 
                    endforeach ?>
                    </td>


                    <!-- verificada jefe departamento -->
                    <td class="text-center"> 
                    <?php $mostrado = false;
                    foreach($datos['programaciones_modulos_activas'] as $programaciones_activas):
                    if ($programaciones_activas->id_modulo == $dep->id_modulo && $programaciones_activas->activa == 1):
                        $mostrado = true;
                        if ($programaciones_activas->verificada_jefe_dep == 1): ?>
                            <span class="text-success">
                                <i class="fas fa-check-circle" title="Programación verificada"></i> Si
                            </span>
                        <?php else: ?>
                            <span class="text-danger">
                                <i class="fas fa-times-circle" title="Programación no verificada"></i> No
                            </span>
                        <?php endif;
                        break;
                    endif;
                    endforeach;?>
                    </td>


                    <!-- codigo verificacion -->
                    <td class="text-center" >
                    <?php foreach($datos['programaciones_modulos_activas'] as $programaciones_activas):
                        if($programaciones_activas->id_modulo == $dep->id_modulo && $programaciones_activas->nueva==1):?>
                            <?php if($programaciones_activas->codigo_verificacion == ''){?>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#nuevo_codigo<?php echo $dep->id_modulo;?>" class="btn btn-custom">
                                    Generar código
                                </a>
                            <?php } else { ?>
                                <span><?php echo $programaciones_activas->codigo_verificacion?></span>
                    <?php }; endif; endforeach ?>
                    </td>


                    <div class="modal fade" id="nuevo_codigo<?php echo $dep->id_modulo;?>" tabindex="-1" aria-labelledby="myModalLabel<?php echo $dep->id_modulo; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="myModalLabel<?php echo $dep->id_modulo; ?>"><?php echo $dep->modulo;?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="<?php echo RUTA_URL?>/programaciones/codigo_verificacion/<?php echo $dep->id_modulo;?>">
                                        <div class="mb-3">
                                            <div class="input-group">
                                                <span class="input-group-text">Código verificación *</span>
                                                <input type="text" class="form-control" name="codigo_verificacion" id="codigoVerificacion<?php echo $dep->id_modulo; ?>"
                                                    value="<?php echo $dep->nombre_corto . '-' . $dep->id_modulo . '-' . date('Ymd') . '-' . strtoupper(substr(md5(rand()), 0, 6)); ?>" readonly>
                                                <input type="hidden" class="form-control" name="id_ciclo" value="<?php echo $dep->id_ciclo?>">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary" id="boton-modal">Enviar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>



                    <td class="text-center">
                        <?php 
                        $mostrado = false;
                        foreach ($datos['programaciones_modulos_activas'] as $programaciones_activas):
                            // Verifica si el módulo actual coincide con el módulo de la programación activa
                            if ($programaciones_activas->id_modulo == $dep->id_modulo && $programaciones_activas->activa == 1):
                                $mostrado = true;
                                // Verifica si la programación ha sido verificada por el profesor
                                if ($programaciones_activas->verificada_profesor == 1): ?>
                                    <span class="text-success">
                                        <i class="fas fa-check-circle" title="Programación verificada"></i> Sí
                                    </span>
                                <?php else: ?>
                                    <span class="text-danger">
                                        <i class="fas fa-times-circle" title="Programación no verificada"></i> No
                                    </span>
                                <?php endif; ?>
                            <?php endif;
                        endforeach;
                        ?>
                    </td>





                    <td class="text-center">
                        <a  href="<?php echo RUTA_URL?>/programaciones/modulo/<?php echo $dep->id_modulo ?>">
                            <img class="icono" src="<?php echo RUTA_Icon?>ver.png"></img> 
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        
</table>
</div>



<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>


