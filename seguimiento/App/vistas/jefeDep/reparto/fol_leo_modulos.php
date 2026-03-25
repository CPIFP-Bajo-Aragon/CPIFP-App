

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_mi_departamento.php'?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/css/jefeDep/estilos_jefeDep.css">




<div class="btn-container mt-4 ms-5">
    <!-- boton reparto horas -->
    <a class="btn btn-custom active" href="<?php echo RUTA_URL?>/JDReparto/fol_leo_modulos">
        <i class="fas fa-clock"></i> Reparto de horas
    </a>
    <!-- boton programaciones -->
    <a class="btn btn-custom" href="<?php echo RUTA_URL?>/JDProgramaciones/programaciones_fol_leo">
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
                <th>Departamento<i class="fas fa-building ms-2"></i></th>
                <th>Ciclo formativos <i class="fas fa-book"></i></th>
                <th class="text-center">Curso</th>
                <th class="text-center">Grado<i class="fas fa-graduation-cap ms-2"></i></th>
                <th class="text-center">Turno<i class="fas fa-sun ms-2"></i></th>
                <th class="text-center">Modulo</th>
                <th class="text-center">Profesores <i class="fas fa-users"></i></th>
                <th class="text-center">Horas <i class="fas fa-hourglass"></i></th> 
                <th class="text-center">Reparto <i class="fas fa-clock"></i></th>
            </tr>
        </thead>
  
        <!-- BODY TABLA -->
        <tbody>
        <?php foreach($datos['modulos'] as $modulos): ?>
        <tr>

                <td><?php echo $modulos->departamento?></td>
                <td><?php echo $modulos->ciclo?></td>
                <td class="text-center"><?php echo $modulos->nombre_curso.' ('.$modulos->curso.')';?></td>
                <td class="text-center"><?php echo $modulos->grado?></td>
                <td class="text-center"><?php echo $modulos->turno?></td>
                <td><?php echo $modulos->modulo?></td>
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
                        <a href="<?php echo RUTA_URL?>/JDReparto/fol_leo_reparto/<?php echo $modulos->id_modulo?>" class="enlace_estilo"> 
                            <i class="fas fa-clock"></i> Reparto 
                        </a>
                    <?php else: ?>
                        <span class="text-muted estilo_cursor" title="Este módulo aún no tiene fechas de evaluación">
                            <i class="fas fa-clock"></i> Reparto
                        </span>
                    <?php endif; ?>
                </td>

        </tr>
        <?php endforeach; ?>
        </tbody>

</table>
</div>



<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>   









