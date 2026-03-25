

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_seguimiento.php'?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<!-- JS de Bootstrap y jQuery (dependencia de Bootstrap para los modales) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script> 





<!-- CUADRO MODULO CONFORME O NO CONFORME -->
<?php 
$style = '';
$modulo_conforme = true;
$indicadores_verificados = 0; // <- Contador de indicadores que tienen datos reales

foreach ($datos['indicadores_grado'] as $indicador) {
    $encontrado = false;

    foreach ($datos['total_curso'] as $respuestas):
        if ($respuestas->id_indicador == $indicador->id_indicador):
            $encontrado = true;
            $indicadores_verificados++;
            if ($respuestas->total < $indicador->porcentaje):
                $modulo_conforme = false;
                break 2; // ← Rompe ambos bucles
            endif;
        endif;
    endforeach;

    // Si no se encuentra ningún dato real para un indicador, lo contamos como no conforme
    if (!$encontrado) {
        $modulo_conforme = false;
        break;
    }
}

// Estilo visual
if ($modulo_conforme && $indicadores_verificados > 0) {
  $style = 'style="color: green; font-weight: bold; background-color: #d4edda; padding: 10px; border-radius: 5px;"';
} else {
  $style = 'style="color: orangered; font-weight: bold; background-color: #f8d7da; padding: 10px; border-radius: 5px;"';
}
?>

<div class="row m-3">
  <div class="col-12">
    <div class="d-flex flex-wrap justify-content-start">
      <div <?php echo $style; ?>>
        <?php 
          echo $modulo_conforme && $indicadores_verificados > 0 ? "Módulo conforme" : "Módulo no conforme";
        ?>
      </div>
    </div>
  </div>
</div>






