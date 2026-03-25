


<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_seguimiento.php' ?>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">





<div class="row m-5" style="position: sticky; top: 1px; background-color: #fff; z-index: 1; width: 90vw">
      
      <!-- Columna para los campos de fecha -->
      <div class="col-12 col-md-6 col-lg-3 mb-3">
          <div class="input-group">
            <span class="input-group-text" style="background-color: #0583c3; border:1px solid #0583c3; color: white;">Inicio:</span>
            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" style="background-color: white; border:1px solid #0583c3; color: #0583c3;" >
          </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <div class="input-group">
            <span class="input-group-text" style="background-color: #0583c3; border:1px solid #0583c3; color: white; ">Fin:</span>
            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" style="background-color: white; border:1px solid #0583c3; color: #0583c3;" >
          </div>
      </div>

      <!-- Columna para el botón -->
      <div class="col-12 col-md-6 col-lg-3 mb-3">
        <input type="button" id="filtrar" name="filtrar" class="btn btn-custom" value="Filtrar fechas" onclick="filtrarFechas();">
      </div>

      <!-- columna para el filtro de meses -->
      <div class="col-12 col-md-6 col-lg-3 mb-3">
          <div class="input-group" >
          <span class="input-group-text" style="background-color: #0583c3; border:1px solid #0583c3; color: white">Filtro mes:</span>
          <select class="form-control" style="background-color: white; border:1px solid #0583c3; color: #0583c3;" id="mesSeleccionado" onchange="filtrarPorMes()">
            <option value="">Seleccione un mes</option>
            <option value="9">Septiembre</option>
            <option value="10">Octubre</option>
            <option value="11">Noviembre</option>
            <option value="12">Diciembre</option>
            <option value="1">Enero</option>
            <option value="2">Febrero</option>
            <option value="3">Marzo</option>
            <option value="4">Abril</option>
            <option value="5">Mayo</option>
            <option value="6">Junio</option>
        </select>
      </div>

</div>





<div class="row mt-3 mb-4">
  <div class="col-2">
      <input type="button" id="anadir" class="btn" value="Enviar diario" onclick="enviar_diario();">
  </div>
</div> 



