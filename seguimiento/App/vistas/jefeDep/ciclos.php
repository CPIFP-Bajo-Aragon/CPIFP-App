
<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_mi_departamento.php'?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/css/jefeDep/estilos_jefeDep.css">




<div class="table-responsive">
<table class="table tabla-formato">

    <!-- CABECERA TABLA -->
    <thead>
        <tr>
            <th colspan="7" class="mayusculas">Departamento de <?php echo $datos['usuario'][0]->departamento?></th>
        </tr>
        <tr>
            <th>Ciclos formativos <i class="fas fa-book"></i></th>
            <th class="text-center">Grado<i class="fas fa-graduation-cap ms-2"></i></th>
            <th class="text-center">Turno<i class="fas fa-sun ms-2"></i></th>
            <th class="text-center">Reparto horas <i class="fas fa-clock"></i></th>
            <th class="text-center">Programaciones <i class="fas fa-file"></i></th>
        </tr>
    </thead>

    <!-- BODY TABLA -->
    <tbody>
    <?php foreach($datos['ciclos'] as $ciclos): ?>

        <tr>
            <!-- ciclo y codigo -->
            <td><?php echo $ciclos->ciclo.' ('.$ciclos->ciclo_corto.')';?></td>
            <!-- grado -->
            <td class="text-center"><?php echo $ciclos->nombre;?></td>
            <!-- turno -->
            <td class="text-center"><?php echo $ciclos->turno;?></td>
            <!-- reparto horas -->
            <td class="text-center">
                <?php if (!empty($datos['lectivo'])): ?>
                    <a href="<?php echo RUTA_URL ?>/JDReparto/modulos_ciclo/<?php echo $ciclos->id_ciclo ?>" class="enlace_estilo">
                        <i class="fas fa-clock"></i> Reparto horas
                    </a>
                <?php else: ?>
                    <span class="enlace_opaco" title="Debe seleccionar un curso">
                        <i class="fas fa-clock"></i> Reparto horas
                    </span>
                <?php endif; ?>
            </td>
            <!-- programaciones -->
            <td>
                <?php if (!empty($datos['lectivo'])): ?>
                    <a href="<?php echo RUTA_URL?>/JDProgramaciones/programaciones_modulos/<?php echo $ciclos->id_ciclo?>" class="enlace_estilo">
                        <i class="fas fa-file"></i> Programaciones
                    </a>
                    <?php 
                        $suma_nuevas = 0; 
                        if(!empty($datos['nuevas'] )){
                        foreach ($datos['nuevas'] as $nuevas) {
                            if ($ciclos->id_ciclo == $nuevas->id_ciclo && $nuevas->suma > 0) {
                                $suma_nuevas = $nuevas->suma;
                                break; 
                            }
                        } 
                        };
                        if($suma_nuevas > 0): ?>
                            <span class="badge">
                                <?php echo $suma_nuevas; ?>
                            </span>
                        <?php endif;?>
                <?php else: ?>
                        <span class="enlace_opaco" title="Debe seleccionar un curso">
                            <i class="fas fa-file"></i> Programaciones
                        </span>
                <?php endif; ?>
            </td>

        </tr>

    <?php endforeach; ?>
    </tbody>

</table>
</div>



<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>   