<!-- TABLA RESUMEN INDICADORES -->
<div class="table-responsive">
<table class="table tabla-formato">

          <!-- cabecera tabla -->
          <thead>
              <tr>
                  <th>Indicador</th>
                  <th class="text-center">% Minimos</th>
                  <?php foreach ($datos['evaluaciones'] as $evaluaciones):?>
                  <th class="text-center"><?php echo $evaluaciones->evaluacion;?></th>
                  <?php endforeach;?>
                  <th class="text-center">Total curso</th>
              </tr>
          </thead>


          <!-- body tabla -->
          <tbody>
          <?php foreach($datos['indicadores_grado'] as $indicador):?>
          <tr>

                  <!-- nombre indicador -->
                  <td style="color: #0583c3; font-weight: bold; padding: 8px 10px;"><?php echo $indicador->indicador. " (" . $indicador->indicador_corto . ")";?></td>
                  <td class="text-center" style="color: #0583c3; font-weight: bold; padding: 8px 10px;"><?php echo $indicador->porcentaje." %";?></td>

                  <!-- porcentajes evaluaciones -->
                  <?php if($indicador->indicador_corto != "AP2" && $indicador->indicador_corto != "EP1"){

                        foreach ($datos['evaluaciones'] as $evaluaciones): ?>
                          <td class="text-center" 
                              <?php 
                              $color = '';
                              // Recorre los totales de cada evaluación
                              foreach ($datos['seg_totales'] as $tot) {
                                if ($indicador->id_indicador == $tot->id_indicador && $evaluaciones->id_seguimiento == $tot->id_seguimiento) {
                                  if ($indicador->porcentaje > $tot->total) {
                                    $color = 'style="color: orangered; font-weight:bold"';
                                  }
                                  break; 
                                }
                              }
                              echo $color; 
                              ?>>
                              <?php 
                              // Imprime el total de la evaluación
                              foreach ($datos['seg_totales'] as $tot) {
                                if ($indicador->id_indicador == $tot->id_indicador && $evaluaciones->id_seguimiento == $tot->id_seguimiento) {
                                  echo $tot->total . "%";
                                }
                              }
                              ?>
                          </td>
                        <?php endforeach; ?>

                  <?php } else { ?>

                        <?php foreach ($datos['evaluaciones'] as $evaluaciones):?>
                          <td class="text-center"> --- </td>
                        <?php endforeach; ?>

                <?php }?>


                <!-- porcentaje total curso -->
                <td class="text-center"
                    <?php 
                    $color = '';
 
                      foreach ($datos['total_curso'] as $respuestas):
                        if ($respuestas->id_indicador == $indicador->id_indicador):
                            if ($indicador->porcentaje > $respuestas->total){
                                $color = 'style="background-color: #f8d7da; color: orangered; font-weight:bold;"';
                            }else{
                                $color = 'style="background-color:  #d4edda; color:green; font-weight:bold;"';
                            }
                          break;  
                        endif;
                      endforeach;
               
                    echo $color; 
                    ?>>

                    
                    <?php 
                    // Mostrar el total del curso

                      foreach ($datos['total_curso'] as $respuestas):
                        if ($respuestas->id_indicador == $indicador->id_indicador):
                          echo $respuestas->total . '%'; 
                        endif;
                      endforeach;
       
                    ?>
                </td>

          </tr>
          <?php endforeach; ?>
          </tbody>


          <!-- footer tabla -->
          <tfoot>
          <tr> 

              <td style="color:orangered">* Indicar causas y acciones a tomar si alguna casilla sale resaltada</td>
              <td class="text-center"> --- </td> 

              <!-- boton causas y soluciones -->
              <?php foreach ($datos['evaluaciones'] as $evaluaciones): ?>
              <form method="post" action="<?php echo RUTA_URL?>/PAnalisis/causas_soluciones/<?php echo $datos['datos_modulo'][0]->id_modulo?>">
              <td class="text-center">

                  <!-- Botón para abrir el modal -->
                   <button class="btn btn-md btn-custom" type="button" data-bs-toggle="modal" data-bs-target="#causasSolucionesModal_<?php echo $evaluaciones->id_seguimiento?>">
                      Causas y Soluciones
                  </button> 

                  <!-- Modal -->
                  <div class="modal fade" id="causasSolucionesModal_<?php echo $evaluaciones->id_seguimiento?>" tabindex="-1" aria-labelledby="causasSolucionesModalLabel_<?php echo $evaluaciones->id_seguimiento?>" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                  <div class="modal-content">

                      <div class="modal-header">
                          <h5 class="modal-title" id="causasSolucionesModalLabel_<?php echo $evaluaciones->id_seguimiento?>">Causas y Soluciones</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>

                      <div class="modal-body">

                              <div class="mt-3 mb-2 text-left" style="color:#0583c3">CAUSAS</div>

                              <!-- Select para Causas -->
                              <div class="input-group mb-3">
                                  <select name="causa[<?php echo $evaluaciones->id_seguimiento?>]" id="causa_<?php echo $evaluaciones->id_seguimiento?>" class="form-select">
                                      <option value="0">Selecciona una causa</option>
                                      <?php foreach($datos['causas_soluciones'] as $cs): 
                                          if($cs->id_accion == "1"):?>
                                              <option name="causa[]" value="<?php echo $cs->id_solucion?>" 
                                              <?php 
                                                  if(!empty($datos['valoraciones'])):
                                                  foreach($datos['valoraciones'] as $valoracion):
                                                  if ($cs->id_solucion == $valoracion->causa && $evaluaciones->id_seguimiento==$valoracion->id_seguimiento) {
                                                      echo 'selected';
                                                  } else {
                                                      echo '';
                                                  } endforeach; endif;
                                                ?>>
                                              <?php echo $cs->solucion?></option>
                                      <?php endif; endforeach; ?>
                                  </select>
                              </div>

                              <!-- Otro Select para Causas -->
                              <div class="input-group mb-3">
                                  <select name="causa2[<?php echo $evaluaciones->id_seguimiento?>]" id="causa2_<?php echo $evaluaciones->id_seguimiento?>" class="form-select">
                                      <option value="0">Selecciona otra causa</option>
                                      <?php foreach($datos['causas_soluciones'] as $cs): 
                                          if($cs->id_accion == "1"):?>
                                              <option name="causa2[]" value="<?php echo $cs->id_solucion?>" 
                                              <?php 
                                                  if(!empty($datos['valoraciones'])):
                                                  foreach($datos['valoraciones'] as $valoracion):
                                                  if ($cs->id_solucion == $valoracion->causa2 && $evaluaciones->id_seguimiento==$valoracion->id_seguimiento) {
                                                      echo 'selected';
                                                  } else {
                                                      echo '';
                                                  } endforeach; endif;
                                                ?>>
                                              <?php echo $cs->solucion?></option>
                                      <?php endif; endforeach; ?>
                                  </select>
                              </div>

                              <!-- Espaciado entre Causas y Otros campos -->
                              <div class="mt-4 mb-2 text-left" style="color:#0583c3">OTRAS CAUSAS</div>

                              <!-- Campos de otros -->
                              <div class="mb-3">
                              <input type="text" class="form-control" name="otro1[<?php echo $evaluaciones->id_seguimiento ?>]" placeholder="Otras causas" 
                                    value="<?php if(!empty($datos['valoraciones'])):
                                      foreach($datos['valoraciones'] as $valoracion):
                                      if ( $evaluaciones->id_seguimiento==$valoracion->id_seguimiento) :
                                          echo $valoracion->otro1;
                                  endif; endforeach; endif;?>">
                              </div>


                              <div class="mb-3">
                              <input type="text" class="form-control" name="otro2[<?php echo $evaluaciones->id_seguimiento ?>]" placeholder="Otras causas" 
                                    value="<?php  if(!empty($datos['valoraciones'])):
                                      foreach($datos['valoraciones'] as $valoracion):
                                      if ($evaluaciones->id_seguimiento==$valoracion->id_seguimiento) :
                                          echo $valoracion->otro2;                                        
                                      endif; endforeach; endif;?>">
                              </div>


                              <div class="mb-3">
                              <input type="text" class="form-control" name="otro3[<?php echo $evaluaciones->id_seguimiento ?>]" placeholder="Otra causa" 
                                value="<?php if(!empty($datos['valoraciones'])):
                                  foreach($datos['valoraciones'] as $valoracion):
                                    if ($evaluaciones->id_seguimiento==$valoracion->id_seguimiento) :
                                        echo $valoracion->otro3;                                       
                                    endif; endforeach; endif;?>">
                              </div>


                              <div class="mt-4 mb-2 text-left" style="color:#0583c3">SOLUCIONES</div>

                              <!-- Select para Soluciones -->
                              <div class="input-group mt-3 mb-3">
                                  <select name="solucion[<?php echo $evaluaciones->id_seguimiento?>]" id="solucion_<?php echo $evaluaciones->id_seguimiento ?>" class="form-select">
                                      <option value="0">Selecciona una solución</option>
                                      <?php foreach($datos['causas_soluciones'] as $cs):
                                          if($cs->id_accion == "2"):?>
                                              <option name="solucion[]" value="<?php echo $cs->id_solucion?>" 
                                              <?php 
                                                if(!empty($datos['valoraciones'])):
                                                foreach($datos['valoraciones'] as $valoracion):
                                                if ($cs->id_solucion == $valoracion->solucion && $evaluaciones->id_seguimiento==$valoracion->id_seguimiento) {
                                                    echo 'selected';
                                                } else {
                                                    echo '';
                                                } endforeach; endif;
                                              ?>>
                                              <?php echo $cs->solucion?></option>
                                      <?php endif; endforeach; ?>
                                  </select>
                              </div>

                              <!-- Otro Select para Soluciones -->
                              <div class="input-group mb-3">
                                  <select name="solucion2[<?php echo $evaluaciones->id_seguimiento?>]" id="solucion2_<?php echo $evaluaciones->id_seguimiento ?>" class="form-select">
                                      <option value="0">Selecciona otra solución</option>
                                      <?php foreach($datos['causas_soluciones'] as $cs):
                                          if($cs->id_accion == "2"):?>
                                              <option name="solucion2[]" value="<?php echo $cs->id_solucion?>" 
                                                  <?php 
                                                      if(!empty($datos['valoraciones'])):
                                                      foreach($datos['valoraciones'] as $valoracion):
                                                      if ($cs->id_solucion == $valoracion->solucion2 && $evaluaciones->id_seguimiento==$valoracion->id_seguimiento) {
                                                          echo 'selected';
                                                      } else {
                                                          echo '';
                                                      } endforeach; endif;
                                                    ?>>
                                                <?php echo $cs->solucion?>
                                              </option>
                                      <?php endif; endforeach; ?>
                                  </select>
                              </div>

                              <!-- Otro Select para Soluciones -->
                              <div class="input-group mb-3">
                                  <select name="solucion3[<?php echo $evaluaciones->id_seguimiento?>]" id="solucion3_<?php echo $evaluaciones->id_seguimiento ?>" class="form-select">
                                      <option value="0">Selecciona otra solución</option>
                                      <?php foreach($datos['causas_soluciones'] as $cs):
                                          if($cs->id_accion == "2"):?>
                                              <option name="solucion3[]" value="<?php echo $cs->id_solucion?>" 
                                              <?php 
                                                  if(!empty($datos['valoraciones'])):
                                                  foreach($datos['valoraciones'] as $valoracion):
                                                  if ($cs->id_solucion == $valoracion->solucion3 && $evaluaciones->id_seguimiento==$valoracion->id_seguimiento) {
                                                      echo 'selected';
                                                  } else {
                                                      echo '';
                                                  } endforeach; endif;
                                                ?>>
                                              <?php echo $cs->solucion?></option>
                                      <?php endif; endforeach; ?>
                                  </select>
                              </div>

                              <!-- Espaciado entre Soluciones y Observaciones -->
                              <div class="mt-4 mb-2 text-left" style="color:#0583c3">OBSERVACIONES</div>

                              <!-- Textarea para Observaciones -->
                              <div class="mb-3">
                                <textarea class="form-control" id="<?php echo $evaluaciones->id_seguimiento ?>" name="observaciones[<?php echo $evaluaciones->id_seguimiento?>]" placeholder="Escribe tus observaciones" rows="3">
                                  <?php if(!empty($datos['valoraciones'])):
                                    foreach($datos['valoraciones'] as $valoracion):
                                      if ($evaluaciones->id_seguimiento==$valoracion->id_seguimiento):echo $valoracion->observaciones; 
                                  endif; endforeach; endif;?></textarea>
                              </div>

                      </div>


                      <div class="modal-footer">
                          <input type="submit" class="btn" id="boton-modal" name="aceptar" id="confirmar" value="Enviar">
                      </div>


                  </div>
                  </div>
                  </div>

              </td>  
              </form>
              <?php endforeach; ?>

          </tr>
          </tfoot>

