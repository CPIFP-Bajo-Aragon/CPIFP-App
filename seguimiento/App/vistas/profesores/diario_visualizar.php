

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
  <div class="col-12 col-md-5 d-flex flex-column flex-md-row gap-2">
    <a href="<?php echo RUTA_URL ?>/PDiario/<?php echo $datos['datos_modulo'][0]->id_modulo ?>" class="btn btn-custom flex-fill">
      Volver al diario
    </a>
    <a href="<?php echo RUTA_URL ?>/PDiario_visualizar/<?php echo $datos['datos_modulo'][0]->id_modulo ?>" class="btn flex-fill" 
      style="background-color: #0583c3; border:1px solid #0583c3; color: white">
      Visualizar actividades
    </a>
  </div>
</div>




<?php if(!empty($datos['temas'])) :?>
<div class="table-responsive tabla-visualizar mt-4">
<table class="table" style="width: 100%;">

    <thead>
      <tr>
          <th>Fecha</th>
          <th>Temas</th>
          <th>Plan realizado</th>
          <th>Actividades</th>
          <th>Observaciones</th>
      </tr>
    </thead>


    <tbody>

        <?php 
        $calendario = $datos['calendario'];
        for ($i=0; $i < sizeof($calendario); $i++): 

          $esFestivoOsinClase = ($calendario[$i]->es_festivo == 'Sí' || 
                                ($calendario[$i]->tiene_clase == 'No' && $calendario[$i]->id_evaluacion == ''));

          // Estilos para la fila y celdas
          $styleFila = '';
          $styleTd = '';
          if ($esFestivoOsinClase) {
            $styleFila = 'height: 25px;'; // fila más fina
            $styleTd = 'padding-top: 2px; padding-bottom: 2px;';
          }

          // Estilo para fondo de la fecha
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

          $temas_del_dia = [];
          $plan = '';
          $actividad = '';
          $observaciones = '';
          $primero = true;

          foreach ($datos['diario'] as $diario) {

              if ($diario->f_form == $calendario[$i]->fecha) {

                  if ($primero) {
                      $plan = $diario->plan;
                      $actividad = $diario->actividad;
                      $observaciones = $diario->observaciones;
                      $primero = false;
                  }

                  $temas_del_dia[] = [
                      'tema' => $diario->tema,
                      'descripcion' => $diario->descripcion,
                      'horas' => $diario->horas_dia,
                  ];
              }
          }

        ?>


        <tr style="font-size:12px; text-align:center; <?php echo $styleFila; ?>" class="dias">

            <!-- Fecha -->
            <td style="<?php echo $styleFecha; ?> text-align:left; vertical-align: middle; word-break: break-word; max-width: 60px; 
            <?php echo $styleTd; ?>">

                <div style="<?php echo $styleTexto; ?>">
                  <?php echo $calendario[$i]->fecha . ' (' . $calendario[$i]->dia_semana . ') '; 
                        if ($calendario[$i]->tiene_clase == 'Sí'): ?>
                          - <?php echo $calendario[$i]->horas_dia?> hrs.</div>
                        <?php endif;?>
                </div>
                
              <?php if ($calendario[$i]->id_evaluacion): ?>
                <div style="color: #0583c3; font-weight:bold; font-size:14px;">
                  <?php echo $calendario[$i]->evaluacion; ?>
                </div>
              <?php endif; ?>

            </td>


          <?php if ($esFestivoOsinClase): ?>

              <td style="<?php echo $styleFecha; ?> text-align:center; vertical-align: middle; <?php echo $styleTd; ?>"></td>
              <td style="<?php echo $styleFecha; ?> text-align:center; vertical-align: middle; <?php echo $styleTd; ?>"></td>
              <td style="<?php echo $styleFecha; ?> text-align:center; vertical-align: middle; <?php echo $styleTd; ?>"></td>
              <td style="<?php echo $styleFecha; ?> text-align:center; vertical-align: middle; <?php echo $styleTd; ?>"></td>

          <?php else: ?>



                <td style="<?php echo $styleFecha; ?> text-align:left; vertical-align: top; word-break: break-word; max-width: 60px; 
                    <?php echo $styleTd; ?>">
                    <?php if (!empty($temas_del_dia)): ?>
                        <?php foreach ($temas_del_dia as $t):
                            if ($t['descripcion']=='Examenes' || $t['descripcion']=='Dual' ||
                                $t['descripcion']=='Actividades' || $t['descripcion']=='Faltas' ||
                                $t['descripcion']=='Otros' ) { ?>
                                  <?php echo $t['descripcion'].' ('.$t['horas'].' hrs)' ?><br>
                              <?php } else{ ?>
                                 Tema <?php echo $t['tema'].' ('.$t['horas'].' hrs)' ?><br>
                             <?php }?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <em style="color:#999;">(sin datos)</em>
                    <?php endif; ?>
                </td>



                <td style="<?php echo $styleFecha; ?> text-align:left; vertical-align: top; word-break: break-word; max-width: 100px; 
                    <?php echo $styleTd; ?>">
                  <?php if ($plan): ?>
                    <?php echo nl2br(htmlspecialchars($plan)); ?>
                  <?php else: ?>
                    <em style="color:#999;">(sin datos)</em>
                  <?php endif; ?>
                </td>

                <td style="<?php echo $styleFecha; ?> text-align:left; vertical-align: top; word-break: break-word; max-width: 100px; 
                    <?php echo $styleTd; ?>">
                  <?php if ($actividad): ?>
                    <?php echo nl2br(htmlspecialchars($actividad)); ?>
                  <?php else: ?>
                    <em style="color:#999;">(sin datos)</em>
                  <?php endif; ?>
                </td>

                <td style="<?php echo $styleFecha; ?> text-align:left; vertical-align: middle; word-break: break-word; max-width: 100px; 
                    <?php echo $styleTd; ?>">
                  <?php if ($observaciones): ?>
                    <?php echo nl2br(htmlspecialchars($observaciones)); ?>
                  <?php else: ?>
                    <em style="color:#999;">(sin datos)</em>
                  <?php endif; ?>
                </td>

          <?php endif; ?>

        </tr>

    <?php endfor; ?>
    </tbody>


</table>
</div>
<?php endif ?>


<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>




<script>


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












