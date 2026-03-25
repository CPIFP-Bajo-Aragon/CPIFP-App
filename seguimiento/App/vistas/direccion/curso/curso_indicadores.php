

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_configuracion.php'?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">






<!-- Importar % año anterior -->
<div class="row mt-4 ml-4">
    <div class="col-12 col-md-5">
        <form action="<?php echo RUTA_URL ?>/curso/importar_porcentajes" method="post">
            <button type="submit" class="btn btn-md btn-custom" 
                <?php echo empty($datos['indicadores_ano_anterior']) ? 'disabled' : ''; ?>>
                <i class="fas fa-copy"></i> Importar % año anterior
            </button>
        </form>
    </div>
</div>





<!-- Tabla porcentajes por grados -->
<div class="table-responsive">
<table class="table tabla-formato">

        <thead>
            <tr>
                <th>Indicadores y porcentajes por grados
                    <a class="nav-link ms-2" style="font-size: 1.3em; display: inline-flex; align-items: center;" href="<?php echo RUTA_URL?>/curso">
                        <i class="fas fa-arrow-circle-left" style="vertical-align: middle"></i>
                    </a>
                </th>

                <?php foreach($datos['grados'] as $grados):
                if($grados->id_grado!=4): ?>
                <th class="text-center"><?php echo $grados->nombre ?></th>
                <?php endif; endforeach?>

                <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol,[50])):?>
                    <th style="text-align:center">Opciones<i class="fas fa-cogs ms-2" style="color: white; font-size: 1.3em; vertical-align: middle"></i></th>
                <?php endif ?> 
            </tr>
        </thead>


        
        <tbody>

            <?php foreach($datos['indicadores'] as $indicadores): ?>
            <tr>

                <!-- NOMBRE INDICADOR -->
                <td><?php echo '('.$indicadores->indicador_corto.') '.$indicadores->indicador?></td>

                <!-- PORCENTAJE INDICADORES -->
                <?php  
                    foreach($datos['indicadores_grados'] as $ind):
                      foreach($datos['grados'] as $grados):
                        if($ind->id_grado==$grados->id_grado && $ind->id_indicador==$indicadores->id_indicador && $grados->id_grado!=4): ?>
                            <td style="text-align: center;"> <?php echo $ind->porcentaje." %" ?> </td>
                    <?php endif; endforeach; 
                endforeach; ?>


                <!-- EDITAR INDICADORES-->
                <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol,[50])):?>
                <td style="text-align: center;">

                        <a data-bs-toggle="modal" data-bs-target="#editar_<?php echo $indicadores->id_indicador?>">
                            <img class="icono" src="<?php echo RUTA_Icon?>editar.png" style="width: 25px; height: 25px;"></img>
                        </a>

                        <div class="modal fade" id="editar_<?php echo $indicadores->id_indicador?>" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content rounded-3 shadow-lg">

                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h3 class="modal-title ms-3" id="editarModalLabel"><?php echo $indicadores->indicador.' ('.$indicadores->indicador_corto.')'?></h3>
                                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <!-- Modal body -->
                            <div class="modal-body info">
                            <div class="row ms-1 me-1">

                                <form action="<?php echo RUTA_URL?>/curso/editar_indicador/<?php echo $indicadores->id_indicador?>" method="post">
                                    
                                    <!-- Porcentaje por Grado -->
                                    <div class="row mt-3 mb-3">
                                        <?php foreach ($datos['grados'] as $grados): ?>
                                            <?php if ($grados->id_grado != 4): ?>
                                                <div class="mb-3 col-12 col-md-4"> 
                                                    <div class="input-group">
                                                        <label for="porcentaje" class="input-group-text" style="width:110px">% <?php echo $grados->nombre?><sup>*</sup></label>
                                                        <input type="hidden" name="grado[]" value="<?php echo $grados->id_grado?>">
                                                        <?php foreach ($datos['indicadores_grados'] as $ind):
                                                            if ($ind->id_grado == $grados->id_grado && $ind->id_indicador == $indicadores->id_indicador): ?>
                                                                <input type="number" class="form-control form-control-md" id="porcentaje" name="porcentaje[]" min="0" max="100" value="<?php echo $ind->porcentaje ?>" required>
                                                            <?php endif;
                                                        endforeach ?>
                                                    </div>
                                                </div>
                                            <?php endif;
                                        endforeach ?>
                                    </div>

                                    <div class="modal-footer">
                                        <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Confirmar">
                                    </div>
           
                                </form>

                            </div>
                            </div>

                        </div>
                        </div>
                        </div>


                </td>
                <?php endif ?>

            </tr>
            <?php endforeach ?>

        </tbody>

</table>
</div>
   
          


<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>