</table>
</div>





<!-- TABLA RESUMEN EP1 -->
<div class="table-responsive">
<table class="table tabla-formato">

  <!-- cabecera tabla -->
  <thead>
      <tr>
        <th>Indicador EP1</th>
        <th style="text-align: center;">Resultado</th>
        <th>Opciones</th>
        <th>Observaciones</th>
      </tr>
  </thead>

  <!-- body tabla -->
  <tbody>
    <?php foreach($datos['preguntas_ep1'] as $ep1): ?>
        <tr>

          <td style="color: #0583c3; font-weight: bold; padding: 8px 10px;"><?php echo $ep1->pregunta;?></td>    

          <!-- valores ep1 -->
          <td style="text-align: center; color: #0583c3; font-weight: bold; padding: 8px 10px;">
            <?php foreach($datos['valores_ep1'] as $valores):
              if($ep1->id_pregunta == $valores->id_pregunta):
                echo $valores->respuesta. '%';
              endif;
            endforeach; ?>
          </td>

          <!-- boton modal observaciones -->
          <td class="text-center" style="color: #0583c3; font-weight: bold; padding: 8px 10px;">
            <a href="#" data-toggle="modal" data-target="#observaciones<?php echo $ep1->id_pregunta;?>" style="text-decoration: none; color: inherit;">
              <i class="fas fa-comment-alt" style="margin-right: 5px;"></i> Obs.
            </a>
          </td>

          <!-- campo observaciones -->
          <td><?php foreach($datos['valores_ep1'] as $valores):
            if($valores->id_pregunta==$ep1->id_pregunta){
              echo $valores->observaciones;         
            } endforeach; ?>
          </td>

        </tr>
    <?php endforeach; ?>


          <tr>
            <td style="background-color: #d6eaf8; color: #0583c3; font-weight: bold;">Total EP1</td>

            <?php
            $ep1_indicador = null;
            foreach ($datos['indicadores_grado'] as $indicador) {
              if ($indicador->indicador_corto == 'EP1') {
                $ep1_indicador = $indicador;
                break;
              }
            }

            $style_td = 'style="text-align: center;"'; 

            $ep1_total = null;
            foreach ($datos['total_curso'] as $total) {
              if ($total->indicador_corto == 'EP1') {
                $ep1_total = $total;
                if ( $total->total < $ep1_indicador->porcentaje) {
                  $style_td = 'style="background-color: #d6eaf8; color: orangered; font-weight: bold; text-align: center;"';
                } else{
                  $style_td = 'style="background-color: #d6eaf8; color:  #0583c3;; font-weight: bold; text-align: center;"';
                }

                break;
              }
            }
            ?>

            <td colspan="3" <?php echo $style_td; ?>>
              <?php echo $ep1_total ? $ep1_total->total . ' %' : ''; ?>
            </td>
          </tr>



  </tbody>

