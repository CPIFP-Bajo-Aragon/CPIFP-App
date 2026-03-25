

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_mi_departamento.php'?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/css/jefeDep/estilos_jefeDep.css">





<div class="btn-container mt-4 ms-5">
    <!-- boton reparto horas -->
    <?php if (!empty($datos['lectivo'])): ?>
        <a class="btn btn-custom" href="<?php echo RUTA_URL?>/JDReparto/fol_leo_modulos">
            <i class="fas fa-clock"></i> Reparto de horas
        </a>
    <?php else: ?>
        <span class="btn btn-custom enlace_opaco">
            <i class="fas fa-clock"></i> Reparto de horas
        </span>
    <?php endif; ?>
    <!-- boton programaciones -->
    <a class="btn btn-custom active" href="<?php echo RUTA_URL?>/JDProgramaciones/programaciones_fol_leo">
        <i class="fas fa-file"></i> Programaciones
        <?php if(!empty($datos['nuevas'][0]->suma)):?>
            <span class="badge">
                <?php echo $datos['nuevas'][0]->suma;?>
            </span>
        <?php endif; ?>
    </a>
</div>




<div class="table-responsive">
<table class="table tabla-formato m-5">

        <!-- CABECERA TABLA -->
        <thead>
            <tr>
                <th class="text-center align-middle">Departamento<i class="fas fa-building ms-2"></i></th>
                <th class="text-center align-middle">Ciclo Formativo <i class="fas fa-book"></i></th>
                <th class="text-center align-middle">Curso <i class="fas fa-calendar-day"></i></th>
                <th class="text-center align-middle">Edicion curso anterior</th>
                <th class="text-center align-middle">Profesor curso anterior</th>
                <th class="text-center align-middle">Profesor curso actual</th>
                <th class="text-center align-middle">Es nueva?</th>
                <th class="text-center align-middle">Edicion vigente</th>
                <th class="text-center align-middle"> Descargar Archivo <i class="fas fa-file"></i></th>
                <th class="text-center">Verificada <br>
            </tr>
        </thead>

        <!-- BODY TABLA -->
        <tbody>
        <?php if(!empty($datos['modulos'])):
        foreach ($datos['modulos'] as $modulos) : ?>
            
                <tr>
                
                    <td><?php echo $modulos->departamento?></td>
                    <td class="text-left"><?php echo $modulos->ciclo.' ('.$modulos->ciclo_corto.')'?></td>
                    <td class="text-center"><?php echo $modulos->curso;?></td>

                    <!-- edicion anterior -->
                    <td class="text-center">
                        <?php
                        // Buscar si el módulo actual tiene programaciones activas
                        $tiene_programacion = false;
                        foreach ($datos['programaciones_anio_anterior'] as $numero) {
                            if ($modulos->id_modulo == $numero->id_modulo) {
                                $tiene_programacion = true;
                                break;
                            }
                        }
                        if ($tiene_programacion) {
                            $mostrado = false;
                            foreach ($datos['programaciones_anio_anterior'] as $programaciones_activas) {
                                if ($programaciones_activas->id_modulo == $modulos->id_modulo) {
                                        echo $programaciones_activas->num_version;
                                        $mostrado = true;
                                        break;
                                }
                            }
                            if (!$mostrado) {
                                echo "<p>Sin edición anterior</p>";
                            }
                        } else {
                            echo "<p class='texto_naranja_fuerte'>Módulo sin programación</p>";
                        }
                        ?>
                    </td>

                    <!-- porfesor curso anterior -->
                    <td> <?php foreach($datos['profesor_anterior'] as $profesor_anterior):
                        if($profesor_anterior->id_modulo == $modulos->id_modulo):?>
                            <span><?php echo $profesor_anterior->nombre_completo;?>; </span>
                        <?php endif; 
                        endforeach ?>
                    </td>

                    <!-- porfesor actual -->
                    <td> 
                        <?php foreach($datos['profesor_modulo_ciclo'] as $profesor_modulo):
                        if($profesor_modulo->id_modulo == $modulos->id_modulo):?>
                            <span><?php echo $profesor_modulo->nombre_completo;?>; </span>
                        <?php endif; 
                        endforeach ?>
                    </td>

                    <!-- es nueva -->
                    <td class="text-center">
                    <?php $mostrado = false;
                    foreach ($datos['profesor_modulo_ciclo'] as $profesor_modulo):
                        if ($profesor_modulo->cambia_programacion != -1 && $profesor_modulo->id_modulo == $modulos->id_modulo):
                            foreach ($datos['programaciones_modulos_activas'] as $programaciones_activas):
                                if ($programaciones_activas->id_modulo == $modulos->id_modulo && !$mostrado): ?>
                                    <span class="texto_naranja_fuerte"><?php echo $programaciones_activas->nueva == 1 ? 'Si' : 'No'; ?></span>
                                    <?php
                                    $mostrado = true;
                                    break; 
                                endif;
                            endforeach;
                            break; 
                        endif;
                    endforeach;?>
                    </td>

                    <!-- edición actual -->
                    <td class="text-center"> 
                    <?php $mostrado = false;
                    foreach ($datos['profesor_modulo_ciclo'] as $profesor_modulo):
                        if ($profesor_modulo->cambia_programacion != -1 && $profesor_modulo->id_modulo == $modulos->id_modulo):
                            foreach ($datos['programaciones_modulos_activas'] as $programaciones_activas):
                                if ($programaciones_activas->id_modulo == $modulos->id_modulo && !$mostrado): ?>
                                    <span><?php echo $programaciones_activas->num_version; ?></span>
                                    <?php
                                    $mostrado = true;
                                    break; 
                                endif;
                            endforeach;
                            break;
                        endif;
                    endforeach;?>
                    </td>

                    
                    <!-- descargar archivo -->
                    <td class="text-center color_principal">
                    <?php
                    $mostrado = false;
                    foreach ($datos['profesor_modulo_ciclo'] as $profesor_modulo):
                    if ($profesor_modulo->cambia_programacion != -1 && $profesor_modulo->id_modulo == $modulos->id_modulo):
                        foreach($datos['programaciones_modulos_activas'] as $programaciones_activas):
                            if ($programaciones_activas->id_modulo == $modulos->id_modulo && !$mostrado): ?>
                                <form method="post" action="<?php echo RUTA_URL ?>/jefeDep/descargar_programacion/<?php echo $modulos->id_modulo ?>">
                                    <input type="hidden" name="ruta_archivo" id="ruta" value="<?php echo $programaciones_activas->ruta ?>">
                                    <button type="submit" class="nav-link evaluacion-link boton_descargar">
                                        <i class="fa fa-download"></i> Descargar
                                    </button>
                                </form>
                                <?php
                                $mostrado = true;
                                break;
                            endif;
                        endforeach;
                    break; 
                    endif;
                    endforeach;?>
                    </td>
    

                    <!-- verificar programacion -->
                    <td class="text-center">
                    <?php  $programacion_mostrar = null;

                        // Buscar si hay programación activa y nueva para este módulo
                        foreach($datos['programaciones_modulos_activas'] as $programacion) {
                            if ($programacion->id_modulo == $modulos->id_modulo && $programacion->activa == 1) {
                                $programacion_mostrar = $programacion;
                                break;
                            }
                        }

                        // Si hay una programación activa y nueva para este módulo
                        if ($programacion_mostrar):

                            if ($programacion_mostrar->verificada_jefe_dep == 1): ?>
                                <span class="text-success">
                                    <i class="fas fa-check-circle" title="Programación verificada"></i> Verificada
                                </span>
                            <?php else: ?>
                                <button type="button" class="btn btn-custom" data-toggle="modal" data-target="#verificar<?php echo $modulos->id_modulo?>">
                                    <i class="fas fa-check"></i> Verificar
                                </button>

                                <!-- Modal con formulario -->
                                <div class="modal fade" id="verificar<?php echo $modulos->id_modulo?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel<?php echo $modulos->id_modulo ?>" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form method="post" action="<?php echo RUTA_URL?>/JDProgramaciones/verificar_programacion_fol_leo/<?php echo $modulos->id_modulo?>">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalLabel<?php echo $modulos->id_modulo ?>"><?php echo $modulos->modulo ?></h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Confirma que has revisado la programación de este módulo y que está correcta</p>
                                                    <input type="hidden" name="confirma" value="1">
                                                    <input type="hidden" name="id_ciclo" value="<?php echo $modulos->id_ciclo?>">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success" id="boton-modal">Confirmar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>

                </tr>

        <?php endforeach; 
        endif; ?>
        </tbody>

</table>
</div>



<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>   

