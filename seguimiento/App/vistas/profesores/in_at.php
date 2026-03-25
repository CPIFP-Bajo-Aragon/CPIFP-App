



<form method="post" action="<?php echo RUTA_URL?>/PEnsenanza/insertar_at/<?php echo $datos['datos_modulo'][0]->id_modulo?>">
<div  class="card m-5 mb-5 shadow-lg" style="width:85%">


    <div class="card-header" style="background-color:#0583c3; color:white;">
        <h5>Ambiente de trabajo (AT)</h5>
    </div>


    <div class="card-body">
    <div class="table-responsive">
    <table class="table">
                  
            <thead>
                <tr>
                    <th style="width:45%"></th>
                    <!-- campo evaluaciones -->
                    <?php foreach ($datos['evaluaciones'] as $evaluaciones):?>
                        <th style="color:#0583c3; text-align: center;">
                        <?php echo $evaluaciones->evaluacion;?><br><?php echo '(' . $evaluaciones->fecha . ')';?>
                        </th>
                    <?php endforeach; ?>
                    <!-- Campo Total Curso -->
                    <th style="color:#0583c3; text-align: center;">Total <br> Curso</th>
                </tr>
            </thead>


            <tbody>
            <?php foreach ($datos['indicador_at'] as $at):?>
            <tr>
                <!-- Celda para la pregunta -->
                <td style="color:#0583c3;"><?php echo $at->pregunta; ?></td>     
                <!-- Celdas para las respuestas -->
                <?php foreach ($datos['evaluaciones'] as $evaluaciones): ?>
                <td>
                    <?php $respuesta = null;
                    foreach ($datos['respuestas_at'] as $respuestas) {
                        if ($respuestas->id_pregunta == $at->id_pregunta && $respuestas->id_evaluacion == $evaluaciones->id_evaluacion) {
                            $respuesta = $respuestas->respuesta;
                            break;
                        }
                    } ?>
                    <input type="number" class="form-control form-control-sm" id="pregunta_<?php echo $at->id_pregunta;?>_<?php echo $evaluaciones->id_evaluacion;?>" 
                    name="respuestas[<?php echo $at->indicador_corto.'-'.$at->id_pregunta.'-'.$evaluaciones->id_seguimiento?>]"
                    style="text-align: center; width: 100%;"
                    value="<?php foreach($datos['respuestas_at'] as $respuestas) :
                        if ($respuestas->id_pregunta == $at->id_pregunta && $evaluaciones->id_seguimiento == $respuestas->id_seguimiento && $respuestas->id_indicador == $at->id_indicador):
                            echo $respuesta;
                        endif;
                    endforeach;?>"
                    min="0" max="10">
                </td> 
                <?php endforeach; ?>

                <td></td>
                
            </tr>
            <?php  endforeach; ?>
            </tbody>


            <!-- FILA TOTALES -->
            <tfoot>
                <tr>
                    <td><strong style="color:#0583c3;">Total</strong></td>

                    <?php foreach ($datos['evaluaciones'] as $evaluaciones): ?>
                    <td>
                        <input type="text" class="form-control form-control-sm" name="total[<?php echo $evaluaciones->id_evaluacion; ?>]" 
                        style="text-align: center; width: 100%;"
                        value="<?php foreach($datos['seg_totales'] as $totales):
                        if($evaluaciones->id_seguimiento==$totales->id_seguimiento && $totales->id_indicador==$at->id_indicador):
                            echo $totales->total.'%';
                        endif;  endforeach;?>" readonly>
                    </td>
                    <?php endforeach; ?>

                    <td>
                        <input type="text" class="form-control form-control-sm"  style="text-align: center; width: 100%; color: #0583c3; font-weight: bold;"
                        value="<?php 
                            foreach($datos['total_curso'] as $respuestas) {
                                if($respuestas->id_indicador == $at->id_indicador) {
                                    echo $respuestas->total . '%'; 
                                }
                            }
                        ?>" readonly>
                    </td>
                </tr>
            </tfoot>

    </table>

        <div class="d-flex justify-content-end mb-3">
            <div class="mt-2">
                <input type="submit" id="boton-modal" class="btn" value="Confirmar">
            </div>
        </div>

    </div>
    </div>

</div>
</form>