<?php if(!empty($datos['temas'])) :?>
<div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
<table class="table tabla-formato">
    
        <!-- thead tabla -->
        <thead style="position: sticky; top: 0; background-color: white; z-index:2;">
          
                <!-- FILA TEMAS -->
                <tr>
                    <th colspan="2">Temas</th>
                    <?php foreach($datos['temas'] as $temas): ?>
                        <th style="font-size:13px; text-align:center; width:auto;">
                            <div>
                                <?php
                                    switch ($temas->descripcion) {
                                        case "Dual": echo "Dual"; break;
                                        case "Examenes": echo "Exams."; break;
                                        case "Faltas": echo "Faltas"; break;
                                        case "Otros": echo "Otros"; break;
                                        case "Actividades": echo "Activ."; break;
                                        default: echo "Tem." . $temas->tema;
                                    }
                                ?>
                                <i class="bi bi-info-circle-fill" title="<?php echo $temas->descripcion;?>" style="color: white; cursor: pointer;"></i>
                            </div>
                        </th>
                    <?php endforeach; ?>
                    <th></th>
                </tr>


              <!-- FILA HORAS PREVISTAS -->
              <tr>
                  <th colspan="2" style="font-size:13px; width:auto; background-color: #d6eaf8; color:#0583c3;">Horas previstas</th>
                  <?php foreach($datos['temas'] as $temas): ?>
                  <th style="font-size:13px; width:auto; background-color: #d6eaf8; color:#0583c3; text-align:center;">
                    <div><?php echo $temas->total_horas?> hrs</div>
                  </th>
                  <?php endforeach; ?>
                  <th style="font-size:13px; width:auto; background-color: #d6eaf8; color:#0583c3; text-align:center;"></th>
              </tr>


              <!-- FILA HORAS IMPARTIDAS -->
              <tr>
                <th colspan="2" style="font-size:13px; width:auto; background-color: #d6eaf8; color:#0583c3;">Horas impartidas</th>
                <?php if (!empty($datos['temas'])) {
                    foreach ($datos['temas'] as $temas) {
                        $total = 0;
                        foreach ($datos['suma_horas_temas'] as $suma_temas) {
                            if ($temas->id_tema == $suma_temas->id_tema) {
                                $total = $suma_temas->suma_horas > 0 ? $suma_temas->suma_horas : 0;
                                break;
                            }
                        }
                        $color = ($total <= $temas->total_horas) ? 'green' : 'orangered'; ?>
                        <th style="font-size:13px; width:auto; background-color: #d6eaf8; color: <?php echo $color; ?>; text-align:center;">
                          <div><?php echo $total; ?> hrs</div>
                        </th>
                    <?php
                  }
                } ?>
                <th style="font-size:13px; width:auto; background-color: #d6eaf8; color:#0583c3; text-align:center;"></th>
              </tr>

        </thead>


        <form method="post" id="form_diario" action="<?php echo RUTA_URL?>/PDiario/insertar_diario/<?php echo $datos['datos_modulo'][0]->id_modulo?>">
        <tbody>

              <?php $calendario = $datos['calendario'];
              for ($i=0; $i < sizeof($calendario); $i++): ?>

              <tr style="font-size:12px; text-align:center" class="dias">


                    <!-- MUESTRA FECHA -->
                    <?php
                    $styleFecha = '';
                    if ($calendario[$i]->es_festivo == 'Sí') {
                      $styleFecha = 'background-color:#f8d7da;';
                    } elseif ($calendario[$i]->tiene_clase == 'No' && $calendario[$i]->id_evaluacion == '') {
                      $styleFecha = 'background-color:#dae0e5;';
                    } elseif ($calendario[$i]->tiene_clase == 'No' && $calendario[$i]->id_evaluacion != '') {
                      $styleFecha = 'background-color:#dae0e5;';
                    } elseif ($calendario[$i]->id_evaluacion != '') {
                      $styleFecha = 'background-color:white;';
                    }
                    $styleTexto = $calendario[$i]->id_evaluacion ? 'color: #0583c3; font-weight: bold;' : '';
                  ?>
                  <td style="<?php echo $styleFecha; ?>">
                    <div style="<?php echo $styleTexto; ?>">
                      <?php echo $calendario[$i]->fecha . ' (' . $calendario[$i]->dia_semana . ')'; ?>
                    </div>

                    <?php if ($calendario[$i]->id_evaluacion): ?>
                      <div style="color:#0583c3; font-weight:bold; font-size:14px;">
                        <?php echo $calendario[$i]->evaluacion; ?>
                      </div>
                    <?php endif; ?>
                  </td>



                  <!-- COLUMNA HORAS DIA-->
                  <?php
                    $styleHoras = '';
                    if ($calendario[$i]->es_festivo == 'Sí') {
                      $styleHoras = 'background-color:#f8d7da;';
                    } elseif ($calendario[$i]->tiene_clase == 'No') {
                      $styleHoras = 'background-color:#dae0e5;';
                    }
                  ?>
                  <td style="<?php echo $styleHoras; ?>">
                    <?php if ($calendario[$i]->es_festivo == 'No'): ?>
                      <div><?php echo $calendario[$i]->horas_dia?> Hrs.</div>
                    <?php endif; ?>
                  </td>



                  <!-- MUESTRA INPUTS TEMAS -->
                  <?php foreach($datos['temas'] as $temas):

                      $styleInput = '';
                      if ($calendario[$i]->es_festivo == 'Sí') {
                        $styleInput = 'background-color:#f8d7da;';
                      } elseif ($calendario[$i]->tiene_clase == 'No') {
                        $styleInput = 'background-color:#dae0e5;';
                      }

                      $valorInput = '';
                      foreach($datos['diario'] as $diario) {
                        if ($diario->f_form == $calendario[$i]->fecha && $diario->id_tema == $temas->id_tema) {
                          $valorInput = $diario->horas_dia;
                          break;
                        }
                      }
                    ?>

                    <td class="collapsible" style="<?php echo $styleInput;?>">
                      <?php if ($calendario[$i]->tiene_clase == 'Sí' && $calendario[$i]->es_festivo == 'No'): ?>
                        <input type="number" class="valor_horas form-control" style="width: 65px;" name="diario[<?php echo $calendario[$i]->fecha . '@' . $temas->id_tema ?>]"
                          value="<?php echo $valorInput;?>">
                      <?php endif; ?>
                    </td>
                  <?php endforeach; ?>


                  <!-- BOTON +  -->
                  <?php if ($calendario[$i]->tiene_clase == 'Sí' && $calendario[$i]->es_festivo == 'No'): ?>
                      <td>
                       <button type="button" class="btn" data-bs-toggle="collapse" style="background-color: #0583c3; color: white;"data-bs-target="#d[<?php echo $calendario[$i]->fecha . '@' . $temas->id_tema ?>]">+</button>
                      </td>
                  <?php else: ?>
                      <td style="<?php echo $calendario[$i]->es_festivo == 'Sí' ? 'background-color:#f8d7da;' : 'background-color:#dae0e5;'; ?>"></td>
                  <?php endif; ?>
               
              </tr>

                  
              <tr id="d[<?php echo $calendario[$i]->fecha.'@'.$temas->id_tema?>]" class="collapse">   
                <td colspan="100%" style="background-color: #f9f9f9;">
                    
                    <div style="padding: 10px; display: flex; justify-content: space-between;">
                        <div style="width: 30%; padding: 5px;">
                          <span><em style="color:grey">Plan realizado</em></span>
                          <textarea class="form-control" id="plan" name="plan[<?php echo $calendario[$i]->fecha?>@plan]" rows="3"><?php foreach ($datos['diario'] as $diario) : if ($diario->f_form == $calendario[$i]->fecha && $diario->plan != ''):echo $diario->plan;break; endif; endforeach;?></textarea>
                        </div>
                        <div style="width: 30%; padding: 5px;">
                          <span><em style="color:grey">Actividades</em></span>
                          <textarea class="form-control" id="actividad" name="actividad[<?php echo $calendario[$i]->fecha?>@actividad]" rows="3"><?php foreach ($datos['diario'] as $diario) : if ($diario->f_form == $calendario[$i]->fecha && $diario->actividad != ''):echo $diario->actividad;break; endif; endforeach;?></textarea>
                        </div>
                        <div style="width: 30%; padding: 5px;">
                          <span><em style="color:grey">Observaciones</em></span>
                          <textarea class="form-control" id="observaciones" name="observaciones[<?php echo $calendario[$i]->fecha?>@observaciones]" rows="3"><?php foreach ($datos['diario'] as $diario) : if ($diario->f_form == $calendario[$i]->fecha && $diario->observaciones != ''):echo $diario->observaciones;break; endif; endforeach;?></textarea>
                        </div>
                    </div>

                    <!-- Botón -->
                    <div style="text-align: end; padding-top: 10px;">
                       <input type="button" id="anadir_dos" style="background-color:orangered; color: white;" class="btn" value="Enviar diario" onclick="enviar_diario();">
                    </div>

                  </td>
                </tr>
              
            <?php endfor; ?>

        </tbody>
        </form>

