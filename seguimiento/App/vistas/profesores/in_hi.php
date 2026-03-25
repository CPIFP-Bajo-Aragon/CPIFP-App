




<div  class="card m-5 mb-5 shadow-lg" style="width:85%">


    <div class="card-header" style="background-color:#0583c3; color:white;">
      <h5>HORAS DE DOCENCIA IMPARTIDAS (HI)</h5>
    </div>


    <div class="card-body">
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
            <?php foreach ($datos['indicador_hi'] as $hi):?>
            <tr>
                <!-- Celda pregunta -->
                <td style="color:#0583c3;"><?php echo $hi->pregunta; ?></td>
                      
                <!-- Celdas respuestas -->
                <?php foreach ($datos['evaluaciones'] as $evaluaciones): ?>
                <td>
                    <?php $respuesta = null;
                      foreach ($datos['respuestas_hi'] as $respuestas) {
                          if ($respuestas->id_pregunta == $hi->id_pregunta && $respuestas->id_evaluacion == $evaluaciones->id_evaluacion) {
                              $respuesta = $respuestas->respuesta;
                              break;
                          }
                      } ?>

                    <input type="number" class="form-control form-control-sm" id="<?php echo $aa->id_pregunta; ?>_<?php echo $evaluaciones->id_evaluacion;?>" 
                    name="respuestas[]" style="text-align: center; width: 100%;"
                    value="<?php foreach($datos['respuestas_hi'] as $respuestas ):
                      if($respuestas->id_pregunta==$hi->id_pregunta && $evaluaciones->id_seguimiento==$respuestas->id_seguimiento && $respuestas->id_indicador==$hi->id_indicador):
                          echo $respuesta;
                        endif;  
                      endforeach;?>" readonly style="background-color:#efefef;">  
                </td> 
                <?php endforeach; ?>

                <td></td>

            </tr>
            <?php endforeach;?> 
            </tbody>

            <tfoot>
            <tr>

                <td><strong style="color:#0583c3;">Total</strong></td>

                <?php foreach ($datos['evaluaciones'] as $evaluaciones): ?>
                <td>
                  <input type="text" class="form-control form-control-sm" name="total[<?php echo $evaluaciones->id_evaluacion; ?>]" 
                  style="text-align: center; width: 100%;"
                  value="<?php foreach($datos['seg_totales'] as $totales):
                    if($evaluaciones->id_seguimiento==$totales->id_seguimiento && $totales->id_indicador==$hi->id_indicador):
                        echo $totales->total.'%';
                    endif;  
                    endforeach;?>" readonly>
                </td>
                <?php endforeach; ?>

                <td>
                  <input type="text" class="form-control form-control-sm"  style="text-align: center; width: 100%; color: #0583c3; font-weight: bold;"
                    value="<?php foreach($datos['total_curso'] as $respuestas) {
                      if($respuestas->id_indicador == $hi->id_indicador) {
                          echo $respuestas->total . '%'; 
                        } } ?>" readonly>
                </td>

            </tr>
            </tfoot>

      </table>
      </div>
      </div>

</div>



