

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_mi_departamento.php'?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/css/jefeDep/estilos_jefeDep.css">




<div class="table-responsive">
<table class="table tabla-formato">

        <!-- CABECERA TABLA -->
        <thead>
            <tr>
                <th colspan="5" class="alineado_medio">
                    <div class="cabecera_btn_volver">
                        <p class="margen_derecho_20px">
                            <b><?=!empty($datos['modulos_ciclo']) ? $datos['modulos_ciclo'][0]->ciclo : '' ?></b>
                        </p>
                        <a class="nav-link" href="<?php echo RUTA_URL?>/JDReparto">
                            <i class="fas fa-arrow-circle-left"></i> Volver
                        </a>
                    </div>
                </th>
            </tr>
            <tr>
                <th>Módulos del ciclo <i class="fas fa-book-open"></i></th>
                <th class="text-center">Curso</th>
                <th class="text-center">Profesores <i class="fas fa-users"></i></th>
                <th class="text-center">Horas <i class="fas fa-hourglass"></i></th> 
                <th class="text-center">Reparto <i class="fas fa-clock"></i> </th>
            </tr>
        </thead>


        <!-- BODY TABLA -->
        <tbody>
        <?php if(!empty($datos['modulos_ciclo'])):
        foreach ($datos['modulos_ciclo'] as $modulos) : ?>
            
                <tr>
                    <!-- nombre y codigo del modulo -->
                    <td><?php echo $modulos->modulo.' ('.$modulos->nombre_corto.')'?></td>
                    <!-- curso -->
                    <td class="text-center"><?php echo $modulos->curso;?></td>
                    <!-- profesores -->
                    <td>
                        <?php $foundProf = false; 
                        foreach ($datos['profesores_departamento'] as $prof) {
                            foreach ($datos['profesores_modulo'] as $mod) {
                                if ($prof->id_profesor == $mod->id_profesor && $mod->id_modulo == $modulos->id_modulo) {
                                    echo $prof->nombre_completo.'<br>';
                                    $foundProf = true;
                                }
                            }
                        } ?>
                    </td>
                    <!-- horas -->
                    <td class="text-center">
                        <?php $foundProf = false; 
                        foreach ($datos['profesores_departamento'] as $prof) {
                            foreach ($datos['profesores_modulo'] as $mod) {
                                if ($prof->id_profesor == $mod->id_profesor && $mod->id_modulo == $modulos->id_modulo) {
                                    echo $mod->horas_profesor . ' hrs'.'<br>';
                                    $foundProf = true;
                                }
                            }
                        } ?>
                    </td>
                    <!-- reparto -->
                    <td class="text-center">
                        <?php if ($modulos->habilitado == 1): ?>
                            <a href="<?php echo RUTA_URL ?>/JDReparto/modulo_reparto/<?php echo $modulos->id_modulo ?>" class="enlace_estilo"> 
                                <i class="fas fa-clock"></i> Reparto 
                            </a>
                        <?php else: ?>
                            <span class="text-muted estilo_cursor" title="Este módulo aún no tiene fechas de evaluación">
                                <i class="fas fa-clock"></i> Reparto
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>

        <?php endforeach; 
        endif; ?>
        </tbody>

</table>
</div>

        

<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>   