</table>
</div>
<?php endif ?>



<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>







<script>



// validacion de maximo horas
  function enviar_diario() {
      const inputs = document.querySelectorAll(".valor_horas");
      for (const input of inputs) {
        const valor = input.value;
        if (valor === "") continue; 
        const numero = parseFloat(valor);
        if (numero < 0 || numero > 6 || isNaN(numero)) {
          alert("El maximo de horas permitido por dia es de 6.");
          input.focus();
          return;
        }
      }
      document.getElementById("form_diario").submit();
  }



  // Establecer el valor por defecto si existe ese mes en las opciones
  const mesActual = new Date().getMonth() + 1;
  const selectMes = document.getElementById('mesSeleccionado');
  for (let option of selectMes.options) {
    if (parseInt(option.value) === mesActual) {
      option.selected = true;
      break;
    }
  }



  // Función para filtrar filas por rango de fechas
  function filtrarFechas() {
    var fechaInicio = document.getElementById("fecha_inicio").value;
    var fechaFin = document.getElementById("fecha_fin").value;
    var filas = document.querySelectorAll(".dias"); // Todas las filas con clase 'dias'

    // Mostrar todas las filas al inicio
    filas.forEach(function(fila) {
      fila.style.display = "";
    });

    if (fechaInicio && fechaFin) {
      filas.forEach(function(fila) {
        var fechaCelda = fila.querySelector("td").innerText.split(" ")[0];
        var partes = fechaCelda.split("-");
        var fechaCeldaFormato = partes[2] + "-" + partes[1] + "-" + partes[0]; 

        if (fechaCeldaFormato < fechaInicio || fechaCeldaFormato > fechaFin) {
          fila.style.display = "none";
        }
      });
    }
  }




  // Función para filtrar filas por mes seleccionado
  function filtrarPorMes() {
    const mes = document.getElementById('mesSeleccionado').value;
    const filas = document.querySelectorAll('.dias');

    filas.forEach(fila => {
      const fechaCelda = fila.querySelector("td").innerText.trim().split(" ")[0]; // dd-mm-yyyy
      const partesFecha = fechaCelda.split("-");
      const mesFila = parseInt(partesFecha[1]);

      if (mes && mes != mesFila) {
        fila.style.display = "none";
      } else {
        fila.style.display = "";
      }
    });
  }


  // para que muestre el mes actual
  filtrarPorMes();





</script>










