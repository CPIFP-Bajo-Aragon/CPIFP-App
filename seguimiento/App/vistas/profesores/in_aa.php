



<form method="post" action="<?php echo RUTA_URL?>/PEnsenanza/insertar_aa/<?php echo $datos['datos_modulo'][0]->id_modulo?>">
<div  class="card m-5 mb-5 shadow-lg" style="width:85%">


      <div class="card-header" style="background-color:#0583c3; color:white;">
          <h5>Asitencia de los alumnos (AA)</h5>
      </div>


      <div class="card-body mt-3">
      <div class="table-responsive">
      <table class="table">

                <thead>
                  <tr>
                    <th style="width:45%"></th>
                    <!-- Campo nombre evaluacion -->
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
                <?php foreach ($datos['indicador_aa'] as $aa):?>
                <tr>

                    <!-- Celda pregunta -->
                    <td style="color:#0583c3;"><?php echo $aa->pregunta; ?></td>
                          
                    <!-- Celdas respuestas -->
                    <?php foreach ($datos['evaluaciones'] as $evaluaciones): ?>
                    <td>
                            <?php $respuesta = null;
                              foreach ($datos['respuestas_aa'] as $respuestas) {
                                  if ($respuestas->id_pregunta == $aa->id_pregunta && $respuestas->id_evaluacion == $evaluaciones->id_evaluacion) {
                                      $respuesta = $respuestas->respuesta;
                                      break;
                                  }
                              } ?>

                            <?php if($aa->id_pregunta=='35' || $aa->id_pregunta=='36' ){?>                             
                                <input type="number" class="form-control form-control-sm" id="<?php echo $aa->id_pregunta; ?>_<?php echo $evaluaciones->id_evaluacion;?>" 
                                name="respuestas[]" style="text-align: center; width: 100%;"
                                value="<?php foreach($datos['respuestas_aa'] as $respuestas ):
                                        if($respuestas->id_pregunta==$aa->id_pregunta && $evaluaciones->id_seguimiento==$respuestas->id_seguimiento && $respuestas->id_indicador==$aa->id_indicador):
                                            echo $respuesta;
                                        endif;  
                                        endforeach;?>" readonly style="background-color:#efefef;">  
                            <?php } else { ?>
                                <input type="number" class="form-control form-control-sm" id="pregunta_<?php echo $aa->id_pregunta;?>_<?php echo $evaluaciones->id_evaluacion;?>" 
                                name="respuestas[<?php echo $aa->indicador_corto.'-'.$aa->id_pregunta.'-'.$evaluaciones->id_seguimiento?>]"
                                style="text-align: center; width: 100%;"
                                    value="<?php foreach($datos['respuestas_aa'] as $respuestas) :
                                        if ($respuestas->id_pregunta == $aa->id_pregunta && $evaluaciones->id_seguimiento == $respuestas->id_seguimiento && $respuestas->id_indicador == $aa->id_indicador):
                                            echo $respuesta;
                                        endif;
                                        endforeach;?>">
                            <?php } ?> 
                    </td> 
                    <?php endforeach; ?>

                    <td></td>       

                </tr>
                <?php endforeach; ?>
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
                                    if($evaluaciones->id_seguimiento==$totales->id_seguimiento && $totales->id_indicador==$aa->id_indicador):
                                        echo $totales->total.'%';
                                    endif;  
                                    endforeach;?>" readonly>
                      </td>
                      <?php endforeach; ?>

                        <td>
                          <input type="text" class="form-control form-control-sm"  style="text-align: center; width: 100%; color: #0583c3; font-weight: bold;"
                            value="<?php foreach($datos['total_curso'] as $respuestas) {
                                    if($respuestas->id_indicador == $aa->id_indicador) {
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



