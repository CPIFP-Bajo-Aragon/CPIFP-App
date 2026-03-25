



<form method="post" action="<?php echo RUTA_URL?>/PEnsenanza/insertar_ap2/<?php echo $datos['datos_modulo'][0]->id_modulo?>">
<div class="card m-5 mb-5 shadow-lg" style="width:85%"> 


      <div class="card-header" style="background-color:#0583c3; color:white;">
          <h5>Aprobados en 2ª Convocatoria (AP2)</h5>
      </div>


      <div class="card-body mt-3">
      <div class="table-responsive">
      <table class="table" style="width:80%">

             <!-- head tabla -->
              <thead>
                  <tr>
                    <th style="width:60%;"></th>
                    <th style="color:#0583c3;text-align: center; width:50%;">Total</th> 
                  </tr>
              </thead>

              <!-- body tabla -->
              <tbody>
                <?php foreach ($datos['indicador_ap2'] as $ap2):?>
                    <tr>
                        <td style="color:#0583c3;"><?php echo $ap2->pregunta;?></td>
                        <td>
                          <input type="number" class="form-control form-control-sm" id="pregunta_<?php echo $ap2->id_pregunta;?>_<?php echo $datos['segui_ap2'][0]->ap2;?>" 
                          name="respuestas[<?php echo $ap2->indicador_corto.'-'.$ap2->id_pregunta.'-'.$datos['segui_ap2'][0]->ap2;?>]"
                          style="text-align: center; width: 100%;"
                          value="<?php foreach($datos['respuestas_ap2'] as $respuestas_ap2):
                                if($respuestas_ap2->id_pregunta==$ap2->id_pregunta):
                                  echo $respuestas_ap2->respuesta;
                                endif; endforeach; ?>"  min="0"> 
                        </td> 
                    </tr>
                  <?php endforeach; ?> 
              </tbody>

              <!-- footer tabla -->
              <tfoot>
                  <tr>
                    <td style="color:#0583c3;">Total</td>
                    <td>
                      <input type="text" class="form-control form-control-sm" style="text-align: center; width: 100%; color: #0583c3; font-weight: bold;"
                        value="<?php foreach($datos['total_curso'] as $respuestas){
                              if($respuestas->id_indicador == 7) {
                                  echo $respuestas->total . '%'; 
                              }
                        } ?>" readonly>
                    </td>
                  </tr>
              </tfoot>

        </table>

          <!-- boton envio -->
          <div class="d-flex justify-content-end">
              <div class="mt-2">
                  <input type="submit" class="btn"  id="boton-modal" value="Confirmar">
              </div>
          </div>

    </div>
    </div>
    
  </div>
</form>








