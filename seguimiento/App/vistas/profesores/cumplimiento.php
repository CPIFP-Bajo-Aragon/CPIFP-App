

<?php require_once RUTA_APP. '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP. '/vistas/inc/menu_seguimiento.php' ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">




<!-- PORCENTAJES RESUMENES -->
<div class="row">
    <div class="col-12">
        <div class="d-flex flex-wrap justify-content-start">
            <?php foreach ($datos['total_ep2'] as $total):?>
                <div class="total-cuadro">
                    <span>
                        <?php foreach ($datos['evaluaciones'] as $evaluaciones): 
                            if($evaluaciones->id_seguimiento == $total->id_seguimiento){
                                echo $evaluaciones->evaluacion;
                            }
                        endforeach; ?>
                    </span>
                    <span>
                        <?php foreach ($datos['evaluaciones'] as $evaluaciones): 
                            if($evaluaciones->id_seguimiento == $total->id_seguimiento){
                                echo $total->total . ' %';
                            }
                        endforeach; ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>



<!-- FORMULARIO PREGUNTAS -->
<form  method="post" action="<?php echo RUTA_URL?>/PCumplimiento/insertar_cumplimiento/<?php echo $datos['datos_modulo'][0]->id_modulo?>">
<?php foreach($datos['solo_categorias'] as $categ):?>

    <div class="table-responsive">
    <table class="table tabla-formato w-75">

            <thead>
                <tr>
                    <!-- categoria -->
                    <th style="width:40%">
                        <?php echo $categ->categoria;?>
                        <?php if ($categ->id_categoria == 9 || $categ->id_categoria == 10 ):?>
                          <br><span>(Solo si lo ha habido)</span> 
                        <?php endif ?>
                    </th>
                    <!-- evaluaciones -->
                    <?php foreach ($datos['evaluaciones'] as $evaluaciones): ?>
                    <th class="text-center"><?php echo $evaluaciones->evaluacion;?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>


            <tbody>
            <?php foreach ($datos['preguntas'] as $pregun):
            if($pregun->id_categoria==$categ->id_categoria):?>

                <tr>
                    <!-- preguntas -->
                    <td><?php echo $pregun->pregunta?></td> 
                            
                    <!-- select respuestas -->
                    <?php foreach ($datos['evaluaciones'] as $evaluaciones):?>
                    <td style="width:400px;">
                             
                        <select name="<?php echo $pregun->id_indicador.'-'.$categ->id_categoria.'-'.$pregun->id_pregunta.'-'.$evaluaciones->id_seguimiento?>" 
                            id="evaluacion_<?php echo $pregun->id_pregunta . '_' . $evaluaciones->id_evaluacion?>" class="form-select">

                            <!-- Opción por defecto -->
                            <option value="" 
                                <?php foreach ($datos['ep2'] as $respu) { 
                                    if(($pregun->id_pregunta == $respu->id_pregunta) && ($respu->id_seguimiento == $evaluaciones->id_seguimiento) && ($respu->respuesta == '')) {
                                        echo "selected";
                                    }
                                } ?>>
                            </option>

                            <!-- opcion no o casi -->
                            <option value="0" 
                                <?php foreach ($datos['ep2'] as $respu) { 
                                        if(($pregun->id_pregunta == $respu->id_pregunta) && ($respu->id_seguimiento == $evaluaciones->id_seguimiento) && ($respu->respuesta == 0)) {
                                            echo "selected";
                                        }
                                } ?>>No o casi
                            </option>

                            <!-- opcion poco -->
                            <option value="2.5" 
                                <?php foreach ($datos['ep2'] as $respu) { 
                                    if(($pregun->id_pregunta == $respu->id_pregunta) && ($respu->id_seguimiento == $evaluaciones->id_seguimiento) && ($respu->respuesta == 2.5)) {
                                        echo "selected";
                                    }
                                } ?>>Poco
                            </option>

                            <!-- opcion a medias -->
                            <option value="5" 
                                <?php foreach ($datos['ep2'] as $respu) { 
                                    if(($pregun->id_pregunta == $respu->id_pregunta) && ($respu->id_seguimiento == $evaluaciones->id_seguimiento) && ($respu->respuesta == 5)) {
                                        echo "selected";
                                    }
                                } ?>>A medias
                            </option>

                            <!-- opcion bastante -->
                            <option value="7.5" 
                                <?php foreach ($datos['ep2'] as $respu) { 
                                    if(($pregun->id_pregunta == $respu->id_pregunta) && ($respu->id_seguimiento == $evaluaciones->id_seguimiento) && ($respu->respuesta == 7.5)) {
                                        echo "selected";
                                    }
                                } ?>>Bastante
                            </option>

                            <!-- opcion si o casi -->
                            <option value="10" 
                                <?php foreach ($datos['ep2'] as $respu) { 
                                    if(($pregun->id_pregunta == $respu->id_pregunta) && ($respu->id_seguimiento == $evaluaciones->id_seguimiento) && ($respu->respuesta == 10)) {
                                        echo "selected";
                                    }
                                } ?>>Si o casi
                            </option>

                        </select>
                                 
                    </td>
                    <?php endforeach; ?>

                </tr>

            <?php endif; 
            endforeach?>
            </tbody>

        </table>
        </div>

<?php endforeach;?>

    <!-- BOTON ENVIO -->
    <div class="col mt-5 mb-4"  style="text-align: center;">
        <input type="submit" id="boton-modal" class="btn" value="Enviar respuestas">
    </div>

</form>



<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>


