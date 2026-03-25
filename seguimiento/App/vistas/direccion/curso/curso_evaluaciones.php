

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_configuracion.php'?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">



<?php if(!empty($datos['cursos'])):?>
<?php foreach($datos['turnos'] as $turnos): ?>
<form method="post" action="<?php echo RUTA_URL?>/curso/fechas_evaluaciones/<?php echo $turnos->id_turno;?>">

    <!-- Tabla responsiva -->
    <div class="table-responsive">
        <table class="table table-bordered m-5 tabla-formato">

            <thead>
                <tr>
                    <th colspan="<?php echo 1 + (count($datos['grados']) * count($datos['cursos'])); ?>" >
                        Configuración de evaluaciones para el turno de <?php echo $turnos->turno; ?>
                        <a class="nav-link ms-2" style="font-size: 1.3em; display: inline-flex; align-items: center;" href="<?php echo RUTA_URL?>/curso">
                            <i class="fas fa-arrow-circle-left" style="vertical-align: middle"></i>
                        </a>
                    </th>
                </tr>
                <tr>
                    <th></th>
                    <?php foreach($datos['grados'] as $grados): ?>
                        <th colspan="<?php echo count($datos['cursos'])?>" style="text-align: center; font-size: 0.8em; padding: 8px;"><?php echo $grados->nombre ?></th>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <th></th>
                    <?php foreach($datos['grados'] as $grados): ?>
                        <?php foreach($datos['cursos'] as $cursos): ?>
                            <th style="text-align: center; font-size: 13px; padding: 8px;"><?php echo $cursos->numero ?>º</th>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tr>
            </thead>



            <?php
                $fecha_inicio = DateTime::createFromFormat('d-m-Y', $datos['lectivo'][0]->fecha_inicio)->format('Y-m-d');
                $fecha_fin = DateTime::createFromFormat('d-m-Y', $datos['lectivo'][0]->fecha_fin)->format('Y-m-d');
            ?>
            <tbody>
                <?php foreach($datos['numero_evaluaciones'] as $numero): ?>
                <tr>
                    <td style="text-align: center; font-size: 13px; padding: 8px; vertical-align:middle;"><?php echo $numero->evaluacion; ?></td>
                    <?php foreach($datos['grados'] as $grados): ?>
                        <?php foreach($datos['cursos'] as $cursos): ?>
                            <td style="text-align: center;vertical-align:middle; font-size: 13px; padding: 8px; word-wrap: break-word;">
                                <?php 
                                $fechaValor = ''; 

                                foreach($datos['fechas_evaluaciones'] as $fecha) :
                                    if ($fecha->turno == $turnos->turno && $fecha->nombre == $grados->nombre && $fecha->numero == $cursos->numero && $numero->id_evaluacion == $fecha->id_evaluacion) {
                                        $fechaValor = $fecha->fecha;
                                        break;
                                    }
                                endforeach; 
                                ?>

                                <input type="date" class="form-control form-control-md" 
                                id="fecha_<?php echo $turnos->id_turno ?>_<?php echo $grados->id_grado ?>_<?php echo $cursos->id_numero ?>" 
                                name="combinacion[<?php echo $grados->id_grado . '-' . $numero->id_evaluacion . '-' . $cursos->id_numero; ?>]" 
                                value="<?php echo !empty($fechaValor) ? date('Y-m-d', strtotime($fechaValor)) : ''; ?>" 
                                min="<?php echo $fecha_inicio; ?>" 
                                max="<?php echo $fecha_fin; ?>">

                            </td>

                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <button type="submit" class="btn btn-custom">Guardar Fechas</button>
    </div>

</form>
<?php endforeach;?>
<?php endif;?>




<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>   