</table>
</div>




<!-- MODAL OBSERVACIONES EP1 -->
<?php foreach($datos['preguntas_ep1'] as $ep1): ?>
<div class="modal fade" id="observaciones<?php echo $ep1->id_pregunta;?>" tabindex="-1" role="dialog" aria-labelledby="modalObservationLabel<?php echo $ep1->id_pregunta;?>" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">

      <!-- MODAL HEADER -->
      <div class="modal-header">
        <h5 class="modal-title" id="modalObservationLabel<?php echo $ep1->id_pregunta; ?>">Observaciones <?php echo $ep1->pregunta;?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
      </div>

       <!-- MODAL BODY -->
      <div class="modal-body">
      <form action="<?php echo RUTA_URL ?>/PAnalisis/guardar_observaciones/<?php echo $datos['datos_modulo'][0]->id_modulo ?>" method="POST">
            <?php 
            $observacion = '';
            foreach($datos['valores_ep1'] as $valores) {
                if ($valores->id_pregunta == $ep1->id_pregunta) {
                    $observacion = $valores->observaciones;
                    break;
                }
            }
            ?>
            <textarea class="form-control" id="observacion<?php echo $ep1->id_pregunta; ?>" name="observaciones" rows="4">
                <?php echo htmlspecialchars($observacion);?>
            </textarea>
            <input type="hidden" name="id_pregunta" value="<?php echo $ep1->id_pregunta; ?>">
      </div>

      <!-- MODAL FOOTER -->
      <div class="modal-footer">
        <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Guardar">
      </div>

    </form>

</div>
</div>
</div>
</div>
<?php endforeach; ?>





<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>




<script>
  // Función para enviar datos de causas, soluciones y observaciones
  function enviarCausasSoluciones(idSeguimiento) {
      var causa = document.getElementById('causa_' + idSeguimiento).value;
      var solucion = document.getElementById('solucion_' + idSeguimiento).value;
      var observaciones = document.getElementById('observaciones_' + idSeguimiento).value;

      if (causa != "0" && solucion != "0" && observaciones != "") {
          // Enviar datos al servidor (AJAX o formulario según sea necesario)
          alert("Datos enviados: Causa: " + causa + ", Solución: " + solucion + ", Observaciones: " + observaciones);
      } else {
          alert("Por favor, complete todos los campos.");
      }
  }
</script>





















          




 